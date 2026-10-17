<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InitialCountController extends Controller
{
    /**
     * Display the initial stock count verification session.
     */
    public function index(): View
    {
        $session = InventoryCount::with(['items.product.unit', 'items.asset', 'conductor', 'verifier', 'location'])
            ->first();

        // Jika belum ada sesi stok awal, inisialisasi sesi perdana berdasarkan data seeder Excel
        if (! $session) {
            $mainLocation = Location::where('code', 'GU-STG-LED')->first() ?? Location::first();
            $conductor = User::role('Warehouse')->first() ?? Auth::user();

            $session = InventoryCount::create([
                'count_number' => 'OP-202610-0001',
                'title' => 'Pendataan Fisik Stok Awal Gedung Utama (Berdasarkan Rekap Spreadsheet Excel)',
                'location_id' => $mainLocation?->id,
                'conducted_by' => $conductor->id,
                'status' => 'pending_verification',
                'notes' => 'Sesi verifikasi saldo fisik perdana sebelum sistem live operasional.',
            ]);

            // Masukkan data saldo dari seeder sebagai item opname yang siap disahkan
            $balances = StockBalance::with('product')->get();
            foreach ($balances as $bal) {
                InventoryCountItem::create([
                    'inventory_count_id' => $session->id,
                    'product_id' => $bal->product_id,
                    'ownership' => $bal->ownership,
                    'condition' => $bal->condition,
                    'system_quantity' => $bal->quantity,
                    'physical_quantity' => $bal->quantity,
                    'difference' => 0,
                    'is_verified' => false,
                    'notes' => 'Hitung fisik sesuai Excel ('.$bal->condition.')',
                ]);
            }

            $session->load(['items.product.unit', 'items.asset', 'conductor', 'verifier', 'location']);
        }

        $items = $session->items()->with(['product.unit', 'product.category'])->get();
        $availableProducts = Product::where('is_active', true)->with('unit')->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        return view('initial_counts.index', compact('session', 'items', 'availableProducts', 'locations'));
    }

    /**
     * Warehouse verifies and commits the initial stock count.
     */
    public function verify(Request $request, InventoryCount $count): RedirectResponse
    {
        // Proteksi otorisasi Spatie
        if (! Auth::user()->can('verify stock count') && ! Auth::user()->hasRole(['Warehouse', 'Owner', 'Superuser'])) {
            abort(403, 'Hanya Tim Warehouse atau Owner yang berwenang mengesahkan stok awal.');
        }

        if ($count->status === 'verified') {
            return back()->with('info', 'Sesi stok awal ini sudah disahkan sebelumnya.');
        }

        DB::transaction(function () use ($count) {
            $count->update([
                'status' => 'verified',
                'verified_by' => Auth::id(),
                'finalized_at' => now(),
                'notes' => ($count->notes ? $count->notes."\n" : '').'Disahkan resmi oleh '.Auth::user()->name.' pada '.now()->format('d/m/Y H:i').'.',
            ]);

            $count->items()->update(['is_verified' => true]);

            // Sync atau update saldo stok fisik berjalan
            foreach ($count->items as $item) {
                $balance = StockBalance::firstOrNew([
                    'product_id' => $item->product_id,
                    'location_id' => $count->location_id,
                    'ownership' => $item->ownership,
                    'condition' => $item->condition,
                ]);
                $balance->quantity = $item->physical_quantity;
                $balance->save();
            }

            // Buat record mutasi resmi Initial Count jika belum ada
            $movementNumber = 'MOV-INIT-'.date('Ym').'-0001';
            $existingMovement = StockMovement::where('movement_number', $movementNumber)->first();

            if (! $existingMovement) {
                $movement = StockMovement::create([
                    'movement_number' => $movementNumber,
                    'type' => 'initial_count',
                    'source_location_id' => null,
                    'destination_location_id' => $count->location_id,
                    'reference_type' => InventoryCount::class,
                    'reference_id' => $count->id,
                    'created_by' => Auth::id(),
                    'approved_by' => Auth::id(),
                    'status' => 'completed',
                    'notes' => 'Pengesahan saldo awal fisik Gudang Utama.',
                ]);

                foreach ($count->items as $item) {
                    StockMovementItem::create([
                        'stock_movement_id' => $movement->id,
                        'product_id' => $item->product_id,
                        'asset_id' => $item->asset_id,
                        'ownership' => $item->ownership,
                        'condition' => $item->condition,
                        'quantity' => $item->physical_quantity,
                        'notes' => 'Saldo awal resmi',
                    ]);
                }
            }

            AuditLog::log(
                action: 'verify_initial_stock',
                entityType: 'InventoryCount',
                entityId: $count->id,
                description: 'Tim Warehouse ('.Auth::user()->name.') mengesahkan saldo stok awal pada sesi '.$count->count_number.'.'
            );
        });

        return redirect()->route('initial-counts.index')
            ->with('success', 'Stok awal berhasil diverifikasi dan resmi disahkan sebagai saldo berjalan operasional Omah Visual!');
    }

    /**
     * Clear all items in the initial stock count session so user can start with a clean slate.
     */
    public function clearItems(Request $request, InventoryCount $count): RedirectResponse
    {
        if (! Auth::user()->hasRole(['Warehouse', 'Owner', 'Superuser'])) {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Warehouse, Owner, atau Superuser.');
        }

        DB::transaction(function () use ($count, $request) {
            $count->items()->delete();
            $count->update([
                'status' => 'pending_verification',
                'verified_by' => null,
                'finalized_at' => null,
                'notes' => 'Sesi stok awal dikosongkan untuk pengisian mandiri manual dari nol.',
            ]);

            if ($request->boolean('reset_balances')) {
                StockBalance::query()->update(['quantity' => 0]);
            }

            // Hapus mutasi initial stock jika ada
            $movementNumber = 'MOV-INIT-'.date('Ym').'-0001';
            $movement = StockMovement::where('movement_number', $movementNumber)->first();
            if ($movement) {
                $movement->items()->delete();
                $movement->delete();
            }

            AuditLog::log(
                action: 'clear_initial_stock_items',
                entityType: 'InventoryCount',
                entityId: $count->id,
                description: 'Daftar item stok awal dikosongkan oleh '.Auth::user()->name.' untuk pengisian mandiri dari awal.'
            );
        });

        return redirect()->route('initial-counts.index')
            ->with('success', 'Daftar item stok awal telah dikosongkan! Anda dapat mulai menambahkan saldo barang secara mandiri di bawah.');
    }

    /**
     * Add a manual stock item to the initial count session.
     */
    public function addItem(Request $request, InventoryCount $count): RedirectResponse
    {
        if (! Auth::user()->hasRole(['Warehouse', 'Owner', 'Superuser'])) {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Warehouse, Owner, atau Superuser.');
        }

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'condition' => ['required', 'in:available,damaged,in_service'],
            'ownership' => ['required', 'in:company,external'],
            'physical_quantity' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $item = InventoryCountItem::updateOrCreate(
            [
                'inventory_count_id' => $count->id,
                'product_id' => $validated['product_id'],
                'condition' => $validated['condition'],
                'ownership' => $validated['ownership'],
            ],
            [
                'system_quantity' => 0,
                'physical_quantity' => $validated['physical_quantity'],
                'difference' => $validated['physical_quantity'],
                'is_verified' => false,
                'notes' => $validated['notes'] ?? 'Input manual stok awal',
            ]
        );

        return redirect()->route('initial-counts.index')
            ->with('success', 'Barang berhasil ditambahkan ke sesi stok awal.');
    }

    /**
     * Delete an item from the initial count session.
     */
    public function deleteItem(Request $request, InventoryCount $count, InventoryCountItem $item): RedirectResponse
    {
        if (! Auth::user()->hasRole(['Warehouse', 'Owner', 'Superuser'])) {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Warehouse, Owner, atau Superuser.');
        }

        $item->delete();

        return redirect()->route('initial-counts.index')
            ->with('success', 'Item berhasil dihapus dari sesi stok awal.');
    }

    /**
     * Reset initial stock count status back to pending_verification for testing / demo purposes.
     */
    public function resetSimulation(InventoryCount $count): RedirectResponse
    {
        if (! Auth::user()->hasRole(['Warehouse', 'Owner', 'Superuser'])) {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Warehouse, Owner, atau Superuser.');
        }

        DB::transaction(function () use ($count) {
            $count->update([
                'status' => 'pending_verification',
                'verified_by' => null,
                'finalized_at' => null,
                'notes' => 'Sesi verifikasi saldo fisik perdana sebelum sistem live operasional (Mode Simulasi).',
            ]);

            $count->items()->update(['is_verified' => false]);

            // Hapus mutasi initial stock yang sebelumnya tercatat jika ada
            $movementNumber = 'MOV-INIT-'.date('Ym').'-0001';
            $movement = StockMovement::where('movement_number', $movementNumber)->first();
            if ($movement) {
                $movement->items()->delete();
                $movement->delete();
            }

            AuditLog::log(
                action: 'reset_initial_stock_simulation',
                entityType: 'InventoryCount',
                entityId: $count->id,
                description: 'Status stok awal di-reset ke pending oleh '.Auth::user()->name.' untuk pengujian simulasi.'
            );
        });

        return redirect()->route('initial-counts.index')
            ->with('info', 'Status stok awal berhasil di-reset ke mode simulasi (Menunggu Pengesahan). Anda dapat mencoba menekan tombol "Sahkan Saldo Stok Awal" kembali!');
    }
}
