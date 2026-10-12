<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Location;
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
