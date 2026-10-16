<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public const STATUS_OPTIONS = [
        'tersedia' => 'Tersedia / Siap Jalan',
        'dipakai' => 'Sedang Dipakai Event',
        'maintenance' => 'Maintenance / Bengkel',
        'standby' => 'Standby / Cadangan',
    ];

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
                    ->orWhere('vehicle_code', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('special_condition', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $vehicles = $query->paginate(15)->withQueryString();

        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create(): View
    {
        $statusOptions = self::STATUS_OPTIONS;

        return view('vehicles.create', compact('statusOptions'));
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
            'status' => ['required', 'string', 'in:tersedia,dipakai,maintenance,standby'],
            'special_condition' => ['nullable', 'string', 'max:255'],
            'has_etoll' => ['nullable', 'boolean'],
            'has_kunci_ban' => ['nullable', 'boolean'],
            'has_stnk' => ['nullable', 'boolean'],
            'has_kir' => ['nullable', 'boolean'],
            'has_barcode_bbm' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'stnk_photo' => ['nullable', 'image', 'max:5120'],
            'etoll_photo' => ['nullable', 'image', 'max:5120'],
            'barcode_bbm_photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $code = 'VH-'.str_pad((string) (Vehicle::count() + 1), 3, '0', STR_PAD_LEFT);

        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('vehicles/photos', 'public') : null;
        $stnkPath = $request->hasFile('stnk_photo') ? $request->file('stnk_photo')->store('vehicles/stnk', 'public') : null;
        $etollPath = $request->hasFile('etoll_photo') ? $request->file('etoll_photo')->store('vehicles/etoll', 'public') : null;
        $bbmPath = $request->hasFile('barcode_bbm_photo') ? $request->file('barcode_bbm_photo')->store('vehicles/bbm', 'public') : null;

        Vehicle::create([
            'vehicle_code' => $code,
            'name' => $validated['name'],
            'plate_number' => $validated['plate_number'] ?? null,
            'type' => $validated['type'] ?? 'Pick Up',
            'status' => $validated['status'] ?? 'tersedia',
            'special_condition' => $validated['special_condition'] ?? 'Normal',
            'has_etoll' => $request->boolean('has_etoll', true),
            'has_kunci_ban' => $request->boolean('has_kunci_ban', true),
            'has_stnk' => $request->boolean('has_stnk', true),
            'has_kir' => $request->boolean('has_kir', true),
            'has_barcode_bbm' => $request->boolean('has_barcode_bbm', true),
            'photo_path' => $photoPath,
            'stnk_photo_path' => $stnkPath,
            'etoll_photo_path' => $etollPath,
            'barcode_bbm_photo_path' => $bbmPath,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('vehicles.index')
            ->with('success', "Armada {$validated['name']} ({$code}) berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Vehicle $vehicle): View
    {
        $statusOptions = self::STATUS_OPTIONS;

        return view('vehicles.edit', compact('vehicle', 'statusOptions'));
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
            'status' => ['required', 'string', 'in:tersedia,dipakai,maintenance,standby'],
            'special_condition' => ['nullable', 'string', 'max:255'],
            'has_etoll' => ['nullable', 'boolean'],
            'has_kunci_ban' => ['nullable', 'boolean'],
            'has_stnk' => ['nullable', 'boolean'],
            'has_kir' => ['nullable', 'boolean'],
            'has_barcode_bbm' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'stnk_photo' => ['nullable', 'image', 'max:5120'],
            'etoll_photo' => ['nullable', 'image', 'max:5120'],
            'barcode_bbm_photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'plate_number' => $validated['plate_number'] ?? null,
            'type' => $validated['type'] ?? $vehicle->type,
            'status' => $validated['status'],
            'special_condition' => $validated['special_condition'] ?? 'Normal',
            'has_etoll' => $request->boolean('has_etoll'),
            'has_kunci_ban' => $request->boolean('has_kunci_ban'),
            'has_stnk' => $request->boolean('has_stnk'),
            'has_kir' => $request->boolean('has_kir'),
            'has_barcode_bbm' => $request->boolean('has_barcode_bbm'),
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if (! $vehicle->vehicle_code) {
            $updateData['vehicle_code'] = 'VH-'.str_pad((string) $vehicle->id, 3, '0', STR_PAD_LEFT);
        }

        if ($request->hasFile('photo')) {
            if ($vehicle->photo_path) {
                Storage::disk('public')->delete($vehicle->photo_path);
            }
            $updateData['photo_path'] = $request->file('photo')->store('vehicles/photos', 'public');
        }

        if ($request->hasFile('stnk_photo')) {
            if ($vehicle->stnk_photo_path) {
                Storage::disk('public')->delete($vehicle->stnk_photo_path);
            }
            $updateData['stnk_photo_path'] = $request->file('stnk_photo')->store('vehicles/stnk', 'public');
        }

        if ($request->hasFile('etoll_photo')) {
            if ($vehicle->etoll_photo_path) {
                Storage::disk('public')->delete($vehicle->etoll_photo_path);
            }
            $updateData['etoll_photo_path'] = $request->file('etoll_photo')->store('vehicles/etoll', 'public');
        }

        if ($request->hasFile('barcode_bbm_photo')) {
            if ($vehicle->barcode_bbm_photo_path) {
                Storage::disk('public')->delete($vehicle->barcode_bbm_photo_path);
            }
            $updateData['barcode_bbm_photo_path'] = $request->file('barcode_bbm_photo')->store('vehicles/bbm', 'public');
        }

        $vehicle->update($updateData);

        return redirect()->route('vehicles.index')
            ->with('success', "Data armada {$vehicle->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $name = $vehicle->name;

        if ($vehicle->photo_path) {
            Storage::disk('public')->delete($vehicle->photo_path);
        }
        if ($vehicle->stnk_photo_path) {
            Storage::disk('public')->delete($vehicle->stnk_photo_path);
        }
        if ($vehicle->etoll_photo_path) {
            Storage::disk('public')->delete($vehicle->etoll_photo_path);
        }
        if ($vehicle->barcode_bbm_photo_path) {
            Storage::disk('public')->delete($vehicle->barcode_bbm_photo_path);
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', "Armada {$name} berhasil dihapus.");
    }
}
