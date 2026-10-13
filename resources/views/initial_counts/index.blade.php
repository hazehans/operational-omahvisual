@extends('layouts.app')

@section('title', 'Sahkan Stok Awal')
@section('page_title', 'Sesi Pengesahan Saldo Awal Gudang')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-md border border-indigo-500/20">
                    {{ $session->count_number }}
                </span>
                @if($session->status === 'verified')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Disahkan Resmi (Verified)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        Menunggu Pengesahan Warehouse
                    </span>
                @endif
            </div>

            <h1 class="text-xl md:text-2xl font-extrabold text-white">
                {{ $session->title }}
            </h1>
            <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                {{ $session->notes }}
            </p>

            <div class="flex flex-wrap items-center gap-4 mt-4 text-xs text-slate-400">
                <div>
                    Pendata: <span class="font-semibold text-slate-200">{{ $session->conductor?->name ?? 'Tim Warehouse' }}</span>
                </div>
                <div>•</div>
                <div>
                    Lokasi: <span class="font-semibold text-slate-200">{{ $session->location?->name ?? 'Gedung Utama' }}</span>
                </div>
                @if($session->verified_by)
                    <div>•</div>
                    <div>
                        Disahkan oleh: <span class="font-semibold text-emerald-400">{{ $session->verifier?->name }}</span> ({{ $session->finalized_at?->format('d/m/Y H:i') }})
                    </div>
                @endif
            </div>
        </div>

        <!-- Tombol Pengesahan & Simulasi -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @if($session->status !== 'verified')
                <form action="{{ route('initial-counts.verify', $session) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin data fisik ini sudah valid dan siap disahkan sebagai saldo awal resmi operasional Omah Visual LED?');">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-linear-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Sahkan Saldo Stok Awal (Verifikasi Warehouse)</span>
                    </button>
                </form>
            @else
                <div class="px-5 py-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Saldo Telah Aktif & Diposting Resmi</span>
                </div>

                <!-- Tombol Simulasi Ulang (Demo Feature) -->
                <form action="{{ route('initial-counts.reset-simulation', $session) }}" method="POST" onsubmit="return confirm('Reset status stok awal ke mode simulasi (Pending) agar Anda dapat mencoba alur tombol pengesahan kembali?');">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-semibold flex items-center justify-center gap-2 transition-all shadow-md active:scale-95" title="Gunakan ini untuk mencoba alur verifikasi berkali-kali">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Uji Ulang Simulasi (Reset ke Pending)</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-bold text-white">Rincian Fisik Barang Excel yang Diverifikasi ({{ $items->count() }} Item)</h2>
            <span class="text-xs text-slate-400">Kombinasi Kondisi & Kepemilikan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Kode & Nama Barang</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Kondisi Fisik</th>
                        <th class="px-5 py-3.5">Kepemilikan</th>
                        <th class="px-5 py-3.5 text-right">Fisik Terhitung</th>
                        <th class="px-5 py-3.5 text-center">Status Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-indigo-400 font-bold block">{{ $item->product->code }}</span>
                                <span class="font-semibold text-white">{{ $item->product->name }}</span>
                                @if($item->product->sqm_per_unit)
                                    <span class="block text-[11px] text-slate-400">Luas: {{ $item->product->sqm_per_unit }} m²/unit</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-300">
                                {{ $item->product->category->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($item->condition === 'available')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Tersedia (Bagus)
                                    </span>
                                @elseif($item->condition === 'damaged')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Rusak
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Dalam Servis
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-slate-300 capitalize font-medium">
                                    {{ $item->ownership === 'company' ? 'Aset Omah Visual' : 'Vendor Rekanan' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-white text-sm">
                                {{ (float) $item->physical_quantity }} <span class="text-xs text-slate-400 font-normal">{{ $item->product->unit->symbol ?? 'unit' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($item->is_verified)
                                    <span class="inline-flex items-center gap-1 text-emerald-400 font-medium">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Sah</span>
                                    </span>
                                @else
                                    <span class="text-amber-400">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                Tidak ada item dalam sesi pendataan fisik ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

