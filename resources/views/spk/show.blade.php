@extends('layouts.app')

@section('title', 'SPK ' . $assignment->spk_number . ' - Omah Visual')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 1 }">
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
            <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300">
                Penugasan Team lapangan untuk memenuhi kebutuhan event client dengan detail:
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

                <!-- Right: Crew & Vehicle -->
                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-2 text-xs">
                        <div class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] mb-2">Penugasan Crew:</div>
                        @for($i = 1; $i <= 7; $i++)
                            @php $field = "crew_{$i}_name"; @endphp
                            <div class="flex justify-between py-0.5 border-b border-slate-200/50 dark:border-slate-700/50">
                                <span class="text-slate-500 font-semibold">Crew {{ $i }}:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $assignment->$field ?? '-' }}</span>
                            </div>
                        @endfor
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-2 text-xs">
                        <div class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] mb-2">Logistik Kendaraan:</div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>Kendaraan: <span class="font-bold">{{ $assignment->vehicle_name ?? 'TRAGA 1' }}</span></div>
                            <div>E-Toll: <span class="font-semibold text-emerald-600">✓ Siap</span></div>
                            <div>Kunci Ban: <span class="font-semibold text-emerald-600">✓ Siap</span></div>
                            <div>STNK: <span class="font-semibold text-emerald-600">✓ Siap</span></div>
                            <div>Kartu KIR: <span class="font-semibold text-emerald-600">✓ Siap</span></div>
                            <div>Barcode BBM: <span class="font-semibold text-emerald-600">✓ Siap</span></div>
                        </div>
                    </div>
                </div>
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

                <!-- Strict Rule Banner -->
                <div class="text-center py-2 px-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-400 font-extrabold text-xs tracking-wider uppercase">
                    ⚠️ PIC Kepulangan Barang Wajib Orang yg Berbeda dari Keberangkatan
                </div>

                <!-- Red Banner: KEPULANGAN -->
                <div class="rounded-2xl border-2 border-rose-400/60 overflow-hidden shadow-xs">
                    <div class="bg-rose-600 text-white px-4 py-2.5 font-bold text-sm tracking-wider flex items-center justify-between">
                        <span>KEPULANGAN (BARANG KEMBALI KE GUDANG)</span>
                        @if($assignment->return_status === 'verified')
                            <span class="text-xs px-2 py-0.5 rounded bg-white/20 text-white font-bold">✓ TELAH DISAHKAN</span>
                        @endif
                    </div>
                    <div class="p-5 bg-rose-50/30 dark:bg-slate-900/40">
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
                        @else
                            <form action="{{ route('spk.verify-return', $assignment) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama PIC Event Kepulangan *</label>
                                    <input type="text" name="return_pic_event_name" value="{{ old('return_pic_event_name') }}" required placeholder="Wajib beda orang!" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-sm">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: HALAMAN 2 - CHECKLIST BARANG KERJA TEAM -->
    <div x-show="activeTab === 2" class="space-y-4">
        <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-xs">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Checklist Barang Kerja TEAM Omahvisual LED</h2>
                    <p class="text-xs text-slate-500">Kabinet, kabel jumper, panel listrik, ground stake, stage level, dan 13 item toolbox.</p>
                </div>
                <div class="text-xs font-semibold text-slate-500">
                    Klik kotak untuk menandai status keluar dan kembali secara instan.
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @php $no = 1; $currentCat = ''; @endphp
                        @foreach($assignment->checklistItems->where('page_number', 2) as $item)
                            @if($currentCat !== $item->category)
                                @php $currentCat = $item->category; @endphp
                                <tr class="bg-slate-50/80 dark:bg-slate-800/40 font-bold text-slate-900 dark:text-white">
                                    <td colspan="6" class="px-3 py-2 tracking-wider text-[11px] uppercase text-indigo-600 dark:text-indigo-400">
                                        📌 {{ $currentCat }}
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="px-3 py-2 text-center text-slate-400">{{ $no++ }}</td>
                                <td class="px-3 py-2 font-semibold text-slate-900 dark:text-white">{{ $item->item_name }}</td>
                                <td class="px-3 py-2 text-slate-500">{{ $item->item_detail ?? '-' }}</td>
                                <td class="px-3 py-2 text-center font-bold">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="px-3 py-2 text-center bg-indigo-50/30 dark:bg-indigo-950/20">
                                    <input type="checkbox" {{ $item->checked_out ? 'checked' : '' }} onchange="toggleChecklistItem({{ $item->id }}, 'checked_out', this.checked)" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                <td class="px-3 py-2 text-center bg-rose-50/30 dark:bg-rose-950/20">
                                    <input type="checkbox" {{ $item->checked_in ? 'checked' : '' }} onchange="toggleChecklistItem({{ $item->id }}, 'checked_in', this.checked)" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 cursor-pointer">
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
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">CHECKLIST PIC Omahvisual LED</h2>
                    <p class="text-xs text-slate-500">Kit operator laptop/capture card, Novastar VX series, modul spare part, rigging gantung, peralatan tidur.</p>
                </div>
                <div class="text-xs font-semibold text-slate-500">
                    Klik kotak untuk menandai status keluar dan kembali secara instan.
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @php $no = 1; $currentCat = ''; @endphp
                        @foreach($assignment->checklistItems->where('page_number', 3) as $item)
                            @if($currentCat !== $item->category)
                                @php $currentCat = $item->category; @endphp
                                <tr class="bg-slate-50/80 dark:bg-slate-800/40 font-bold text-slate-900 dark:text-white">
                                    <td colspan="6" class="px-3 py-2 tracking-wider text-[11px] uppercase text-indigo-600 dark:text-indigo-400">
                                        📌 {{ $currentCat }}
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="px-3 py-2 text-center text-slate-400">{{ $no++ }}</td>
                                <td class="px-3 py-2 font-semibold text-slate-900 dark:text-white">{{ $item->item_name }}</td>
                                <td class="px-3 py-2 text-slate-500">{{ $item->item_detail ?? '-' }}</td>
                                <td class="px-3 py-2 text-center font-bold">{{ $item->quantity }} {{ $item->unit }}</td>
                                <td class="px-3 py-2 text-center bg-indigo-50/30 dark:bg-indigo-950/20">
                                    <input type="checkbox" {{ $item->checked_out ? 'checked' : '' }} onchange="toggleChecklistItem({{ $item->id }}, 'checked_out', this.checked)" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                <td class="px-3 py-2 text-center bg-rose-50/30 dark:bg-rose-950/20">
                                    <input type="checkbox" {{ $item->checked_in ? 'checked' : '' }} onchange="toggleChecklistItem({{ $item->id }}, 'checked_in', this.checked)" class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 cursor-pointer">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
        // Updated successfully
    })
    .catch(err => {
        console.error('Failed to toggle item', err);
        alert('Gagal mengupdate checklist.');
    });
}
</script>
@endsection

