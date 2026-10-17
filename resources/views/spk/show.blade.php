@extends('layouts.app')

@section('title', 'SPK ' . $assignment->spk_number . ' - Omah Visual')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: parseInt(new URLSearchParams(window.location.search).get('tab') || sessionStorage.getItem('spk_active_tab_{{ $assignment->id }}') || 1),
    showAssignModal: false,
    showQcModal: false,
    showAddItemModal: false,
    addItemPage: 2,
    filterOnlyQty: false,
    showEditItemModal: false,
    showTemplateModal: false,
    showAddCrewModal: false,
    showEditCrewModal: false,
    showAddVehicleModal: false,
    showEditVehicleModal: false,
    editItem: { id: null, name: '', detail: '', qty: 1, unit: 'Pcs' },
    editCrew: { id: null, crew_id: null, nickname: '', role_label: '', fee_loading_in: 0, fee_loading_out: 0, fee_meal: 0, fee_standby: 0, fee_transport: 0, fee_bonus: 0, status: 'offered', payment_status: 'unpaid', notes: '' },
    editVehicle: { id: null, vehicle_name: '', plate_number: '', driver_name: '', has_etoll: false, has_kunci_ban: false, has_stnk: false, has_kir: false, has_barcode_bbm: false, notes: '' },
    newCrew: { crew_id: '', role_label: 'Crew Lapangan', fee_loading_in: 100000, fee_loading_out: 100000, fee_meal: 25000, fee_standby: 50000, fee_transport: 0, fee_bonus: 0, status: 'offered', notes: '' },
    crewProfiles: {{ Js::from($crews->keyBy('id')) }},
    onSelectCrew(id) {
        const c = this.crewProfiles[id];
        if (c) {
            this.newCrew.fee_loading_in = c.default_fee_loading_in ?? 100000;
            this.newCrew.fee_loading_out = c.default_fee_loading_out ?? 100000;
            this.newCrew.fee_meal = c.default_fee_meal ?? 25000;
            this.newCrew.fee_standby = c.default_fee_standby ?? 50000;
        }
    },
    openEditModal(item) {
        this.editItem = { ...item };
        this.showEditItemModal = true;
    },
    openEditCrewModal(ca) {
        this.editCrew = {
            id: ca.id,
            crew_id: ca.crew_id,
            nickname: ca.crew ? ca.crew.nickname : 'Kru',
            role_label: ca.role_label,
            fee_loading_in: ca.fee_loading_in,
            fee_loading_out: ca.fee_loading_out,
            fee_meal: ca.fee_meal,
            fee_standby: ca.fee_standby,
            fee_transport: ca.fee_transport,
            fee_bonus: ca.fee_bonus,
            status: ca.status,
            payment_status: ca.payment_status,
            notes: ca.notes || ''
        };
        this.showEditCrewModal = true;
    },
    openEditVehicleModal(v) {
        this.editVehicle = {
            id: v.id,
            vehicle_name: v.vehicle_name,
            plate_number: v.plate_number || '',
            driver_name: v.driver_name || '',
            has_etoll: !!v.has_etoll,
            has_kunci_ban: !!v.has_kunci_ban,
            has_stnk: !!v.has_stnk,
            has_kir: !!v.has_kir,
            has_barcode_bbm: !!v.has_barcode_bbm,
            notes: v.notes || ''
        };
        this.showEditVehicleModal = true;
    }
}" x-init="$watch('activeTab', val => {
    sessionStorage.setItem('spk_active_tab_{{ $assignment->id }}', val);
    const url = new URL(window.location);
    url.searchParams.set('tab', val);
    window.history.replaceState({}, '', url);
})">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('orders.index') }}" class="hover:text-indigo-600 transition-colors">Pesanan Event</a>
                <span>/</span>
                <a href="{{ route('orders.show', $assignment->eventOrder) }}" class="hover:text-indigo-600 transition-colors">{{ $assignment->eventOrder->order_number }}</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">SPK & Checklist</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                <span>📑 {{ $assignment->spk_number }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                    Omahvisual LED
                </span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                {{ $assignment->eventOrder->event_name }} • {{ $assignment->eventOrder->venue_name }} • Client: {{ $assignment->eventOrder->customer->name }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('spk.print', $assignment) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white text-sm font-semibold shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Lembar SPK Fisik (A4)</span>
            </a>
        </div>
    </div>

    <!-- 3-PAGE TABS -->
    <div class="border-b border-slate-200 dark:border-slate-800 flex items-center gap-2 overflow-x-auto">
        <button @click="activeTab = 1" :class="activeTab === 1 ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium'" class="px-4 py-3 border-b-2 text-sm whitespace-nowrap transition-all flex items-center gap-2">
            <span class="w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-[11px] flex items-center justify-center font-bold">1</span>
            <span>Halaman 1: Serah Terima & Penugasan</span>
        </button>

        <button @click="activeTab = 2" :class="activeTab === 2 ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium'" class="px-4 py-3 border-b-2 text-sm whitespace-nowrap transition-all flex items-center gap-2">
            <span class="w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-[11px] flex items-center justify-center font-bold">2</span>
            <span>Halaman 2: Checklist Barang Kerja Team</span>
            <span class="text-xs px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600">{{ $assignment->checklistItems->where('page_number', 2)->count() }} Item</span>
        </button>

        <button @click="activeTab = 3" :class="activeTab === 3 ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium'" class="px-4 py-3 border-b-2 text-sm whitespace-nowrap transition-all flex items-center gap-2">
            <span class="w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-800 text-[11px] flex items-center justify-center font-bold">3</span>
            <span>Halaman 3: Checklist PIC (Kit Operator & Spares)</span>
            <span class="text-xs px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600">{{ $assignment->checklistItems->where('page_number', 3)->count() }} Item</span>
        </button>
    </div>

    <!-- TAB 1: HALAMAN 1 - SERAH TERIMA & PENUGASAN -->
    <div x-show="activeTab === 1" class="space-y-6">
        <!-- Event & Logistics Specs -->
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs space-y-6">
            <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center justify-between">
                <span>Penugasan Team lapangan untuk memenuhi kebutuhan event client:</span>
                <button type="button" @click="showAssignModal = true" class="px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span>Edit Tim & Armada</span>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left: Event Specs -->
                <div class="space-y-2.5 text-sm">
                    <div class="flex"><span class="w-36 text-slate-500">Nama Client:</span><span class="font-bold text-slate-900 dark:text-white">{{ $assignment->eventOrder->customer->name }}</span></div>
                    <div class="flex"><span class="w-36 text-slate-500">Tgl Event:</span><span class="font-semibold">{{ $assignment->eventOrder->event_start_date->format('d/m/Y') }}</span></div>
                    <div class="flex"><span class="w-36 text-slate-500">Jam Event:</span><span>{{ $assignment->eventOrder->event_time ?? '-' }}</span></div>
                    <div class="flex"><span class="w-36 text-slate-500">Tgl Loading:</span><span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $assignment->eventOrder->loading_date->format('d/m/Y') }}</span></div>
                    <div class="flex"><span class="w-36 text-slate-500">Jam Loading:</span><span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $assignment->eventOrder->loading_time }}</span></div>
                    <div class="flex"><span class="w-36 text-slate-500">Lokasi Event:</span><span class="font-semibold">{{ $assignment->eventOrder->venue_name }}</span></div>
                    <div class="flex"><span class="w-36 text-slate-500">Ukuran LED:</span><span class="font-bold text-slate-900 dark:text-white">{{ $assignment->eventOrder->screens->map(fn($s) => $s->width_m.'x'.$s->height_m.'m ('.$s->brand_series.')')->implode(', ') }}</span></div>
                    <div class="flex"><span class="w-36 text-slate-500">Jumlah SQM:</span><span class="font-extrabold text-indigo-600 dark:text-indigo-400">{{ $assignment->eventOrder->total_sqm }} SQM</span></div>
                    <div class="flex"><span class="w-36 text-slate-500">Keterangan Lain:</span><span class="text-slate-600 dark:text-slate-400">{{ $assignment->eventOrder->notes ?? '-' }}</span></div>
                </div>

                <!-- Right: Ringkasan Cepat & Status -->
                <div class="space-y-4">
                    <!-- Quick Fleet & Crew Header Card -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px]">Ringkasan Tim & Armada:</span>
                            <span class="text-[11px] text-slate-500">PIC: <strong class="text-indigo-600 dark:text-indigo-400">{{ $assignment->crew_pic_event_name ?? '-' }}</strong></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
                            <div>Total Kru Ditugaskan: <strong class="text-slate-900 dark:text-white">{{ $assignment->crewAssignments->count() }} Orang</strong></div>
                            <div>Total Unit Armada: <strong class="text-slate-900 dark:text-white">{{ $assignment->assignedVehicles->count() > 0 ? $assignment->assignedVehicles->count() . ' Kendaraan' : ($assignment->vehicle_name ?? '1 Armada') }}</strong></div>
                        </div>
                    </div>

                    <!-- Operational Cost Quick Summary -->
                    <div class="p-4 rounded-xl bg-linear-to-br from-indigo-50/60 to-cyan-50/40 dark:from-slate-800/80 dark:to-slate-800/40 border border-indigo-100 dark:border-slate-700 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px]">Kalkulasi Margin & Laba Event:</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $assignment->eventOrder->net_profit >= 0 ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300' : 'bg-rose-100 dark:bg-rose-900/40 text-rose-700' }}">
                                Margin: {{ $assignment->eventOrder->total_price > 0 ? round(($assignment->eventOrder->net_profit / $assignment->eventOrder->total_price) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div>Omset Event: <strong class="text-slate-900 dark:text-white block">Rp {{ number_format($assignment->eventOrder->total_price, 0, ',', '.') }}</strong></div>
                            <div>Total Honor Kru: <strong class="text-amber-600 dark:text-amber-400 block">Rp {{ number_format($assignment->eventOrder->total_crew_fees, 0, ',', '.') }}</strong></div>
                            <div>BBM/Tol/Hotel/Lain: <strong class="text-slate-700 dark:text-slate-300 block">Rp {{ number_format($assignment->eventOrder->cost_fuel + $assignment->eventOrder->cost_toll + $assignment->eventOrder->cost_lodging + $assignment->eventOrder->cost_other, 0, ',', '.') }}</strong></div>
                            <div>Laba Bersih (Net): <strong class="text-emerald-600 dark:text-emerald-400 font-extrabold text-sm block">Rp {{ number_format($assignment->eventOrder->net_profit, 0, ',', '.') }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION A: LOGISTIK ARMADA KENDARAAN (BISA LEBIH DARI 1 PICKUP / TRUK) -->
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <span>🚛 Armada Kendaraan Logistik (Pickup / Truk)</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                                {{ $assignment->assignedVehicles->count() }} Unit Terpasang
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500">Mendukung lebih dari 1 pickup/truk untuk event berskala besar lengkap dengan checklist dokumen fisik.</p>
                    </div>

                    <button type="button" @click="showAddVehicleModal = true" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition-colors self-start sm:self-auto">
                        <span>+ Tambah Armada Kendaraan</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($assignment->assignedVehicles as $veh)
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-2.5 text-xs">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-1.5">
                                        <span>🚐 {{ $veh->vehicle_name }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        Plat: <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $veh->plate_number ?? '-' }}</strong> • Driver: <strong class="text-slate-800 dark:text-slate-200">{{ $veh->driver_name ?? 'Belum ditentukan' }}</strong>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="openEditVehicleModal({{ Js::from($veh) }})" class="p-1 rounded bg-white dark:bg-slate-700 hover:bg-slate-100 text-slate-600 dark:text-slate-300" title="Edit Armada">
                                        ✏️
                                    </button>
                                    <form action="{{ route('spk.vehicles.destroy', $veh) }}" method="POST" onsubmit="return confirm('Hapus armada {{ $veh->vehicle_name }} dari event ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded bg-white dark:bg-slate-700 hover:bg-rose-50 text-rose-500" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-1.5 pt-2 border-t border-slate-200 dark:border-slate-700/60 text-[11px]">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $veh->has_etoll ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                    <span>E-Toll: <strong>{{ $veh->has_etoll ? 'Siap' : 'Belum' }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $veh->has_kunci_ban ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                    <span>Kunci Ban: <strong>{{ $veh->has_kunci_ban ? 'Siap' : 'Belum' }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $veh->has_stnk ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                    <span>STNK: <strong>{{ $veh->has_stnk ? 'Siap' : 'Belum' }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $veh->has_kir ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                    <span>KIR: <strong>{{ $veh->has_kir ? 'Siap' : 'Belum' }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5 col-span-2">
                                    <span class="w-2 h-2 rounded-full {{ $veh->has_barcode_bbm ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600' }}"></span>
                                    <span>Barcode MyPertamina: <strong>{{ $veh->has_barcode_bbm ? 'Siap / Terpasang' : 'Belum' }}</strong></span>
                                </div>
                            </div>

                            @if($veh->notes)
                                <div class="p-1.5 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[10px] text-slate-500 italic">
                                    Catatan: {{ $veh->notes }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <!-- Fallback Legacy Single Vehicle -->
                        <div class="col-span-full p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-dashed border-slate-300 dark:border-slate-700 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white text-sm">Armada Utama (Legacy): {{ $assignment->vehicle_name ?? 'TRAGA 1' }}</span>
                                <p class="text-slate-500 text-[11px] mt-0.5">
                                    E-Toll: {{ $assignment->has_etoll ? '✓' : '✗' }} • Kunci Ban: {{ $assignment->has_kunci_ban ? '✓' : '✗' }} • STNK: {{ $assignment->has_stnk ? '✓' : '✗' }} • KIR: {{ $assignment->has_kir ? '✓' : '✗' }} • Barcode BBM: {{ $assignment->has_barcode_bbm ? '✓' : '✗' }}
                                </p>
                            </div>
                            <button type="button" @click="showAddVehicleModal = true" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-500 transition-colors">
                                + Migrasikan / Tambah Unit Armada Tambahan
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- SECTION B: PENUGASAN KRU & STRUKTUR HONOR (LOADING IN, OUT, MAKAN, OPERATOR) -->
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <span>👥 Penugasan Kru & Struktur Honor Mandiri</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                                Total: Rp {{ number_format($assignment->eventOrder->total_crew_fees, 0, ',', '.') }}
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500">Tiap kru memiliki standar bayaran (self-rated), penawaran via WhatsApp, status konfirmasi ACC, serta pencairan honor.</p>
                    </div>

                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <a href="{{ route('crew-payroll.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition-colors">
                            💰 Lihat Dashboard Payroll
                        </a>
                        <button type="button" @click="showAddCrewModal = true" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                            <span>+ Tambah Penugasan Kru</span>
                        </button>
                    </div>
                </div>

                <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-2xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600 dark:text-slate-400">
                            <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="px-4 py-2.5">Kru & Rekening</th>
                                    <th class="px-4 py-2.5">Peran</th>
                                    <th class="px-4 py-2.5">Rincian Komponen Honor (In / Out / Makan / Standby)</th>
                                    <th class="px-4 py-2.5 text-right">Total Honor</th>
                                    <th class="px-4 py-2.5 text-center">Status ACC</th>
                                    <th class="px-4 py-2.5 text-center">Status Bayar</th>
                                    <th class="px-4 py-2.5 text-right">Aksi & WA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($assignment->crewAssignments as $ca)
                                    @php
                                        $waText = "Halo {$ca->crew->nickname}, Omah Visual menawarkan penugasan event:\n"
                                            . "📌 Event: {$assignment->eventOrder->event_name}\n"
                                            . "📅 Tanggal: {$assignment->eventOrder->event_start_date->format('d/m/Y')}\n"
                                            . "📍 Lokasi: {$assignment->eventOrder->venue_name}\n"
                                            . "💼 Peran: {$ca->role_label}\n"
                                            . "-----------------------------------\n"
                                            . ($ca->fee_loading_in > 0 ? "• Loading In: Rp " . number_format($ca->fee_loading_in, 0, ',', '.') . "\n" : "")
                                            . ($ca->fee_loading_out > 0 ? "• Loading Out: Rp " . number_format($ca->fee_loading_out, 0, ',', '.') . "\n" : "")
                                            . ($ca->fee_meal > 0 ? "• Uang Makan: Rp " . number_format($ca->fee_meal, 0, ',', '.') . "\n" : "")
                                            . ($ca->fee_standby > 0 ? "• Standby/Operator: Rp " . number_format($ca->fee_standby, 0, ',', '.') . "\n" : "")
                                            . ($ca->fee_transport > 0 ? "• Transport: Rp " . number_format($ca->fee_transport, 0, ',', '.') . "\n" : "")
                                            . ($ca->fee_bonus > 0 ? "• Bonus/Lembur: Rp " . number_format($ca->fee_bonus, 0, ',', '.') . "\n" : "")
                                            . "💰 TOTAL HONOR: Rp " . number_format($ca->total_fee, 0, ',', '.') . "\n\n"
                                            . "Mohon konfirmasi kesediaan (ACC/Bisa). Terima kasih!";
                                        $caWaUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $ca->crew->phone) . "?text=" . urlencode($waText);
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                        <!-- Kru & Rekening -->
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg overflow-hidden bg-slate-200 dark:bg-slate-800 shrink-0 border border-slate-300 dark:border-slate-700 flex items-center justify-center font-bold text-slate-700 dark:text-slate-300 text-xs">
                                                    @if($ca->crew->photo_path)
                                                        <img src="{{ asset('storage/' . $ca->crew->photo_path) }}" alt="{{ $ca->crew->nickname }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span>{{ strtoupper(substr($ca->crew->nickname, 0, 2)) }}</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900 dark:text-white text-xs">
                                                        {{ $ca->crew->nickname }}
                                                        <span class="font-normal text-[10px] text-slate-400">({{ $ca->crew->full_name }})</span>
                                                    </div>
                                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                                        {{ $ca->crew->bank_name ?? 'Bank' }}: <span class="font-mono">{{ $ca->crew->bank_account_number ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Peran -->
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                                {{ $ca->role_label }}
                                            </span>
                                        </td>

                                        <!-- Komponen Fee -->
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1 text-[10px]">
                                                @if($ca->fee_loading_in > 0) <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">In: {{ number_format($ca->fee_loading_in/1000, 0) }}k</span> @endif
                                                @if($ca->fee_loading_out > 0) <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Out: {{ number_format($ca->fee_loading_out/1000, 0) }}k</span> @endif
                                                @if($ca->fee_meal > 0) <span class="px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300">Makan: {{ number_format($ca->fee_meal/1000, 0) }}k</span> @endif
                                                @if($ca->fee_standby > 0) <span class="px-1.5 py-0.5 rounded bg-cyan-50 dark:bg-cyan-950/50 text-cyan-700 dark:text-cyan-300">Op: {{ number_format($ca->fee_standby/1000, 0) }}k</span> @endif
                                                @if($ca->fee_transport > 0) <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300">Trp: {{ number_format($ca->fee_transport/1000, 0) }}k</span> @endif
                                                @if($ca->fee_bonus > 0) <span class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300">Bns: {{ number_format($ca->fee_bonus/1000, 0) }}k</span> @endif
                                            </div>
                                        </td>

                                        <!-- Total Fee -->
                                        <td class="px-4 py-3 text-right">
                                            <span class="font-extrabold text-slate-900 dark:text-white text-xs">
                                                Rp {{ number_format($ca->total_fee, 0, ',', '.') }}
                                            </span>
                                        </td>

                                        <!-- Status ACC -->
                                        <td class="px-4 py-3 text-center">
                                            @if($ca->status === 'acc')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">
                                                    ✓ ACC Siap
                                                </span>
                                            @elseif($ca->status === 'offered')
                                                <form action="{{ route('spk.crews.update', $ca) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="role_label" value="{{ $ca->role_label }}">
                                                    <input type="hidden" name="fee_loading_in" value="{{ $ca->fee_loading_in }}">
                                                    <input type="hidden" name="fee_loading_out" value="{{ $ca->fee_loading_out }}">
                                                    <input type="hidden" name="fee_meal" value="{{ $ca->fee_meal }}">
                                                    <input type="hidden" name="fee_standby" value="{{ $ca->fee_standby }}">
                                                    <input type="hidden" name="fee_transport" value="{{ $ca->fee_transport }}">
                                                    <input type="hidden" name="fee_bonus" value="{{ $ca->fee_bonus }}">
                                                    <input type="hidden" name="status" value="acc">
                                                    <button type="submit" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 hover:bg-emerald-100 hover:text-emerald-700 transition-colors" title="Klik untuk konfirmasi ACC">
                                                        ⏳ Ditawarkan (Klik ACC)
                                                    </button>
                                                </form>
                                            @elseif($ca->status === 'completed')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400">
                                                    Menolak
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Status Bayar -->
                                        <td class="px-4 py-3 text-center">
                                            @if($ca->payment_status === 'paid')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">
                                                    ✅ Lunas
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">
                                                    Belum
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Aksi & WA -->
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ $caWaUrl }}" target="_blank" class="p-1 rounded bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-600 dark:text-emerald-400" title="Kirim Penawaran WhatsApp">
                                                    💬
                                                </a>
                                                <button type="button" @click="openEditCrewModal({{ Js::from($ca) }})" class="p-1 rounded bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 text-slate-600 dark:text-slate-300" title="Edit Fee / Peran">
                                                    ✏️
                                                </button>
                                                <a href="{{ route('crew-payroll.slip', $ca) }}" target="_blank" class="p-1 rounded bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 text-slate-600 dark:text-slate-300" title="Lihat Slip Gaji">
                                                    🧾
                                                </a>
                                                <form action="{{ route('spk.crews.destroy', $ca) }}" method="POST" onsubmit="return confirm('Batalkan penugasan kru {{ $ca->crew->nickname }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 rounded bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 text-rose-500" title="Hapus">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                                            Belum ada penugasan kru dengan tarif terperinci pada SPK ini. Klik <strong>+ Tambah Penugasan Kru</strong> untuk mulai menugaskan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECTION C: BIAYA OPERASIONAL TAMBAHAN & ESTIMASI LABA BERSIH -->
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <span>⛽ Biaya Operasional Event (BBM, Tol, Hotel & Lain-lain)</span>
                        </h3>
                        <p class="text-xs text-slate-500">Pencatatan pengeluaran logistik jalan dan lapangan untuk kalkulasi margin profit riil event.</p>
                    </div>
                </div>

                <form action="{{ route('spk.costs.update', $assignment) }}" method="POST" class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Biaya BBM (Rp)</label>
                            <input type="number" name="cost_fuel" value="{{ old('cost_fuel', $assignment->eventOrder->cost_fuel) }}" min="0" step="1000" placeholder="0"
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Biaya Tol (Rp)</label>
                            <input type="number" name="cost_toll" value="{{ old('cost_toll', $assignment->eventOrder->cost_toll) }}" min="0" step="1000" placeholder="0"
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Biaya Penginapan (Rp)</label>
                            <input type="number" name="cost_lodging" value="{{ old('cost_lodging', $assignment->eventOrder->cost_lodging) }}" min="0" step="1000" placeholder="0"
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Biaya Lain-Lain (Rp)</label>
                            <input type="number" name="cost_other" value="{{ old('cost_other', $assignment->eventOrder->cost_other) }}" min="0" step="1000" placeholder="0"
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Keterangan Pengeluaran Lain-Lain</label>
                            <input type="text" name="cost_other_notes" value="{{ old('cost_other_notes', $assignment->eventOrder->cost_other_notes) }}" placeholder="Misal: Parkir loading gate barat, lakban kabel panggung, konsumsi supir..."
                                class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-xs transition-colors">
                                Simpan Biaya Operasional
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 2-PHASE GATES -->
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800 space-y-6">
                <div class="text-xs text-slate-600 dark:text-slate-400 space-y-1">
                    <p>• Dengan menandatangani checklist ini, berarti kamu bertanggung jawab penuh atas isi checklist dan menyatakan bahwa semua kelengkapan sudah dicek dan diserahterimakan dengan teliti dan benar.</p>
                    <p>• Terdapat 2 Lembar checklist yang sangat Crusial, dan wajib teliti.</p>
                </div>

                <!-- Blue Banner: KEBERANGKATAN -->
                <div class="rounded-2xl border-2 border-indigo-400/60 overflow-hidden shadow-xs">
                    <div class="bg-indigo-600 text-white px-4 py-2.5 font-bold text-sm tracking-wider flex items-center justify-between">
                        <span>KEBERANGKATAN (BARANG KELUAR GUDANG)</span>
                        @if($assignment->departure_status === 'verified')
                            <span class="text-xs px-2 py-0.5 rounded bg-white/20 text-white font-bold">✓ TELAH DISAHKAN</span>
                        @endif
                    </div>
                    <div class="p-5 bg-indigo-50/30 dark:bg-slate-900/40">
                        @if($assignment->departure_status === 'verified')
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-xs text-slate-500">TTD PIC EVENT</div>
                                    <div class="mt-2 text-base font-bold text-slate-900 dark:text-white">{{ $assignment->departure_pic_event_name }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500">JAM DAN TGL KELUAR</div>
                                    <div class="mt-2 text-base font-bold text-indigo-600 dark:text-indigo-400">{{ $assignment->departure_datetime?->format('d/m/Y H:i') }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500">TTD PIC GUDANG</div>
                                    <div class="mt-2 text-base font-bold text-slate-900 dark:text-white">{{ $assignment->departure_pic_warehouse_name }}</div>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-indigo-200 dark:border-slate-800 flex justify-end">
                                <form action="{{ route('spk.reset-departure', $assignment) }}" method="POST" onsubmit="return confirm('Batalkan pengesahan keberangkatan? Anda dapat mengedit nama PIC dan mengesahkan ulang.');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-100 hover:bg-rose-100 text-indigo-700 hover:text-rose-700 dark:bg-slate-800 dark:hover:bg-rose-900/40 dark:text-indigo-300 dark:hover:text-rose-300 text-xs font-bold transition-colors flex items-center gap-1.5">
                                        <span>↩️ Batalkan / Edit Ulang Pengesahan Keberangkatan</span>
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('spk.verify-departure', $assignment) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama PIC Event Lapangan *</label>
                                    <input type="text" name="departure_pic_event_name" value="{{ old('departure_pic_event_name', $assignment->crew_pic_event_name) }}" required placeholder="Nama PIC Event" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama PIC Gudang *</label>
                                    <input type="text" name="departure_pic_warehouse_name" value="{{ old('departure_pic_warehouse_name', Auth::user()->name) }}" required placeholder="Nama PIC Gudang" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-sm">
                                </div>
                                <div>
                                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-md transition-all">
                                        Sahkan Keberangkatan (ACC Keluar)
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Emerald Banner: UJI KELAYAKAN INSTALASI & BAST PANGGUNG (VENUE QC) -->
                <div class="rounded-2xl border-2 border-emerald-400/60 overflow-hidden shadow-xs">
                    <div class="bg-emerald-600 text-white px-4 py-2.5 font-bold text-sm tracking-wider flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span>⚡ UJI KELAYAKAN VENUE & BAST INSTALASI PANGGUNG</span>
                            <a href="{{ route('spk.live', $assignment->eventOrder->public_token) }}" target="_blank" class="text-[11px] px-2 py-0.5 rounded bg-emerald-700 hover:bg-emerald-800 text-emerald-100 flex items-center gap-1 font-semibold transition-colors">
                                <span>📱 Portal Live Crew</span>
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($assignment->qc_status === 'passed')
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-white/20 text-white font-bold">✓ LOLOS QC & SIAP SHOW</span>
                            @elseif($assignment->qc_status === 'issues_found')
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-400 text-slate-900 font-bold">⚠️ DITEMUKAN KENDALA TEKNIS</span>
                            @else
                                <span class="text-xs px-2.5 py-0.5 rounded-full bg-white/10 text-emerald-100 font-medium">○ MENUNGGU VERIFIKASI VENUE</span>
                            @endif
                            <button type="button" @click="showQcModal = true" class="px-2.5 py-1 rounded bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-bold shadow-xs transition-colors">
                                Perbarui QC Manual
                            </button>
                        </div>
                    </div>
                    <div class="p-5 bg-emerald-50/30 dark:bg-slate-900/40 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-3 rounded-xl bg-white dark:bg-slate-800/60 border border-emerald-200 dark:border-slate-700/80 space-y-1">
                                <div class="text-[11px] font-bold text-slate-500 uppercase">⚡ Grounding Listrik</div>
                                <div class="text-sm font-extrabold {{ $assignment->qc_grounding_safe ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                    {{ $assignment->qc_grounding_safe ? '✓ Aman (< 2V AC)' : '○ Belum Dites' }}
                                </div>
                                <div class="text-[10px] text-slate-400">Pencegah induksi panggung</div>
                            </div>

                            <div class="p-3 rounded-xl bg-white dark:bg-slate-800/60 border border-emerald-200 dark:border-slate-700/80 space-y-1">
                                <div class="text-[11px] font-bold text-slate-500 uppercase">🎨 Uji Pola Warna RGB</div>
                                <div class="text-sm font-extrabold {{ $assignment->qc_test_rgb ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                    {{ $assignment->qc_test_rgb ? '✓ Pola RGB Bersih' : '○ Belum Dites' }}
                                </div>
                                <div class="text-[10px] text-slate-400">Red, Green, Blue, White bersih</div>
                            </div>

                            <div class="p-3 rounded-xl bg-white dark:bg-slate-800/60 border border-emerald-200 dark:border-slate-700/80 space-y-1">
                                <div class="text-[11px] font-bold text-slate-500 uppercase">📺 Sinyal & Refresh Rate</div>
                                <div class="text-sm font-extrabold {{ $assignment->qc_video_signal_ok ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                    {{ $assignment->qc_video_signal_ok ? '✓ Normal (3840Hz Stabil)' : '○ Belum Dites' }}
                                </div>
                                <div class="text-[10px] text-slate-400">Bebas flicker kamera</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="text-xs space-y-1">
                                <div class="text-slate-500">Pemeriksa / Teknisi Venue: <span class="font-bold text-slate-900 dark:text-white">{{ $assignment->qc_technician_name ?? 'Belum diverifikasi' }}</span></div>
                                <div class="text-slate-500">Waktu Verifikasi: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $assignment->qc_verified_at?->format('d/m/Y H:i') ?? '-' }}</span></div>
                                <div class="text-slate-500">Catatan Lapangan: <span class="italic text-slate-700 dark:text-slate-300">{{ $assignment->qc_notes ?? 'Tidak ada catatan kendala.' }}</span></div>
                            </div>

                            @if($assignment->qc_photo_path)
                                <div class="flex items-center gap-3">
                                    <a href="{{ asset('storage/' . $assignment->qc_photo_path) }}" target="_blank" class="block w-24 h-16 rounded-lg overflow-hidden border border-emerald-300 dark:border-slate-700 shrink-0 shadow-xs hover:opacity-90 transition-opacity">
                                        <img src="{{ asset('storage/' . $assignment->qc_photo_path) }}" alt="Dokumentasi Panggung" class="w-full h-full object-cover">
                                    </a>
                                    <div class="text-xs">
                                        <div class="font-bold text-slate-900 dark:text-white">Dokumentasi Visual Panggung</div>
                                        <a href="{{ asset('storage/' . $assignment->qc_photo_path) }}" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:underline text-[11px] font-semibold">
                                            Lihat Foto Asli / BAST &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Info Banner -->
                <div class="text-center py-2 px-4 rounded-xl bg-slate-100 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 text-xs">
                    ℹ️ PIC Kepulangan dapat diserahkan ke PIC yang sama atau PIC pengganti di lapangan.
                </div>

                <!-- Red Banner: KEPULANGAN -->
                <div class="rounded-2xl border-2 border-rose-400/60 overflow-hidden shadow-xs">
                    <div class="bg-rose-600 text-white px-4 py-2.5 font-bold text-sm tracking-wider flex items-center justify-between">
                        <span>KEPULANGAN (BARANG KEMBALI KE GUDANG)</span>
                        @if($assignment->return_status === 'verified')
                            <span class="text-xs px-2 py-0.5 rounded bg-white/20 text-white font-bold">✓ TELAH DISAHKAN</span>
                        @endif
                    </div>
                    <div class="p-5 bg-rose-50/30 dark:bg-slate-900/40 space-y-4">
                        @if($assignment->return_status === 'verified')
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-xs text-slate-500">TTD PIC EVENT KEPULANGAN</div>
                                    <div class="mt-2 text-base font-bold text-slate-900 dark:text-white">{{ $assignment->return_pic_event_name }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500">JAM DAN TGL KEMBALI</div>
                                    <div class="mt-2 text-base font-bold text-rose-600 dark:text-rose-400">{{ $assignment->return_datetime?->format('d/m/Y H:i') }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500">TTD PIC GUDANG KEPULANGAN</div>
                                    <div class="mt-2 text-base font-bold text-slate-900 dark:text-white">{{ $assignment->return_pic_warehouse_name }}</div>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-rose-200 dark:border-slate-800 flex justify-end">
                                <form action="{{ route('spk.reset-return', $assignment) }}" method="POST" onsubmit="return confirm('Batalkan pengesahan kepulangan? Anda dapat mengedit nama PIC dan mengesahkan ulang.');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 hover:text-rose-800 dark:bg-slate-800 dark:hover:bg-rose-900/40 dark:text-rose-300 dark:hover:text-rose-200 text-xs font-bold transition-colors flex items-center gap-1.5">
                                        <span>↩️ Batalkan / Edit Ulang Pengesahan Kepulangan</span>
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('spk.verify-return', $assignment) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama PIC Event Kepulangan *</label>
                                    <input type="text" name="return_pic_event_name" value="{{ old('return_pic_event_name', $assignment->crew_pic_event_name ?? $assignment->departure_pic_event_name) }}" required placeholder="Nama PIC Event Kepulangan" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama PIC Gudang Kepulangan *</label>
                                    <input type="text" name="return_pic_warehouse_name" value="{{ old('return_pic_warehouse_name', Auth::user()->name) }}" required placeholder="Nama PIC Gudang" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-sm">
                                </div>
                                <div>
                                    <button type="submit" class="w-full px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-sm shadow-md transition-all">
                                        Sahkan Kepulangan (ACC Masuk)
                                    </button>
                                </div>
                            </form>
                        @endif

                        @php
                            $unreturnedItems = $assignment->checklistItems->where('checked_out', true)->where('checked_in', false);
                        @endphp
                        @if($unreturnedItems->count() > 0)
                            <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-300 text-xs space-y-2">
                                <div class="font-bold flex flex-wrap items-center justify-between gap-2">
                                    <span>⚠️ Terdeteksi {{ $unreturnedItems->count() }} item keluar yang belum dicentang kembali!</span>
                                    <a href="{{ route('adjustments.create', ['reason' => 'Barang belum kembali pasca-event ' . $assignment->eventOrder->event_name . ' (' . $assignment->spk_number . '): ' . $unreturnedItems->pluck('item_name')->implode(', ')]) }}" class="px-2.5 py-1 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-bold text-[11px] shadow-xs">
                                        Lapor Penyesuaian Stok (Hilang) &rarr;
                                    </a>
                                </div>
                                <p class="text-[11px] text-amber-800 dark:text-amber-400">
                                    Item: {{ $unreturnedItems->pluck('item_name')->take(6)->implode(', ') }}{{ $unreturnedItems->count() > 6 ? ' dkk...' : '' }}
                                </p>
                            </div>
                        @endif

                        <div class="pt-3 border-t border-rose-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">Aksi Cepat Inspeksi Gudang Pasca-Event:</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('service-tickets.create', ['notes' => 'Kerusakan fisik pasca-event ' . $assignment->eventOrder->event_name . ' (' . $assignment->spk_number . ')']) }}" class="px-3 py-1.5 rounded-xl bg-slate-900 dark:bg-slate-800 hover:bg-indigo-600 text-white font-bold flex items-center gap-1.5 shadow-xs transition-colors">
                                    <span>🛠️ Buka Tiket Servis Workshop</span>
                                </a>
                                <a href="{{ route('adjustments.create', ['reason' => 'Koreksi selisih / kehilangan pasca-event ' . $assignment->eventOrder->event_name . ' (' . $assignment->spk_number . ')']) }}" class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold flex items-center gap-1.5 shadow-xs transition-colors">
                                    <span>⚠️ Lapor Selisih / Hilang</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: HALAMAN 2 - CHECKLIST BARANG KERJA TEAM -->
    <div x-show="activeTab === 2" class="space-y-4">
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Checklist Barang Kerja TEAM Omahvisual LED</h2>
                    <p class="text-xs text-slate-500">Kabinet, kabel jumper, panel listrik, ground stake, stage level, dan 13 item toolbox.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="showTemplateModal = true" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-300 dark:border-slate-700">
                        <span>📋 Template Muatan</span>
                    </button>
                    <button type="button" @click="filterOnlyQty = !filterOnlyQty" :class="filterOnlyQty ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-300 dark:border-slate-700">
                        <span x-text="filterOnlyQty ? '✓ Filter: QTY > 0' : '🔍 Filter: QTY > 0'"></span>
                    </button>
                    <button type="button" @click="addItemPage = 2; showAddItemModal = true" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5">
                        <span>+ Tambah Barang (Lembar 2)</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-100 dark:bg-slate-800/60 uppercase text-[10px] text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-3 py-2.5 w-10 text-center">No</th>
                            <th class="px-3 py-2.5">Barang Bawaan KE VENUE</th>
                            <th class="px-3 py-2.5">Detail / Keterangan</th>
                            <th class="px-3 py-2.5 w-20 text-center">QTY</th>
                            <th class="px-3 py-2.5 w-24 text-center bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 font-bold">Keluar</th>
                            <th class="px-3 py-2.5 w-24 text-center bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 font-bold">Kembali</th>
                            <th class="px-3 py-2.5 w-56 text-center">Catatan & Aksi Pasca-Event</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @php $no = 1; $currentCat = ''; @endphp
                        @foreach($assignment->checklistItems->where('page_number', 2) as $item)
                            @if($currentCat !== $item->category)
                                @php $currentCat = $item->category; @endphp
                                <tr class="bg-slate-50/80 dark:bg-slate-800/40 font-bold text-slate-900 dark:text-white">
                                    <td colspan="7" class="px-3 py-2 tracking-wider text-[11px] uppercase text-indigo-600 dark:text-indigo-400">
                                        📌 {{ $currentCat }}
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30" :class="filterOnlyQty && {{ $item->quantity <= 0 ? 'true' : 'false' }} ? 'hidden' : ''">
                                <td class="px-3 py-2 text-center text-slate-400">{{ $no++ }}</td>
                                <td class="px-3 py-2 font-semibold text-slate-900 dark:text-white">{{ $item->item_name }}</td>
                                <td class="px-3 py-2 text-slate-500">{{ $item->item_detail ?? '-' }}</td>
                                <td class="px-3 py-2 text-center font-bold">
                                    <span class="cursor-pointer hover:text-indigo-600 transition-colors" @click="openEditModal({{ Js::from([
                                        'id' => $item->id,
                                        'name' => $item->item_name,
                                        'detail' => $item->item_detail ?? '',
                                        'qty' => $item->quantity,
                                        'unit' => $item->unit,
                                    ]) }})" title="Klik untuk ubah QTY / Nama">
                                        {{ $item->quantity }} {{ $item->unit }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center bg-indigo-50/30 dark:bg-indigo-950/20">
                                    <input type="checkbox" {{ $item->checked_out ? 'checked' : '' }} onchange="toggleChecklistItem({{ $item->id }}, 'checked_out', this.checked)" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                <td class="px-3 py-2 text-center bg-rose-50/30 dark:bg-rose-950/20">
                                    <input type="checkbox" {{ $item->checked_in ? 'checked' : '' }} onchange="toggleChecklistItem({{ $item->id }}, 'checked_in', this.checked)" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 cursor-pointer">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <div class="space-y-1">
                                        <div id="item-note-{{ $item->id }}" class="{{ $item->condition_notes ? '' : 'hidden' }} text-[11px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 font-medium text-left">
                                            📝 <span class="note-text">{{ $item->condition_notes }}</span>
                                        </div>
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openEditModal({{ Js::from([
                                                'id' => $item->id,
                                                'name' => $item->item_name,
                                                'detail' => $item->item_detail ?? '',
                                                'qty' => $item->quantity,
                                                'unit' => $item->unit,
                                            ]) }})" class="p-1.5 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/40 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit QTY & Nama Barang">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('spk.items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus item {{ addslashes($item->item_name) }} dari checklist SPK?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-900/40 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Hapus Item">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <button type="button" onclick="editItemNote({{ $item->id }})" class="p-1.5 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/40 text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors" title="Tambah / Ubah Catatan Lapangan">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                </svg>
                                            </button>
                                            <a href="{{ route('service-tickets.create', ['item_name' => $item->item_name, 'quantity' => $item->quantity, 'notes' => 'Kerusakan pasca-event ' . $assignment->eventOrder->event_name . ' (' . $assignment->spk_number . ') - ' . $item->item_name . ($item->item_detail ? ' (' . $item->item_detail . ')' : '') . ($item->condition_notes ? '. Catatan: ' . $item->condition_notes : '')]) }}" class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white transition-colors" title="Buka Tiket Servis Workshop">
                                                🛠️ Servis
                                            </a>
                                            <a href="{{ route('adjustments.create', ['item_name' => $item->item_name, 'quantity' => $item->quantity, 'condition' => 'available', 'reason' => 'Hilang / tertinggal pasca-event ' . $assignment->eventOrder->event_name . ' (' . $assignment->spk_number . ') - ' . $item->item_name . ($item->item_detail ? ' (' . $item->item_detail . ')' : '') . ($item->condition_notes ? '. Catatan: ' . $item->condition_notes : '')]) }}" class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 hover:bg-amber-600 text-amber-600 dark:text-amber-400 hover:text-white transition-colors" title="Lapor Selisih / Hilang">
                                                ⚠️ Hilang
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: HALAMAN 3 - CHECKLIST PIC -->
    <div x-show="activeTab === 3" class="space-y-4">
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">CHECKLIST PIC Omahvisual LED</h2>
                    <p class="text-xs text-slate-500">Kit operator laptop/capture card, Novastar VX series, modul spare part, rigging gantung, peralatan tidur.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="showTemplateModal = true" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-300 dark:border-slate-700">
                        <span>📋 Template Muatan</span>
                    </button>
                    <button type="button" @click="filterOnlyQty = !filterOnlyQty" :class="filterOnlyQty ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-300 dark:border-slate-700">
                        <span x-text="filterOnlyQty ? '✓ Filter: QTY > 0' : '🔍 Filter: QTY > 0'"></span>
                    </button>
                    <button type="button" @click="addItemPage = 3; showAddItemModal = true" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5">
                        <span>+ Tambah Barang (Lembar 3)</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-100 dark:bg-slate-800/60 uppercase text-[10px] text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-3 py-2.5 w-10 text-center">No</th>
                            <th class="px-3 py-2.5">Barang Bawaan KE VENUE</th>
                            <th class="px-3 py-2.5">Detail / Keterangan</th>
                            <th class="px-3 py-2.5 w-20 text-center">QTY</th>
                            <th class="px-3 py-2.5 w-24 text-center bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 font-bold">Keluar</th>
                            <th class="px-3 py-2.5 w-24 text-center bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 font-bold">Kembali</th>
                            <th class="px-3 py-2.5 w-56 text-center">Catatan & Aksi Pasca-Event</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @php $no = 1; $currentCat = ''; @endphp
                        @foreach($assignment->checklistItems->where('page_number', 3) as $item)
                            @if($currentCat !== $item->category)
                                @php $currentCat = $item->category; @endphp
                                <tr class="bg-slate-50/80 dark:bg-slate-800/40 font-bold text-slate-900 dark:text-white">
                                    <td colspan="7" class="px-3 py-2 tracking-wider text-[11px] uppercase text-indigo-600 dark:text-indigo-400">
                                        📌 {{ $currentCat }}
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30" :class="filterOnlyQty && {{ $item->quantity <= 0 ? 'true' : 'false' }} ? 'hidden' : ''">
                                <td class="px-3 py-2 text-center text-slate-400">{{ $no++ }}</td>
                                <td class="px-3 py-2 font-semibold text-slate-900 dark:text-white">{{ $item->item_name }}</td>
                                <td class="px-3 py-2 text-slate-500">{{ $item->item_detail ?? '-' }}</td>
                                <td class="px-3 py-2 text-center font-bold">
                                    <span class="cursor-pointer hover:text-indigo-600 transition-colors" @click="openEditModal({{ Js::from([
                                        'id' => $item->id,
                                        'name' => $item->item_name,
                                        'detail' => $item->item_detail ?? '',
                                        'qty' => $item->quantity,
                                        'unit' => $item->unit,
                                    ]) }})" title="Klik untuk ubah QTY / Nama">
                                        {{ $item->quantity }} {{ $item->unit }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-center bg-indigo-50/30 dark:bg-indigo-950/20">
                                    <input type="checkbox" {{ $item->checked_out ? 'checked' : '' }} onchange="toggleChecklistItem({{ $item->id }}, 'checked_out', this.checked)" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                <td class="px-3 py-2 text-center bg-rose-50/30 dark:bg-rose-950/20">
                                    <input type="checkbox" {{ $item->checked_in ? 'checked' : '' }} onchange="toggleChecklistItem({{ $item->id }}, 'checked_in', this.checked)" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 cursor-pointer">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <div class="space-y-1">
                                        <div id="item-note-{{ $item->id }}" class="{{ $item->condition_notes ? '' : 'hidden' }} text-[11px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 font-medium text-left">
                                            📝 <span class="note-text">{{ $item->condition_notes }}</span>
                                        </div>
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openEditModal({{ Js::from([
                                                'id' => $item->id,
                                                'name' => $item->item_name,
                                                'detail' => $item->item_detail ?? '',
                                                'qty' => $item->quantity,
                                                'unit' => $item->unit,
                                            ]) }})" class="p-1.5 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/40 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit QTY & Nama Barang">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('spk.items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus item {{ addslashes($item->item_name) }} dari checklist SPK?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-900/40 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Hapus Item">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <button type="button" onclick="editItemNote({{ $item->id }})" class="p-1.5 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/40 text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors" title="Tambah / Ubah Catatan Lapangan">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                </svg>
                                            </button>
                                            <a href="{{ route('service-tickets.create', ['item_name' => $item->item_name, 'quantity' => $item->quantity, 'notes' => 'Kerusakan pasca-event ' . $assignment->eventOrder->event_name . ' (' . $assignment->spk_number . ') - ' . $item->item_name . ($item->item_detail ? ' (' . $item->item_detail . ')' : '') . ($item->condition_notes ? '. Catatan: ' . $item->condition_notes : '')]) }}" class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white transition-colors" title="Buka Tiket Servis Workshop">
                                                🛠️ Servis
                                            </a>
                                            <a href="{{ route('adjustments.create', ['item_name' => $item->item_name, 'quantity' => $item->quantity, 'condition' => 'available', 'reason' => 'Hilang / tertinggal pasca-event ' . $assignment->eventOrder->event_name . ' (' . $assignment->spk_number . ') - ' . $item->item_name . ($item->item_detail ? ' (' . $item->item_detail . ')' : '') . ($item->condition_notes ? '. Catatan: ' . $item->condition_notes : '')]) }}" class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 hover:bg-amber-600 text-amber-600 dark:text-amber-400 hover:text-white transition-colors" title="Lapor Selisih / Hilang">
                                                ⚠️ Hilang
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL 1: EDIT TIM & ARMADA LOGISTIK -->
    <div x-show="showAssignModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAssignModal" @click="showAssignModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-300 dark:border-slate-700">
                <form action="{{ route('spk.update-assignment', $assignment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Tim Lapangan & Armada</h3>
                                <p class="text-xs text-slate-500">Sesuaikan roster 7 crew lapangan dan kelengkapan kendaraan sebelum keberangkatan.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('crews.create') }}" target="_blank" class="text-xs px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 font-bold hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors flex items-center gap-1">
                                    + Tambah Kru Baru
                                </a>
                                <button type="button" @click="showAssignModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
                            </div>
                        </div>

                        <!-- PIC Event -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nama PIC Event Lapangan</label>
                            <input type="text" list="staff_list" name="crew_pic_event_name" value="{{ old('crew_pic_event_name', $assignment->crew_pic_event_name) }}" placeholder="Ketik nama atau pilih staf..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>

                        <!-- Crew 1 to 7 Grid -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Anggota Crew Lapangan (1 s/d 7)</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @for($i = 1; $i <= 7; $i++)
                                    @php $field = "crew_{$i}_name"; @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="w-16 text-xs text-slate-500 font-semibold">Crew {{ $i }}:</span>
                                        <input type="text" list="staff_list" name="{{ $field }}" value="{{ old($field, $assignment->$field) }}" placeholder="Nama crew..." class="flex-1 px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Vehicle & Checkboxes -->
                        <div class="pt-3 border-t border-slate-200 dark:border-slate-800 space-y-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Armada & Kesiapan Dokumen</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <select name="vehicle_name" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                                        @foreach($vehicles ?? [] as $veh)
                                            <option value="{{ $veh->name }}" {{ old('vehicle_name', $assignment->vehicle_name) === $veh->name ? 'selected' : '' }}>
                                                {{ $veh->name }} {{ $veh->plate_number ? '('.$veh->plate_number.')' : '' }}
                                            </option>
                                        @endforeach
                                        <option value="MOBIL PRIBADI / LAINNYA" {{ old('vehicle_name', $assignment->vehicle_name) === 'MOBIL PRIBADI / LAINNYA' ? 'selected' : '' }}>MOBIL PRIBADI / LAINNYA</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="has_etoll" value="1" {{ old('has_etoll', $assignment->has_etoll) ? 'checked' : '' }} class="rounded text-indigo-600">
                                        <span>E-Toll Saldo Cukup</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="has_kunci_ban" value="1" {{ old('has_kunci_ban', $assignment->has_kunci_ban) ? 'checked' : '' }} class="rounded text-indigo-600">
                                        <span>Kunci Ban / Dongkrak</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="has_stnk" value="1" {{ old('has_stnk', $assignment->has_stnk) ? 'checked' : '' }} class="rounded text-indigo-600">
                                        <span>STNK Asli / Aktif</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="has_kir" value="1" {{ old('has_kir', $assignment->has_kir) ? 'checked' : '' }} class="rounded text-indigo-600">
                                        <span>Kartu / Uji KIR</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer col-span-2">
                                        <input type="checkbox" name="has_barcode_bbm" value="1" {{ old('has_barcode_bbm', $assignment->has_barcode_bbm) ? 'checked' : '' }} class="rounded text-indigo-600">
                                        <span>Barcode Subsidi BBM (MyPertamina)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showAssignModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md">
                            Simpan Perubahan Tim & Armada
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 1B: TAMBAH ARMADA KENDARAAN (MULTI-VEHICLE) -->
    <div x-show="showAddVehicleModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddVehicleModal" @click="showAddVehicleModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-300 dark:border-slate-700">
                <form action="{{ route('spk.vehicles.store', $assignment) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Tambah Armada Kendaraan</h3>
                                <p class="text-xs text-slate-500">Tambahkan unit pickup / truk pengangkut logistik event.</p>
                            </div>
                            <button type="button" @click="showAddVehicleModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Pilih Master Armada / Ketik Baru *</label>
                            <input type="text" list="vehicles_master_list" name="vehicle_name" required placeholder="Misal: TRAGA 1 / GRAN MAX 2 / L300"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-bold">
                            <datalist id="vehicles_master_list">
                                @foreach($vehicles ?? [] as $veh)
                                    <option value="{{ $veh->name }}">{{ $veh->plate_number ? '('.$veh->plate_number.')' : '' }}</option>
                                @endforeach
                            </datalist>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nomor Polisi (Plat)</label>
                                <input type="text" name="plate_number" placeholder="N 1234 AB"
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-mono uppercase text-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Supir (Driver)</label>
                                <input type="text" list="staff_list" name="driver_name" placeholder="Nama supir..."
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Checklist Perlengkapan Fisik Mobil</label>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="has_etoll" value="1" checked class="rounded text-indigo-600">
                                    <span>E-Toll Saldo Cukup</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="has_kunci_ban" value="1" checked class="rounded text-indigo-600">
                                    <span>Kunci Ban / Dongkrak</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="has_stnk" value="1" checked class="rounded text-indigo-600">
                                    <span>STNK Asli / Aktif</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="has_kir" value="1" checked class="rounded text-indigo-600">
                                    <span>Buku / Uji KIR</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer col-span-2">
                                    <input type="checkbox" name="has_barcode_bbm" value="1" checked class="rounded text-indigo-600">
                                    <span>Barcode MyPertamina (Subsidi Solar)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Armada</label>
                            <input type="text" name="notes" placeholder="Misal: Muat khusus kabinet P3.9 dan panel listrik utama"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showAddVehicleModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md">
                            Simpan Armada
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 1C: EDIT ARMADA KENDARAAN -->
    <div x-show="showEditVehicleModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditVehicleModal" @click="showEditVehicleModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-300 dark:border-slate-700">
                <form :action="'{{ url('/spk/vehicles') }}/' + editVehicle.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Kesiapan Armada</h3>
                                <p class="text-xs text-slate-500">Perbarui driver dan kesiapan dokumen kendaraan.</p>
                            </div>
                            <button type="button" @click="showEditVehicleModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Armada *</label>
                            <input type="text" name="vehicle_name" x-model="editVehicle.vehicle_name" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-bold">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nomor Polisi (Plat)</label>
                                <input type="text" name="plate_number" x-model="editVehicle.plate_number"
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-mono uppercase text-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Supir (Driver)</label>
                                <input type="text" list="staff_list" name="driver_name" x-model="editVehicle.driver_name"
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                            </div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Checklist Perlengkapan Fisik Mobil</label>
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="has_etoll" value="1" x-model="editVehicle.has_etoll" class="rounded text-indigo-600">
                                    <span>E-Toll Saldo Cukup</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="has_kunci_ban" value="1" x-model="editVehicle.has_kunci_ban" class="rounded text-indigo-600">
                                    <span>Kunci Ban / Dongkrak</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="has_stnk" value="1" x-model="editVehicle.has_stnk" class="rounded text-indigo-600">
                                    <span>STNK Asli / Aktif</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="has_kir" value="1" x-model="editVehicle.has_kir" class="rounded text-indigo-600">
                                    <span>Buku / Uji KIR</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer col-span-2">
                                    <input type="checkbox" name="has_barcode_bbm" value="1" x-model="editVehicle.has_barcode_bbm" class="rounded text-indigo-600">
                                    <span>Barcode MyPertamina (Subsidi Solar)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Armada</label>
                            <input type="text" name="notes" x-model="editVehicle.notes"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showEditVehicleModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md">
                            Simpan Perubahan Armada
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 1D: TAMBAH PENUGASAN KRU & STRUKTUR HONOR -->
    <div x-show="showAddCrewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddCrewModal" @click="showAddCrewModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-300 dark:border-slate-700">
                <form action="{{ route('spk.crews.store', $assignment) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Tambah Penugasan Kru Lapangan</h3>
                                <p class="text-xs text-slate-500">Pilih kru untuk auto-fill rate card standar, lalu sesuaikan penawaran.</p>
                            </div>
                            <button type="button" @click="showAddCrewModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Pilih Kru *</label>
                                <select name="crew_id" x-model="newCrew.crew_id" @change="onSelectCrew($event.target.value)" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-bold text-slate-900 dark:text-white">
                                    <option value="">-- Pilih Kru Lapangan --</option>
                                    @foreach($crews as $crw)
                                        <option value="{{ $crw->id }}">
                                            {{ $crw->nickname }} ({{ $crw->full_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Peran Pada Event *</label>
                                <input type="text" list="roles_preset_list" name="role_label" x-model="newCrew.role_label" required placeholder="PIC / Operator / Teknisi / Crew..."
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                                <datalist id="roles_preset_list">
                                    <option value="PIC Event">PIC Event</option>
                                    <option value="Operator LED / Visual">Operator LED / Visual</option>
                                    <option value="Teknisi Rigging & Listrik">Teknisi Rigging & Listrik</option>
                                    <option value="Driver / Supir Logistik">Driver / Supir Logistik</option>
                                    <option value="Crew Lapangan">Crew Lapangan</option>
                                    <option value="Helper">Helper</option>
                                </datalist>
                            </div>
                        </div>

                        <!-- Modular Rate Inputs -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Komponen Honor Kru (Otomatis / Nego)</span>
                                <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold"
                                    x-text="'Total: Rp ' + (Number(newCrew.fee_loading_in || 0) + Number(newCrew.fee_loading_out || 0) + Number(newCrew.fee_meal || 0) + Number(newCrew.fee_standby || 0) + Number(newCrew.fee_transport || 0) + Number(newCrew.fee_bonus || 0)).toLocaleString('id-ID')"></span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Loading In (Rp)</label>
                                    <input type="number" name="fee_loading_in" x-model.number="newCrew.fee_loading_in" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Loading Out (Rp)</label>
                                    <input type="number" name="fee_loading_out" x-model.number="newCrew.fee_loading_out" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Uang Makan (Rp)</label>
                                    <input type="number" name="fee_meal" x-model.number="newCrew.fee_meal" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Standby/Operator (Rp)</label>
                                    <input type="number" name="fee_standby" x-model.number="newCrew.fee_standby" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Transport Luar Kota (Rp)</label>
                                    <input type="number" name="fee_transport" x-model.number="newCrew.fee_transport" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Bonus / Lembur (Rp)</label>
                                    <input type="number" name="fee_bonus" x-model.number="newCrew.fee_bonus" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Status Penugasan *</label>
                                <select name="status" x-model="newCrew.status" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                                    <option value="offered">Ditawarkan (Pending ACC Kru)</option>
                                    <option value="acc">Langsung ACC (Kru Sudah Setuju)</option>
                                    <option value="completed">Selesai</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Tugas (Opsional)</label>
                                <input type="text" name="notes" x-model="newCrew.notes" placeholder="Misal: Standby jam 13.00, pegang video switcher..."
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showAddCrewModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md">
                            Tugaskan Kru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 1E: EDIT PENUGASAN KRU & STRUKTUR HONOR -->
    <div x-show="showEditCrewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditCrewModal" @click="showEditCrewModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-300 dark:border-slate-700">
                <form :action="'{{ url('/spk/crews') }}/' + editCrew.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Edit Penugasan: <span x-text="editCrew.nickname"></span></h3>
                                <p class="text-xs text-slate-500">Sesuaikan peran, negosiasi honor, dan status konfirmasi.</p>
                            </div>
                            <button type="button" @click="showEditCrewModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Peran Pada Event *</label>
                            <input type="text" list="roles_preset_list" name="role_label" x-model="editCrew.role_label" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                        </div>

                        <!-- Modular Rate Inputs -->
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Komponen Honor Kru</span>
                                <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold"
                                    x-text="'Total: Rp ' + (Number(editCrew.fee_loading_in || 0) + Number(editCrew.fee_loading_out || 0) + Number(editCrew.fee_meal || 0) + Number(editCrew.fee_standby || 0) + Number(editCrew.fee_transport || 0) + Number(editCrew.fee_bonus || 0)).toLocaleString('id-ID')"></span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Loading In (Rp)</label>
                                    <input type="number" name="fee_loading_in" x-model.number="editCrew.fee_loading_in" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Loading Out (Rp)</label>
                                    <input type="number" name="fee_loading_out" x-model.number="editCrew.fee_loading_out" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Uang Makan (Rp)</label>
                                    <input type="number" name="fee_meal" x-model.number="editCrew.fee_meal" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Standby/Operator (Rp)</label>
                                    <input type="number" name="fee_standby" x-model.number="editCrew.fee_standby" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Transport Luar Kota (Rp)</label>
                                    <input type="number" name="fee_transport" x-model.number="editCrew.fee_transport" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-0.5">Bonus / Lembur (Rp)</label>
                                    <input type="number" name="fee_bonus" x-model.number="editCrew.fee_bonus" min="0" step="1000"
                                        class="w-full px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-mono font-bold text-slate-900 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Status Penugasan *</label>
                                <select name="status" x-model="editCrew.status" required
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                                    <option value="offered">Ditawarkan (Pending)</option>
                                    <option value="acc">ACC (Dikonfirmasi)</option>
                                    <option value="completed">Selesai</option>
                                    <option value="declined">Ditolak / Batal</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Status Pembayaran</label>
                                <select name="payment_status" x-model="editCrew.payment_status"
                                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                                    <option value="unpaid">Belum Lunas</option>
                                    <option value="paid">Lunas</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Tugas (Opsional)</label>
                            <input type="text" name="notes" x-model="editCrew.notes"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showEditCrewModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: VERIFIKASI QC VENUE MANUAL -->
    <div x-show="showQcModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showQcModal" @click="showQcModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-300 dark:border-slate-700">
                <form action="{{ route('spk.update-qc', $assignment) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Verifikasi QC Instalasi Venue</h3>
                                <p class="text-xs text-slate-500">Pembaruan status kelayakan LED di venue event.</p>
                            </div>
                            <button type="button" @click="showQcModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Status Kelayakan *</label>
                            <select name="qc_status" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-bold">
                                <option value="passed" {{ old('qc_status', $assignment->qc_status) === 'passed' ? 'selected' : '' }}>✅ Lolos Uji Kelayakan & Siap Show</option>
                                <option value="issues_found" {{ old('qc_status', $assignment->qc_status) === 'issues_found' ? 'selected' : '' }}>⚠️ Ada Kendala Teknis / Modul Rusak</option>
                                <option value="pending" {{ old('qc_status', $assignment->qc_status) === 'pending' ? 'selected' : '' }}>○ Pending / Menunggu Pemeriksaan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Teknisi / PIC Pemeriksa *</label>
                            <input type="text" name="qc_technician_name" value="{{ old('qc_technician_name', $assignment->qc_technician_name ?? Auth::user()->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>

                        <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-slate-800 text-xs">
                            <label class="flex items-center gap-2 cursor-pointer font-medium">
                                <input type="checkbox" name="qc_grounding_safe" value="1" {{ old('qc_grounding_safe', $assignment->qc_grounding_safe) ? 'checked' : '' }} class="rounded text-emerald-600">
                                <span>⚡ Grounding Listrik Aman (< 2V AC ke Ground)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer font-medium">
                                <input type="checkbox" name="qc_test_rgb" value="1" {{ old('qc_test_rgb', $assignment->qc_test_rgb) ? 'checked' : '' }} class="rounded text-emerald-600">
                                <span>🎨 Uji Pola Warna RGB (Red, Green, Blue, White Bersih)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer font-medium">
                                <input type="checkbox" name="qc_video_signal_ok" value="1" {{ old('qc_video_signal_ok', $assignment->qc_video_signal_ok) ? 'checked' : '' }} class="rounded text-emerald-600">
                                <span>📺 Sinyal Video & Refresh Rate 3840Hz Stabil Bebas Flicker</span>
                            </label>
                        </div>

                        <div class="space-y-2 bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Dokumentasi Foto Layar (Opsional)</label>
                            <input type="hidden" name="remove_qc_photo" id="remove_qc_photo_input" value="0">
                            <div class="relative w-full h-32 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                                <img id="qc_photo_preview" src="{{ $assignment->qc_photo_path ? asset('storage/' . $assignment->qc_photo_path) : '' }}" alt="QC Layar" class="w-full h-full object-cover {{ $assignment->qc_photo_path ? '' : 'hidden' }}">
                                <div id="qc_photo_placeholder" class="text-center p-2 text-slate-400 {{ $assignment->qc_photo_path ? 'hidden' : '' }}">
                                    <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-[9px]">16:9 Landscape Foto Panggung Layar Default</span>
                                </div>
                            </div>
                            <input type="file" name="qc_photo" id="qc_photo_input" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                            <div class="flex items-center justify-between pt-1">
                                <p class="text-[10px] text-slate-400">Crop Otomatis 16:9</p>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="crop_qc_photo_btn" class="hidden text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">✂️ Crop</button>
                                    <button type="button" id="remove_qc_photo_btn" onclick="removeImage('qc_photo')" class="{{ $assignment->qc_photo_path ? '' : 'hidden' }} text-[11px] font-bold text-rose-500 hover:text-rose-600 hover:underline">🗑️ Hapus</button>
                                    <button type="button" id="undo_qc_photo_btn" onclick="undoRemoveImage('qc_photo', '{{ $assignment->qc_photo_path ? asset('storage/' . $assignment->qc_photo_path) : '' }}')" class="hidden text-[11px] font-bold text-amber-500 hover:underline">↩️ Batal</button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Teknis Lapangan</label>
                            <textarea name="qc_notes" rows="2" placeholder="Catatan modul cadangan dipakai, penempatan genset, dll..." class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">{{ old('qc_notes', $assignment->qc_notes) }}</textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showQcModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md">
                            Simpan Verifikasi QC
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: TAMBAH BARANG MANUAL KE SPK -->
    <div x-show="showAddItemModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto" @keydown.escape.window="showAddItemModal = false">
        <div class="fixed inset-0" @click="showAddItemModal = false"></div>

        <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-2xl border border-slate-300 dark:border-slate-700 space-y-4 my-auto" @click.stop>
            <form action="{{ route('spk.items.store', $assignment) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Barang ke SPK</h3>
                            <p class="text-xs text-slate-500">Pilih dari Master Barang Gudang atau ketik barang custom.</p>
                        </div>
                        <button type="button" @click="showAddItemModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
                    </div>

                    <!-- Master Barang Selector -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Cari dari Master Barang Gudang
                        </label>
                        <input type="text" list="master_products_list" id="quick_product_select" placeholder="Ketik nama atau kode barang master..." oninput="selectProductFromMaster(this.value)" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-indigo-300 dark:border-indigo-800 text-xs text-slate-900 dark:text-white font-medium">
                        <datalist id="master_products_list">
                            @if(isset($products))
                                @foreach($products as $prod)
                                    @php $stockQty = $prod->stockBalances->sum('quantity'); @endphp
                                    <option value="{{ $prod->name }}" data-category="{{ $prod->category->name ?? 'BARANG KERJA' }}" data-unit="{{ $prod->unit->name ?? 'pcs' }}" data-stock="{{ (int)$stockQty }}">
                                        {{ $prod->name }} • Stok: {{ (int)$stockQty }} {{ $prod->unit->name ?? 'pcs' }} ({{ $prod->code }})
                                    </option>
                                @endforeach
                            @endif
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Target Halaman / Lembar *</label>
                        <select name="page_number" x-model="addItemPage" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-bold">
                            <option value="2">Halaman 2: Checklist Barang Kerja Team</option>
                            <option value="3">Halaman 3: Checklist PIC (Kit Operator & Spares)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Kategori Barang *</label>
                        <input type="text" name="category" id="new_item_category" list="checklist_categories" required placeholder="Contoh: KABINET & KELISTRIKAN, TOOLS BOX, dll." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        <datalist id="checklist_categories">
                            <option value="KABINET & KELISTRIKAN">
                            <option value="TOOLS BOX">
                            <option value="GROUND STAKE">
                            <option value="STAGE LEVEL">
                            <option value="KIT OPERATOR">
                            <option value="PROSESOR">
                            <option value="SPARE PART LED">
                            <option value="ATK TAMBAHAN">
                            <option value="LED GANTUNG">
                            <option value="PERALATAN TIDUR">
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Barang Bawaan *</label>
                        <input type="text" name="item_name" id="new_item_name" required placeholder="Nama barang bawaan..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-semibold">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Jumlah (QTY) *</label>
                            <input type="number" name="quantity" min="0" value="1" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Satuan *</label>
                            <input type="text" name="unit" id="new_item_unit" list="unit_options" value="pcs" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                            <datalist id="unit_options">
                                <option value="pcs">
                                <option value="box">
                                <option value="unit">
                                <option value="roll">
                                <option value="batang">
                                <option value="set">
                                <option value="meter">
                                <option value="lembar">
                            </datalist>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Detail / Keterangan Spesifik (Opsional)</label>
                        <input type="text" name="item_detail" id="new_item_detail" placeholder="Contoh: Panjang 15m, Warna Biru, Cadangan..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="px-6 py-4 -mx-6 -mb-6 mt-5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" @click="showAddItemModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md transition-colors">
                        + Tambah ke Checklist
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: EDIT ITEM CHECKLIST (showEditItemModal) -->
    <div x-show="showEditItemModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto" @keydown.escape.window="showEditItemModal = false">
        <div class="fixed inset-0" @click="showEditItemModal = false"></div>

        <div class="relative z-10 w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-2xl border border-slate-300 dark:border-slate-700 space-y-4 my-auto" @click.stop>
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>✏️ Edit Item Checklist</span>
                    </h3>
                    <p class="text-xs text-slate-500">Sesuaikan kuantitas bawaan atau keterangan item.</p>
                </div>
                <button type="button" @click="showEditItemModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
            </div>

            <form :action="'/spk/items/' + editItem.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Barang *</label>
                    <input type="text" name="item_name" x-model="editItem.name" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-semibold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Jumlah (QTY) *</label>
                        <input type="number" name="quantity" min="0" x-model="editItem.qty" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Satuan *</label>
                        <input type="text" name="unit" list="unit_options" x-model="editItem.unit" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Detail / Keterangan</label>
                    <input type="text" name="item_detail" x-model="editItem.detail" placeholder="Detail spesifik..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                </div>

                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" @click="showEditItemModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-300 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md transition-colors">
                        Simpan Perubahan Item
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: PILIH TEMPLATE CHECKLIST (showTemplateModal) -->
    <div x-show="showTemplateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto" @keydown.escape.window="showTemplateModal = false">
        <div class="fixed inset-0" @click="showTemplateModal = false"></div>

        <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-2xl border border-slate-300 dark:border-slate-700 space-y-4 my-auto" @click.stop>
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pilih Template Muatan Checklist</h3>
                    <p class="text-xs text-slate-500">Sesuaikan daftar bawaan sesuai skala event dan kebutuhan lapangan.</p>
                </div>
                <button type="button" @click="showTemplateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
            </div>

            <div class="space-y-3">
                <!-- Option 1: Template Esensial -->
                <form action="{{ route('spk.apply-template', $assignment) }}" method="POST" onsubmit="return confirm('Terapkan Template Esensial? Item checklist saat ini akan diperbarui sesuai hasil kalkulasi layar & alat utama.')">
                    @csrf
                    <input type="hidden" name="template" value="essential">
                    <input type="hidden" name="tab" :value="activeTab">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border-2 border-indigo-200 dark:border-indigo-900/60 hover:border-indigo-600 dark:hover:border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/20 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-all flex items-start gap-3">
                        <span class="text-2xl">⚡</span>
                        <div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                <span>Template Esensial (Default Praktis)</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-600 text-white font-bold">Rekomendasi</span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Hanya memuat barang yang benar-benar dipakai berdasarkan kalkulasi layar (Kabinet, Jumper, Panel 16A, 1 Box Toolbox Teknisi, Video Processor, Laptop & Modul Spare). Bebas dari puluhan baris yang tidak dipakai.
                            </p>
                        </div>
                    </button>
                </form>

                <!-- Option 2: Template Lengkap -->
                <form action="{{ route('spk.apply-template', $assignment) }}" method="POST" onsubmit="return confirm('Terapkan Template Lengkap? Semua item aksesoris, perincian toolbox, rigging, dan perlengkapan tidur kru akan dimuat.')">
                    @csrf
                    <input type="hidden" name="template" value="full">
                    <input type="hidden" name="tab" :value="activeTab">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 bg-slate-50 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all flex items-start gap-3">
                        <span class="text-2xl">📦</span>
                        <div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white">
                                Template Lengkap (Full Event & Luar Kota)
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Memuat 50+ item lengkap mencakup rincian 13 item toolbox individual, rigging ground stake, cat walk, APD ketinggian, dan perlengkapan tidur kru.
                            </p>
                        </div>
                    </button>
                </form>

                <!-- Option 3: Kosongkan Checklist -->
                <form action="{{ route('spk.apply-template', $assignment) }}" method="POST" onsubmit="return confirm('Yakin ingin mengosongkan seluruh checklist SPK ini? Anda dapat menambahkan barang manual dari awal.')">
                    @csrf
                    <input type="hidden" name="template" value="empty">
                    <input type="hidden" name="tab" :value="activeTab">
                    <button type="submit" class="w-full text-left p-4 rounded-xl border border-rose-200 dark:border-rose-900/60 hover:border-rose-500 bg-rose-50/30 dark:bg-rose-950/20 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-all flex items-start gap-3">
                        <span class="text-2xl">🗑️</span>
                        <div>
                            <div class="font-bold text-sm text-rose-600 dark:text-rose-400">
                                Kosongkan Seluruh Checklist (Input Manual)
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Hapus semua item saat ini agar daftar checklist menjadi bersih/kosong untuk input custom dari Master Barang secara mandiri.
                            </p>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Datalist Staff / Users / Master Kru -->
    <datalist id="staff_list">
        @if(isset($crews))
            @foreach($crews as $crew)
                <option value="{{ $crew->nickname }}">{{ $crew->nickname }} - {{ $crew->full_name }} ({{ is_array($crew->positions) ? implode(', ', $crew->positions) : ($crew->position ?? 'Kru') }})</option>
            @endforeach
        @endif
        @foreach($users as $user)
            <option value="{{ $user->name }}">{{ $user->name }} ({{ $user->roles->pluck('name')->implode(', ') }})</option>
        @endforeach
    </datalist>
</div>

<script>
function toggleChecklistItem(itemId, field, value) {
    fetch(`/spk/items/${itemId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ field: field, value: value })
    })
    .then(res => res.json())
    .then(data => {
        if (field === 'condition_notes') {
            const noteContainer = document.getElementById(`item-note-${itemId}`);
            if (noteContainer) {
                if (value && value.trim()) {
                    noteContainer.classList.remove('hidden');
                    const noteSpan = noteContainer.querySelector('.note-text');
                    if (noteSpan) noteSpan.textContent = value;
                } else {
                    noteContainer.classList.add('hidden');
                }
            }
        }
    })
    .catch(err => {
        console.error('Failed to toggle item', err);
        alert('Gagal mengupdate checklist.');
    });
}

function editItemNote(itemId) {
    const noteContainer = document.getElementById(`item-note-${itemId}`);
    let currentNote = '';
    if (noteContainer) {
        const span = noteContainer.querySelector('.note-text');
        if (span) currentNote = span.textContent.trim();
    }
    const newNote = prompt("Masukkan catatan kondisi barang / temuan di lapangan:", currentNote);
    if (newNote !== null) {
        toggleChecklistItem(itemId, 'condition_notes', newNote);
    }
}

function selectProductFromMaster(val) {
    const datalist = document.getElementById('master_products_list');
    if (!datalist) return;
    const option = Array.from(datalist.options).find(o => o.value === val);
    if (option) {
        const nameInput = document.getElementById('new_item_name');
        const catInput = document.getElementById('new_item_category');
        const unitInput = document.getElementById('new_item_unit');
        if (nameInput) nameInput.value = option.value;
        if (catInput && option.dataset.category) catInput.value = option.dataset.category;
        if (unitInput && option.dataset.unit) unitInput.value = option.dataset.unit;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.setupImageCropper === 'function') {
        window.setupImageCropper('qc_photo_input', 'qc_photo_preview', 'qc_photo_placeholder', 16/9, 'crop_qc_photo_btn');
    }
});
</script>
@endsection

