<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\EventCrewAssignment;
use App\Models\EventOrder;
use App\Models\EventVehicle;
use App\Models\Product;
use App\Models\SpkAssignment;
use App\Models\SpkChecklistItem;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\LedCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SpkController extends Controller
{
    /**
     * Display the 3-page interactive SPK.
     */
    public function show(SpkAssignment $assignment): View
    {
        $assignment->load([
            'eventOrder.customer',
            'eventOrder.screens',
            'checklistItems',
            'crewAssignments.crew',
            'assignedVehicles.vehicle',
        ]);
        $users = User::orderBy('name')->get();
        $crews = Crew::where('is_active', true)->orderBy('nickname')->get();
        $vehicles = Vehicle::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->with(['category', 'unit', 'stockBalances'])
            ->orderBy('name')
            ->get();

        return view('spk.show', compact('assignment', 'users', 'crews', 'vehicles', 'products'));
    }

    /**
     * Print-friendly view formatted to match the hardcopy spk_ref.pdf.
     */
    public function print(SpkAssignment $assignment): View
    {
        $assignment->load([
            'eventOrder.customer',
            'eventOrder.screens',
            'checklistItems',
            'crewAssignments.crew',
            'assignedVehicles.vehicle',
        ]);

        return view('spk.print', compact('assignment'));
    }

    /**
     * Verify Departure Gate (Keberangkatan).
     */
    public function verifyDeparture(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'departure_pic_event_name' => ['required', 'string', 'max:255'],
            'departure_pic_warehouse_name' => ['required', 'string', 'max:255'],
            'departure_notes' => ['nullable', 'string'],
        ]);

        $assignment->update([
            'departure_pic_event_name' => $validated['departure_pic_event_name'],
            'departure_pic_warehouse_name' => $validated['departure_pic_warehouse_name'],
            'departure_datetime' => now(),
            'departure_status' => 'verified',
            'departure_notes' => $validated['departure_notes'] ?? null,
        ]);

        $assignment->eventOrder->update(['status' => 'loading']);

        return back()->with('status', 'Pemeriksaan Keberangkatan Berhasil Disahkan! Barang resmi keluar gudang.');
    }

    /**
     * Verify Return Gate (Kepulangan).
     * Note: PIC Keberangkatan dan Kepulangan diperbolehkan orang yang sama bila bertugas penuh.
     */
    public function verifyReturn(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'return_pic_event_name' => ['required', 'string', 'max:255'],
            'return_pic_warehouse_name' => ['required', 'string', 'max:255'],
            'return_notes' => ['nullable', 'string'],
        ]);

        $assignment->update([
            'return_pic_event_name' => $validated['return_pic_event_name'],
            'return_pic_warehouse_name' => $validated['return_pic_warehouse_name'],
            'return_datetime' => now(),
            'return_status' => 'verified',
            'return_notes' => $validated['return_notes'] ?? null,
        ]);

        $assignment->eventOrder->update(['status' => 'completed']);

        return back()->with('status', 'Pemeriksaan Kepulangan Berhasil Disahkan! Seluruh barang telah kembali ke gudang.');
    }

    /**
     * Reset Departure Gate (Batalkan Pengesahan Keberangkatan untuk koreksi data).
     */
    public function resetDeparture(SpkAssignment $assignment): RedirectResponse
    {
        $assignment->update([
            'departure_status' => 'pending',
            'departure_datetime' => null,
            'departure_pic_event_name' => null,
            'departure_pic_warehouse_name' => null,
        ]);

        $assignment->eventOrder->update(['status' => 'confirmed']);

        return back()->with('status', 'Pengesahan Keberangkatan berhasil dibatalkan. Anda dapat mengedit kembali.');
    }

    /**
     * Reset Return Gate (Batalkan Pengesahan Kepulangan untuk koreksi data).
     */
    public function resetReturn(SpkAssignment $assignment): RedirectResponse
    {
        $assignment->update([
            'return_status' => 'pending',
            'return_datetime' => null,
            'return_pic_event_name' => null,
            'return_pic_warehouse_name' => null,
        ]);

        $assignment->eventOrder->update(['status' => 'loading']);

        return back()->with('status', 'Pengesahan Kepulangan berhasil dibatalkan. Anda dapat mengedit kembali.');
    }

    /**
     * Add a new item to SPK checklist (Page 2 or Page 3).
     */
    public function addChecklistItem(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'page_number' => ['required', 'in:2,3'],
            'category' => ['required', 'string', 'max:100'],
            'item_name' => ['required', 'string', 'max:255'],
            'item_detail' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $assignment->checklistItems()->create([
            'page_number' => (int) $validated['page_number'],
            'category' => $validated['category'],
            'item_name' => $validated['item_name'],
            'item_detail' => $validated['item_detail'] ?? null,
            'quantity' => (int) $validated['quantity'],
            'unit' => $validated['unit'] ?? 'Unit',
            'checked_out' => false,
            'checked_in' => false,
        ]);

        $tab = (int) $validated['page_number'];

        return redirect()->to(route('spk.show', $assignment).'?tab='.$tab)
            ->with('status', 'Barang baru berhasil ditambahkan ke Checklist!');
    }

    /**
     * Update an item on SPK checklist (Quantity, Item Name, or Detail).
     */
    public function updateChecklistItem(Request $request, SpkChecklistItem $item): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
            'item_name' => ['nullable', 'string', 'max:255'],
            'item_detail' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
        ]);

        $item->update([
            'quantity' => (int) $validated['quantity'],
            'item_name' => $validated['item_name'] ?? $item->item_name,
            'item_detail' => $validated['item_detail'] ?? $item->item_detail,
            'unit' => $validated['unit'] ?? $item->unit,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'item' => $item,
            ]);
        }

        $tab = (int) ($item->page_number ?? 2);

        return redirect()->to(route('spk.show', $item->spkAssignment).'?tab='.$tab)
            ->with('status', 'Data item checklist berhasil diperbarui!');
    }

    /**
     * Delete an item from SPK checklist.
     */
    public function deleteChecklistItem(Request $request, SpkChecklistItem $item): JsonResponse|RedirectResponse
    {
        $tab = (int) ($item->page_number ?? 2);
        $assignment = $item->spkAssignment;
        $item->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari checklist.',
            ]);
        }

        return redirect()->to(route('spk.show', $assignment).'?tab='.$tab)
            ->with('status', 'Item berhasil dihapus dari checklist.');
    }

    /**
     * Apply or reset SPK Checklist template.
     */
    public function applyTemplate(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'template' => ['required', 'in:essential,full,empty'],
            'tab' => ['nullable', 'in:2,3'],
        ]);

        $tab = (int) ($validated['tab'] ?? 2);
        $template = $validated['template'];

        if ($template === 'empty') {
            $assignment->checklistItems()->delete();
            $msg = 'Seluruh checklist muatan berhasil dikosongkan. Anda dapat menambahkan barang manual dari awal.';
        } else {
            app(LedCalculationService::class)->generateStandardChecklist($assignment, $assignment->eventOrder, $template);
            $msg = $template === 'essential'
                ? 'Template Esensial (Alat Utama & Hasil Kalkulasi) berhasil diterapkan!'
                : 'Template Lengkap (Seluruh Aksesoris & Perlengkapan) berhasil diterapkan!';
        }

        return redirect()->to(route('spk.show', $assignment).'?tab='.$tab)->with('status', $msg);
    }

    /**
     * Toggle checklist item status (Keluar / Kembali).
     */
    public function toggleItem(Request $request, SpkChecklistItem $item): JsonResponse
    {
        $validated = $request->validate([
            'field' => ['required', 'in:checked_out,checked_in,condition_notes'],
            'value' => ['nullable'],
            'condition_notes' => ['nullable', 'string'],
        ]);

        if ($validated['field'] === 'condition_notes') {
            $item->update([
                'condition_notes' => $validated['condition_notes'] ?? $validated['value'],
            ]);
        } else {
            $item->update([
                $validated['field'] => (bool) $validated['value'],
                'condition_notes' => $validated['condition_notes'] ?? $item->condition_notes,
            ]);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);
    }

    /**
     * Bulk toggle checklist items for a page or category.
     */
    public function bulkToggleItems(Request $request, SpkAssignment $assignment): JsonResponse
    {
        $validated = $request->validate([
            'page_number' => ['required', 'integer', 'in:2,3'],
            'field' => ['required', 'in:checked_out,checked_in'],
            'value' => ['required', 'boolean'],
            'category' => ['nullable', 'string'],
        ]);

        $query = $assignment->checklistItems()->where('page_number', $validated['page_number']);

        if (! empty($validated['category'])) {
            $query->where('category', $validated['category']);
        }

        $query->update([
            $validated['field'] => $validated['value'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status checklist berhasil diperbarui.',
        ]);
    }

    /**
     * Public temporary/live view for field crew via unique token (No login required).
     */
    public function liveTokenView(string $token): View
    {
        $order = EventOrder::with(['customer', 'screens', 'spkAssignment.checklistItems'])
            ->where('public_token', $token)
            ->firstOrFail();

        return view('spk.crew-live', compact('order'));
    }

    /**
     * Update Crew Team Roster & Fleet Logistics Assignment.
     */
    public function updateAssignment(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'crew_pic_event_name' => ['nullable', 'string', 'max:255'],
            'crew_1_name' => ['nullable', 'string', 'max:255'],
            'crew_2_name' => ['nullable', 'string', 'max:255'],
            'crew_3_name' => ['nullable', 'string', 'max:255'],
            'crew_4_name' => ['nullable', 'string', 'max:255'],
            'crew_5_name' => ['nullable', 'string', 'max:255'],
            'crew_6_name' => ['nullable', 'string', 'max:255'],
            'crew_7_name' => ['nullable', 'string', 'max:255'],
            'vehicle_name' => ['nullable', 'string', 'max:255'],
            'has_etoll' => ['nullable', 'boolean'],
            'has_kunci_ban' => ['nullable', 'boolean'],
            'has_stnk' => ['nullable', 'boolean'],
            'has_kir' => ['nullable', 'boolean'],
            'has_barcode_bbm' => ['nullable', 'boolean'],
        ]);

        $assignment->update([
            'crew_pic_event_name' => $validated['crew_pic_event_name'] ?? null,
            'crew_1_name' => $validated['crew_1_name'] ?? null,
            'crew_2_name' => $validated['crew_2_name'] ?? null,
            'crew_3_name' => $validated['crew_3_name'] ?? null,
            'crew_4_name' => $validated['crew_4_name'] ?? null,
            'crew_5_name' => $validated['crew_5_name'] ?? null,
            'crew_6_name' => $validated['crew_6_name'] ?? null,
            'crew_7_name' => $validated['crew_7_name'] ?? null,
            'vehicle_name' => $validated['vehicle_name'] ?? $assignment->vehicle_name,
            'has_etoll' => $request->boolean('has_etoll'),
            'has_kunci_ban' => $request->boolean('has_kunci_ban'),
            'has_stnk' => $request->boolean('has_stnk'),
            'has_kir' => $request->boolean('has_kir'),
            'has_barcode_bbm' => $request->boolean('has_barcode_bbm'),
        ]);

        if (! empty($validated['vehicle_name'])) {
            $assignment->eventOrder->update([
                'vehicle_loading_in' => $validated['vehicle_name'],
                'vehicle_loading_out' => $validated['vehicle_name'],
            ]);
        }

        return back()->with('status', 'Penugasan Kru & Kesiapan Armada Kendaraan berhasil diperbarui!');
    }

    /**
     * Submit Venue QC and BAST verification by crew on field (via live public token).
     */
    public function submitVenueQc(Request $request, string $token): RedirectResponse
    {
        $order = EventOrder::with('spkAssignment')->where('public_token', $token)->firstOrFail();
        $assignment = $order->spkAssignment;
        abort_if(! $assignment, 404, 'SPK tidak ditemukan untuk pesanan ini.');

        $validated = $request->validate([
            'qc_technician_name' => ['required', 'string', 'max:255'],
            'qc_status' => ['required', 'in:passed,issues_found'],
            'qc_test_rgb' => ['nullable', 'boolean'],
            'qc_grounding_safe' => ['nullable', 'boolean'],
            'qc_video_signal_ok' => ['nullable', 'boolean'],
            'qc_notes' => ['nullable', 'string', 'max:1000'],
            'qc_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:15360'],
        ]);

        $photoPath = $assignment->qc_photo_path;
        if ($request->boolean('remove_qc_photo')) {
            if ($assignment->qc_photo_path) {
                Storage::disk('public')->delete($assignment->qc_photo_path);
            }
            $photoPath = null;
        } elseif ($request->hasFile('qc_photo')) {
            if ($assignment->qc_photo_path) {
                Storage::disk('public')->delete($assignment->qc_photo_path);
            }
            $photoPath = $request->file('qc_photo')->store('venue-qc', 'public');
        }

        $assignment->update([
            'qc_technician_name' => $validated['qc_technician_name'],
            'qc_status' => $validated['qc_status'],
            'qc_test_rgb' => $request->boolean('qc_test_rgb'),
            'qc_grounding_safe' => $request->boolean('qc_grounding_safe'),
            'qc_video_signal_ok' => $request->boolean('qc_video_signal_ok'),
            'qc_notes' => $validated['qc_notes'] ?? null,
            'qc_photo_path' => $photoPath,
            'qc_verified_at' => now(),
        ]);

        return back()->with('status', 'Laporan Uji Kelayakan (QC) Venue & BAST berhasil disimpan!');
    }

    /**
     * Update Venue QC directly by authenticated users on SPK.
     */
    public function updateQc(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'qc_technician_name' => ['required', 'string', 'max:255'],
            'qc_status' => ['required', 'in:pending,passed,issues_found'],
            'qc_test_rgb' => ['nullable', 'boolean'],
            'qc_grounding_safe' => ['nullable', 'boolean'],
            'qc_video_signal_ok' => ['nullable', 'boolean'],
            'qc_notes' => ['nullable', 'string', 'max:1000'],
            'qc_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:15360'],
        ]);

        $photoPath = $assignment->qc_photo_path;
        if ($request->boolean('remove_qc_photo')) {
            if ($assignment->qc_photo_path) {
                Storage::disk('public')->delete($assignment->qc_photo_path);
            }
            $photoPath = null;
        } elseif ($request->hasFile('qc_photo')) {
            if ($assignment->qc_photo_path) {
                Storage::disk('public')->delete($assignment->qc_photo_path);
            }
            $photoPath = $request->file('qc_photo')->store('venue-qc', 'public');
        }

        $assignment->update([
            'qc_technician_name' => $validated['qc_technician_name'],
            'qc_status' => $validated['qc_status'],
            'qc_test_rgb' => $request->boolean('qc_test_rgb'),
            'qc_grounding_safe' => $request->boolean('qc_grounding_safe'),
            'qc_video_signal_ok' => $request->boolean('qc_video_signal_ok'),
            'qc_notes' => $validated['qc_notes'] ?? null,
            'qc_photo_path' => $photoPath,
            'qc_verified_at' => now(),
        ]);

        return back()->with('status', 'Status QC Instalasi Venue berhasil diperbarui!');
    }

    /**
     * Add crew assignment with specific role and fees.
     */
    public function addCrewAssignment(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'crew_id' => ['required', 'exists:crews,id'],
            'role_label' => ['required', 'string', 'max:100'],
            'fee_loading_in' => ['nullable', 'numeric', 'min:0'],
            'fee_loading_out' => ['nullable', 'numeric', 'min:0'],
            'fee_meal' => ['nullable', 'numeric', 'min:0'],
            'fee_standby' => ['nullable', 'numeric', 'min:0'],
            'fee_transport' => ['nullable', 'numeric', 'min:0'],
            'fee_bonus' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:offered,acc,declined,completed'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $crew = Crew::findOrFail($validated['crew_id']);

        EventCrewAssignment::create([
            'event_order_id' => $assignment->event_order_id,
            'spk_assignment_id' => $assignment->id,
            'crew_id' => $crew->id,
            'role_label' => $validated['role_label'],
            'fee_loading_in' => $validated['fee_loading_in'] ?? 0,
            'fee_loading_out' => $validated['fee_loading_out'] ?? 0,
            'fee_meal' => $validated['fee_meal'] ?? 0,
            'fee_standby' => $validated['fee_standby'] ?? 0,
            'fee_transport' => $validated['fee_transport'] ?? 0,
            'fee_bonus' => $validated['fee_bonus'] ?? 0,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('status', "Kru {$crew->nickname} berhasil ditugaskan ke event ({$validated['role_label']})!");
    }

    /**
     * Update crew assignment fees or confirmation status.
     */
    public function updateCrewAssignment(Request $request, EventCrewAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'role_label' => ['required', 'string', 'max:100'],
            'fee_loading_in' => ['nullable', 'numeric', 'min:0'],
            'fee_loading_out' => ['nullable', 'numeric', 'min:0'],
            'fee_meal' => ['nullable', 'numeric', 'min:0'],
            'fee_standby' => ['nullable', 'numeric', 'min:0'],
            'fee_transport' => ['nullable', 'numeric', 'min:0'],
            'fee_bonus' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:offered,acc,declined,completed'],
            'payment_status' => ['nullable', 'in:unpaid,paid'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $assignment->update([
            'role_label' => $validated['role_label'],
            'fee_loading_in' => $validated['fee_loading_in'] ?? 0,
            'fee_loading_out' => $validated['fee_loading_out'] ?? 0,
            'fee_meal' => $validated['fee_meal'] ?? 0,
            'fee_standby' => $validated['fee_standby'] ?? 0,
            'fee_transport' => $validated['fee_transport'] ?? 0,
            'fee_bonus' => $validated['fee_bonus'] ?? 0,
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'] ?? $assignment->payment_status,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('status', "Penugasan kru {$assignment->crew->nickname} berhasil diperbarui!");
    }

    /**
     * Delete crew assignment.
     */
    public function deleteCrewAssignment(EventCrewAssignment $assignment): RedirectResponse
    {
        $name = $assignment->crew?->nickname ?? 'Kru';
        $assignment->delete();

        return back()->with('status', "Penugasan kru {$name} berhasil dibatalkan/dihapus.");
    }

    /**
     * Add vehicle (pickup / truck) to event.
     */
    public function addVehicleAssignment(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'vehicle_name' => ['required', 'string', 'max:150'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'has_etoll' => ['nullable', 'boolean'],
            'has_kunci_ban' => ['nullable', 'boolean'],
            'has_stnk' => ['nullable', 'boolean'],
            'has_kir' => ['nullable', 'boolean'],
            'has_barcode_bbm' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        EventVehicle::create([
            'event_order_id' => $assignment->event_order_id,
            'spk_assignment_id' => $assignment->id,
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'vehicle_name' => $validated['vehicle_name'],
            'plate_number' => $validated['plate_number'] ?? null,
            'driver_name' => $validated['driver_name'] ?? null,
            'has_etoll' => $request->boolean('has_etoll'),
            'has_kunci_ban' => $request->boolean('has_kunci_ban'),
            'has_stnk' => $request->boolean('has_stnk'),
            'has_kir' => $request->boolean('has_kir'),
            'has_barcode_bbm' => $request->boolean('has_barcode_bbm'),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('status', "Armada kendaraan {$validated['vehicle_name']} berhasil ditambahkan ke penugasan event!");
    }

    /**
     * Update vehicle checklist or status.
     */
    public function updateVehicleAssignment(Request $request, EventVehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_name' => ['required', 'string', 'max:150'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'has_etoll' => ['nullable', 'boolean'],
            'has_kunci_ban' => ['nullable', 'boolean'],
            'has_stnk' => ['nullable', 'boolean'],
            'has_kir' => ['nullable', 'boolean'],
            'has_barcode_bbm' => ['nullable', 'boolean'],
            'departure_status' => ['nullable', 'string', 'max:50'],
            'return_status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $vehicle->update([
            'vehicle_name' => $validated['vehicle_name'],
            'plate_number' => $validated['plate_number'] ?? $vehicle->plate_number,
            'driver_name' => $validated['driver_name'] ?? $vehicle->driver_name,
            'has_etoll' => $request->boolean('has_etoll'),
            'has_kunci_ban' => $request->boolean('has_kunci_ban'),
            'has_stnk' => $request->boolean('has_stnk'),
            'has_kir' => $request->boolean('has_kir'),
            'has_barcode_bbm' => $request->boolean('has_barcode_bbm'),
            'departure_status' => $validated['departure_status'] ?? $vehicle->departure_status,
            'return_status' => $validated['return_status'] ?? $vehicle->return_status,
            'notes' => $validated['notes'] ?? $vehicle->notes,
        ]);

        return back()->with('status', "Kesiapan armada {$vehicle->vehicle_name} berhasil diperbarui!");
    }

    /**
     * Delete vehicle assignment.
     */
    public function deleteVehicleAssignment(EventVehicle $vehicle): RedirectResponse
    {
        $name = $vehicle->vehicle_name;
        $vehicle->delete();

        return back()->with('status', "Armada {$name} berhasil dihapus dari event.");
    }

    /**
     * Update operational costs for the event (BBM, Tol, Penginapan, Lain-lain).
     */
    public function updateOperationalCosts(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'cost_fuel' => ['nullable', 'numeric', 'min:0'],
            'cost_toll' => ['nullable', 'numeric', 'min:0'],
            'cost_lodging' => ['nullable', 'numeric', 'min:0'],
            'cost_other' => ['nullable', 'numeric', 'min:0'],
            'cost_other_notes' => ['nullable', 'string'],
        ]);

        $assignment->eventOrder->update([
            'cost_fuel' => $validated['cost_fuel'] ?? 0,
            'cost_toll' => $validated['cost_toll'] ?? 0,
            'cost_lodging' => $validated['cost_lodging'] ?? 0,
            'cost_other' => $validated['cost_other'] ?? 0,
            'cost_other_notes' => $validated['cost_other_notes'] ?? null,
        ]);

        return back()->with('status', 'Biaya Operasional Tambahan (BBM, Tol, Penginapan, Lain-lain) berhasil disimpan!');
    }
}
