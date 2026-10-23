<?php

namespace App\Http\Controllers;

use App\Models\EventOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventCalendarController extends Controller
{
    /**
     * Display the operational monthly event calendar.
     */
    public function index(Request $request): View
    {
        Carbon::setLocale('id');

        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }
        if ($year < 2020 || $year > 2040) {
            $year = now()->year;
        }

        $currentDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        // Query events active during this month
        $orders = EventOrder::with([
            'customer',
            'screens',
            'crewAssignments.crew',
            'assignedVehicles',
            'spkAssignment',
        ])
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('loading_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->orWhereBetween('event_start_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->orWhereBetween('event_end_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('event_start_date', '<=', $startOfMonth->toDateString())
                            ->where('event_end_date', '>=', $endOfMonth->toDateString());
                    });
            })
            ->orderBy('event_start_date')
            ->get();

        $startOfGrid = $startOfMonth->copy()->startOfWeek();
        $endOfGrid = $endOfMonth->copy()->endOfWeek();

        // Group events by date string (YYYY-MM-DD)
        $eventsByDate = [];
        $dayConflicts = [];

        foreach ($orders as $order) {
            $startDate = $order->loading_date
                ? Carbon::parse($order->loading_date)
                : ($order->event_start_date ? Carbon::parse($order->event_start_date) : null);
            $endDate = $order->event_end_date
                ? Carbon::parse($order->event_end_date)
                : ($order->event_start_date ? Carbon::parse($order->event_start_date) : $startDate);

            if (! $startDate) {
                continue;
            }

            $cursor = $startDate->copy();
            // Bound cursor to avoid infinite loops if dates are misconfigured
            $limit = 0;
            while ($cursor->lte($endDate) && $limit < 31) {
                $limit++;
                $dateKey = $cursor->toDateString();
                if (! isset($eventsByDate[$dateKey])) {
                    $eventsByDate[$dateKey] = [];
                }

                $eventsByDate[$dateKey][] = [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'event_name' => $order->event_name,
                    'status' => $order->status,
                    'venue_name' => $order->venue_name ?? '-',
                    'venue_address' => $order->venue_address ?? '',
                    'customer_name' => $order->customer?->name ?? 'Pelanggan Umum',
                    'loading_date' => $order->loading_date?->format('d M Y') ?? '-',
                    'loading_time' => $order->loading_time ?? '-',
                    'event_start_date' => $order->event_start_date?->format('d M Y') ?? '-',
                    'event_end_date' => $order->event_end_date?->format('d M Y') ?? '-',
                    'total_sqm' => (float) $order->total_sqm,
                    'crews_count' => $order->crewAssignments->count(),
                    'vehicles_count' => $order->assignedVehicles->count(),
                    'is_loading_day' => $order->loading_date && $order->loading_date->isSameDay($cursor),
                    'is_event_day' => $order->event_start_date && $cursor->gte($order->event_start_date) && ($order->event_end_date ? $cursor->lte($order->event_end_date) : true),
                    'detail_url' => route('orders.show', $order),
                    'spk_url' => $order->spkAssignment ? route('spk.show', $order->spkAssignment) : null,
                    'live_url' => $order->public_token ? route('spk.live', $order->public_token) : null,
                    'crews' => $order->crewAssignments->map(fn ($ca) => [
                        'nickname' => $ca->crew?->nickname ?? 'Kru',
                        'role' => $ca->role_label,
                        'reminder_url' => $ca->whatsapp_reminder_url,
                    ])->values()->all(),
                ];

                $cursor->addDay();
            }
        }

        // Detect high-capacity or overlapping dates
        foreach ($eventsByDate as $dateKey => $items) {
            $totalSqm = collect($items)->sum('total_sqm');
            $count = count($items);
            if ($count >= 2 || $totalSqm >= 50) {
                $dayConflicts[$dateKey] = [
                    'count' => $count,
                    'total_sqm' => $totalSqm,
                    'is_heavy' => $totalSqm >= 60,
                ];
            }
        }

        // Summary KPIs for the month
        $stats = [
            'total_events' => $orders->count(),
            'total_sqm' => round((float) $orders->sum('total_sqm'), 1),
            'confirmed_events' => $orders->where('status', 'confirmed')->count(),
            'loading_events' => $orders->where('status', 'loading')->count(),
            'total_crews_involved' => $orders->pluck('crewAssignments')->flatten()->unique('crew_id')->count(),
        ];

        return view('calendar.index', compact(
            'currentDate',
            'month',
            'year',
            'startOfGrid',
            'endOfGrid',
            'eventsByDate',
            'dayConflicts',
            'stats'
        ));
    }
}
