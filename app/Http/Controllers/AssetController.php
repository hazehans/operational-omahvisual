<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    /**
     * Display a listing of individual serialized assets.
     */
    public function index(Request $request): View
    {
        $query = Asset::with(['product.unit', 'currentLocation']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'ILIKE', "%{$search}%")
                    ->orWhere('serial_number', 'ILIKE', "%{$search}%")
                    ->orWhere('barcode', 'ILIKE', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'ILIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('current_location_id', $request->location_id);
        }

        $assets = $query->orderBy('asset_code', 'asc')->paginate(20)->withQueryString();
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        return view('assets.index', compact('assets', 'locations'));
    }

    /**
     * Show the form for creating a new serialized asset unit.
     */
    public function create(Request $request): View
    {
        abort_if(auth()->user()->hasRole('Teknisi Servis'), 403, 'Teknisi tidak dapat mendaftarkan unit aset.');

        $products = Product::where('is_active', true)
            ->where('tracking_type', 'individual')
            ->orderBy('name')
            ->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $selectedProductId = $request->query('product_id');

        return view('assets.create', compact('products', 'locations', 'selectedProductId'));
    }

    /**
     * Store a newly created serialized asset in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(auth()->user()->hasRole('Teknisi Servis'), 403, 'Teknisi tidak dapat mendaftarkan unit aset.');

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'current_location_id' => ['required', 'exists:locations,id'],
            'asset_code' => ['nullable', 'string', 'max:50', 'unique:assets,asset_code'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'ownership' => ['required', 'in:company,external'],
            'condition' => ['required', 'in:available,damaged,in_service'],
            'status' => ['required', 'in:in_storage,deployed,maintenance,lost'],
            'notes' => ['nullable', 'string'],
        ], [
            'product_id.required' => 'Pilih jenis barang/produk yang didaftarkan.',
            'current_location_id.required' => 'Pilih lokasi penyimpanan aset ini.',
            'asset_code.unique' => 'Kode aset internal ini sudah terdaftar sebelumnya.',
        ]);

        if (empty($validated['asset_code'])) {
            $validated['asset_code'] = Asset::generateAssetCode();
        }

        $asset = Asset::create($validated);

        AuditLog::log(
            action: 'create_asset',
            entityType: 'Asset',
            entityId: $asset->id,
            oldValues: null,
            newValues: $validated,
            description: 'Mendaftarkan unit aset serial baru: '.$asset->asset_code.' ('.$asset->product->name.')'
        );

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Unit aset serial '.$asset->asset_code.' berhasil didaftarkan ke sistem.');
    }

    /**
     * Display the specified asset.
     */
    public function show(Asset $asset): View
    {
        $asset->load(['product.category', 'product.unit', 'currentLocation', 'movementItems.stockMovement']);
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        return view('assets.show', compact('asset', 'locations'));
    }

    /**
     * Update condition and status of an individual asset.
     */
    public function updateStatus(Request $request, Asset $asset): RedirectResponse
    {
        abort_if(auth()->user()->hasRole('Teknisi Servis'), 403, 'Teknisi tidak dapat mengubah status aset manual. Status dikelola otomatis melalui Tiket Servis.');

        $validated = $request->validate([
            'condition' => ['required', 'in:available,damaged,in_service'],
            'status' => ['required', 'in:in_storage,deployed,maintenance,lost'],
            'current_location_id' => ['required', 'exists:locations,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldValues = $asset->only(['condition', 'status', 'current_location_id', 'notes']);
        $asset->update($validated);

        AuditLog::log(
            action: 'update_asset_status',
            entityType: 'Asset',
            entityId: $asset->id,
            oldValues: $oldValues,
            newValues: $validated,
            description: 'Mengubah status aset '.$asset->asset_code.' menjadi kondisi: '.$validated['condition'].', status: '.$validated['status']
        );

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Status dan kondisi aset '.$asset->asset_code.' berhasil diperbarui.');
    }
}
