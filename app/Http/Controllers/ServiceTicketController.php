<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Location;
use App\Models\Product;
use App\Models\ServiceSparepart;
use App\Models\ServiceTicket;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServiceTicketController extends Controller
{
    /**
     * Display a listing of service and maintenance tickets.
     */
    public function index(Request $request): View
    {
        $query = ServiceTicket::with([
            'product.unit',
            'asset',
            'reporter',
            'technician',
            'sourceLocation',
            'workshopLocation',
            'spareparts',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'ILIKE', "%{$search}%")
                    ->orWhere('issue_description', 'ILIKE', "%{$search}%")
                    ->orWhere('diagnosis', 'ILIKE', "%{$search}%")
                    ->orWhereHas('product', fn ($pq) => $pq->where('name', 'ILIKE', "%{$search}%"))
                    ->orWhereHas('asset', fn ($aq) => $aq->where('asset_code', 'ILIKE', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Metric Statistics
        $metrics = [
            'total_active' => ServiceTicket::whereNotIn('status', ['completed', 'scrapped'])->count(),
            'pending_inspection' => ServiceTicket::where('status', 'pending_inspection')->count(),
            'in_progress' => ServiceTicket::where('status', 'in_progress')->count(),
            'waiting_parts' => ServiceTicket::where('status', 'waiting_parts')->count(),
            'quality_control' => ServiceTicket::where('status', 'quality_control')->count(),
            'completed_this_month' => ServiceTicket::where('status', 'completed')
                ->whereMonth('completion_date', now()->month)
                ->whereYear('completion_date', now()->year)
                ->count(),
        ];

        return view('service_tickets.index', compact('tickets', 'metrics'));
    }

    /**
     * Show the form for creating a new service ticket.
     */
    public function create(Request $request): View
    {
        abort_if(! Auth::user()->can('create service ticket'), 403, 'Anda tidak memiliki hak akses untuk membuka tiket servis baru.');

        $preselectedProduct = null;
        $preselectedAsset = null;

        if ($request->filled('asset_id')) {
            $preselectedAsset = Asset::with('product', 'currentLocation')->find($request->asset_id);
            if ($preselectedAsset) {
                $preselectedProduct = $preselectedAsset->product;
            }
        } elseif ($request->filled('product_id')) {
            $preselectedProduct = Product::find($request->product_id);
        } elseif ($request->filled('item_name')) {
            $preselectedProduct = Product::where('name', 'ILIKE', '%'.$request->item_name.'%')->first();
        }

        $products = Product::where('is_active', true)->orderBy('name')->get();
        $assets = Asset::with('product', 'currentLocation')->where('condition', '!=', 'in_service')->orderBy('asset_code')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $technicians = User::orderBy('name')->get();

        $defaultWorkshop = Location::where('name', 'ILIKE', '%Workshop%')->first() ?? $locations->first();

        return view('service_tickets.create', compact(
            'products',
            'assets',
            'locations',
            'technicians',
            'preselectedProduct',
            'preselectedAsset',
            'defaultWorkshop'
        ));
    }

    /**
     * Store a newly created service ticket in storage and transfer stock to Workshop.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()->can('create service ticket'), 403, 'Anda tidak memiliki hak akses untuk membuka tiket servis baru.');

        if (! $request->filled('workshop_location_id')) {
            $defaultWorkshop = Location::where('name', 'ILIKE', '%Workshop%')->first()
                ?? Location::where('name', 'ILIKE', '%Servis%')->first()
                ?? Location::first();
            if ($defaultWorkshop) {
                $request->merge(['workshop_location_id' => $defaultWorkshop->id]);
            }
        }

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'issue_description' => ['required', 'string'],
            'source_location_id' => ['required', 'exists:locations,id'],
            'workshop_location_id' => ['required', 'exists:locations,id'],
            'technician_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if individual asset
        if ($product->isIndividual() && empty($validated['asset_id'])) {
            return back()->withInput()->withErrors([
                'asset_id' => 'Produk ini menggunakan pelacakan unit serial, wajib memilih unit aset spesifik.',
            ]);
        }

        // Check stock availability if bulk
        if (! $product->isIndividual()) {
            $balance = StockBalance::where('product_id', $product->id)
                ->where('location_id', $validated['source_location_id'])
                ->where('condition', 'available')
                ->first();

            $availableQty = $balance ? (float) $balance->quantity : 0;
            if ($availableQty < (float) $validated['quantity']) {
                return back()->withInput()->withErrors([
                    'quantity' => "Stok di lokasi asal tidak mencukupi (Tersedia: {$availableQty}, diminta: {$validated['quantity']}).",
                ]);
            }
        }

        $ticket = DB::transaction(function () use ($validated, $product) {
            $ticketNumber = ServiceTicket::generateTicketNumber();

            $ticket = ServiceTicket::create([
                'ticket_number' => $ticketNumber,
                'product_id' => $validated['product_id'],
                'asset_id' => $validated['asset_id'] ?? null,
                'reported_by' => Auth::id(),
                'technician_id' => $validated['technician_id'] ?? null,
                'status' => 'pending_inspection',
                'priority' => $validated['priority'],
                'quantity' => $validated['quantity'],
                'issue_description' => $validated['issue_description'],
                'source_location_id' => $validated['source_location_id'],
                'workshop_location_id' => $validated['workshop_location_id'],
                'intake_date' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // 1. Catat perpindahan fisik stok ke Workshop
            $movementNumber = StockMovement::generateMovementNumber('transfer');
            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => 'transfer',
                'source_location_id' => $validated['source_location_id'],
                'destination_location_id' => $validated['workshop_location_id'],
                'created_by' => Auth::id(),
                'status' => 'completed',
                'reference_type' => ServiceTicket::class,
                'reference_id' => $ticket->id,
                'notes' => "Masuk servis: Tiket {$ticket->ticket_number} - {$validated['issue_description']}",
            ]);

            StockMovementItem::create([
                'stock_movement_id' => $movement->id,
                'product_id' => $product->id,
                'asset_id' => $validated['asset_id'] ?? null,
                'ownership' => 'company',
                'condition' => 'in_service',
                'quantity' => $validated['quantity'],
                'notes' => "Karantina Servis {$ticket->ticket_number}",
            ]);

            // 2. Update status aset jika individual
            if ($product->isIndividual() && ! empty($validated['asset_id'])) {
                $asset = Asset::findOrFail($validated['asset_id']);
                $asset->update([
                    'condition' => 'in_service',
                    'status' => 'maintenance',
                    'current_location_id' => $validated['workshop_location_id'],
                ]);
            } else {
                // 3. Update saldo stok fisik jika kuantitas
                // Kurangi dari asal (kondisi available)
                $srcBalance = StockBalance::where('product_id', $product->id)
                    ->where('location_id', $validated['source_location_id'])
                    ->where('condition', 'available')
                    ->where('ownership', 'company')
                    ->first();
                if ($srcBalance) {
                    $srcBalance->decrement('quantity', (float) $validated['quantity']);
                }

                // Tambahkan ke workshop (kondisi in_service)
                $workshopBalance = StockBalance::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'location_id' => $validated['workshop_location_id'],
                        'ownership' => 'company',
                        'condition' => 'in_service',
                    ],
                    ['quantity' => 0]
                );
                $workshopBalance->increment('quantity', (float) $validated['quantity']);
            }

            // Audit Trail
            AuditLog::log(
                action: 'create_service_ticket',
                entityType: 'ServiceTicket',
                entityId: $ticket->id,
                newValues: $ticket->toArray(),
                description: "Membuat tiket servis {$ticket->ticket_number} untuk {$product->name} ke Workshop."
            );

            return $ticket;
        });

        return redirect()->route('service-tickets.show', $ticket)
            ->with('success', "Tiket servis {$ticket->ticket_number} berhasil dibuat dan barang dialihkan ke Workshop.");
    }

    /**
     * Display the specified service ticket.
     */
    public function show(ServiceTicket $ticket): View
    {
        $ticket->load([
            'product.unit',
            'product.category',
            'asset.currentLocation',
            'reporter',
            'technician',
            'sourceLocation',
            'workshopLocation',
            'returnLocation',
            'spareparts.product.unit',
            'spareparts.sourceLocation',
        ]);

        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $technicians = User::orderBy('name')->get();

        // Sparepart candidate products
        $sparepartProducts = Product::with(['unit', 'stockBalances.location'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $defaultSparepartLocation = Location::where('name', 'ILIKE', '%Sparepart%')->first()
            ?? Location::where('name', 'ILIKE', '%Workshop%')->first()
            ?? $ticket->workshopLocation
            ?? $locations->first();

        return view('service_tickets.show', compact('ticket', 'locations', 'technicians', 'sparepartProducts', 'defaultSparepartLocation'));
    }

    /**
     * Update diagnosis, action taken, and technician assignment.
     */
    public function update(Request $request, ServiceTicket $ticket): RedirectResponse
    {
        abort_if(! Auth::user()->can('execute service ticket'), 403, 'Hanya Teknisi Servis atau Superuser yang berwenang memperbarui lembar kerja teknisi.');

        $validated = $request->validate([
            'diagnosis' => ['nullable', 'string'],
            'action_taken' => ['nullable', 'string'],
            'technician_id' => ['nullable', 'exists:users,id'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldValues = $ticket->only(['diagnosis', 'action_taken', 'technician_id', 'priority', 'notes']);
        $ticket->update($validated);

        AuditLog::log(
            action: 'update_service_details',
            entityType: 'ServiceTicket',
            entityId: $ticket->id,
            oldValues: $oldValues,
            newValues: $validated,
            description: "Memperbarui detail diagnosa & pengerjaan tiket {$ticket->ticket_number}"
        );

        return back()->with('success', "Detail perbaikan tiket {$ticket->ticket_number} berhasil diperbarui.");
    }

    /**
     * Transition ticket status (in_progress, waiting_parts, quality_control, completed, scrapped).
     */
    public function updateStatus(Request $request, ServiceTicket $ticket): RedirectResponse
    {
        abort_if(! Auth::user()->can('execute service ticket'), 403, 'Hanya Teknisi Servis atau Superuser yang berwenang mengubah status pengerjaan atau menyelesaikan servis.');

        $validated = $request->validate([
            'status' => ['required', 'in:pending_inspection,in_progress,waiting_parts,quality_control,completed,scrapped'],
            'return_location_id' => ['nullable', 'required_if:status,completed', 'exists:locations,id'],
            'notes' => ['nullable', 'string'],
        ], [
            'return_location_id.required_if' => 'Lokasi rak pengembalian wajib dipilih saat perbaikan selesai.',
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($ticket, $validated, $oldStatus, $newStatus) {
            $updates = [
                'status' => $newStatus,
            ];

            if (! empty($validated['notes'])) {
                $updates['notes'] = $ticket->notes."\n[".now()->format('d/m/Y H:i')."] Status -> {$newStatus}: ".$validated['notes'];
            }

            // A. JIKA STATUS MENJADI SELESAI (COMPLETED) -> KEMBALIKAN KE STOK SIAP SEWA
            if ($newStatus === 'completed') {
                $returnLocationId = $validated['return_location_id'];
                $updates['completion_date'] = now();
                $updates['return_location_id'] = $returnLocationId;

                $product = $ticket->product;

                // 1. Catat mutasi transfer pengembalian dari Workshop ke Rak Siap Pakai
                $movementNumber = StockMovement::generateMovementNumber('transfer');
                $movement = StockMovement::create([
                    'movement_number' => $movementNumber,
                    'type' => 'transfer',
                    'source_location_id' => $ticket->workshop_location_id,
                    'destination_location_id' => $returnLocationId,
                    'created_by' => Auth::id(),
                    'status' => 'completed',
                    'reference_type' => ServiceTicket::class,
                    'reference_id' => $ticket->id,
                    'notes' => "Servis Selesai & Lolos QC: {$ticket->ticket_number} dikembalikan ke rak siap pakai.",
                ]);

                StockMovementItem::create([
                    'stock_movement_id' => $movement->id,
                    'product_id' => $product->id,
                    'asset_id' => $ticket->asset_id,
                    'ownership' => 'company',
                    'condition' => 'available',
                    'quantity' => $ticket->quantity,
                    'notes' => "Hasil Servis Lolos QC {$ticket->ticket_number}",
                ]);

                // 2. Update status aset
                if ($ticket->asset) {
                    $ticket->asset->update([
                        'condition' => 'available',
                        'status' => 'in_storage',
                        'current_location_id' => $returnLocationId,
                    ]);
                } else {
                    // Update saldo stok kuantitas
                    // Kurangi in_service dari workshop
                    $workshopBalance = StockBalance::where('product_id', $product->id)
                        ->where('location_id', $ticket->workshop_location_id)
                        ->where('condition', 'in_service')
                        ->where('ownership', 'company')
                        ->first();
                    if ($workshopBalance) {
                        $workshopBalance->decrement('quantity', (float) $ticket->quantity);
                    }

                    // Tambahkan available ke rak tujuan
                    $returnBalance = StockBalance::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'location_id' => $returnLocationId,
                            'ownership' => 'company',
                            'condition' => 'available',
                        ],
                        ['quantity' => 0]
                    );
                    $returnBalance->increment('quantity', (float) $ticket->quantity);
                }
            }

            // B. JIKA STATUS MENJADI AFKIR / RUSAK TOTAL (SCRAPPED)
            elseif ($newStatus === 'scrapped') {
                $updates['completion_date'] = now();

                if ($ticket->asset) {
                    $ticket->asset->update([
                        'condition' => 'damaged',
                        'status' => 'maintenance',
                    ]);
                } else {
                    // Pindah saldo in_service ke damaged di workshop
                    $workshopBalance = StockBalance::where('product_id', $ticket->product_id)
                        ->where('location_id', $ticket->workshop_location_id)
                        ->where('condition', 'in_service')
                        ->where('ownership', 'company')
                        ->first();
                    if ($workshopBalance) {
                        $workshopBalance->decrement('quantity', (float) $ticket->quantity);
                    }

                    $damagedBalance = StockBalance::firstOrCreate(
                        [
                            'product_id' => $ticket->product_id,
                            'location_id' => $ticket->workshop_location_id,
                            'ownership' => 'company',
                            'condition' => 'damaged',
                        ],
                        ['quantity' => 0]
                    );
                    $damagedBalance->increment('quantity', (float) $ticket->quantity);
                }
            }

            $ticket->update($updates);

            AuditLog::log(
                action: 'update_service_status',
                entityType: 'ServiceTicket',
                entityId: $ticket->id,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => $newStatus],
                description: "Mengubah status tiket servis {$ticket->ticket_number} dari {$oldStatus} menjadi {$newStatus}"
            );
        });

        return back()->with('success', "Status tiket {$ticket->ticket_number} berhasil diubah menjadi: {$ticket->status_label}.");
    }

    /**
     * Add sparepart used for this repair (automatically deducts from inventory).
     */
    public function addSparepart(Request $request, ServiceTicket $ticket): RedirectResponse
    {
        abort_if(! Auth::user()->can('execute service ticket'), 403, 'Hanya Teknisi Servis atau Superuser yang berwenang mencatat pemakaian suku cadang.');

        if (! $request->filled('source_location_id')) {
            $defaultLocation = Location::where('name', 'ILIKE', '%Sparepart%')->first()
                ?? Location::where('name', 'ILIKE', '%Workshop%')->first()
                ?? $ticket->workshopLocation
                ?? Location::first();
            if ($defaultLocation) {
                $request->merge(['source_location_id' => $defaultLocation->id]);
            }
        }

        $validated = $request->validate([
            'sparepart_product_id' => ['required', 'exists:products,id'],
            'source_location_id' => ['required', 'exists:locations,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
        ]);

        $sparepartProduct = Product::findOrFail($validated['sparepart_product_id']);

        // Check stock availability
        $balance = StockBalance::where('product_id', $sparepartProduct->id)
            ->where('location_id', $validated['source_location_id'])
            ->where('condition', 'available')
            ->first();

        $availableQty = $balance ? (float) $balance->quantity : 0;
        if ($availableQty < (float) $validated['quantity']) {
            return back()->withErrors([
                'sparepart_quantity' => "Stok suku cadang {$sparepartProduct->name} tidak mencukupi (Tersedia: {$availableQty}, diminta: {$validated['quantity']}).",
            ]);
        }

        DB::transaction(function () use ($ticket, $sparepartProduct, $validated, $balance) {
            // 1. Kurangi stok suku cadang dari lemari
            $balance->decrement('quantity', (float) $validated['quantity']);

            // 2. Buat record pemakaian suku cadang
            ServiceSparepart::create([
                'service_ticket_id' => $ticket->id,
                'product_id' => $sparepartProduct->id,
                'source_location_id' => $validated['source_location_id'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // 3. Catat stock movement outbound
            $movementNumber = StockMovement::generateMovementNumber('outbound');
            $movement = StockMovement::create([
                'movement_number' => $movementNumber,
                'type' => 'outbound',
                'source_location_id' => $validated['source_location_id'],
                'destination_location_id' => $ticket->workshop_location_id,
                'created_by' => Auth::id(),
                'status' => 'completed',
                'reference_type' => ServiceTicket::class,
                'reference_id' => $ticket->id,
                'notes' => "Konsumsi Sparepart untuk Tiket {$ticket->ticket_number} ({$sparepartProduct->name} x {$validated['quantity']})",
            ]);

            StockMovementItem::create([
                'stock_movement_id' => $movement->id,
                'product_id' => $sparepartProduct->id,
                'ownership' => 'company',
                'condition' => 'available',
                'quantity' => $validated['quantity'],
                'notes' => "Sparepart perbaikan: {$ticket->ticket_number}",
            ]);

            AuditLog::log(
                action: 'use_sparepart',
                entityType: 'ServiceTicket',
                entityId: $ticket->id,
                newValues: $validated,
                description: "Menggunakan suku cadang {$sparepartProduct->name} sebanyak {$validated['quantity']} untuk {$ticket->ticket_number}."
            );
        });

        return back()->with('success', "Suku cadang {$sparepartProduct->name} berhasil dicatat dan dipotong dari stok lemari.");
    }

    /**
     * Remove / rollback sparepart from service ticket.
     */
    public function removeSparepart(ServiceTicket $ticket, ServiceSparepart $sparepart): RedirectResponse
    {
        abort_if(! Auth::user()->can('execute service ticket'), 403, 'Hanya Teknisi Servis atau Superuser yang berwenang membatalkan pemakaian suku cadang.');

        if ($sparepart->service_ticket_id !== $ticket->id) {
            abort(403);
        }

        DB::transaction(function () use ($ticket, $sparepart) {
            // Kembalikan saldo stok suku cadang
            $balance = StockBalance::firstOrCreate(
                [
                    'product_id' => $sparepart->product_id,
                    'location_id' => $sparepart->source_location_id,
                    'ownership' => 'company',
                    'condition' => 'available',
                ],
                ['quantity' => 0]
            );
            $balance->increment('quantity', (float) $sparepart->quantity);

            $sparepartName = $sparepart->product->name ?? 'Sparepart';
            $sparepart->delete();

            AuditLog::log(
                action: 'remove_sparepart',
                entityType: 'ServiceTicket',
                entityId: $ticket->id,
                description: "Membatalkan pemakaian suku cadang {$sparepartName} pada tiket {$ticket->ticket_number}."
            );
        });

        return back()->with('success', 'Pemakaian suku cadang berhasil dibatalkan dan stok dikembalikan.');
    }
}
