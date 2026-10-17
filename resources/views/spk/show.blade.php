@extends('layouts.app')

@section('title', 'SPK ' . $assignment->spk_number . ' - Omah Visual')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 1,
    showAssignModal: false,
    showQcModal: false,
    showAddItemModal: false,
    addItemPage: 2,
    filterOnlyQty: false,
    showEditItemModal: false,
    editItem: { id: null, name: '', detail: '', qty: 1, unit: 'Pcs' }
}">
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

                <!-- Right: Crew & Vehicle -->
                <div class="space-y-4">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-2 text-xs">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px]">Penugasan Crew Lapangan:</span>
                            <span class="text-[11px] text-slate-400">PIC: <strong>{{ $assignment->crew_pic_event_name ?? '-' }}</strong></span>
                        </div>
                        @for($i = 1; $i <= 7; $i++)
                            @php $field = "crew_{$i}_name"; @endphp
                            <div class="flex justify-between py-0.5 border-b border-slate-200/50 dark:border-slate-700/50">
                                <span class="text-slate-500 font-semibold">Crew {{ $i }}:</span>
                                <span class="font-medium text-slate-800 dark:text-slate-200">{{ $assignment->$field ?? '-' }}</span>
                            </div>
                        @endfor
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/80 space-y-2 text-xs">
                        <div class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] mb-2">Logistik Kendaraan & Kelengkapan:</div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>Kendaraan: <span class="font-bold text-slate-900 dark:text-white">{{ $assignment->vehicle_name ?? 'TRAGA 1' }}</span></div>
                            <div>E-Toll: <span class="font-semibold {{ $assignment->has_etoll ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $assignment->has_etoll ? '✓ Siap' : '✗ Belum' }}</span></div>
                            <div>Kunci Ban: <span class="font-semibold {{ $assignment->has_kunci_ban ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $assignment->has_kunci_ban ? '✓ Siap' : '✗ Belum' }}</span></div>
                            <div>STNK: <span class="font-semibold {{ $assignment->has_stnk ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $assignment->has_stnk ? '✓ Siap' : '✗ Belum' }}</span></div>
                            <div>Kartu KIR: <span class="font-semibold {{ $assignment->has_kir ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $assignment->has_kir ? '✓ Siap' : '✗ Belum' }}</span></div>
                            <div>Barcode BBM: <span class="font-semibold {{ $assignment->has_barcode_bbm ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $assignment->has_barcode_bbm ? '✓ Siap' : '✗ Belum' }}</span></div>
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
                    <button type="button" @click="filterOnlyQty = !filterOnlyQty" :class="filterOnlyQty ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-300 dark:border-slate-700">
                        <span x-text="filterOnlyQty ? '✓ Filter: Hanya QTY > 0' : '🔍 Filter: QTY > 0'"></span>
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
                                    <span class="cursor-pointer hover:text-indigo-600" @click="editItem = { id: {{ $item->id }}, name: '{{ addslashes($item->item_name) }}', detail: '{{ addslashes($item->item_detail ?? '') }}', qty: {{ $item->quantity }}, unit: '{{ addslashes($item->unit) }}' }; showEditItemModal = true" title="Klik untuk ubah QTY">
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
                                            <button type="button" @click="editItem = { id: {{ $item->id }}, name: '{{ addslashes($item->item_name) }}', detail: '{{ addslashes($item->item_detail ?? '') }}', qty: {{ $item->quantity }}, unit: '{{ addslashes($item->unit) }}' }; showEditItemModal = true" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400" title="Edit QTY & Detail">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('spk.items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus item {{ addslashes($item->item_name) }} dari checklist SPK?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded hover:bg-rose-100 dark:hover:bg-rose-900/40 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400" title="Hapus Item">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <button type="button" onclick="editItemNote({{ $item->id }})" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="Tambah / Edit Catatan">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
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
                    <button type="button" @click="filterOnlyQty = !filterOnlyQty" :class="filterOnlyQty ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-300 dark:border-slate-700">
                        <span x-text="filterOnlyQty ? '✓ Filter: Hanya QTY > 0' : '🔍 Filter: QTY > 0'"></span>
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
                                    <span class="cursor-pointer hover:text-indigo-600" @click="editItem = { id: {{ $item->id }}, name: '{{ addslashes($item->item_name) }}', detail: '{{ addslashes($item->item_detail ?? '') }}', qty: {{ $item->quantity }}, unit: '{{ addslashes($item->unit) }}' }; showEditItemModal = true" title="Klik untuk ubah QTY">
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
                                            <button type="button" @click="editItem = { id: {{ $item->id }}, name: '{{ addslashes($item->item_name) }}', detail: '{{ addslashes($item->item_detail ?? '') }}', qty: {{ $item->quantity }}, unit: '{{ addslashes($item->unit) }}' }; showEditItemModal = true" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400" title="Edit QTY & Detail">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('spk.items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus item {{ addslashes($item->item_name) }} dari checklist SPK?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded hover:bg-rose-100 dark:hover:bg-rose-900/40 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400" title="Hapus Item">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <button type="button" onclick="editItemNote({{ $item->id }})" class="p-1 rounded hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="Tambah / Edit Catatan">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
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
    <div x-show="showAddItemModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showAddItemModal" @click="showAddItemModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-300 dark:border-slate-700">
                <form action="{{ route('spk.items.store', $assignment) }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Barang ke SPK</h3>
                                <p class="text-xs text-slate-500">Masukkan barang tambahan sesuai kebutuhan spesifik event.</p>
                            </div>
                            <button type="button" @click="showAddItemModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
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
                            <input type="text" name="category" list="checklist_categories" required placeholder="Contoh: KABEL & DISTRIBUSI, TOOLBOX, RIGGING, dll." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                            <datalist id="checklist_categories">
                                <option value="KABINET LED & HARDCASE">
                                <option value="KABEL POWER & DATA JUMPER">
                                <option value="PANEL LISTRIK & DISTRIBUSI">
                                <option value="GROUND STAKE & TRUSS">
                                <option value="STAGE LEVEL & LEVELING">
                                <option value="13 ITEM TOOLBOX TEAM">
                                <option value="VIDEO PROCESSOR & SENDING CARD">
                                <option value="OPERATOR KIT & LAPTOP">
                                <option value="SPARE PARTS & MODULE CADANGAN">
                                <option value="PERALATAN TIDUR & CAMPING KRU">
                            </datalist>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Barang Bawaan *</label>
                            <input type="text" name="item_name" required placeholder="Nama barang bawaan..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Jumlah (QTY) *</label>
                                <input type="number" name="quantity" min="0" value="1" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Satuan *</label>
                                <input type="text" name="unit" list="unit_options" value="Pcs" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                                <datalist id="unit_options">
                                    <option value="Pcs">
                                    <option value="Box">
                                    <option value="Unit">
                                    <option value="Roll">
                                    <option value="Batang">
                                    <option value="Set">
                                    <option value="Meter">
                                </datalist>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Detail / Keterangan Spesifik (Opsional)</label>
                            <input type="text" name="item_detail" placeholder="Contoh: Panjang 15m, Warna Biru, Cadangan..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showAddItemModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md">
                            + Tambah ke Checklist
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 4: EDIT ITEM CHECKLIST (showEditItemModal) -->
    <div x-show="showEditItemModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showEditItemModal" @click="showEditItemModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-300 dark:border-slate-700">
                <form :action="'/spk/items/' + editItem.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Item Checklist</h3>
                                <p class="text-xs text-slate-500">Sesuaikan kuantitas bawaan atau keterangan item.</p>
                            </div>
                            <button type="button" @click="showEditItemModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Barang *</label>
                            <input type="text" name="item_name" x-model="editItem.name" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
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
                            <input type="text" name="item_detail" x-model="editItem.detail" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showEditItemModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md">
                            Simpan Perubahan Item
                        </button>
                    </div>
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

document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.setupImageCropper === 'function') {
        window.setupImageCropper('qc_photo_input', 'qc_photo_preview', 'qc_photo_placeholder', 16/9, 'crop_qc_photo_btn');
    }
});
</script>
@endsection

