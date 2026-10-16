<?php

namespace App\Http\Controllers;

use App\Models\EventOrder;
use App\Models\SpkAssignment;
use App\Models\SpkChecklistItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpkController extends Controller
{
    /**
     * Display the 3-page interactive SPK.
     */
    public function show(SpkAssignment $assignment): View
    {
        $assignment->load(['eventOrder.customer', 'eventOrder.screens', 'checklistItems']);
        $users = User::orderBy('name')->get();

        return view('spk.show', compact('assignment', 'users'));
    }

    /**
     * Print-friendly view formatted to match the hardcopy spk_ref.pdf.
     */
    public function print(SpkAssignment $assignment): View
    {
        $assignment->load(['eventOrder.customer', 'eventOrder.screens', 'checklistItems']);

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
     * Verify Return Gate (Kepulangan - Rule: PIC Kepulangan Wajib Orang yg Berbeda).
     */
    public function verifyReturn(Request $request, SpkAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'return_pic_event_name' => ['required', 'string', 'max:255'],
            'return_pic_warehouse_name' => ['required', 'string', 'max:255'],
            'return_notes' => ['nullable', 'string'],
        ]);

        // Enforcement of business rule from spk_ref.pdf: PIC Kepulangan Barang Wajib Orang Berbeda
        if (
            ! empty($assignment->departure_pic_event_name) &&
            strcasecmp(trim($assignment->departure_pic_event_name), trim($validated['return_pic_event_name'])) === 0
        ) {
            return back()->withErrors([
                'return_pic_event_name' => 'Aturan Operasional Wajib: PIC Kepulangan Barang WAJIB orang yang berbeda dari PIC Keberangkatan ('.$assignment->departure_pic_event_name.')!',
            ])->withInput();
        }

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
            'qc_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $photoPath = $assignment->qc_photo_path;
        if ($request->hasFile('qc_photo')) {
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
            'qc_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $photoPath = $assignment->qc_photo_path;
        if ($request->hasFile('qc_photo')) {
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
}
