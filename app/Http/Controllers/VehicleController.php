<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles.
     */
    public function index(Request $request): View
    {
        $query = Vehicle::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('plate_number', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->paginate(15)->withQueryString();

        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create(): View
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:vehicles,name'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:100'],
            'has_etoll' => ['nullable', 'boolean'],
            'has_kunci_ban' => ['nullable', 'boolean'],
            'has_stnk' => ['nullable', 'boolean'],
            'has_kir' => ['nullable', 'boolean'],
            'has_barcode_bbm' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Vehicle::create([
            'name' => $validated['name'],
            'plate_number' => $validated['plate_number'] ?? null,
            'type' => $validated['type'] ?? 'Pick Up',
            'has_etoll' => $request->boolean('has_etoll', true),
            'has_kunci_ban' => $request->boolean('has_kunci_ban', true),
            'has_stnk' => $request->boolean('has_stnk', true),
            'has_kir' => $request->boolean('has_kir', true),
            'has_barcode_bbm' => $request->boolean('has_barcode_bbm', true),
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('vehicles.index')
            ->with('success', "Armada {$validated['name']} berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:vehicles,name,'.$vehicle->id],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:100'],
            'has_etoll' => ['nullable', 'boolean'],
            'has_kunci_ban' => ['nullable', 'boolean'],
            'has_stnk' => ['nullable', 'boolean'],
            'has_kir' => ['nullable', 'boolean'],
            'has_barcode_bbm' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $vehicle->update([
            'name' => $validated['name'],
            'plate_number' => $validated['plate_number'] ?? null,
            'type' => $validated['type'] ?? $vehicle->type,
            'has_etoll' => $request->boolean('has_etoll'),
            'has_kunci_ban' => $request->boolean('has_kunci_ban'),
            'has_stnk' => $request->boolean('has_stnk'),
            'has_kir' => $request->boolean('has_kir'),
            'has_barcode_bbm' => $request->boolean('has_barcode_bbm'),
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('vehicles.index')
            ->with('success', "Data armada {$vehicle->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $name = $vehicle->name;
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', "Armada {$name} berhasil dihapus.");
    }
}
