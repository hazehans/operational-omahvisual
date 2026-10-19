<?php

namespace App\Http\Controllers;

use App\Models\EventOrder;
use App\Models\SpkAssignment;
use Illuminate\View\View;

class CustomerTrackingController extends Controller
{
    /**
     * Display the public customer self-service order tracking portal.
     */
    public function show(string $token): View
    {
        $order = EventOrder::with([
            'customer',
            'screens',
            'spkAssignment.assignedVehicles.vehicle',
            'spkAssignment.picEventUser',
            'invoices.payments',
        ])
            ->where('public_token', $token)
            ->firstOrFail();

        $spk = $order->spkAssignment;

        // Determine current overall progress percentage (0 - 100)
        $progressPercentage = match (true) {
            $order->status === 'completed' || $spk?->return_status === 'verified' => 100,
            $spk?->qc_status === 'passed' || $order->status === 'running' => 80,
            $spk?->departure_status === 'verified' => 55,
            $order->status === 'loading' => 35,
            default => 15,
        };

        // Determine timeline stage states
        $isCancelled = $order->status === 'cancelled';

        $timeline = [
            'confirmed' => [
                'stage_number' => 1,
                'title' => 'Pesanan Terkonfirmasi & Jadwal Terkunci',
                'description' => 'Jadwal panggung, formasi layar LED, dan spesifikasi teknis telah terdaftar dalam sistem.',
                'state' => $isCancelled ? 'cancelled' : 'completed',
                'timestamp' => $order->created_at,
                'badge' => $isCancelled ? 'Dibatalkan' : 'Selesai Terjadwal',
            ],
            'warehouse_loading' => [
                'stage_number' => 2,
                'title' => 'Pemuatan Barang di Gudang (Loading Out)',
                'description' => 'Pemeriksaan modul kabinet LED, prosesor video, kabel sinyal/power, dan rigging oleh tim gudang.',
                'state' => match (true) {
                    $isCancelled => 'cancelled',
                    $spk?->departure_status === 'verified' => 'completed',
                    $order->status === 'loading' => 'current',
                    default => 'upcoming',
                },
                'timestamp' => $spk?->departure_datetime,
                'badge' => $spk?->departure_status === 'verified' ? 'Muatan Lengkap' : ($order->status === 'loading' ? 'Proses Loading' : 'Menunggu Jadwal'),
            ],
            'en_route' => [
                'stage_number' => 3,
                'title' => 'Armada Logistik Menuju Venue Acara',
                'description' => 'Armada membawa seluruh perlengkapan panggung menuju lokasi acara.',
                'state' => match (true) {
                    $isCancelled => 'cancelled',
                    $spk?->qc_status === 'passed' || in_array($order->status, ['running', 'completed']) => 'completed',
                    $spk?->departure_status === 'verified' => 'current',
                    default => 'upcoming',
                },
                'timestamp' => $spk?->departure_datetime,
                'vehicles' => $this->resolveVehicleSummary($spk),
                'badge' => match (true) {
                    $spk?->qc_status === 'passed' || in_array($order->status, ['running', 'completed']) => 'Tiba di Lokasi',
                    $spk?->departure_status === 'verified' => 'Dalam Perjalanan',
                    default => 'Standby di Gudang',
                },
            ],
            'qc_stage_ready' => [
                'stage_number' => 4,
                'title' => 'Instalasi & Uji Kelayakan QC Panggung (BAST)',
                'description' => 'Pemasangan frame, grounding listrik, uji warna modul (RGB), dan sinyal video processor.',
                'state' => match (true) {
                    $isCancelled => 'cancelled',
                    $spk?->qc_status === 'passed' => 'completed',
                    ($spk?->departure_status === 'verified' && $spk?->qc_status !== 'passed') || $order->status === 'loading' => 'current',
                    default => 'upcoming',
                },
                'timestamp' => $spk?->qc_verified_at,
                'technician' => $spk?->qc_technician_name ?? 'Teknisi Lapangan Omah Visual',
                'grounding' => (bool) $spk?->qc_grounding_safe,
                'test_rgb' => (bool) $spk?->qc_test_rgb,
                'video_signal' => (bool) $spk?->qc_video_signal_ok,
                'photo_path' => $spk?->qc_photo_path,
                'qc_notes' => $spk?->qc_notes,
                'badge' => match (true) {
                    $spk?->qc_status === 'passed' => 'QC Lolos (BAST Siap)',
                    $spk?->qc_status === 'failed' => 'Evaluasi Penyesuaian',
                    default => 'Menunggu Perakitan',
                },
            ],
            'show_time' => [
                'stage_number' => 5,
                'title' => 'Acara Berlangsung & Selesai (Show Time)',
                'description' => 'Operator dan teknisi standby mengawal jalannya visual panggung hingga acara selesai.',
                'state' => match (true) {
                    $isCancelled => 'cancelled',
                    $order->status === 'completed' || $spk?->return_status === 'verified' => 'completed',
                    $order->status === 'running' || ($spk?->qc_status === 'passed' && ! in_array($order->status, ['completed', 'cancelled'])) => 'current',
                    default => 'upcoming',
                },
                'timestamp' => $spk?->return_datetime,
                'badge' => match (true) {
                    $order->status === 'completed' || $spk?->return_status === 'verified' => 'Acara Selesai',
                    $order->status === 'running' => 'Live Acara Berlangsung',
                    default => 'Standby Acara',
                },
            ],
        ];

        // PIC Event contact details
        $picEventName = $spk?->crew_pic_event_name
            ?? $spk?->picEventUser?->name
            ?? 'Koordinator Lapangan Omah Visual';

        $latestInvoice = $order->invoices->first();

        return view('tracking.show', compact(
            'order',
            'spk',
            'progressPercentage',
            'timeline',
            'picEventName',
            'latestInvoice'
        ));
    }

    /**
     * Resolve vehicle names safely for public display without internal operational costs.
     */
    protected function resolveVehicleSummary(?SpkAssignment $spk): string
    {
        if (! $spk) {
            return 'Armada Logistik Omah Visual';
        }

        $assigned = $spk->assignedVehicles
            ->map(fn ($av) => $av->vehicle?->name ?? $av->driver_name)
            ->filter()
            ->values();

        if ($assigned->isNotEmpty()) {
            return $assigned->implode(', ');
        }

        return $spk->vehicle_name ?: 'Armada Logistik Omah Visual';
    }
}
