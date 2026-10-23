@extends('layouts.app')

@section('title', 'Kalender Jadwal Operasional & Kru')
@section('page_title', 'Kalender Jadwal Event & Kru')

@section('content')
<div x-data="{
    statusFilter: 'all',
    searchQuery: '',
    selectedEvent: null,
    showModal: false,
    openEventDetail(evt) {
        this.selectedEvent = evt;
        this.showModal = true;
    },
    matchesFilter(evt) {
        const matchesStatus = (this.statusFilter === 'all') || (evt.status === this.statusFilter);
        const query = this.searchQuery.toLowerCase().trim();
        const matchesQuery = !query ||
            evt.event_name.toLowerCase().includes(query) ||
            evt.venue_name.toLowerCase().includes(query) ||
            evt.customer_name.toLowerCase().includes(query) ||
            evt.order_number.toLowerCase().includes(query);
        return matchesStatus && matchesQuery;
    }
}" class="space-y-6">

    <!-- Header & Navigation Bar -->
    <div class="rounded-3xl bg-linear-to-r from-slate-900 via-indigo-950/50 to-slate-900 border border-slate-800 p-6 md:p-8 shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-2">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Jadwal Operasional Terpadu</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                    <span>{{ $currentDate->translatedFormat('F Y') }}</span>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 font-mono font-normal border border-slate-700">
                        {{ $stats['total_events'] }} Event Terjadwal
                    </span>
                </h1>
                <p class="text-xs md:text-sm text-slate-400 mt-1 max-w-2xl">
                    Pantau agenda bongkar pasang panggung, loading out gudang, penugasan kru lapangan, serta deteksi bentrok stok videotron.
                </p>
            </div>

            <!-- Month Switcher & Controls -->
            @php
                $prevMonth = $currentDate->copy()->subMonth();
                $nextMonth = $currentDate->copy()->addMonth();
            @endphp
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('calendar.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                   class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 shadow-sm transition-all"
                   title="Bulan Sebelumnya ({{ $prevMonth->translatedFormat('F Y') }})">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <a href="{{ route('calendar.index', ['month' => now()->month, 'year' => now()->year]) }}"
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Hari Ini</span>
                </a>

                <a href="{{ route('calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                   class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 shadow-sm transition-all"
                   title="Bulan Berikutnya ({{ $nextMonth->translatedFormat('F Y') }})">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                @can('create orders')
                <a href="{{ route('orders.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Pesanan</span>
                </a>
                @endcan
            </div>
        </div>

        <!-- KPI Mini Stats Banner -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
            <div class="p-3.5 rounded-2xl bg-slate-800/40 border border-slate-800 space-y-1">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Event Bulan Ini</span>
                <div class="text-xl font-black text-white">{{ $stats['total_events'] }} <span class="text-xs font-normal text-slate-400">Acara</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/40 border border-slate-800 space-y-1">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Luas Layar Tersewa</span>
                <div class="text-xl font-black text-indigo-400">{{ $stats['total_sqm'] }} <span class="text-xs font-normal text-slate-400">m²</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/40 border border-slate-800 space-y-1">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Kru Lapangan Terlibat</span>
                <div class="text-xl font-black text-emerald-400">{{ $stats['total_crews_involved'] }} <span class="text-xs font-normal text-slate-400">Personel</span></div>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-800/40 border border-slate-800 space-y-1">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Status Loading / Aktif</span>
                <div class="text-xl font-black text-amber-400">{{ $stats['loading_events'] }} <span class="text-xs font-normal text-slate-400">Berjalan</span></div>
            </div>
        </div>
    </div>

    <!-- Potential Conflict / Overlap Banner -->
    @if(count($dayConflicts) > 0)
    <div class="p-4 md:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-start gap-3.5">
            <div class="p-2.5 rounded-xl bg-amber-500/20 text-amber-400 mt-0.5 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-amber-300 flex items-center gap-2">
                    <span>Terdeteksi {{ count($dayConflicts) }} Hari Padat / Multi-Event Bersamaan</span>
                    <span class="px-2 py-0.5 rounded text-[10px] bg-amber-500/30 text-amber-200 font-bold uppercase">Perhatian Gudang</span>
                </h3>
                <p class="text-xs text-amber-200/80 mt-0.5 leading-relaxed">
                    Terdapat tanggal dengan lebih dari satu acara berjalan serentak. Pastikan alokasi armada truk, ketersediaan kabel jumper powercon, dan processor Novastar cadangan telah diverifikasi oleh Tim Gudang.
                </p>
            </div>
        </div>
        <div class="flex flex-wrap gap-1.5 shrink-0 text-xs">
            @foreach($dayConflicts as $date => $conf)
                <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 border border-amber-500/40 text-amber-300 font-mono font-bold">
                    {{ \Carbon\Carbon::parse($date)->format('d M') }} ({{ $conf['count'] }} Event &bull; {{ $conf['total_sqm'] }} m²)
                </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Filter & Live Search Toolbar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-md">
        <!-- Live Search -->
        <div class="relative flex-1 max-w-md">
            <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" x-model="searchQuery" placeholder="Cari nama event, venue, atau customer..."
                   class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
        </div>

        <!-- Status Filter Pills -->
        <div class="flex flex-wrap items-center gap-1.5 text-xs">
            <span class="text-slate-400 font-medium mr-1 text-[11px]">Filter:</span>
            <button @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                Semua
            </button>
            <button @click="statusFilter = 'confirmed'" :class="statusFilter === 'confirmed' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                ✓ Confirmed
            </button>
            <button @click="statusFilter = 'loading'" :class="statusFilter === 'loading' ? 'bg-sky-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                🚚 Loading
            </button>
            <button @click="statusFilter = 'completed'" :class="statusFilter === 'completed' ? 'bg-purple-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                ★ Selesai
            </button>
        </div>
    </div>

    <!-- Interactive Calendar Grid -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
        <!-- Weekday Headers -->
        <div class="grid grid-cols-7 border-b border-slate-800 bg-slate-950/80 text-center font-bold text-xs uppercase tracking-wider text-slate-400">
            <div class="py-3.5 border-r border-slate-800/80">Senin</div>
            <div class="py-3.5 border-r border-slate-800/80">Selasa</div>
            <div class="py-3.5 border-r border-slate-800/80">Rabu</div>
            <div class="py-3.5 border-r border-slate-800/80">Kamis</div>
            <div class="py-3.5 border-r border-slate-800/80">Jumat</div>
            <div class="py-3.5 border-r border-slate-800/80 text-amber-400/90">Sabtu</div>
            <div class="py-3.5 text-rose-400/90">Minggu</div>
        </div>

        <!-- Days Grid -->
        <div class="grid grid-cols-7 divide-x divide-y divide-slate-800/80 bg-slate-950/20">
            @php
                $cursorDay = $startOfGrid->copy();
            @endphp

            @while($cursorDay->lte($endOfGrid))
                @php
                    $dateKey = $cursorDay->toDateString();
                    $isCurrentMonth = $cursorDay->month === $currentDate->month;
                    $isToday = $cursorDay->isToday();
                    $dayEvents = $eventsByDate[$dateKey] ?? [];
                    $hasConflict = isset($dayConflicts[$dateKey]);
                @endphp

                <div class="min-h-35 md:min-h-40 p-2 md:p-2.5 transition-colors {{ $isCurrentMonth ? 'bg-slate-900/60 hover:bg-slate-850/60' : 'bg-slate-950/60 opacity-40' }} flex flex-col justify-between group">
                    <!-- Day Number & Indicators -->
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl text-xs font-black {{ $isToday ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/40 ring-2 ring-indigo-400' : ($cursorDay->isWeekend() ? 'text-amber-400 font-bold' : 'text-slate-300') }}">
                            {{ $cursorDay->day }}
                        </span>

                        @if($hasConflict)
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold text-[10px] border border-amber-500/30" title="{{ $dayConflicts[$dateKey]['count'] }} Event serentak di hari ini (Total {{ $dayConflicts[$dateKey]['total_sqm'] }} m²)">
                                ⚡ {{ $dayConflicts[$dateKey]['count'] }}
                            </span>
                        @elseif(count($dayEvents) > 0)
                            <span class="text-[10px] text-slate-500 font-medium">
                                {{ count($dayEvents) }} job
                            </span>
                        @endif
                    </div>

                    <!-- Events Container for this Day -->
                    <div class="space-y-1.5 flex-1 overflow-y-auto max-h-27.5 pr-0.5">
                        @foreach($dayEvents as $evt)
                            @php
                                $cardClass = match($evt['status']) {
                                    'confirmed' => 'bg-emerald-950/60 hover:bg-emerald-900/80 border-emerald-500/30 text-emerald-200',
                                    'loading' => 'bg-sky-950/60 hover:bg-sky-900/80 border-sky-500/30 text-sky-200',
                                    'completed' => 'bg-purple-950/60 hover:bg-purple-900/80 border-purple-500/30 text-purple-200',
                                    default => 'bg-slate-800/80 hover:bg-slate-700 border-slate-700 text-slate-200',
                                };
                            @endphp
                            <div x-show="matchesFilter({{ Js::from($evt) }})"
                                 @click="openEventDetail({{ Js::from($evt) }})"
                                 class="p-1.5 rounded-xl text-left cursor-pointer transition-all border transform hover:-translate-y-0.5 shadow-xs {{ $cardClass }}">

                                <div class="flex items-center justify-between gap-1">
                                    <div class="font-bold text-[11px] truncate leading-tight">
                                        {{ $evt['event_name'] }}
                                    </div>
                                    <span class="text-[9px] font-mono px-1 py-0.2 rounded bg-black/30 shrink-0">
                                        {{ $evt['total_sqm'] }}m²
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-[10px] opacity-80 mt-0.5">
                                    <span class="truncate max-w-20">{{ $evt['venue_name'] }}</span>
                                    @if($evt['is_loading_day'])
                                        <span class="text-sky-300 font-bold shrink-0">🚚 In</span>
                                    @elseif($evt['is_event_day'])
                                        <span class="text-amber-300 font-bold shrink-0">🎭 Show</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @php
                    $cursorDay->addDay();
                @endphp
            @endwhile
        </div>
    </div>

    <!-- Event Detail Pop-up Modal -->
    <div x-show="showModal"
         x-cloak
         @keydown.escape.window="showModal = false"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">

        <div @click.away="showModal = false"
             class="bg-slate-900 border border-slate-700/80 rounded-3xl max-w-xl w-full p-6 md:p-8 space-y-6 shadow-2xl relative text-left">

            <!-- Modal Header -->
            <div class="flex items-start justify-between gap-4 border-b border-slate-800 pb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider"
                              :class="{
                                  'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': selectedEvent?.status === 'confirmed',
                                  'bg-sky-500/20 text-sky-300 border border-sky-500/30': selectedEvent?.status === 'loading',
                                  'bg-purple-500/20 text-purple-300 border border-purple-500/30': selectedEvent?.status === 'completed',
                                  'bg-slate-800 text-slate-300 border border-slate-700': !['confirmed','loading','completed'].includes(selectedEvent?.status)
                              }"
                              x-text="selectedEvent?.status">
                        </span>
                        <span class="text-xs font-mono text-slate-400" x-text="selectedEvent?.order_number"></span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black text-white" x-text="selectedEvent?.event_name"></h2>
                    <p class="text-xs text-indigo-400 font-semibold mt-0.5" x-text="'Klien: ' + selectedEvent?.customer_name"></p>
                </div>

                <button @click="showModal = false" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content / Event Specs -->
            <div class="space-y-4 text-xs">
                <!-- Location & Schedule -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <div class="space-y-1">
                        <span class="text-slate-400 font-semibold text-[11px] block uppercase tracking-wider">Lokasi Venue:</span>
                        <div class="font-bold text-white text-sm" x-text="selectedEvent?.venue_name"></div>
                        <div class="text-slate-400 text-xs" x-text="selectedEvent?.venue_address"></div>
                    </div>

                    <div class="space-y-1 sm:border-l sm:border-slate-800 sm:pl-3">
                        <span class="text-slate-400 font-semibold text-[11px] block uppercase tracking-wider">Waktu Operasional:</span>
                        <div>Loading: <strong class="text-slate-200" x-text="selectedEvent?.loading_date + ' (' + selectedEvent?.loading_time + ')'"></strong></div>
                        <div>Acara: <strong class="text-slate-200" x-text="selectedEvent?.event_start_date + ' s/d ' + selectedEvent?.event_end_date"></strong></div>
                        <div>Dimensi Layar: <strong class="text-indigo-400" x-text="selectedEvent?.total_sqm + ' m²'"></strong></div>
                    </div>
                </div>

                <!-- Assigned Crews with 1-Click WhatsApp Reminder -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold uppercase tracking-wider text-slate-300 text-[11px]">Kru Ditugaskan (<span x-text="selectedEvent?.crews?.length || 0"></span> Personel):</span>
                        <span class="text-[10px] text-slate-500">Kirim reminder langsung via WA</span>
                    </div>

                    <template x-if="selectedEvent?.crews && selectedEvent.crews.length > 0">
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            <template x-for="c in selectedEvent.crews" :key="c.nickname + c.role">
                                <div class="p-2.5 rounded-xl bg-slate-800/60 border border-slate-700/60 flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-600/40 text-indigo-300 font-bold flex items-center justify-center text-[10px]" x-text="c.nickname ? c.nickname.substring(0,2).toUpperCase() : 'KR'"></div>
                                        <div>
                                            <span class="font-bold text-white text-xs block" x-text="c.nickname"></span>
                                            <span class="text-[10px] text-indigo-400 font-semibold" x-text="c.role"></span>
                                        </div>
                                    </div>
                                    <a :href="c.reminder_url" target="_blank"
                                       class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] flex items-center gap-1 shadow-sm transition-all"
                                       title="Kirim Pesan WhatsApp Pengingat Jadwal & Link SPK Live">
                                        <span>⏰ Reminder WA</span>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!selectedEvent?.crews || selectedEvent.crews.length === 0">
                        <div class="p-4 rounded-xl bg-slate-950 text-slate-500 text-center italic">
                            Belum ada kru yang di-plotting pada SPK event ini.
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex flex-wrap items-center justify-end gap-2.5 pt-4 border-t border-slate-800">
                <template x-if="selectedEvent?.live_url">
                    <a :href="selectedEvent.live_url" target="_blank" class="px-4 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 text-xs font-bold flex items-center gap-1.5 transition-all">
                        <span>📱 Buka SPK Live Kru</span>
                    </a>
                </template>

                <template x-if="selectedEvent?.spk_url">
                    <a :href="selectedEvent.spk_url" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold flex items-center gap-1.5 shadow-md transition-all">
                        <span>📑 SPK Operasional</span>
                    </a>
                </template>

                <template x-if="selectedEvent?.detail_url">
                    <a :href="selectedEvent.detail_url" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition-all">
                        <span>Detail Pesanan &rarr;</span>
                    </a>
                </template>
            </div>

        </div>
    </div>

</div>
@endsection

