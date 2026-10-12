<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of stock adjustment requests.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending_approval');

        $query = StockAdjustment::with(['location', 'requester', 'approver', 'items.product.unit']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $adjustments = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $counts = [
            'pending_approval' => StockAdjustment::where('status', 'pending_approval')->count(),
            'approved' => StockAdjustment::where('status', 'approved')->count(),
            'rejected' => StockAdjustment::where('status', 'rejected')->count(),
        ];

        return view('adjustments.index', compact('adjustments', 'status', 'counts'));
    }

    /**
     * Show the form for creating a new stock adjustment request.
     */
    public function create(): View
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('adjustments.create', compact('locations', 'products'));
    }

    /**
     * Store a newly created stock adjustment request in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'location_id' => ['required', 'exists:locations,id'],
            'product_id' => ['required', 'exists:products,id'],
            'condition' => ['required', 'in:available,damaged,in_service'],
            'ownership' => ['required', 'in:company,external'],
            'adjusted_quantity' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'min:5'],
            'proof_attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'reason.required' => 'Alasan penyesuaian selisih fisik wajib dicantumkan.',
            'adjusted_quantity.required' => 'Jumlah fisik hasil temuan wajib diisi.',
        ]);

        // Cek saldo sistem saat ini
        $balance = StockBalance::where('product_id', $validated['product_id'])
            ->where('location_id', $validated['location_id'])
            ->where('condition', $validated['condition'])
            ->where('ownership', $validated['ownership'])
            ->first();

        $currentQty = $balance ? (float) $balance->quantity : 0;
        $adjustedQty = (float) $validated['adjusted_quantity'];
        $difference = $adjustedQty - $currentQty;

        $proofPath = null;
        if ($request->hasFile('proof_attachment')) {
            $proofPath = $request->file('proof_attachment')->store('adjustments/proofs', 'public');
        }

        DB::transaction(function () use ($validated, $currentQty, $adjustedQty, $difference, $proofPath) {
            $adjustmentNumber = StockAdjustment::generateAdjustmentNumber();

            $adjustment = StockAdjustment::create([
                'adjustment_number' => $adjustmentNumber,
                'location_id' => $validated['location_id'],
                'requested_by' => Auth::id(),
                'status' => 'pending_approval',
                'reason' => $validated['reason'],
                'proof_attachment_path' => $proofPath,
            ]);

            StockAdjustmentItem::create([
                'stock_adjustment_id' => $adjustment->id,
                'product_id' => $validated['product_id'],
                'ownership' => $validated['ownership'],
                'condition' => $validated['condition'],
                'current_quantity' => $currentQty,
                'adjusted_quantity' => $adjustedQty,
                'difference' => $difference,
                'notes' => 'Selisih: '.($difference > 0 ? '+' : '').$difference,
            ]);

            AuditLog::log(
                action: 'request_stock_adjustment',
                entityType: 'StockAdjustment',
                entityId: $adjustment->id,
                description: 'Tim Warehouse ('.Auth::user()->name.') mengajukan penyesuaian stok nomor '.$adjustment->adjustment_number.' (selisih '.$difference.').'
            );
        });

        return redirect()->route('adjustments.index')
            ->with('success', 'Pengajuan penyesuaian stok berhasil dikirim ke Owner untuk menunggu persetujuan (approval).');
    }

    /**
     * Owner approves the stock adjustment request.
     */
    public function approve(Request $request, StockAdjustment $adjustment): RedirectResponse
    {
        // Otorisasi: Khusus Owner / Branch Manager
        if (! Auth::user()->can('approve stock adjustment') && ! Auth::user()->hasRole(['Owner', 'Branch Manager', 'Superuser'])) {
            abort(403, 'Hanya Owner atau Branch Manager yang memiliki otoritas menyetujui perubahan saldo stok.');
        }

        if ($adjustment->status !== 'pending_approval') {
            return back()->with('info', 'Pengajuan ini sudah berstatus '.$adjustment->status.'.');
        }

        DB::transaction(function () use ($adjustment) {
            // 1. Buat record mutasi resmi tipe adjustment
            $movementNumber = StockMovement::generateMovementNumber('adjustment');
            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => 'adjustment',
                'source_location_id' => $adjustment->location_id,
                'destination_location_id' => $adjustment->location_id,
                'reference_type' => StockAdjustment::class,
                'reference_id' => $adjustment->id,
                'created_by' => $adjustment->requested_by,
                'approved_by' => Auth::id(),
                'status' => 'completed',
                'notes' => 'Disetujui Owner: '.$adjustment->reason,
            ]);

            // 2. Terapkan pembaruan saldo resmi
            foreach ($adjustment->items as $item) {
                StockMovementItem::create([
                    'stock_movement_id' => $movement->id,
                    'product_id' => $item->product_id,
                    'asset_id' => $item->asset_id,
                    'ownership' => $item->ownership,
                    'condition' => $item->condition,
                    'quantity' => $item->difference,
                    'notes' => 'Penyesuaian saldo resmi',
                ]);

                $balance = StockBalance::firstOrCreate(
                    [
                        'product_id' => $item->product_id,
                        'location_id' => $adjustment->location_id,
                        'ownership' => $item->ownership,
                        'condition' => $item->condition,
                    ],
                    ['quantity' => 0]
                );
                $balance->update(['quantity' => $item->adjusted_quantity]);
            }

            // 3. Update status adjustment
            $adjustment->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'stock_movement_id' => $movement->id,
            ]);

            AuditLog::log(
                action: 'approve_stock_adjustment',
                entityType: 'StockAdjustment',
                entityId: $adjustment->id,
                description: 'Owner ('.Auth::user()->name.') menyetujui penyesuaian stok '.$adjustment->adjustment_number.'. Saldo resmi telah diperbarui.'
            );
        });

        return redirect()->route('adjustments.index')
            ->with('success', 'Penyesuaian stok disetujui! Saldo resmi telah otomatis diselaraskan.');
    }

    /**
     * Owner rejects the stock adjustment request.
     */
    public function reject(Request $request, StockAdjustment $adjustment): RedirectResponse
    {
        if (! Auth::user()->can('approve stock adjustment') && ! Auth::user()->hasRole(['Owner', 'Branch Manager', 'Superuser'])) {
            abort(403, 'Hanya Owner atau Branch Manager yang memiliki otoritas menolak pengajuan penyesuaian.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi agar tim gudang dapat melakukan kroscek ulang.',
        ]);

        $adjustment->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        AuditLog::log(
            action: 'reject_stock_adjustment',
            entityType: 'StockAdjustment',
            entityId: $adjustment->id,
            description: 'Owner ('.Auth::user()->name.') menolak penyesuaian '.$adjustment->adjustment_number.' dengan alasan: '.$validated['rejection_reason']
        );

        return redirect()->route('adjustments.index')
            ->with('warning', 'Pengajuan penyesuaian stok telah ditolak. Saldo resmi tidak mengalami perubahan.');
    }
}
