<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Location;
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

        return view('initial_counts.index', compact('session', 'items'));
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
}
