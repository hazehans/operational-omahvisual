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
                    {{ $order->status === 'confirmed' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : '' }}
                    {{ $order->status === 'draft' ? 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20' : '' }}
                    {{ $order->status === 'cancelled' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' : '' }}">
                    {{ ucfirst($order->status) }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                    {{ ($order->status_wilayah ?? 'dalam_kota') === 'luar_kota' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
                    {{ ($order->status_wilayah ?? 'dalam_kota') === 'luar_kota' ? '⚠️ Luar Kota' : '📍 Dalam Kota' }}
                </span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                <span>📍 {{ $order->venue_name }} {{ $order->kabupaten ? "({$order->kabupaten}, {$order->provinsi})" : '' }}</span>
                <span>•</span>
                <span>🚚 Loading: {{ $order->loading_date->format('d M Y') }} ({{ $order->loading_time }})</span>
                <span>•</span>
                <span>🎭 Acara: {{ $order->event_start_date->format('d M Y') }} ({{ $order->jam_acara ?? $order->ready_time ?? '-' }})</span>
                @if($order->jam_menyala)
                    <span>•</span>
                    <span>💡 Menyala: {{ $order->jam_menyala }}</span>
                @endif
                @if($order->waktu_gladi)
                    <span>•</span>
                    <span>⏱️ Gladi: {{ $order->waktu_gladi }}</span>
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Edit Order -->
            <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all shadow-xs">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Pesanan</span>
            </a>

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

            <!-- Invoice Action Button -->
            <a href="{{ route('invoices.create', ['order_id' => $order->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-sm font-semibold shadow-md shadow-cyan-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                </svg>
                <span>+ Buat Invoice</span>
            </a>


            <!-- Delete Order -->
            <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan {{ $order->order_number }} ini? Seluruh data SPK dan formasi layar akan dihapus.');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/40 text-rose-600 dark:text-rose-400 transition-colors shadow-xs" title="Hapus Pesanan Ini">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Dual Access Link Bars: Crew Field Live & Customer Self-Service Tracking Portal -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- 1. Live Token Bar for Crew Mobile Access -->
        <div class="p-4 rounded-2xl bg-linear-to-r from-emerald-500/10 via-teal-500/10 to-emerald-500/5 border border-emerald-500/20 flex flex-col justify-between gap-3 shadow-xs">
            <div class="flex items-start gap-3">
                <span class="p-2 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v16a2 2 0 002 2z" />
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📱 Link Live Crew Lapangan</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold">Crew Internal</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Akses khusus teknisi & driver untuk checklist bongkar muat dan input form QC venue.
                    </p>
                    <div class="text-[11px] text-slate-400 dark:text-slate-500 font-mono truncate mt-1">
                        {{ route('spk.live', $order->public_token) }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1 border-t border-emerald-500/10">
                <button onclick="navigator.clipboard.writeText('{{ route('spk.live', $order->public_token) }}'); alert('Link Crew Lapangan berhasil disalin! Silakan kirimkan ke grup WhatsApp Crew.')" 
                    class="flex-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5">
                    <span>📋 Salin Link Crew</span>
                </button>
                <a href="{{ route('spk.live', $order->public_token) }}" target="_blank" 
                    class="p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-emerald-600 transition-colors" title="Buka Pratinjau Mobile">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- 2. Customer Self-Service Tracking Portal Bar -->
        <div class="p-4 rounded-2xl bg-linear-to-r from-cyan-500/10 via-indigo-500/10 to-cyan-500/5 border border-cyan-500/20 flex flex-col justify-between gap-3 shadow-xs">
            <div class="flex items-start gap-3">
                <span class="p-2 rounded-xl bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🌐 Portal Pelacakan Customer & EO</span>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 font-bold">Klien & VJ Team</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Tautan publik mandiri untuk memantau status persiapan, foto BAST QC, dan spesifikasi layar VJ.
                    </p>
                    <div class="text-[11px] text-slate-400 dark:text-slate-500 font-mono truncate mt-1">
                        {{ route('orders.track', $order->public_token) }}
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-cyan-500/10">
                <a href="{{ $order->tracking_whatsapp_url }}" target="_blank"
                    class="flex-1 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1.5">
                    <span>💬 Kirim ke WA Customer</span>
                </a>

                <button onclick="navigator.clipboard.writeText('{{ route('orders.track', $order->public_token) }}'); alert('Tautan Pelacakan Customer berhasil disalin!')" 
                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-all">
                    📋 Salin Link
                </button>

                <a href="{{ route('orders.track', $order->public_token) }}" target="_blank" 
                    class="p-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-cyan-600 transition-colors" title="Buka Portal Tracking">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid: Event Specs & Customer -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: LED Screens & Checklists Summary -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 3D Stage Designer & 2D Blueprint Visualizer -->
            <x-stage-designer-3d :order="$order" :screens="$order->screens" :saveUrl="route('orders.update-stage-layout', $order)" canvasHeight="440px" />

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
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Wilayah:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ ($order->status_wilayah ?? 'dalam_kota') === 'luar_kota' ? 'Luar Kota' : 'Dalam Kota' }} ({{ $order->kabupaten ?? 'Malang' }})</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kelistrikan:</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $order->kelistrikan ?? $order->electricity_source ?? '-' }}</span>
                    </div>
                    @if($order->kelistrikan_keterangan)
                        <div class="text-[11px] text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 p-2 rounded-lg">
                            ⚡ {{ $order->kelistrikan_keterangan }}
                        </div>
                    @endif
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

            <!-- Invoices & Billing Card -->
            <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Faktur & Tagihan (Invoices)</h3>
                    <a href="{{ route('invoices.create', ['order_id' => $order->id]) }}" class="text-[11px] font-semibold text-cyan-600 dark:text-cyan-400 hover:underline">
                        + Buat Baru
                    </a>
                </div>

                @forelse($order->invoices as $inv)
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-700/60 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('invoices.show', $inv) }}" class="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $inv->invoice_number }}
                            </a>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                {{ $inv->status === 'paid' ? 'bg-emerald-500/10 text-emerald-600' : ($inv->status === 'partial' ? 'bg-amber-500/10 text-amber-600' : 'bg-rose-500/10 text-rose-600') }}">
                                {{ $inv->status === 'paid' ? 'Lunas' : ($inv->status === 'partial' ? 'DP Masuk' : 'Belum Bayar') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Tagihan: <strong class="text-slate-900 dark:text-white">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</strong></span>
                            <span>Sisa: <strong class="{{ $inv->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">Rp {{ number_format($inv->balance_due, 0, ',', '.') }}</strong></span>
                        </div>
                        <div class="pt-1.5 border-t border-slate-200/50 dark:border-slate-700/50 flex items-center justify-between text-[11px]">
                            <a href="{{ route('invoices.print', $inv) }}" target="_blank" class="text-slate-500 hover:text-slate-900 dark:hover:text-white">
                                🖨️ Cetak Faktur A4
                            </a>
                            <a href="{{ route('invoices.show', $inv) }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                                Kelola Pembayaran →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-center rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-dashed border-slate-200 dark:border-slate-700 text-xs text-slate-400">
                        Belum ada faktur tagihan diterbitkan untuk pesanan ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

