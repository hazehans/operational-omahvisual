@extends('layouts.app')

@section('title', 'Edit Pesanan ' . $order->order_number . ' - Omah Visual')

@section('content')
@php
    $primaryScreen = $order->screens->first();
    $spk = $order->spkAssignment;
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    <!-- Breadcrumb & Header Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('orders.index') }}" class="hover:text-indigo-600 transition-colors">Pesanan Event</a>
                <span>/</span>
                <a href="{{ route('orders.show', $order) }}" class="hover:text-indigo-600 transition-colors">{{ $order->order_number }}</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Edit Pesanan</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                <span>✏️ Edit Pesanan {{ $order->order_number }}</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Perbarui detail acara, spesifikasi layar LED, alokasi kru, atau finansial pesanan ini.
            </p>
        </div>

        <div>
            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Detail Pesanan</span>
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-700 dark:text-rose-400">
            <div class="flex items-center gap-2 font-semibold text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Mohon periksa kembali input Anda:</span>
            </div>
            <ul class="list-disc list-inside text-xs mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.update', $order) }}" method="POST" id="orderEditForm" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- SECTION 1: CUSTOMER & EVENT INFO -->
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">1</span>
                    <span>Informasi Klien & Acara</span>
                </h2>
                <a href="{{ route('customers.index') }}" target="_blank" class="text-xs text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 inline-flex items-center gap-1">
                    <span>Database Client</span>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>

            <!-- Existing Customer Selector -->
            <div class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Pilih Client Terdaftar *</label>
                <select name="customer_id" id="customerSelect" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id', $order->customer_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->organization ?? 'Personal' }}) - {{ $c->phone }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Event *</label>
                    <input type="text" name="event_name" value="{{ old('event_name', $order->event_name) }}" required class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Event *</label>
                    <select name="event_type" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="indoor" {{ old('event_type', $order->event_type) === 'indoor' ? 'selected' : '' }}>Indoor (Gedung / Ballroom)</option>
                        <option value="outdoor" {{ old('event_type', $order->event_type) === 'outdoor' ? 'selected' : '' }}>Outdoor (Lapangan / Tenda Terbuka)</option>
                        <option value="semi-outdoor" {{ old('event_type', $order->event_type) === 'semi-outdoor' ? 'selected' : '' }}>Semi Outdoor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Lokasi Venue *</label>
                    <input type="text" name="venue_name" value="{{ old('venue_name', $order->venue_name) }}" required class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Regional & Location Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Status Wilayah *</label>
                    <select name="status_wilayah" id="statusWilayahSelect" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="dalam_kota" {{ old('status_wilayah', $order->status_wilayah) === 'dalam_kota' ? 'selected' : '' }}>Dalam Kota (Area Utama)</option>
                        <option value="luar_kota" {{ old('status_wilayah', $order->status_wilayah) === 'luar_kota' ? 'selected' : '' }}>Luar Kota (Perlu Biaya Tambahan)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kabupaten / Kota</label>
                    <input type="text" name="kabupaten" value="{{ old('kabupaten', $order->kabupaten ?? 'Kota Malang') }}" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Provinsi Acara</label>
                    <input type="text" name="provinsi" value="{{ old('provinsi', $order->provinsi ?? 'Jawa Timur') }}" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Link Sharelok Google Maps</label>
                    <input type="text" name="google_maps_url" value="{{ old('google_maps_url', $order->google_maps_url) }}" placeholder="https://maps.google.com/..." class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Out of Town Warning Alert -->
            <div id="luarKotaWarning" class="{{ old('status_wilayah', $order->status_wilayah) === 'luar_kota' ? '' : 'hidden' }} p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-400 text-xs flex items-start gap-2.5">
                <span class="text-base leading-none">⚠️</span>
                <div>
                    <span class="font-bold">Peringatan Matriks Biaya Wilayah Luar Kota:</span>
                    <span>Pastikan biaya bahan bakar armada, tol, uang saku/makan kru harian, dan penginapan telah dihitung ke Total Harga Sewa di Bagian 5.</span>
                </div>
            </div>

            <!-- Schedule & Timing -->
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-3 p-4 rounded-xl bg-slate-50/70 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800">
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Tgl Loading *</label>
                    <input type="date" name="loading_date" value="{{ old('loading_date', $order->loading_date?->format('Y-m-d')) }}" required class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Jam Loading *</label>
                    <input type="time" name="loading_time" value="{{ old('loading_time', $order->loading_time) }}" required class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Tgl Acara *</label>
                    <input type="date" name="event_start_date" value="{{ old('event_start_date', $order->event_start_date?->format('Y-m-d')) }}" required class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Jam Acara</label>
                    <input type="time" name="jam_acara" value="{{ old('jam_acara', $order->jam_acara ?? $order->ready_time) }}" class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Jam Menyala</label>
                    <input type="time" name="jam_menyala" value="{{ old('jam_menyala', $order->jam_menyala) }}" class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Waktu Gladi</label>
                    <input type="time" name="waktu_gladi" value="{{ old('waktu_gladi', $order->waktu_gladi) }}" class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
            </div>

            <!-- Kelistrikan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kelistrikan *</label>
                    <select name="kelistrikan" id="kelistrikanSelect" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="Genset" {{ old('kelistrikan', $order->kelistrikan ?? $order->electricity_source) === 'Genset' ? 'selected' : '' }}>Genset</option>
                        <option value="Disediakan Client" {{ old('kelistrikan', $order->kelistrikan ?? $order->electricity_source) === 'Disediakan Client' ? 'selected' : '' }}>Disediakan Client</option>
                        <option value="PLN Lokasi" {{ old('kelistrikan', $order->kelistrikan ?? $order->electricity_source) === 'PLN Lokasi' ? 'selected' : '' }}>PLN Gedung / Lokasi</option>
                        <option value="Lainnya" {{ old('kelistrikan', $order->kelistrikan ?? $order->electricity_source) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Keterangan Kelistrikan</label>
                    <input type="text" name="kelistrikan_keterangan" value="{{ old('kelistrikan_keterangan', $order->kelistrikan_keterangan ?? $order->electricity_source) }}" placeholder="Misal: Genset 40KVA Silent / Jarak colokan 50 meter" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- SECTION 2: SMART LED SCREEN CALCULATOR & EDITABLE EQUIPMENT -->
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-600/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">2</span>
                    <span>Spesifikasi Layar LED & Kalkulator Otomatis Peralatan</span>
                </h2>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnRecalculate" class="text-xs px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1 transition-colors" title="Hitung ulang rekomendasi sistem">
                        <span>⚡ Hitung Ulang Otomatis</span>
                    </button>
                </div>
            </div>

            <!-- Screen 1 Form -->
            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20 space-y-4" id="screenWrapper">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Layar</label>
                        <input type="text" name="screens[0][screen_name]" value="{{ old('screens.0.screen_name', $primaryScreen?->screen_name ?? 'Main Screen') }}" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Seri / Merk LED</label>
                        <select name="screens[0][brand_series]" id="calcBrand" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                            <option value="SAGA" {{ old('screens.0.brand_series', $primaryScreen?->brand_series) === 'SAGA' ? 'selected' : '' }}>LED SAGA</option>
                            <option value="LITE" {{ old('screens.0.brand_series', $primaryScreen?->brand_series) === 'LITE' ? 'selected' : '' }}>LED LITE</option>
                            <option value="LAMPRO" {{ old('screens.0.brand_series', $primaryScreen?->brand_series) === 'LAMPRO' ? 'selected' : '' }}>LED LAMPRO</option>
                            <option value="MAVIC" {{ old('screens.0.brand_series', $primaryScreen?->brand_series) === 'MAVIC' ? 'selected' : '' }}>LED MAVIC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pixel Pitch</label>
                        <select name="screens[0][pixel_pitch]" id="calcPitch" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                            <option value="P3.9" {{ old('screens.0.pixel_pitch', $primaryScreen?->pixel_pitch) === 'P3.9' ? 'selected' : '' }}>P3.9 (Indoor/Outdoor)</option>
                            <option value="P2.6" {{ old('screens.0.pixel_pitch', $primaryScreen?->pixel_pitch) === 'P2.6' ? 'selected' : '' }}>P2.6 (High Resolution)</option>
                            <option value="P2.9" {{ old('screens.0.pixel_pitch', $primaryScreen?->pixel_pitch) === 'P2.9' ? 'selected' : '' }}>P2.9</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Metode Pemasangan</label>
                        <select name="screens[0][rigging_method]" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                            <option value="ground_stake" {{ old('screens.0.rigging_method', $primaryScreen?->rigging_method) === 'ground_stake' ? 'selected' : '' }}>Ground Stake (Pemberat Barbel/Jerigen)</option>
                            <option value="hanging" {{ old('screens.0.rigging_method', $primaryScreen?->rigging_method) === 'hanging' ? 'selected' : '' }}>Hanging Rigging (Takel & Truss)</option>
                            <option value="stage_frame" {{ old('screens.0.rigging_method', $primaryScreen?->rigging_method) === 'stage_frame' ? 'selected' : '' }}>Stage Frame Panggung</option>
                        </select>
                    </div>
                </div>

                <!-- Width and Height Inputs with Live Calculation -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-1">Panjang Layar / Horizontal (Meter) *</label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0.5" name="screens[0][width_m]" id="calcWidth" value="{{ old('screens.0.width_m', $primaryScreen?->width_m ?? 6.0) }}" required
                                class="w-full pl-3 pr-8 py-2 rounded-xl bg-white dark:bg-slate-900 border-2 border-indigo-500/40 focus:border-indigo-600 text-sm font-bold text-slate-900 dark:text-white">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-semibold">m</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-1">Lebar / Tinggi Layar / Vertikal (Meter) *</label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0.5" name="screens[0][height_m]" id="calcHeight" value="{{ old('screens.0.height_m', $primaryScreen?->height_m ?? 3.0) }}" required
                                class="w-full pl-3 pr-8 py-2 rounded-xl bg-white dark:bg-slate-900 border-2 border-indigo-500/40 focus:border-indigo-600 text-sm font-bold text-slate-900 dark:text-white">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-semibold">m</span>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Panggung / Spek Khusus</label>
                        <input type="text" name="screens[0][stage_specification]" value="{{ old('screens.0.stage_specification', $primaryScreen?->stage_specification) }}" placeholder="Misal: Ground stage level 40cm, kain hitam keliling" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                    </div>
                </div>

                <!-- DYNAMIC EDITABLE EQUIPMENT INPUTS -->
                <div class="p-4 rounded-xl bg-linear-to-r from-indigo-900/10 via-purple-900/10 to-indigo-900/10 dark:from-indigo-950/40 dark:to-purple-950/40 border border-indigo-200 dark:border-indigo-800/60 space-y-4">
                    <div class="flex items-center justify-between border-b border-indigo-100 dark:border-indigo-900/40 pb-2">
                        <div class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                            <span>📦 Kebutuhan Alat & Perlengkapan (Dinamis / Dapat Diedit)</span>
                            <span class="px-2 py-0.5 rounded text-[10px] bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 font-semibold" id="resSqm">
                                {{ $primaryScreen?->total_sqm ?? '18.00' }} m²
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-500">Nilai terhitung otomatis per dimensi, silakan ubah jika ada kebutuhan khusus</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-white/90 dark:bg-slate-900/90 p-2.5 rounded-xl border border-indigo-100 dark:border-slate-800">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Total Kabinet (Unit)</label>
                            <input type="number" min="1" name="screens[0][total_cabinets]" id="inputTotalCabinets" value="{{ old('screens.0.total_cabinets', $primaryScreen?->total_cabinets ?? 36) }}" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-900 dark:text-white">
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-500">
                                <span>1m: <input type="number" min="0" name="screens[0][cabinets_1m]" id="inputCabinets1m" value="{{ old('screens.0.cabinets_1m', $primaryScreen?->cabinets_1m ?? 36) }}" class="w-12 px-1 py-0.5 rounded border text-center"></span>
                                <span>50cm: <input type="number" min="0" name="screens[0][cabinets_50cm]" id="inputCabinets50cm" value="{{ old('screens.0.cabinets_50cm', $primaryScreen?->cabinets_50cm ?? 0) }}" class="w-12 px-1 py-0.5 rounded border text-center"></span>
                            </div>
                        </div>

                        <div class="bg-white/90 dark:bg-slate-900/90 p-2.5 rounded-xl border border-indigo-100 dark:border-slate-800">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Box Hardcase (Flightcase)</label>
                            <input type="number" min="1" name="screens[0][total_hardcases]" id="inputTotalHardcases" value="{{ old('screens.0.total_hardcases', $primaryScreen?->total_hardcases ?? 6) }}" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-bold text-amber-600 dark:text-amber-400">
                            <div class="text-[10px] text-slate-500 mt-1">Estimasi 6 kabinet / box</div>
                        </div>

                        <div class="bg-white/90 dark:bg-slate-900/90 p-2.5 rounded-xl border border-indigo-100 dark:border-slate-800">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Prosesor Video LED</label>
                            <input type="text" name="screens[0][processor_model]" id="inputProcessorModel" value="{{ old('screens.0.processor_model', $primaryScreen?->processor_model ?? 'Novastar VX400 / VX4S') }}" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                            <div class="text-[10px] text-slate-500 mt-1" id="resPixels">± 1.18M Piksel</div>
                        </div>

                        <div class="bg-white/90 dark:bg-slate-900/90 p-2.5 rounded-xl border border-indigo-100 dark:border-slate-800">
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Kabel Power & LAN Jumper</label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <div>
                                    <span class="text-[9px] text-slate-400">Power (pcs)</span>
                                    <input type="number" min="0" name="screens[0][power_jumpers_qty]" id="inputPowerJumpers" value="{{ old('screens.0.power_jumpers_qty', $primaryScreen?->power_jumpers_qty ?? 36) }}" class="w-full px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold">
                                </div>
                                <div>
                                    <span class="text-[9px] text-slate-400">LAN (pcs)</span>
                                    <input type="number" min="0" name="screens[0][lan_jumpers_qty]" id="inputLanJumpers" value="{{ old('screens.0.lan_jumpers_qty', $primaryScreen?->lan_jumpers_qty ?? 36) }}" class="w-full px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">🛡️ Modul Spare (pcs)</label>
                            <input type="number" min="0" name="screens[0][spare_modules_qty]" id="inputSpareModules" value="{{ old('screens.0.spare_modules_qty', $primaryScreen?->spare_modules_qty ?? 3) }}" class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">⚡ Spare Power Supply (PSU)</label>
                            <input type="number" min="0" name="screens[0][spare_psu_qty]" id="inputSparePsu" value="{{ old('screens.0.spare_psu_qty', $primaryScreen?->spare_psu_qty ?? 1) }}" class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">🎴 Spare Receiving Card (RC)</label>
                            <input type="number" min="0" name="screens[0][spare_receiving_cards_qty]" id="inputSpareRc" value="{{ old('screens.0.spare_receiving_cards_qty', $primaryScreen?->spare_receiving_cards_qty ?? 1) }}" class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 3: VIRTUAL 3D STAGE & LAYOUT LED DESIGNER -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">3</span>
                    <span>Visualisasi Panggung 3D & Formasi Layar (Drag & Drop)</span>
                </h2>
                <span class="text-xs text-slate-500">
                    Perubahan formasi akan langsung memperbarui Blueprint 2D SPK
                </span>
            </div>
            <x-stage-designer-3d :order="$order" :screens="$order->screens" :saveUrl="route('orders.update-stage-layout', $order)" canvasHeight="460px" />
        </div>

        <!-- SECTION 4: CREW TEAM & VEHICLE ASSIGNMENT -->
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-5">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-cyan-600/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xs">4</span>
                    <span>Penugasan Crew Lapangan & Armada Mobil</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Crew 1 s/d Crew 7 akan dicetak pada Halaman 1 Lembar SPK.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Armada Kendaraan *</label>
                    <select name="vehicle_name" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm font-medium">
                        @php
                            $selectedVeh = old('vehicle_name', $spk?->vehicle_name ?? $order->vehicle_loading_in ?? 'TRAGA 1');
                        @endphp
                        @if(isset($vehicles) && $vehicles->count() > 0)
                            @foreach($vehicles as $veh)
                                <option value="{{ $veh->name }}" {{ $selectedVeh === $veh->name ? 'selected' : '' }}>
                                    {{ $veh->name }} ({{ $veh->license_plate ?? 'Armada' }}) - {{ $veh->type }}
                                </option>
                            @endforeach
                        @else
                            <option value="TRAGA 1" {{ $selectedVeh === 'TRAGA 1' ? 'selected' : '' }}>TRAGA 1 (N 8142 BB)</option>
                            <option value="TRAGA 2" {{ $selectedVeh === 'TRAGA 2' ? 'selected' : '' }}>TRAGA 2 (N 8299 BC)</option>
                            <option value="TRAGA 3" {{ $selectedVeh === 'TRAGA 3' ? 'selected' : '' }}>TRAGA 3 (N 9102 BF)</option>
                            <option value="GRANMAX BLINDVAN" {{ $selectedVeh === 'GRANMAX BLINDVAN' ? 'selected' : '' }}>GRANMAX BLINDVAN</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">PIC Event Lapangan *</label>
                    <input type="text" name="crew_pic_event_name" list="crewList" value="{{ old('crew_pic_event_name', $spk?->crew_pic_event_name) }}" placeholder="Pilih / ketik nama PIC" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm">
                </div>
            </div>

            <!-- Crew Roster 1 to 7 with Autocomplete Datalist -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                @for($i = 1; $i <= 7; $i++)
                    @php $field = "crew_{$i}_name"; @endphp
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Crew {{ $i }}</label>
                        <input type="text" name="crew_{{ $i }}_name" list="crewList" value="{{ old('crew_' . $i . '_name', $spk?->$field) }}" placeholder="Nama crew {{ $i }}" class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs">
                    </div>
                @endfor
            </div>

            <!-- Datalist of Real Crews -->
            <datalist id="crewList">
                @if(isset($crews))
                    @foreach($crews as $cr)
                        <option value="{{ $cr->nickname }}">{{ $cr->full_name }} ({{ $cr->position }}) - {{ $cr->phone }}</option>
                    @endforeach
                @endif
            </datalist>
        </div>

        <!-- SECTION 5: FINANCIALS & STATUS -->
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-5">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-600/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">5</span>
                    <span>Status & Finansial Pesanan</span>
                </h2>
                <div>
                    <select name="status" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-semibold">
                        <option value="draft" {{ old('status', $order->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="confirmed" {{ old('status', $order->status) === 'confirmed' ? 'selected' : '' }}>Confirmed (Disetujui)</option>
                        <option value="loading" {{ old('status', $order->status) === 'loading' ? 'selected' : '' }}>Loading (Berjalan)</option>
                        <option value="completed" {{ old('status', $order->status) === 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                        <option value="cancelled" {{ old('status', $order->status) === 'cancelled' ? 'selected' : '' }}>Cancelled (Batal)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Harga Sewa (Rp)</label>
                    <input type="number" name="total_price" value="{{ old('total_price', $order->total_price) }}" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Uang Muka / DP (Rp)</label>
                    <input type="number" name="down_payment" value="{{ old('down_payment', $order->down_payment) }}" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Rekening Pembayaran</label>
                    <select name="down_payment_method" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm">
                        <option value="BCA Muhammad Burhanudin" {{ old('down_payment_method', $order->down_payment_method) === 'BCA Muhammad Burhanudin' ? 'selected' : '' }}>BCA - Muhammad Burhanudin (4390865177)</option>
                        <option value="BCA Balqist Hamada" {{ old('down_payment_method', $order->down_payment_method) === 'BCA Balqist Hamada' ? 'selected' : '' }}>BCA - Balqist Hamada (3660399282)</option>
                        <option value="BRI Muhammad Burhanudin" {{ old('down_payment_method', $order->down_payment_method) === 'BRI Muhammad Burhanudin' ? 'selected' : '' }}>BRI - Muhammad Burhanudin</option>
                        <option value="Cash / Tunai" {{ old('down_payment_method', $order->down_payment_method) === 'Cash / Tunai' ? 'selected' : '' }}>Cash / Tunai di Lokasi</option>
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Tambahan Multimedia & TV</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <label class="text-slate-500 w-16">TV 43":</label>
                        <input type="number" min="0" name="tv_43_qty" value="{{ old('tv_43_qty', $order->tv_43_qty ?? 0) }}" class="w-16 px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border text-center">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-slate-500 w-16">TV 50":</label>
                        <input type="number" min="0" name="tv_50_qty" value="{{ old('tv_50_qty', $order->tv_50_qty ?? 0) }}" class="w-16 px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border text-center">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-slate-500 w-16">TV 55":</label>
                        <input type="number" min="0" name="tv_55_qty" value="{{ old('tv_55_qty', $order->tv_55_qty ?? 0) }}" class="w-16 px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border text-center">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-slate-500 w-16">TV 65":</label>
                        <input type="number" min="0" name="tv_65_qty" value="{{ old('tv_65_qty', $order->tv_65_qty ?? 0) }}" class="w-16 px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border text-center">
                    </div>
                </div>
            </div>
        </div>

        <!-- SUBMIT ACTION -->
        <div class="flex items-center justify-between pt-3">
            <a href="{{ route('orders.show', $order) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Detail</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('orders.show', $order) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Perubahan Pesanan</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- CLIENT JAVASCRIPT FOR REALTIME CALCULATION & TOGGLES -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusWilayahSelect = document.getElementById('statusWilayahSelect');
    const luarKotaWarning = document.getElementById('luarKotaWarning');
    if (statusWilayahSelect && luarKotaWarning) {
        statusWilayahSelect.addEventListener('change', function () {
            if (this.value === 'luar_kota') {
                luarKotaWarning.classList.remove('hidden');
            } else {
                luarKotaWarning.classList.add('hidden');
            }
        });
    }

    const widthInput = document.getElementById('calcWidth');
    const heightInput = document.getElementById('calcHeight');
    const brandInput = document.getElementById('calcBrand');
    const pitchInput = document.getElementById('calcPitch');

    const inputTotalCabinets = document.getElementById('inputTotalCabinets');
    const inputCabinets1m = document.getElementById('inputCabinets1m');
    const inputCabinets50cm = document.getElementById('inputCabinets50cm');
    const inputTotalHardcases = document.getElementById('inputTotalHardcases');
    const inputProcessorModel = document.getElementById('inputProcessorModel');
    const inputPowerJumpers = document.getElementById('inputPowerJumpers');
    const inputLanJumpers = document.getElementById('inputLanJumpers');
    const inputSpareModules = document.getElementById('inputSpareModules');
    const inputSparePsu = document.getElementById('inputSparePsu');
    const inputSpareRc = document.getElementById('inputSpareRc');

    function updateCalculations(forceRecalc = false) {
        const width = parseFloat(widthInput.value) || 0;
        const height = parseFloat(heightInput.value) || 0;
        const brand = brandInput.value;
        const pitch = pitchInput.value;

        if (width <= 0 || height <= 0) return;

        const sqm = (width * height).toFixed(2);
        const columns = Math.round(width / 0.5);
        const wholeMeters = Math.floor(height);
        const remainder = +(height - wholeMeters).toFixed(2);

        const cabinets1m = columns * wholeMeters;
        const cabinets50cm = (remainder >= 0.4) ? (columns * 1) : 0;
        const totalCabinets = cabinets1m + cabinets50cm;
        const hardcases = Math.ceil(cabinets1m / 6) + Math.ceil(cabinets50cm / 8);

        const pixelsPerCabinet = (pitch === 'P2.6') ? 73728 : 32768;
        const totalPixels = totalCabinets * pixelsPerCabinet;

        let processor = 'Novastar VX400 / VX4S';
        if (totalPixels > 2600000 && totalPixels <= 3900000) {
            processor = 'Novastar VX1000';
        } else if (totalPixels > 1300000 && totalPixels <= 2600000) {
            processor = 'Novastar VX600';
        } else if (totalPixels > 3900000) {
            processor = 'Novastar VX1000 Pro / VX20 + Fiber Optic';
        }

        const spareModules = Math.max(2, Math.ceil(sqm * 0.15));
        const spareParts = Math.max(1, Math.ceil(sqm * 0.05));

        document.getElementById('resSqm').textContent = sqm + ' m²';
        document.getElementById('resPixels').textContent = '± ' + (totalPixels / 1000000).toFixed(2) + 'M Piksel';

        if (forceRecalc) {
            inputTotalCabinets.value = totalCabinets;
            inputCabinets1m.value = cabinets1m;
            inputCabinets50cm.value = cabinets50cm;
            inputTotalHardcases.value = hardcases;
            inputProcessorModel.value = processor;
            inputPowerJumpers.value = totalCabinets;
            inputLanJumpers.value = totalCabinets;
            inputSpareModules.value = spareModules;
            inputSparePsu.value = spareParts;
            inputSpareRc.value = spareParts;
        }
    }

    document.getElementById('btnRecalculate')?.addEventListener('click', () => {
        updateCalculations(true);
    });

    widthInput.addEventListener('input', () => updateCalculations(false));
    heightInput.addEventListener('input', () => updateCalculations(false));
    brandInput.addEventListener('change', () => updateCalculations(false));
    pitchInput.addEventListener('change', () => updateCalculations(false));

    // Prevent Enter key on input fields from submitting the form unintentionally
    const orderEditForm = document.getElementById('orderEditForm');
    if (orderEditForm) {
        orderEditForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.nodeName !== 'TEXTAREA' && e.target.type !== 'submit') {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endsection

