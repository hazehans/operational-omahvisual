@extends('layouts.app')

@section('title', 'Pesanan & SPK LED - Omah Visual')

@section('content')
<div class="space-y-5">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                <span>📋 Pesanan Event & SPK LED</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Monitoring operasional persewaan LED, jadwal loading & gladi, armada lapangan, dan penerbitan SPK resmi.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('calendar.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-all shadow-xs">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Kalender Operasional</span>
            </a>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Input Order Baru</span>
            </a>
        </div>
    </div>

    <!-- Operational Metrics Cards (Kasir LED Focus) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="bg-white dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Event Bulan Ini</span>
                <span class="p-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $thisMonthOrders }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">booking</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terkonfirmasi</span>
                <span class="p-1.5 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $confirmedCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">siap jalan</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Loading / Live Hari Ini</span>
                <span class="p-1.5 rounded-xl bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-sky-600 dark:text-sky-400">{{ $loadingTodayCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">aktif lapangan</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Event Selesai</span>
                <span class="p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $completedCount }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">tuntas</span>
            </div>
        </div>
    </div>

    <!-- Filter and Search Toolbar (Kasir LED Highlight Transaksi) -->
    <div class="bg-white dark:bg-slate-900/60 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800/80 space-y-3 shadow-xs">
        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-col lg:flex-row items-center gap-2.5">
            <!-- Search -->
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor order, client, instansi, atau venue acara..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Month Filter -->
            <div class="w-full sm:w-40">
                <select name="month" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Bulan</option>
                    <option value="this_month" {{ request('month') === 'this_month' ? 'selected' : '' }}>Bulan Ini ({{ now()->translatedFormat('F') }})</option>
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $monthName = \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F');
                        @endphp
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endfor
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-36">
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Terkonfirmasi</option>
                    <option value="loading" {{ request('status') === 'loading' ? 'selected' : '' }}>Sedang Loading</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Rigging / Gantung Filter -->
            <div class="w-full sm:w-36">
                <select name="rigging_filter" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Instalasi</option>
                    <option value="gantung" {{ request('rigging_filter') === 'gantung' ? 'selected' : '' }}>🎯 LED Gantung</option>
                    <option value="ground" {{ request('rigging_filter') === 'ground' ? 'selected' : '' }}>Ground Stack</option>
                </select>
            </div>

            <div class="flex items-center gap-1.5 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'month', 'status', 'rigging_filter']))
                    <a href="{{ route('orders.index') }}" class="w-full sm:w-auto text-center px-3 py-2 text-xs text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-semibold">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Quick Filter Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pt-1 text-[11px]">
            <span class="text-slate-400 font-semibold mr-1 shrink-0">Quick Filter:</span>
            <a href="{{ route('orders.index') }}" class="px-2.5 py-1 rounded-lg border {{ !request()->hasAny(['month', 'status', 'rigging_filter']) ? 'bg-indigo-600 text-white border-indigo-600 font-bold' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                Semua
            </a>
            <a href="{{ route('orders.index', ['month' => 'this_month']) }}" class="px-2.5 py-1 rounded-lg border {{ request('month') === 'this_month' ? 'bg-indigo-600 text-white border-indigo-600 font-bold' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                Bulan Ini
            </a>
            <a href="{{ route('orders.index', ['status' => 'confirmed']) }}" class="px-2.5 py-1 rounded-lg border {{ request('status') === 'confirmed' ? 'bg-amber-600 text-white border-amber-600 font-bold' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                Terkonfirmasi
            </a>
            <a href="{{ route('orders.index', ['status' => 'loading']) }}" class="px-2.5 py-1 rounded-lg border {{ request('status') === 'loading' ? 'bg-sky-600 text-white border-sky-600 font-bold' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                Sedang Loading
            </a>
            <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="px-2.5 py-1 rounded-lg border {{ request('status') === 'completed' ? 'bg-emerald-600 text-white border-emerald-600 font-bold' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                Selesai
            </a>
            <a href="{{ route('orders.index', ['rigging_filter' => 'gantung']) }}" class="px-2.5 py-1 rounded-lg border {{ request('rigging_filter') === 'gantung' ? 'bg-purple-600 text-white border-purple-600 font-bold' : 'bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-100' }}">
                🎯 LED Gantung
            </a>
        </div>
    </div>

    <!-- Orders Table (Compact, Kasir LED Style) -->
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800/80 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-3.5 py-3 font-semibold">No Order & SPK</th>
                        <th class="px-3.5 py-3 font-semibold">Client & Instansi</th>
                        <th class="px-3.5 py-3 font-semibold">Event & Lokasi</th>
                        <th class="px-3.5 py-3 font-semibold">Jadwal Operasional</th>
                        <th class="px-3.5 py-3 font-semibold">Spesifikasi Layar LED</th>
                        <th class="px-3.5 py-3 font-semibold">Armada</th>
                        <th class="px-3.5 py-3 font-semibold">Status</th>
                        <th class="px-3.5 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($orders as $order)
                        @php
                            $isRigging = $order->screens->contains(function ($s) {
                                $m = strtolower($s->rigging_method ?? '');
                                return str_contains($m, 'hang') || str_contains($m, 'gantung') || str_contains($m, 'rig');
                            });
                            $screenBrands = $order->screens->map(function ($s) {
                                $brand = \App\Services\LedCalculationService::obfuscateBrand($s->brand_series);
                                return "{$brand} {$s->pixel_pitch}";
                            })->unique()->implode(', ');
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <!-- No Order & SPK -->
                            <td class="px-3.5 py-2.5 whitespace-nowrap">
                                <div class="font-bold text-slate-900 dark:text-white font-mono">{{ $order->order_number }}</div>
                                @if($order->spkAssignment)
                                    <div class="text-[10px] font-mono text-indigo-600 dark:text-indigo-400 mt-0.5 font-semibold">
                                        {{ $order->spkAssignment->spk_number }}
                                    </div>
                                @endif
                            </td>

                            <!-- Client & Instansi (No Phone in Preview) -->
                            <td class="px-3.5 py-2.5" title="Kontak Phone: {{ $order->customer->phone ?? '-' }}">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $order->customer->salutation ? $order->customer->salutation.' ' : '' }}{{ $order->customer->name }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ $order->customer->organization ?: 'Pelanggan Individu' }}
                                </div>
                            </td>

                            <!-- Event & Lokasi -->
                            <td class="px-3.5 py-2.5">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $order->event_name }}</div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                    <span>📍 {{ $order->venue_name }}</span>
                                </div>
                            </td>

                            <!-- Jadwal Loading & Acara -->
                            <td class="px-3.5 py-2.5 whitespace-nowrap">
                                <div class="text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                                    🚚 Loading: {{ $order->loading_date?->format('d M') }} ({{ $order->loading_time }})
                                </div>
                                <div class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">
                                    🎭 Acara: {{ $order->event_start_date?->format('d M') }} • {{ $order->event_time ?: ($order->jam_acara ?: '-') }}
                                </div>
                            </td>

                            <!-- Spesifikasi Layar LED -->
                            <td class="px-3.5 py-2.5">
                                <div class="flex items-center gap-1.5 font-bold text-slate-900 dark:text-white">
                                    <span>{{ $order->total_sqm }} m²</span>
                                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-normal">
                                        {{ $order->screens->count() }} Set
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ $screenBrands ?: "{$order->total_sqm} m²" }}
                                </div>
                                @if($isRigging)
                                    <div class="mt-0.5">
                                        <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                            🎯 Gantung / Rigging
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <!-- Armada -->
                            <td class="px-3.5 py-2.5 whitespace-nowrap">
                                <div class="text-xs font-semibold text-slate-900 dark:text-white">
                                    🚐 {{ $order->vehicle_loading_in ?? 'Armada Gudang' }}
                                </div>
                                @if($order->spkAssignment)
                                    <div class="flex items-center gap-1 mt-0.5 text-[10px]">
                                        @if($order->spkAssignment->departure_status === 'verified')
                                            <span class="text-emerald-500 font-semibold" title="Barang Keluar Sudah Diverifikasi">✓ Keluar</span>
                                        @else
                                            <span class="text-slate-400">○ Gudang</span>
                                        @endif
                                        <span>/</span>
                                        @if($order->spkAssignment->return_status === 'verified')
                                            <span class="text-emerald-500 font-semibold" title="Barang Kembali Sudah Diverifikasi">✓ Kembali</span>
                                        @else
                                            <span class="text-slate-400">○ Lapangan</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-3.5 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $order->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $order->status === 'loading' ? 'bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20' : '' }}
                                    {{ $order->status === 'confirmed' ? 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : '' }}
                                    {{ $order->status === 'cancelled' ? 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' : '' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-3.5 py-2.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('orders.show', $order) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 dark:bg-slate-800 dark:hover:bg-indigo-900/30 text-slate-700 hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400 transition-colors" title="Lihat Detail Pesanan">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('orders.edit', $order) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-50 dark:bg-slate-800 dark:hover:bg-amber-900/30 text-slate-700 hover:text-amber-600 dark:text-slate-300 dark:hover:text-amber-400 transition-colors" title="Edit Pesanan">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    @if($order->spkAssignment)
                                        <a href="{{ route('spk.show', $order->spkAssignment) }}" class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 transition-colors" title="Buka SPK 3 Lembar">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>

                                        <a href="{{ route('spk.live', $order->public_token) }}" target="_blank" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 transition-colors" title="Buka Link Live Crew Lapangan">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada pesanan event sesuai filter</p>
                                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau reset filter untuk melihat semua data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-3.5 border-t border-slate-200 dark:border-slate-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
