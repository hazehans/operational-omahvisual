@extends('layouts.app')

@section('title', 'Input Order Baru LED - Omah Visual')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Breadcrumb & Title -->
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('orders.index') }}" class="hover:text-indigo-600 transition-colors">Pesanan Event</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-slate-200">Input Order Baru</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
            <span>✨ Input Pesanan & Kalkulator Otomatis SPK</span>
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Input cepat pesanan dari WhatsApp, kalkulasi kabinet, kabel jumper, prosesor, serta terbitkan SPK 3-halaman otomatis.
        </p>
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

    <form action="{{ route('orders.store') }}" method="POST" id="orderForm" class="space-y-6">
        @csrf

        <!-- SECTION 1: CUSTOMER & EVENT INFO -->
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">1</span>
                    <span>Informasi Klien & Acara</span>
                </h2>
                <div class="flex items-center gap-2">
                    <label class="text-xs text-slate-500 flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" id="isNewCustomerToggle" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span>Customer Baru</span>
                    </label>
                </div>
            </div>

            <!-- Existing vs New Customer Selector -->
            <div id="existingCustomerWrapper" class="space-y-1">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">Pilih Client Terdaftar</label>
                <select name="customer_id" id="customerSelect" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Customer dari Database (Ketik Nama / Instansi) --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-phone="{{ $c->phone }}" data-org="{{ $c->organization }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->organization ?? 'Personal' }}) - {{ $c->phone }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="newCustomerWrapper" class="grid-cols-1 sm:grid-cols-3 gap-4 hidden">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Client *</label>
                    <input type="text" name="customer_name" id="newCustomerName" value="{{ old('customer_name') }}" placeholder="Misal: Mas Dani Bless" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp *</label>
                    <input type="text" name="customer_phone" id="newCustomerPhone" value="{{ old('customer_phone') }}" placeholder="Misal: 081234567890" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Vendor / EO / Instansi</label>
                    <input type="text" name="customer_organization" value="{{ old('customer_organization') }}" placeholder="Misal: Bless Production" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Event *</label>
                    <input type="text" name="event_name" value="{{ old('event_name') }}" required placeholder="Misal: Wedding Puri & Dimas" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipe Event *</label>
                    <select name="event_type" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="indoor" {{ old('event_type') === 'indoor' ? 'selected' : '' }}>Indoor (Gedung / Ballroom)</option>
                        <option value="outdoor" {{ old('event_type') === 'outdoor' ? 'selected' : '' }}>Outdoor (Lapangan / Tenda Terbuka)</option>
                        <option value="semi-outdoor" {{ old('event_type') === 'semi-outdoor' ? 'selected' : '' }}>Semi Outdoor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Lokasi Venue *</label>
                    <input type="text" name="venue_name" value="{{ old('venue_name') }}" required placeholder="Misal: Graha Cakrawala Malang" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Link Sharelok Google Maps</label>
                    <input type="text" name="google_maps_url" value="{{ old('google_maps_url') }}" placeholder="https://maps.google.com/..." class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Sumber Kelistrikan</label>
                    <input type="text" name="electricity_source" value="{{ old('electricity_source', 'Genset 40KVA') }}" placeholder="Misal: Genset 40KVA / PLN 16A" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Schedule -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50/70 dark:bg-slate-800/30 border border-slate-100 dark:border-slate-800">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tgl Loading *</label>
                    <input type="date" name="loading_date" value="{{ old('loading_date', date('Y-m-d')) }}" required class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jam Loading *</label>
                    <input type="text" name="loading_time" value="{{ old('loading_time', '22:00') }}" required placeholder="22:00" class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tgl Acara *</label>
                    <input type="date" name="event_start_date" value="{{ old('event_start_date', date('Y-m-d', strtotime('+1 day'))) }}" required class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jam Acara / Ready</label>
                    <input type="text" name="ready_time" value="{{ old('ready_time', 'Ready Jam 07:00') }}" placeholder="Ready 07:00" class="w-full px-3 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-medium">
                </div>
            </div>
        </div>

        <!-- SECTION 2: SMART LED SCREEN CALCULATOR -->
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-600/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">2</span>
                    <span>Spesifikasi Layar LED & Kalkulator Otomatis</span>
                </h2>
                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-medium">
                    ⚡ Auto-Calc Live
                </span>
            </div>

            <!-- Screen 1 Form -->
            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20 space-y-4" id="screenWrapper">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Layar</label>
                        <input type="text" name="screens[0][screen_name]" value="Main Screen" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Seri / Merk LED</label>
                        <select name="screens[0][brand_series]" id="calcBrand" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                            <option value="SAGA">LED SAGA</option>
                            <option value="LITE">LED LITE</option>
                            <option value="LAMPRO">LED LAMPRO</option>
                            <option value="MAVIC">LED MAVIC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pixel Pitch</label>
                        <select name="screens[0][pixel_pitch]" id="calcPitch" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                            <option value="P3.9">P3.9 (Indoor/Outdoor)</option>
                            <option value="P2.6">P2.6 (High Resolution)</option>
                            <option value="P2.9">P2.9</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Metode Pemasangan</label>
                        <select name="screens[0][rigging_method]" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                            <option value="ground_stake">Ground Stake (Pemberat Barbel/Jerigen)</option>
                            <option value="hanging">Hanging Rigging (Takel & Truss)</option>
                            <option value="stage_frame">Stage Frame Panggung</option>
                        </select>
                    </div>
                </div>

                <!-- Width and Height Inputs with Live Calculation -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-1">Lebar Layar (Meter) *</label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0.5" name="screens[0][width_m]" id="calcWidth" value="6.0" required
                                class="w-full pl-3 pr-8 py-2 rounded-xl bg-white dark:bg-slate-900 border-2 border-indigo-500/40 focus:border-indigo-600 text-sm font-bold text-slate-900 dark:text-white">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-semibold">m</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-1">Tinggi Layar (Meter) *</label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0.5" name="screens[0][height_m]" id="calcHeight" value="3.0" required
                                class="w-full pl-3 pr-8 py-2 rounded-xl bg-white dark:bg-slate-900 border-2 border-indigo-500/40 focus:border-indigo-600 text-sm font-bold text-slate-900 dark:text-white">
                            <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-semibold">m</span>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Panggung / Spek Khusus</label>
                        <input type="text" name="screens[0][stage_specification]" placeholder="Misal: Ground stage level 40cm, kain hitam keliling" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm">
                    </div>
                </div>

                <!-- REALTIME CALCULATION RESULT PANEL -->
                <div class="p-4 rounded-xl bg-linear-to-r from-indigo-900/10 via-purple-900/10 to-indigo-900/10 dark:from-indigo-950/40 dark:to-purple-950/40 border border-indigo-200 dark:border-indigo-800/60">
                    <div class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3 flex items-center justify-between">
                        <span>Hasil Kalkulasi Otomatis Peralatan</span>
                        <span class="text-[11px] font-normal lowercase text-slate-500">dihitung otomatis per ukuran layar</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="bg-white/80 dark:bg-slate-900/80 p-2.5 rounded-xl border border-indigo-100 dark:border-slate-800">
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Total Luas Layar</div>
                            <div class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400" id="resSqm">18.00 m²</div>
                        </div>

                        <div class="bg-white/80 dark:bg-slate-900/80 p-2.5 rounded-xl border border-indigo-100 dark:border-slate-800">
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Kebutuhan Kabinet</div>
                            <div class="text-xl font-extrabold text-slate-900 dark:text-white" id="resCabinets">36 Unit</div>
                            <div class="text-[10px] text-slate-500" id="resKabinetDetail">36x 1m | 0x 50cm</div>
                        </div>

                        <div class="bg-white/80 dark:bg-slate-900/80 p-2.5 rounded-xl border border-indigo-100 dark:border-slate-800">
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Estimasi Box Flight Case</div>
                            <div class="text-xl font-extrabold text-amber-600 dark:text-amber-400" id="resHardcases">6 Hardcase</div>
                            <div class="text-[10px] text-slate-500">Kapasitas 6 kabinet/box</div>
                        </div>

                        <div class="bg-white/80 dark:bg-slate-900/80 p-2.5 rounded-xl border border-indigo-100 dark:border-slate-800">
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Rekomendasi Prosesor</div>
                            <div class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400 mt-1" id="resProcessor">Novastar VX400 / VX4S</div>
                            <div class="text-[10px] text-slate-500" id="resPixels">± 1.18M Piksel</div>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-indigo-100 dark:border-indigo-900/40 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-slate-600 dark:text-slate-300">
                        <div>🔌 Power Jumper: <span class="font-bold text-slate-900 dark:text-white" id="resPowerJumpers">36 pcs</span></div>
                        <div>🌐 LAN Jumper: <span class="font-bold text-slate-900 dark:text-white" id="resLanJumpers">36 pcs</span></div>
                        <div>🛡️ Modul Spare: <span class="font-bold text-slate-900 dark:text-white" id="resSpareModules">3 pcs</span></div>
                        <div>⚡ Spare PSU & RC: <span class="font-bold text-slate-900 dark:text-white" id="resSpareParts">1 PSU / 1 RC</span></div>
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
                    Otomatis dikonversi menjadi Blueprint 2D saat cetak SPK fisik
                </span>
            </div>
            <x-stage-designer-3d canvasHeight="460px" />
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
                        <option value="TRAGA 1">TRAGA 1 (N 8142 BB)</option>
                        <option value="TRAGA 2">TRAGA 2 (N 8299 BC)</option>
                        <option value="TRAGA 3">TRAGA 3 (N 9102 BF)</option>
                        <option value="GRANDMAX">Daihatsu Grandmax Pickup</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">PIC Event Lapangan *</label>
                    <input type="text" name="crew_pic_event_name" list="crewList" value="{{ old('crew_pic_event_name') }}" placeholder="Pilih / ketik nama PIC (misal: Boby / Yoga / Rio)" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm">
                </div>
            </div>

            <!-- Crew Roster 1 to 7 with Autocomplete Datalist -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Crew 1</label>
                    <input type="text" name="crew_1_name" list="crewList" placeholder="Nama crew 1" class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Crew 2</label>
                    <input type="text" name="crew_2_name" list="crewList" placeholder="Nama crew 2" class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Crew 3</label>
                    <input type="text" name="crew_3_name" list="crewList" placeholder="Nama crew 3" class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Crew 4</label>
                    <input type="text" name="crew_4_name" list="crewList" placeholder="Nama crew 4" class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Crew 5</label>
                    <input type="text" name="crew_5_name" list="crewList" placeholder="Nama crew 5" class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Crew 6</label>
                    <input type="text" name="crew_6_name" list="crewList" placeholder="Nama crew 6" class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Crew 7</label>
                    <input type="text" name="crew_7_name" list="crewList" placeholder="Nama crew 7" class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-xs">
                </div>
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

        <!-- SECTION 5: FINANCIALS & MULTIMEDIA ADD-ONS -->
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-5">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-600/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs">5</span>
                    <span>Finansial & Tambahan Multimedia (Opsional)</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Harga Sewa (Rp)</label>
                    <input type="number" name="total_price" value="{{ old('total_price', 3500000) }}" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Uang Muka / DP (Rp)</label>
                    <input type="number" name="down_payment" value="{{ old('down_payment', 1000000) }}" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Rekening Pembayaran</label>
                    <select name="down_payment_method" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-sm">
                        <option value="BCA Muhammad Burhanudin">BCA - Muhammad Burhanudin (4390865177)</option>
                        <option value="BCA Balqist Hamada">BCA - Balqist Hamada (3660399282)</option>
                        <option value="BRI Muhammad Burhanudin">BRI - Muhammad Burhanudin</option>
                        <option value="Cash / Tunai">Cash / Tunai di Lokasi</option>
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Tambahan Multimedia & TV</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="flex items-center gap-2">
                        <label class="text-slate-500 w-16">TV 43":</label>
                        <input type="number" min="0" name="tv_43_qty" value="0" class="w-16 px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border text-center">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-slate-500 w-16">TV 50":</label>
                        <input type="number" min="0" name="tv_50_qty" value="0" class="w-16 px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border text-center">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-slate-500 w-16">TV 55":</label>
                        <input type="number" min="0" name="tv_55_qty" value="0" class="w-16 px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border text-center">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-slate-500 w-16">TV 65":</label>
                        <input type="number" min="0" name="tv_65_qty" value="0" class="w-16 px-2 py-1 rounded bg-slate-50 dark:bg-slate-800 border text-center">
                    </div>
                </div>
            </div>
        </div>

        <!-- SUBMIT ACTION -->
        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="{{ route('orders.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Pesanan & Terbitkan SPK 3 Lembar</span>
            </button>
        </div>
    </form>
</div>

<!-- CLIENT JAVASCRIPT FOR REALTIME CALCULATION & CUSTOMER TOGGLE -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isNewToggle = document.getElementById('isNewCustomerToggle');
    const existingWrapper = document.getElementById('existingCustomerWrapper');
    const newWrapper = document.getElementById('newCustomerWrapper');
    const customerSelect = document.getElementById('customerSelect');

    isNewToggle.addEventListener('change', function () {
        if (this.checked) {
            existingWrapper.classList.add('hidden');
            newWrapper.classList.remove('hidden');
            customerSelect.value = '';
        } else {
            existingWrapper.classList.remove('hidden');
            newWrapper.classList.add('hidden');
        }
    });

    // Realtime Screen Calculation
    const widthInput = document.getElementById('calcWidth');
    const heightInput = document.getElementById('calcHeight');
    const brandInput = document.getElementById('calcBrand');
    const pitchInput = document.getElementById('calcPitch');

    function updateCalculations() {
        const width = parseFloat(widthInput.value) || 0;
        const height = parseFloat(heightInput.value) || 0;
        const brand = brandInput.value;
        const pitch = pitchInput.value;

        if (width <= 0 || height <= 0) return;

        // Perform calculation on client immediately for responsiveness
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
        document.getElementById('resCabinets').textContent = totalCabinets + ' Unit';
        document.getElementById('resKabinetDetail').textContent = `${cabinets1m}x 1m | ${cabinets50cm}x 50cm`;
        document.getElementById('resHardcases').textContent = hardcases + ' Hardcase';
        document.getElementById('resProcessor').textContent = processor;
        document.getElementById('resPixels').textContent = '± ' + (totalPixels / 1000000).toFixed(2) + 'M Piksel';

        document.getElementById('resPowerJumpers').textContent = totalCabinets + ' pcs';
        document.getElementById('resLanJumpers').textContent = totalCabinets + ' pcs';
        document.getElementById('resSpareModules').textContent = spareModules + ' pcs';
        document.getElementById('resSpareParts').textContent = `${spareParts} PSU / ${spareParts} RC`;
    }

    widthInput.addEventListener('input', updateCalculations);
    heightInput.addEventListener('input', updateCalculations);
    brandInput.addEventListener('change', updateCalculations);
    pitchInput.addEventListener('change', updateCalculations);

    // Initial run
    updateCalculations();
});
</script>
@endsection

