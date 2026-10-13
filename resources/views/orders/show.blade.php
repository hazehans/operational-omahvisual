@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number . ' - Omah Visual')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('orders.index') }}" class="hover:text-indigo-600 transition-colors">Pesanan Event</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">{{ $order->order_number }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                <span>{{ $order->event_name }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                    {{ $order->status === 'completed' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : '' }}
                    {{ $order->status === 'loading' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20' : '' }}
                    {{ $order->status === 'confirmed' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : '' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                {{ $order->venue_name }} • Tanggal Loading: {{ $order->loading_date->format('d F Y') }} ({{ $order->loading_time }})
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($order->spkAssignment)
                <a href="{{ route('spk.show', $order->spkAssignment) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-md shadow-indigo-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Buka SPK 3 Lembar</span>
                </a>

                <a href="{{ route('spk.print', $order->spkAssignment) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-sm font-semibold transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF / A4</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Live Token Bar for Crew Mobile Access -->
    <div class="p-4 rounded-2xl bg-linear-to-r from-emerald-500/10 via-teal-500/10 to-indigo-500/10 border border-emerald-500/20 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="p-2 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v16a2 2 0 002 2z" />
                </svg>
            </span>
            <div>
                <div class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📱 Link Live Crew Lapangan (Tanpa Perlu Login)</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-600 font-bold">Token Aktif</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-mono truncate max-w-xl">
                    {{ route('spk.live', $order->public_token) }}
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="navigator.clipboard.writeText('{{ route('spk.live', $order->public_token) }}'); alert('Link Crew Lapangan berhasil disalin! Silakan kirimkan ke grup WhatsApp Crew.')" 
                class="w-full sm:w-auto px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-sm">
                📋 Salin Link WhatsApp
            </button>
            <a href="{{ route('spk.live', $order->public_token) }}" target="_blank" 
                class="p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-emerald-600" title="Buka Pratinjau Mobile">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Main Grid: Event Specs & Customer -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: LED Screens & Checklists Summary -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Screens Card -->
            <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🖥️ Layar LED Terpasang (Total {{ $order->total_sqm }} m²)</span>
                    </h2>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                        {{ $order->screens->count() }} Formasi Layar
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($order->screens as $screen)
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="font-bold text-slate-900 dark:text-white text-base">
                                    {{ $screen->screen_name }}
                                </div>
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                    {{ $screen->brand_series }} • {{ $screen->pixel_pitch }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                    <div class="text-[10px] text-slate-500">Ukuran Layar</div>
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $screen->width_m }}m × {{ $screen->height_m }}m</div>
                                </div>
                                <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                    <div class="text-[10px] text-slate-500">Total Luas</div>
                                    <div class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400">{{ $screen->total_sqm }} m²</div>
                                </div>
                                <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                    <div class="text-[10px] text-slate-500">Kabinet Fisik</div>
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $screen->total_cabinets }} Kabinet</div>
                                    <div class="text-[9px] text-slate-500">{{ $screen->cabinets_1m }}x 1m | {{ $screen->cabinets_50cm }}x 50cm</div>
                                </div>
                                <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                                    <div class="text-[10px] text-slate-500">Box Hardcase</div>
                                    <div class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ $screen->total_hardcases }} Flight Case</div>
                                </div>
                            </div>

                            <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1 pt-1">
                                <div>🎛️ Prosesor: <span class="font-bold text-slate-900 dark:text-white">{{ $screen->processor_model }}</span></div>
                                <div>🏗️ Metode: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $screen->rigging_method)) }}</span> {{ $screen->stage_specification ? '('.$screen->stage_specification.')' : '' }}</div>
                                <div>🔌 Kabel & Jumper: <span class="font-semibold">{{ $screen->power_jumpers_qty }} Power Jumper, {{ $screen->lan_jumpers_qty }} LAN Jumper</span></div>
                                <div>🛡️ Spare Standby: <span class="font-semibold">{{ $screen->spare_modules_qty }} Modul Spare, {{ $screen->spare_receiving_cards_qty }} RC, {{ $screen->spare_psu_qty }} PSU</span></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Gate Verification Overview -->
            @if($order->spkAssignment)
                <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span>🛡️ Status Gerbang Serah Terima SPK</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Aturan Wajib: PIC Kepulangan Barang Wajib Orang yg Berbeda dari Keberangkatan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Keberangkatan -->
                        <div class="p-4 rounded-xl border {{ $order->spkAssignment->departure_status === 'verified' ? 'bg-indigo-50/40 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-800/40' : 'bg-slate-50 dark:bg-slate-800/30 border-slate-200 dark:border-slate-700' }} space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">1. KEBERANGKATAN</span>
                                <span class="text-xs font-bold {{ $order->spkAssignment->departure_status === 'verified' ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $order->spkAssignment->departure_status === 'verified' ? '✓ Disahkan' : '○ Menunggu' }}
                                </span>
                            </div>
                            @if($order->spkAssignment->departure_status === 'verified')
                                <div class="text-xs text-slate-700 dark:text-slate-300">
                                    <div>PIC Event: <span class="font-bold">{{ $order->spkAssignment->departure_pic_event_name }}</span></div>
                                    <div>PIC Gudang: <span class="font-bold">{{ $order->spkAssignment->departure_pic_warehouse_name }}</span></div>
                                    <div class="text-[11px] text-slate-500 mt-1">Waktu: {{ $order->spkAssignment->departure_datetime?->format('d M Y H:i') }}</div>
                                </div>
                            @else
                                <p class="text-xs text-slate-500">Barang belum dicek keluar oleh PIC Gudang & PIC Event.</p>
                            @endif
                        </div>

                        <!-- Kepulangan -->
                        <div class="p-4 rounded-xl border {{ $order->spkAssignment->return_status === 'verified' ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-slate-50 dark:bg-slate-800/30 border-slate-200 dark:border-slate-700' }} space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">2. KEPULANGAN</span>
                                <span class="text-xs font-bold {{ $order->spkAssignment->return_status === 'verified' ? 'text-emerald-600' : 'text-slate-400' }}">
                                    {{ $order->spkAssignment->return_status === 'verified' ? '✓ Disahkan' : '○ Menunggu' }}
                                </span>
                            </div>
                            @if($order->spkAssignment->return_status === 'verified')
                                <div class="text-xs text-slate-700 dark:text-slate-300">
                                    <div>PIC Event: <span class="font-bold">{{ $order->spkAssignment->return_pic_event_name }}</span></div>
                                    <div>PIC Gudang: <span class="font-bold">{{ $order->spkAssignment->return_pic_warehouse_name }}</span></div>
                                    <div class="text-[11px] text-slate-500 mt-1">Waktu: {{ $order->spkAssignment->return_datetime?->format('d M Y H:i') }}</div>
                                </div>
                            @else
                                <p class="text-xs text-slate-500">Barang belum dicek kembali setelah acara selesai.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Client & Financials -->
        <div class="space-y-6">
            <!-- Client Card -->
            <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Informasi Client</h3>
                <div class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ $order->customer->name }}
                </div>
                <div class="text-xs text-slate-500">
                    {{ $order->customer->organization ?? 'Personal Client' }}
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Nomor Telepon:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $order->customer->phone }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Lokasi Venue:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $order->venue_name }}</span>
                    </div>
                </div>

                @if($order->google_maps_url)
                    <div class="pt-2">
                        <a href="{{ $order->google_maps_url }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-slate-200 transition-colors">
                            <span>📍 Buka Google Maps Venue</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Crew Roster Card -->
            @if($order->spkAssignment)
                <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Tim Lapangan & Armada</h3>
                    <div class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🚐 {{ $order->spkAssignment->vehicle_name ?? 'TRAGA 1' }}</span>
                    </div>
                    <div class="text-xs text-slate-500">
                        PIC Event: <span class="font-bold text-slate-900 dark:text-white">{{ $order->spkAssignment->crew_pic_event_name ?? '-' }}</span>
                    </div>

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-xs space-y-1">
                        <div class="text-slate-500 font-semibold mb-1">Roster Crew:</div>
                        @for($i = 1; $i <= 7; $i++)
                            @php $field = "crew_{$i}_name"; @endphp
                            @if(!empty($order->spkAssignment->$field))
                                <div class="flex items-center justify-between py-0.5">
                                    <span class="text-slate-400">Crew {{ $i }}:</span>
                                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ $order->spkAssignment->$field }}</span>
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>
            @endif

            <!-- Finance Card -->
            <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Finansial</h3>
                <div class="flex items-baseline justify-between">
                    <span class="text-xs text-slate-500">Total Harga:</span>
                    <span class="text-lg font-extrabold text-slate-900 dark:text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-baseline justify-between text-xs">
                    <span class="text-slate-500">Uang Muka (DP):</span>
                    <span class="font-semibold text-emerald-600">Rp {{ number_format($order->down_payment, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-baseline justify-between text-xs border-t border-slate-100 dark:border-slate-800 pt-2">
                    <span class="text-slate-500">Sisa Kurang:</span>
                    <span class="font-bold text-rose-600">Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</span>
                </div>
                <div class="text-[11px] text-slate-400">
                    Metode: {{ $order->down_payment_method ?? 'BCA' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

