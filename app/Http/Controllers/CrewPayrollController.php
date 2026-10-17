<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Crew;
use App\Models\EventCrewAssignment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CrewPayrollController extends Controller
{
    /**
     * Display a listing of crew assignments, payroll breakdown, and payment status.
     */
    public function index(Request $request): View
    {
        $query = EventCrewAssignment::with(['crew', 'eventOrder.customer', 'spkAssignment']);

        // Filter bulan (Format: YYYY-MM, default bulan ini)
        $month = $request->input('month', now()->format('Y-m'));
        if ($month) {
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();

            $query->whereHas('eventOrder', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('event_start_date', [$startDate, $endDate]);
            });
        }

        // Filter kru spesifik
        if ($request->filled('crew_id')) {
            $query->where('crew_id', $request->crew_id);
        }

        // Filter status penugasan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter status pembayaran
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter pencarian teks
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('crew', function ($cq) use ($search) {
                    $cq->where('nickname', 'ILIKE', "%{$search}%")
                        ->orWhere('full_name', 'ILIKE', "%{$search}%");
                })->orWhereHas('eventOrder', function ($eq) use ($search) {
                    $eq->where('event_name', 'ILIKE', "%{$search}%")
                        ->orWhere('order_number', 'ILIKE', "%{$search}%")
                        ->orWhere('venue_name', 'ILIKE', "%{$search}%");
                });
            });
        }

        // Metrics kalkulasi
        $metricsQuery = clone $query;
        $totalUnpaid = (clone $metricsQuery)->where('payment_status', 'unpaid')->where('status', '!=', 'declined')->sum('total_fee');
        $totalPaid = (clone $metricsQuery)->where('payment_status', 'paid')->sum('total_fee');
        $totalAssignments = (clone $metricsQuery)->count();

        $assignments = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $crews = Crew::where('is_active', true)->orderBy('nickname')->get();

        return view('payroll.index', compact(
            'assignments',
            'crews',
            'totalUnpaid',
            'totalPaid',
            'totalAssignments',
            'month'
        ));
    }

    /**
     * Mark a crew assignment fee as paid.
     */
    public function markPaid(Request $request, EventCrewAssignment $assignment): RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:transfer,cash'],
            'payment_date' => ['required', 'date'],
            'payment_reference' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $assignment->update([
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'notes' => $validated['notes'] ?? $assignment->notes,
        ]);

        AuditLog::log(
            action: 'pay_crew_fee',
            entityType: 'EventCrewAssignment',
            entityId: $assignment->id,
            description: "Honor kru {$assignment->crew->nickname} sebesar Rp " . number_format($assignment->total_fee, 0, ',', '.') . " ditandai LUNAS oleh " . Auth::user()->name . "."
        );

        return back()->with('success', "Honor untuk {$assignment->crew->nickname} (Rp " . number_format($assignment->total_fee, 0, ',', '.') . ") berhasil ditandai LUNAS.");
    }

    /**
     * Batch mark multiple assignments as paid.
     */
    public function batchMarkPaid(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'assignment_ids' => ['required', 'array'],
            'assignment_ids.*' => ['exists:event_crew_assignments,id'],
            'payment_method' => ['required', 'in:transfer,cash'],
            'payment_date' => ['required', 'date'],
            'payment_reference' => ['nullable', 'string', 'max:150'],
        ]);

        $count = EventCrewAssignment::whereIn('id', $validated['assignment_ids'])->update([
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'payment_reference' => $validated['payment_reference'] ?? null,
        ]);

        return back()->with('success', "{$count} penugasan honor kru berhasil ditandai LUNAS secara massal.");
    }

    /**
     * Display printable freelance payslip for an individual event job.
     */
    public function showSlip(EventCrewAssignment $assignment): View
    {
        $assignment->load(['crew', 'eventOrder.customer', 'spkAssignment']);

        return view('payroll.slip', compact('assignment'));
    }

    /**
     * Display printable freelance monthly recap payslip for a specific crew.
     */
    public function crewMonthlySlip(Request $request, Crew $crew): View
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth()->toDateString();

        $assignments = EventCrewAssignment::with(['eventOrder.customer'])
            ->where('crew_id', $crew->id)
            ->where('status', '!=', 'declined')
            ->whereHas('eventOrder', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('event_start_date', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $totalFee = $assignments->sum('total_fee');
        $totalPaid = $assignments->where('payment_status', 'paid')->sum('total_fee');
        $totalUnpaid = $assignments->where('payment_status', 'unpaid')->sum('total_fee');

        return view('payroll.monthly_slip', compact(
            'crew',
            'assignments',
            'month',
            'totalFee',
            'totalPaid',
            'totalUnpaid'
        ));
    }
}
