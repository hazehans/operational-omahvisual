<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    /**
     * Display a listing of stock movements.
     */
    public function index(Request $request): View
    {
        $query = StockMovement::with(['sourceLocation', 'destinationLocation', 'creator', 'items.product.unit']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('movement_number', 'ILIKE', "%{$search}%");
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('movements.index', compact('movements'));
    }

    /**
     * Show the form for creating a new stock movement.
     */
    public function create(): View
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('movements.create', compact('locations', 'products'));
    }

    /**
     * Store a newly created stock movement in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:inbound,outbound,transfer'],
            'source_location_id' => ['nullable', 'required_if:type,outbound,transfer', 'exists:locations,id'],
            'destination_location_id' => ['nullable', 'required_if:type,inbound,transfer', 'exists:locations,id'],
            'notes' => ['nullable', 'string'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'condition' => ['required', 'in:available,damaged,in_service'],
            'ownership' => ['required', 'in:company,external'],
        ], [
            'source_location_id.required_if' => 'Lokasi asal wajib dipilih untuk mutasi keluar atau transfer.',
            'destination_location_id.required_if' => 'Lokasi tujuan wajib dipilih untuk mutasi masuk atau transfer.',
            'quantity.min' => 'Jumlah barang minimal 0.01.',
        ]);

        // Cek kecukupan saldo jika mutasi keluar atau transfer
        if (in_array($validated['type'], ['outbound', 'transfer'])) {
            $sourceBalance = StockBalance::where('product_id', $validated['product_id'])
                ->where('location_id', $validated['source_location_id'])
                ->where('condition', $validated['condition'])
                ->where('ownership', $validated['ownership'])
                ->first();

            $availableQty = $sourceBalance ? (float) $sourceBalance->quantity : 0;
            if ($availableQty < (float) $validated['quantity']) {
                return back()->withInput()->withErrors([
                    'quantity' => 'Stok di lokasi asal tidak mencukupi (Tersedia: '.$availableQty.', diminta: '.$validated['quantity'].').',
                ]);
            }
        }

        DB::transaction(function () use ($validated) {
            $movementNumber = StockMovement::generateMovementNumber($validated['type']);

            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => $validated['type'],
                'source_location_id' => $validated['source_location_id'] ?? null,
                'destination_location_id' => $validated['destination_location_id'] ?? null,
                'created_by' => Auth::id(),
                'status' => 'completed',
                'notes' => $validated['notes'],
            ]);

            StockMovementItem::create([
                'stock_movement_id' => $movement->id,
                'product_id' => $validated['product_id'],
                'ownership' => $validated['ownership'],
                'condition' => $validated['condition'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'],
            ]);

            // Update Saldo Atomik
            if ($validated['type'] === 'inbound') {
                $destBalance = StockBalance::firstOrCreate(
                    [
                        'product_id' => $validated['product_id'],
                        'location_id' => $validated['destination_location_id'],
                        'ownership' => $validated['ownership'],
                        'condition' => $validated['condition'],
                    ],
                    ['quantity' => 0]
                );
                $destBalance->increment('quantity', $validated['quantity']);
            } elseif ($validated['type'] === 'outbound') {
                $srcBalance = StockBalance::where('product_id', $validated['product_id'])
                    ->where('location_id', $validated['source_location_id'])
                    ->where('condition', $validated['condition'])
                    ->where('ownership', $validated['ownership'])
                    ->first();
                $srcBalance->decrement('quantity', $validated['quantity']);
            } elseif ($validated['type'] === 'transfer') {
                // Kurangi dari asal
                $srcBalance = StockBalance::where('product_id', $validated['product_id'])
                    ->where('location_id', $validated['source_location_id'])
                    ->where('condition', $validated['condition'])
                    ->where('ownership', $validated['ownership'])
                    ->first();
                $srcBalance->decrement('quantity', $validated['quantity']);

                // Tambahkan ke tujuan
                $destBalance = StockBalance::firstOrCreate(
                    [
                        'product_id' => $validated['product_id'],
                        'location_id' => $validated['destination_location_id'],
                        'ownership' => $validated['ownership'],
                        'condition' => $validated['condition'],
                    ],
                    ['quantity' => 0]
                );
                $destBalance->increment('quantity', $validated['quantity']);
            }

            AuditLog::log(
                action: 'record_stock_movement',
                entityType: 'StockMovement',
                entityId: $movement->id,
                description: 'Mencatat transaksi mutasi '.$movement->type.' ('.$movement->movement_number.') sejumlah '.$validated['quantity'].' unit.'
            );
        });

        return redirect()->route('movements.index')
            ->with('success', 'Transaksi mutasi stok berhasil dieksekusi secara atomik.');
    }
}
