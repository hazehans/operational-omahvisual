<?php

namespace App\Http\Controllers;

use App\Models\EventOrder;
use App\Models\SpkAssignment;
use App\Models\SpkChecklistItem;
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

        return view('spk.show', compact('assignment'));
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
            'field' => ['required', 'in:checked_out,checked_in'],
            'value' => ['required', 'boolean'],
            'condition_notes' => ['nullable', 'string'],
        ]);

        $item->update([
            $validated['field'] => $validated['value'],
            'condition_notes' => $validated['condition_notes'] ?? $item->condition_notes,
        ]);

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
}
