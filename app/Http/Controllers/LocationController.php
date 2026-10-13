<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LocationController extends Controller
{
    /**
     * Display a listing of warehouse storage locations.
     */
    public function index(): View
    {
        $locations = Location::withCount(['assets', 'stockBalances'])
            ->orderBy('id', 'asc')
            ->get();

        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create(): View
    {
        $this->authorizeLocationManagement();

        $parentLocations = Location::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('locations.create', compact('parentLocations'));
    }

    /**
     * Store a newly created location in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeLocationManagement();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:locations,code'],
            'parent_id' => ['nullable', 'exists:locations,id'],
            'type' => ['required', 'string', 'in:building,room,aisle,rack,shelf,bin,cabinet,staging_area,locker'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('locations', 'public');
        }

        unset($validated['image']);

        $location = Location::create($validated);

        AuditLog::log(
            action: 'create_location',
            entityType: 'Location',
            entityId: $location->id,
            newValues: $validated,
            description: 'Menambahkan titik lokasi/lemari baru: '.$location->name.' ('.$location->code.')'
        );

        return redirect()->route('locations.show', $location)
            ->with('success', 'Titik lokasi / lemari "'.$location->name.'" berhasil ditambahkan.');
    }

    /**
     * Display items stored in a specific location.
     */
    public function show(Location $location): View
    {
        $location->load([
            'stockBalances.product.unit',
            'assets.product.unit',
            'parent',
            'children',
        ]);

        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location): View
    {
        $this->authorizeLocationManagement();

        $parentLocations = Location::whereNull('parent_id')
            ->where('id', '!=', $location->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('locations.edit', compact('location', 'parentLocations'));
    }

    /**
     * Update the specified location in storage.
     */
    public function update(Request $request, Location $location): RedirectResponse
    {
        $this->authorizeLocationManagement();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('locations', 'code')->ignore($location->id)],
            'parent_id' => ['nullable', 'exists:locations,id', Rule::notIn([$location->id])],
            'type' => ['required', 'string', 'in:building,room,aisle,rack,shelf,bin,cabinet,staging_area,locker'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->boolean('remove_image')) {
            if ($location->image_path) {
                Storage::disk('public')->delete($location->image_path);
            }
            $validated['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            if ($location->image_path) {
                Storage::disk('public')->delete($location->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('locations', 'public');
        }

        unset($validated['image'], $validated['remove_image']);

        $oldValues = $location->only(array_keys($validated));
        $location->update($validated);

        AuditLog::log(
            action: 'update_location',
            entityType: 'Location',
            entityId: $location->id,
            oldValues: $oldValues,
            newValues: $validated,
            description: 'Memperbarui data dan foto titik lokasi: '.$location->name.' ('.$location->code.')'
        );

        return redirect()->route('locations.show', $location)
            ->with('success', 'Titik lokasi "'.$location->name.'" berhasil diperbarui.');
    }

    /**
     * Remove the specified location from storage.
     */
    public function destroy(Location $location): RedirectResponse
    {
        $this->authorizeLocationManagement();

        // Cek apakah lokasi masih menampung aset atau saldo stok
        $hasAssets = $location->assets()->exists();
        $hasStock = $location->stockBalances()->where('quantity', '>', 0)->exists();

        if ($hasAssets || $hasStock) {
            return back()->with('error', 'Titik lokasi tidak dapat dihapus karena masih menampung aset atau stok barang.');
        }

        if ($location->image_path) {
            Storage::disk('public')->delete($location->image_path);
        }

        $locName = $location->name;
        $locCode = $location->code;

        $location->delete();

        AuditLog::log(
            action: 'delete_location',
            entityType: 'Location',
            entityId: $location->id,
            description: 'Menghapus titik lokasi: '.$locName.' ('.$locCode.')'
        );

        return redirect()->route('locations.index')
            ->with('success', 'Titik lokasi "'.$locName.'" berhasil dihapus.');
    }

    /**
     * Authorize location management action.
     */
    private function authorizeLocationManagement(): void
    {
        if (! Auth::user()->can('manage locations') && ! Auth::user()->hasRole(['Warehouse', 'Owner', 'Branch Manager', 'Superuser'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola titik lokasi gudang.');
        }
    }
}
