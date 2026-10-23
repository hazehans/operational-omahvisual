@extends('layouts.app')

@section('title', 'Dashboard Operasional')
@section('page_title', 'Ringkasan Eksekutif & Gudang')

@section('content')
<div class="space-y-8">

    <!-- Hero / Welcome Header -->
    <div class="relative overflow-hidden rounded-2xl bg-linear-to-r from-slate-900 via-indigo-950/70 to-slate-900 border border-slate-800 p-6 md:p-8 shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Sistem Inventori Aktif — Gedung Utama
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">
                    Halo, {{ Auth::user()->name }}!
                </h1>
                <p class="text-sm md:text-base text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Pusat kendali ketersediaan kabinet videotron, 25 unit processor Novastar, armada TV, toolbox teknisi, dan mutasi gudang Omah Visual LED.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                @can('record movement')
                <a href="{{ route('movements.create') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs md:text-sm font-bold tracking-wide uppercase shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Catat Mutasi</span>
                </a>
                @endcan

                @can('request stock adjustment')
                <a href="{{ route('adjustments.create') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs md:text-sm font-bold tracking-wide uppercase border border-slate-700 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Ajukan Koreksi</span>
                </a>
                @endcan

                @can('execute service ticket')
                <a href="{{ route('service-tickets.index') }}" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs md:text-sm font-bold tracking-wide uppercase shadow-lg shadow-purple-600/30 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Bengkel &amp; Servis</span>
                </a>
                @endcan

                <a href="{{ route('calendar.index') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 border border-indigo-500/30 text-xs md:text-sm font-bold tracking-wide uppercase transition-all flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Kalender Event</span>
                </a>

                <a href="{{ route('guide.index') }}" class="px-4 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 text-xs md:text-sm font-bold tracking-wide uppercase transition-all flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Panduan &amp; SOP</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Banner Panduan Penggunaan & SOP (Testing Bos / Tim) -->
    <div class="relative overflow-hidden rounded-2xl bg-linear-to-r from-amber-500/15 via-indigo-950/40 to-slate-900 border border-amber-500/30 p-5 md:p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 shadow-xl">
        <div class="flex items-start gap-4">
            <div class="p-3 rounded-2xl bg-amber-500/20 text-amber-400 mt-0.5 shrink-0 border border-amber-500/30 shadow-md">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-base md:text-lg font-black text-white tracking-tight">Buku Panduan &amp; Standar Operasional (SOP)</h2>
                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-amber-500/20 text-amber-300 font-bold uppercase tracking-wider border border-amber-500/30">Mode Uji Coba Bos</span>
                </div>
                <p class="text-xs md:text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Ingin mencoba alur operasional dari awal hingga akhir? Pelajari 10 langkah siklus event (SPK &rarr; Kru Berangkat &rarr; Kru Pulang), rincian wewenang tiap peran, dan salin kredensial akun simulasi untuk pengetesan mandiri.
                </p>
            </div>
        </div>
        <a href="{{ route('guide.index') }}" class="px-5 py-2.5 rounded-xl bg-linear-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-xs md:text-sm shrink-0 shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2">
            <span>Buka Buku Panduan &rarr;</span>
        </a>
    </div>

    @can('conduct stock count')
    <!-- Banner Pengesahan Stok Awal (Decision 2 User) -->
    @if (!$initialCountSession || $initialCountSession->status !== 'verified')
        <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="p-2.5 rounded-xl bg-amber-500/20 text-amber-400 mt-0.5">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm md:text-base font-bold text-amber-300">Stok Awal Excel Belum Disahkan Resmi</h3>
                    <p class="text-xs md:text-sm text-amber-200/80 mt-0.5">
                        Sesuai PRD, data fisik dari spreadsheet perlu diverifikasi oleh Tim Warehouse sebelum resmi menjadi saldo awal berjalan.
                    </p>
                </div>
            </div>
            <a href="{{ route('initial-counts.index') }}" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs md:text-sm shrink-0 shadow-lg shadow-amber-500/20 transition-all flex items-center gap-1.5">
                <span>Periksa & Sahkan Stok Awal</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    @else
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></div>
                <span class="text-sm font-semibold text-emerald-300">
                    Stok awal resmi telah disahkan oleh {{ $initialCountSession->verifier?->name ?? 'Tim Warehouse' }} pada {{ $initialCountSession->finalized_at?->format('d M Y H:i') }}.
                </span>
            </div>
            <a href="{{ route('initial-counts.index') }}" class="text-sm font-medium text-emerald-400 hover:underline shrink-0">
                Lihat Berita Acara &rarr;
            </a>
        </div>
    @endif
    @endcan

    <!-- 4 Key Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Metric 1: Luas LED Tersedia -->
        <div class="p-5 md:p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-3">
                <span class="uppercase tracking-wider">Kabinet LED Tersedia</span>
                <span class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ number_format($totalLedSqm, 1) }}</span>
                <span class="text-sm font-semibold text-indigo-400">m² siap sewa</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                Total {{ number_format($totalLedCabinets) }} unit kabinet (P3.9 & P2.6) di Staging LED.
            </p>
        </div>

        <!-- Metric 2: Unit Aset Terlacak -->
        <div class="p-5 md:p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-3">
                <span class="uppercase tracking-wider">Unit Aset Individual</span>
                <span class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $totalAssets }}</span>
                <span class="text-sm font-semibold text-cyan-400">Unit Terdaftar</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                Termasuk 25 unit Processor Novastar, TV Samsung, & Toolbox.
            </p>
        </div>

        <!-- Metric 3: Master Katalog Barang -->
        <div class="p-5 md:p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-3">
                <span class="uppercase tracking-wider">Master Katalog</span>
                <span class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $totalProducts }}</span>
                <span class="text-sm font-semibold text-emerald-400">SKU Aktif</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                Kabel jumper, baut, sparepart modul, PSU, dan alat bantu.
            </p>
        </div>

        <!-- Metric 4: Titik Lemari / Lokasi -->
        <div class="p-5 md:p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold mb-3">
                <span class="uppercase tracking-wider">Titik Gudang</span>
                <span class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $totalLocations }}</span>
                <span class="text-sm font-semibold text-purple-400">Titik Area</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                Lemari 1-3, Area Staging LED, & Workshop Omah Visual.
            </p>
        </div>
    </div>

    <!-- Section 2 Kolom: Processor Novastar & Riwayat Mutasi -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri (2 span): Daftar 25 Processor Novastar -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Unit Aset Utama (Processor & Layar)</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Pelacakan unit individual bernilai tinggi dengan kode fisik unik</p>
                </div>
                <a href="{{ route('assets.index') }}" class="text-sm font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">
                    Lihat Semua ({{ $totalAssets }}) &rarr;
                </a>
            </div>

            <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-2xl overflow-hidden shadow-md">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100 dark:bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold text-xs border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-5 py-3.5">Kode Aset</th>
                                <th class="px-5 py-3.5">Perangkat</th>
                                <th class="px-5 py-3.5">Lokasi Fisik</th>
                                <th class="px-5 py-3.5">Kondisi</th>
                                <th class="px-5 py-3.5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                            @forelse($recentAssets as $ast)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-5 py-3.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                        <a href="{{ route('assets.show', $ast) }}" class="hover:underline">
                                            {{ $ast->asset_code }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-slate-900 dark:text-white text-sm">{{ $ast->product->name }}</div>
                                        <div class="text-xs text-slate-500 font-mono">{{ $ast->product->model_series ?? $ast->product->brand }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300 font-medium">
                                        {{ $ast->currentLocation->name ?? 'Gedung Utama' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($ast->condition === 'available')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                Bagus / Siap
                                            </span>
                                        @elseif($ast->condition === 'damaged')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                                Rusak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                                Servis
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-xs text-slate-400 font-medium">
                                            {{ $ast->status === 'in_storage' ? 'Di Gudang' : ($ast->status === 'deployed' ? 'Dipakai Event' : $ast->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                                        Belum ada aset terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (1 span): Titik Lokasi Lemari & Mutasi Terbaru -->
        <div class="space-y-6">
            <!-- Lokasi Lemari -->
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Penyimpanan Gudang</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Distribusi barang di 5 titik area Gedung Utama</p>

                <div class="space-y-2.5">
                    @foreach($locationsWithStock as $loc)
                        <a href="{{ route('locations.show', $loc) }}" class="p-4 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 hover:border-indigo-500/50 transition-all flex items-center justify-between group">
                            <div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-indigo-400 transition-colors">
                                    {{ $loc->name }}
                                </div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $loc->code }}</div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $loc->assets_count }} unit aset</span>
                                <span class="block text-xs text-slate-400">{{ $loc->stock_balances_count }} item saldo</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
