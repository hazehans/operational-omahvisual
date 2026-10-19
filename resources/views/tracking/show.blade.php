<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Tracking Pesanan - {{ $order->event_name }} ({{ $order->order_number }}) - Omah Visual</title>
    <meta name="description" content="Portal pelacakan status pesanan LED screen, kesiapan armada, hasil QC panggung, dan spesifikasi teknis multimedia secara real-time.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen font-sans antialiased selection:bg-cyan-500 selection:text-white pb-16">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-slate-900/80 backdrop-blur-md border-b border-slate-800/80 px-4 py-3 sm:px-6">
        <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-linear-to-tr from-cyan-600 to-indigo-600 flex items-center justify-center font-extrabold text-sm text-white shadow-md shadow-cyan-600/30">
                    OV
                </div>
                <div>
                    <div class="text-xs font-black tracking-wider uppercase text-white flex items-center gap-1.5">
                        <span>OMAH VISUAL</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 font-bold">LED SCREEN</span>
                    </div>
                    <div class="text-[11px] text-slate-400">Portal Pelacakan Pesanan Resmi</div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Live Tracking Aktif</span>
                </div>

                <button onclick="navigator.clipboard.writeText(window.location.href); alert('Tautan pelacakan berhasil disalin ke clipboard!');" 
                    class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-semibold flex items-center gap-1.5 transition-all shadow-xs" 
                    title="Salin Tautan Halaman Ini">
                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>Salin Link</span>
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 pt-6 space-y-6">

        <!-- Hero Section & Progress Summary -->
        <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-slate-900 via-indigo-950/40 to-slate-900 border border-indigo-900/40 p-6 sm:p-8 shadow-2xl">
            <!-- Ambient Glow -->
            <div class="absolute -top-24 -right-24 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 font-mono text-xs font-bold uppercase tracking-wider border border-indigo-500/30">
                            {{ $order->order_number }}
                        </span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                            {{ $order->status === 'completed' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                            {{ $order->status === 'running' ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : '' }}
                            {{ $order->status === 'loading' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : '' }}
                            {{ $order->status === 'confirmed' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : '' }}
                            {{ $order->status === 'cancelled' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : '' }}
                            {{ $order->status === 'draft' ? 'bg-slate-500/20 text-slate-300 border border-slate-500/30' : '' }}">
                            ● {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="text-xs text-slate-400">
                        Penyewa: <span class="font-bold text-slate-200">{{ $order->customer->name ?? 'Klien Omah Visual' }}</span>
                        @if($order->customer?->organization)
                            <span class="text-slate-400">({{ $order->customer->organization }})</span>
                        @endif
                    </div>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                        {{ $order->event_name }}
                    </h1>
                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs sm:text-sm text-slate-300">
                        <div class="flex items-center gap-1.5">
                            <span class="text-indigo-400">📍</span>
                            <span class="font-semibold">{{ $order->venue_name }}</span>
                            @if($order->kabupaten)
                                <span class="text-slate-400">({{ $order->kabupaten }}, {{ $order->provinsi ?? 'Indonesia' }})</span>
                            @endif
                        </div>
                        @if($order->google_maps_url)
                            <a href="{{ $order->google_maps_url }}" target="_blank" class="inline-flex items-center gap-1 text-cyan-400 hover:text-cyan-300 underline font-semibold text-xs">
                                <span>Petunjuk Arah Maps</span>
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Schedule Highlights Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                    <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800">
                        <div class="text-[10px] font-bold uppercase text-slate-400">Jadwal Loading</div>
                        <div class="text-sm font-extrabold text-white mt-0.5">{{ $order->loading_date->format('d M Y') }}</div>
                        <div class="text-xs font-semibold text-amber-400 mt-0.5">🕒 Pukul {{ $order->loading_time }}</div>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800">
                        <div class="text-[10px] font-bold uppercase text-slate-400">Jadwal Acara</div>
                        <div class="text-sm font-extrabold text-white mt-0.5">{{ $order->event_start_date->format('d M Y') }}</div>
                        <div class="text-xs font-semibold text-emerald-400 mt-0.5">🕒 {{ $order->jam_acara ?? $order->ready_time ?? 'Show Utama' }}</div>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800">
                        <div class="text-[10px] font-bold uppercase text-slate-400">Total Luas Layar</div>
                        <div class="text-sm font-extrabold text-cyan-400 mt-0.5">{{ $order->total_sqm }} m²</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $order->screens->count() }} Formasi Layar</div>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800">
                        <div class="text-[10px] font-bold uppercase text-slate-400">Status Pembayaran</div>
                        <div class="text-sm font-extrabold mt-0.5
                            {{ $order->payment_status === 'paid' ? 'text-emerald-400' : '' }}
                            {{ $order->payment_status === 'partial' ? 'text-amber-400' : '' }}
                            {{ $order->payment_status === 'unpaid' ? 'text-rose-400' : '' }}">
                            {{ $order->payment_status === 'paid' ? 'LUNAS' : ($order->payment_status === 'partial' ? 'DP Diterima' : 'Belum Dibayar') }}
                        </div>
                        <div class="text-xs text-slate-400 mt-0.5">Sisa: Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Overall Progress Bar -->
                <div class="pt-2">
                    <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                        <span class="text-slate-300">Progress Kesiapan Operasional</span>
                        <span class="text-cyan-400">{{ $progressPercentage }}% Selesai</span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-800 overflow-hidden p-0.5 border border-slate-700">
                        <div class="h-full rounded-full bg-linear-to-r from-indigo-500 via-cyan-500 to-emerald-400 transition-all duration-700 shadow-sm shadow-cyan-500/50"
                             style="width: {{ $progressPercentage }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5-Stage Visual Stepper Timeline -->
        <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span>🚦 Status Tahapan Persiapan & Pelaksanaan</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Pantau setiap tahapan operasional dari gudang hingga acara selesai secara transparan.</p>
                </div>
            </div>

            <!-- Timeline Stepper -->
            <div class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:left-3 sm:before:left-4 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-800">
                @foreach($timeline as $key => $stage)
                    @php
                        $isDone = $stage['state'] === 'completed';
                        $isCurrent = $stage['state'] === 'current';
                        $isUpcoming = $stage['state'] === 'upcoming';
                        $isCanc = $stage['state'] === 'cancelled';
                    @endphp

                    <div class="relative group">
                        <!-- Step Icon Indicator -->
                        <div class="absolute -left-6 sm:-left-8 top-0.5 flex items-center justify-center w-6 sm:w-8 h-6 sm:h-8 rounded-full font-bold text-xs transition-all
                            {{ $isDone ? 'bg-emerald-500 text-slate-950 ring-4 ring-emerald-500/20 shadow-md shadow-emerald-500/30' : '' }}
                            {{ $isCurrent ? 'bg-cyan-500 text-slate-950 ring-4 ring-cyan-500/30 animate-pulse shadow-md shadow-cyan-500/40' : '' }}
                            {{ $isUpcoming ? 'bg-slate-800 text-slate-400 border border-slate-700' : '' }}
                            {{ $isCanc ? 'bg-rose-500 text-white' : '' }}">
                            @if($isDone)
                                ✓
                            @elseif($isCurrent)
                                ➔
                            @else
                                {{ $stage['stage_number'] }}
                            @endif
                        </div>

                        <!-- Step Content Box -->
                        <div class="p-4 sm:p-5 rounded-2xl border transition-all
                            {{ $isDone ? 'bg-slate-800/40 border-slate-800 hover:border-slate-700' : '' }}
                            {{ $isCurrent ? 'bg-cyan-950/20 border-cyan-500/40 shadow-lg shadow-cyan-950/30' : '' }}
                            {{ $isUpcoming ? 'bg-slate-900/40 border-slate-800/60 opacity-75' : '' }}
                            {{ $isCanc ? 'bg-rose-950/20 border-rose-500/30' : '' }}">
                            
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <div class="text-[11px] font-bold uppercase tracking-wider
                                        {{ $isDone ? 'text-emerald-400' : '' }}
                                        {{ $isCurrent ? 'text-cyan-400' : '' }}
                                        {{ $isUpcoming ? 'text-slate-400' : '' }}">
                                        Tahap 0{{ $stage['stage_number'] }}
                                    </div>
                                    <h3 class="text-base font-bold text-white mt-0.5">
                                        {{ $stage['title'] }}
                                    </h3>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $isDone ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                        {{ $isCurrent ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold' : '' }}
                                        {{ $isUpcoming ? 'bg-slate-800 text-slate-400 border border-slate-700' : '' }}">
                                        {{ $stage['badge'] }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-300 mt-2">
                                {{ $stage['description'] }}
                            </p>

                            <!-- Optional Stage Specific Details -->
                            @if($key === 'en_route' && !empty($stage['vehicles']))
                                <div class="mt-3 p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 text-xs flex items-center gap-2 text-slate-300">
                                    <span>🚚</span>
                                    <span><strong>Armada Ditugaskan:</strong> {{ $stage['vehicles'] }}</span>
                                </div>
                            @endif

                            @if($key === 'qc_stage_ready' && $stage['state'] === 'completed')
                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div class="p-2.5 rounded-xl bg-emerald-950/20 border border-emerald-500/30 text-xs flex items-center gap-2 text-emerald-300">
                                        <span>⚡</span>
                                        <span>Grounding Aman ({{ $stage['grounding'] ? 'Lolos' : '-' }})</span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-emerald-950/20 border border-emerald-500/30 text-xs flex items-center gap-2 text-emerald-300">
                                        <span>🎨</span>
                                        <span>Uji Warna Layar RGB ({{ $stage['test_rgb'] ? 'Lolos' : '-' }})</span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-emerald-950/20 border border-emerald-500/30 text-xs flex items-center gap-2 text-emerald-300">
                                        <span>📡</span>
                                        <span>Sinyal Video Processor ({{ $stage['video_signal'] ? 'Lolos' : '-' }})</span>
                                    </div>
                                </div>
                            @endif

                            @if($stage['timestamp'])
                                <div class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Terverifikasi: {{ $stage['timestamp']->translatedFormat('d M Y, H:i') }} WIB</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quality Control (QC) & Visual BAST Card -->
        <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span>🛡️ Hasil Uji Kelayakan QC & BAST Visual Panggung</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Dokumentasi hasil inspeksi kelistrikan, layar, dan serah terima visual di venue.</p>
                </div>

                <div>
                    @if($spk && $spk->qc_status === 'passed')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold">
                            <span>✓</span>
                            <span>QC Telah Lolos Standar Omah Visual</span>
                        </span>
                    @elseif($spk && $spk->qc_status === 'failed')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-bold">
                            <span>⚠️</span>
                            <span>Dalam Perbaikan Teknisi</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-400 text-xs font-semibold">
                            <span>⏳</span>
                            <span>Menunggu Perakitan Venue</span>
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                <!-- Checklist Points -->
                <div class="space-y-3">
                    <div class="p-4 rounded-2xl border {{ $spk?->qc_grounding_safe ? 'bg-emerald-950/10 border-emerald-500/20' : 'bg-slate-800/40 border-slate-800' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-white flex items-center gap-2">
                                <span>⚡ Grounding & Keamanan Listrik</span>
                            </span>
                            <span class="text-xs font-bold {{ $spk?->qc_grounding_safe ? 'text-emerald-400' : 'text-slate-400' }}">
                                {{ $spk?->qc_grounding_safe ? '✓ Lolos Standar' : 'Pending' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Pengecekan voltase grounding dan pengetesan anti-kebocoran arus demi keamanan artis dan kru panggung.</p>
                    </div>

                    <div class="p-4 rounded-2xl border {{ $spk?->qc_test_rgb ? 'bg-emerald-950/10 border-emerald-500/20' : 'bg-slate-800/40 border-slate-800' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-white flex items-center gap-2">
                                <span>🎨 Uji Warna Layar (Full RGB & White)</span>
                            </span>
                            <span class="text-xs font-bold {{ $spk?->qc_test_rgb ? 'text-emerald-400' : 'text-slate-400' }}">
                                {{ $spk?->qc_test_rgb ? '✓ Lolos Standar' : 'Pending' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Uji layar penuh warna Merah, Hijau, Biru, dan Putih guna memastikan bebas dari dead pixel atau color drift.</p>
                    </div>

                    <div class="p-4 rounded-2xl border {{ $spk?->qc_video_signal_ok ? 'bg-emerald-950/10 border-emerald-500/20' : 'bg-slate-800/40 border-slate-800' }}">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-white flex items-center gap-2">
                                <span>📡 Sinyal Video & Processor NovaStar</span>
                            </span>
                            <span class="text-xs font-bold {{ $spk?->qc_video_signal_ok ? 'text-emerald-400' : 'text-slate-400' }}">
                                {{ $spk?->qc_video_signal_ok ? '✓ Stabil & Siap' : 'Pending' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Kalibrasi port output processor, refresh rate display, dan kesiapan menerima feed dari tim multimedia EO.</p>
                    </div>

                    @if($spk?->qc_notes)
                        <div class="p-3.5 rounded-2xl bg-slate-800/60 border border-slate-700/60 text-xs">
                            <span class="font-bold text-slate-300">Catatan Teknisi:</span>
                            <p class="text-slate-400 mt-0.5">{{ $spk->qc_notes }}</p>
                        </div>
                    @endif

                    <div class="pt-1 text-xs text-slate-400 flex flex-wrap items-center gap-3">
                        <span>Teknisi Bertanggung Jawab: <strong class="text-slate-200">{{ $spk?->qc_technician_name ?? 'Teknisi Omah Visual' }}</strong></span>
                        @if($spk?->qc_verified_at)
                            <span>•</span>
                            <span>{{ $spk->qc_verified_at->translatedFormat('d M Y, H:i') }} WIB</span>
                        @endif
                    </div>
                </div>

                <!-- Visual Documentation Photo -->
                <div class="space-y-2">
                    <div class="text-xs font-bold text-slate-300 flex items-center justify-between">
                        <span>Foto Dokumentasi Hasil Instalasi Panggung (BAST):</span>
                        @if($spk?->qc_photo_path)
                            <button type="button" onclick="openLightbox('{{ asset('storage/' . $spk->qc_photo_path) }}', 'Dokumentasi Visual BAST Layar Panggung')" class="text-cyan-400 hover:text-cyan-300 text-[11px] font-semibold flex items-center gap-1 cursor-pointer">
                                <span>Perbesar Gambar</span>
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    @if($spk?->qc_photo_path)
                        <div class="group relative rounded-2xl overflow-hidden border border-slate-700 bg-slate-900 shadow-md cursor-zoom-in"
                             onclick="openLightbox('{{ asset('storage/' . $spk->qc_photo_path) }}', 'Dokumentasi Visual BAST Layar Panggung')">
                            <img src="{{ asset('storage/' . $spk->qc_photo_path) }}" 
                                alt="Dokumentasi BAST Panggung {{ $order->event_name }}" 
                                class="w-full h-64 object-cover object-center group-hover:scale-102 transition-transform duration-300">
                            <div class="absolute inset-0 bg-linear-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-4">
                                <div class="text-xs text-white">
                                    <div class="font-bold flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        <span>Dokumentasi Resmi Panggung Terpasang</span>
                                    </div>
                                    <div class="text-[10px] text-slate-300 mt-0.5">Diverifikasi oleh {{ $spk->qc_technician_name ?? 'Teknisi' }} (Klik untuk perbesar HD)</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="h-64 rounded-2xl border-2 border-dashed border-slate-800 bg-slate-900/40 flex flex-col items-center justify-center p-6 text-center text-slate-400 space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center text-2xl text-slate-400">
                                📷
                            </div>
                            <div class="text-xs font-bold text-slate-300">Dokumentasi Visual Panggung Belum Diunggah</div>
                            <p class="text-[11px] text-slate-400 max-w-xs leading-relaxed">
                                Foto panggung BAST akan langsung tampil di sini begitu teknisi menyelesaikan pengetesan layar di lokasi acara.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Technical Screen Specs Card (For EO / VJ / Multimedia Crew) -->
        <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span>🖥️ Spesifikasi Teknis Layar LED (Panduan VJ & Multimedia)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Gunakan panduan resolusi piksel dan aspek rasio berikut untuk menyesuaikan resolusi konten visual panggung.</p>
                </div>
                <span class="px-3 py-1 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-xs font-bold self-start sm:self-auto">
                    Total {{ $order->total_sqm }} m² ({{ $order->screens->count() }} Formasi)
                </span>
            </div>

            <!-- Screens List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($order->screens as $screen)
                    <div class="p-5 rounded-2xl bg-slate-800/40 border border-slate-700/70 space-y-4 hover:border-slate-600 transition-all">
                        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                            <div>
                                <h3 class="font-extrabold text-white text-base flex items-center gap-2">
                                    <span>{{ $screen->screen_name }}</span>
                                </h3>
                                <div class="text-xs text-cyan-400 font-semibold mt-0.5">
                                    {{ $screen->brand_series }} • {{ $screen->pixel_pitch }}
                                </div>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-mono">
                                {{ $screen->total_sqm }} m²
                            </span>
                        </div>

                        <!-- Technical Metrics Grid -->
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Dimensi Fisik</div>
                                <div class="text-sm font-extrabold text-white mt-0.5">{{ $screen->width_m }} m × {{ $screen->height_m }} m</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Lebar × Tinggi</div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Resolusi Konten VJ</div>
                                <div class="text-sm font-extrabold text-cyan-400 mt-0.5 font-mono">{{ $screen->pixel_resolution['formatted'] }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Native Display Pixels</div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Aspek Rasio</div>
                                <div class="text-sm font-extrabold text-amber-300 mt-0.5 font-mono">{{ $screen->aspect_ratio }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Perbandingan Layar</div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Metode Rigging</div>
                                <div class="text-sm font-extrabold text-slate-200 mt-0.5">
                                    {{ $screen->rigging_method === 'ground_stake' ? 'Ground Stake' : ($screen->rigging_method === 'hanging' ? 'Hanging Rigging' : 'Stage Frame') }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $screen->processor_model ?? 'Novastar' }}</div>
                            </div>
                        </div>

                        @if($screen->stage_specification)
                            <div class="text-xs text-slate-400 bg-slate-900/50 p-2.5 rounded-xl border border-slate-800">
                                <strong class="text-slate-300">Spesifikasi Stage:</strong> {{ $screen->stage_specification }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Optional Blueprint Stage Layout Images -->
            @if($order->stage_layout_front_image || $order->stage_layout_image)
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <div class="text-xs font-bold text-slate-300 flex items-center justify-between">
                        <span class="flex items-center gap-2">📐 Visualisasi Blueprint Formasi Panggung:</span>
                        <span class="text-[11px] text-cyan-400 font-normal">💡 Klik gambar untuk perbesar HD</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($order->stage_layout_front_image)
                            @php
                                $frontSrc = str_starts_with($order->stage_layout_front_image, 'data:image') ? $order->stage_layout_front_image : asset('storage/' . $order->stage_layout_front_image);
                            @endphp
                            <div class="group p-3 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-2 hover:border-cyan-500/50 transition-all cursor-zoom-in"
                                 onclick="openLightbox('{{ $frontSrc }}', 'Blueprint Tampak Depan Layar & Stage (Front View)')">
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase">
                                    <span>Tampak Depan (Front View)</span>
                                    <span class="text-cyan-400 group-hover:underline font-semibold text-[10px]">🔍 Perbesar HD</span>
                                </div>
                                <div class="relative overflow-hidden rounded-xl bg-slate-950 border border-slate-800">
                                    <img src="{{ $frontSrc }}" 
                                         alt="Tampak Depan Layar" 
                                         class="w-full h-44 object-contain p-2 group-hover:scale-102 transition-transform">
                                </div>
                            </div>
                        @endif

                        @if($order->stage_layout_image)
                            @php
                                $panelSrc = str_starts_with($order->stage_layout_image, 'data:image') ? $order->stage_layout_image : asset('storage/' . $order->stage_layout_image);
                            @endphp
                            <div class="group p-3 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-2 hover:border-cyan-500/50 transition-all cursor-zoom-in"
                                 onclick="openLightbox('{{ $panelSrc }}', 'Blueprint Pemetaan Panel & Kabinet LED (Grid Modul & Resolusi VJ)')">
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase">
                                    <span>Pemetaan Panel &amp; Kabinet LED</span>
                                    <span class="text-cyan-400 group-hover:underline font-semibold text-[10px]">🔍 Perbesar HD</span>
                                </div>
                                <div class="relative overflow-hidden rounded-xl bg-slate-950 border border-slate-800">
                                    <img src="{{ $panelSrc }}" 
                                         alt="Pemetaan Panel &amp; Kabinet LED" 
                                         class="w-full h-44 object-contain p-2 group-hover:scale-102 transition-transform">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Billing Summary & Payment Instructions (Client Safe) -->
        <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span>💳 Rincian Tagihan & Informasi Pembayaran</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Status pembayaran resmi pesanan sewa LED screen Omah Visual.</p>
                </div>

                @if($latestInvoice)
                    <a href="{{ route('invoices.print', $latestInvoice) }}" target="_blank" 
                        class="px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold transition-all shadow-md shadow-cyan-600/20 inline-flex items-center gap-2 self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Unduh Invoice Resmi (#{{ $latestInvoice->invoice_number }})</span>
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-slate-800/40 border border-slate-700/60">
                    <div class="text-[11px] uppercase font-bold text-slate-400">Total Biaya Sewa</div>
                    <div class="text-xl font-black text-white mt-1">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                    <div class="text-xs text-slate-400 mt-1">Termasuk instalasi & operator</div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-800/40 border border-slate-700/60">
                    <div class="text-[11px] uppercase font-bold text-slate-400">Jumlah Telah Dibayar</div>
                    <div class="text-xl font-black text-emerald-400 mt-1">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</div>
                    <div class="text-xs text-emerald-500/80 mt-1">Tercatat di sistem finance</div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-800/40 border border-slate-700/60">
                    <div class="text-[11px] uppercase font-bold text-slate-400">Sisa Tagihan (Pelunasan)</div>
                    <div class="text-xl font-black {{ $order->remaining_balance > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1">
                        Rp {{ number_format($order->remaining_balance, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-slate-400 mt-1">
                        {{ $order->remaining_balance <= 0 ? '✓ Pembayaran Lunas Sepenuhnya' : 'Jatuh tempo saat acara berlangsung' }}
                    </div>
                </div>
            </div>

            <!-- Bank Transfer Accounts for Settlement -->
            @if($order->remaining_balance > 0)
                <div class="p-5 rounded-2xl bg-linear-to-r from-slate-900 via-indigo-950/30 to-slate-900 border border-indigo-900/40 space-y-3">
                    <div class="text-xs font-bold text-slate-200 flex items-center gap-2">
                        <span>🏦 Rekening Pembayaran Resmi Omah Visual:</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between">
                            <div>
                                <div class="font-extrabold text-white text-sm">BCA • 4390865177</div>
                                <div class="text-slate-400 mt-0.5">a/n Muhammad Burhanudin</div>
                            </div>
                            <button onclick="navigator.clipboard.writeText('4390865177'); alert('Nomor rekening BCA 4390865177 berhasil disalin!');" 
                                class="px-2.5 py-1 rounded-lg bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 text-[11px] font-bold">
                                Salin
                            </button>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between">
                            <div>
                                <div class="font-extrabold text-white text-sm">BCA • 3660399282</div>
                                <div class="text-slate-400 mt-0.5">a/n Balqist Hamada</div>
                            </div>
                            <button onclick="navigator.clipboard.writeText('3660399282'); alert('Nomor rekening BCA 3660399282 berhasil disalin!');" 
                                class="px-2.5 py-1 rounded-lg bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 text-[11px] font-bold">
                                Salin
                            </button>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400">
                        *Mohon mengirimkan bukti transfer pelunasan ke nomor WhatsApp admin atau PIC Event Omah Visual untuk pencatatan kwitansi otomatis.
                    </p>
                </div>
            @endif
        </div>

        <!-- PIC Event Direct Assistance Card -->
        <div class="p-6 rounded-3xl bg-linear-to-r from-emerald-950/20 via-cyan-950/20 to-indigo-950/20 border border-emerald-500/20 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl shrink-0">
                    💬
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-white">Butuh Koordinasi Teknis Lapangan?</h3>
                    <p class="text-xs text-slate-300 mt-0.5">
                        PIC Event Anda: <strong class="text-emerald-400">{{ $picEventName }}</strong> siap membantu kelancaran persiapan panggung Anda.
                    </p>
                </div>
            </div>

            <div class="w-full sm:w-auto">
                <a href="{{ $order->customer?->phone ? \App\Support\WhatsAppHelper::buildUrl($order->customer->phone, 'Halo Tim Omah Visual, saya ingin koordinasi mengenai pesanan ' . $order->order_number . ' (' . $order->event_name . ')') : '#' }}" 
                   target="_blank" 
                   class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30 transition-all">
                    <span>Hubungi PIC via WhatsApp</span>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824z"/>
                    </svg>
                </a>
            </div>
        </div>

    </main>

    <!-- Lightbox Zoom Modal -->
    <div id="imageLightboxModal" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md hidden flex-col items-center justify-center p-4 transition-opacity duration-200" onclick="closeLightbox(event)">
        <div class="w-full max-w-5xl flex items-center justify-between text-white pb-3 px-2" onclick="event.stopPropagation()">
            <div class="text-sm sm:text-base font-bold text-slate-200 truncate pr-4" id="lightboxTitle">Pratinjau Gambar HD</div>
            <div class="flex items-center gap-2 shrink-0">
                <a id="lightboxDownloadBtn" href="#" download="blueprint-omahvisual.png" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 transition-colors flex items-center gap-1.5 border border-slate-700">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh HD</span>
                </a>
                <button type="button" onclick="closeLightbox()" class="p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors border border-slate-700" title="Tutup (ESC)">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div class="relative max-w-5xl max-h-[85vh] w-full flex items-center justify-center p-1" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="" alt="HD Preview" class="max-w-full max-h-[82vh] object-contain rounded-2xl shadow-2xl border border-slate-800 bg-slate-950">
        </div>
        <div class="text-[11px] text-slate-400 pt-2 text-center">
            Tekan <kbd class="px-1.5 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-300 font-mono">ESC</kbd> atau klik di luar area gambar untuk menutup
        </div>
    </div>

    <script>
    function openLightbox(src, title) {
        const modal = document.getElementById('imageLightboxModal');
        const img = document.getElementById('lightboxImage');
        const titleEl = document.getElementById('lightboxTitle');
        const dlBtn = document.getElementById('lightboxDownloadBtn');
        if (!modal || !img) return;

        img.src = src;
        if (titleEl) titleEl.textContent = title || 'Pratinjau Gambar HD';
        if (dlBtn) dlBtn.href = src;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox(e) {
        if (e && e.target && e.target.closest('#lightboxImage')) return;
        const modal = document.getElementById('imageLightboxModal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });
    </script>
</body>
</html>

