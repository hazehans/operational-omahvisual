@extends('layouts.app')

@section('title', $location->name)
@section('page_title', 'Isi Penyimpanan — ' . $location->name)

@section('content')
<div class="space-y-6">

    <!-- Top Back -->
    <a href="{{ route('locations.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Daftar Lokasi & Lemari</span>
    </a>

    <!-- Location Header Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-md border border-indigo-500/20">
                        {{ $location->code }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300 capitalize">
                        Area {{ $location->type }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-white">{{ $location->name }}</h1>
                <p class="text-xs text-slate-400 mt-1 max-w-2xl">{{ $location->description }}</p>
            </div>

            <div class="flex items-center gap-4 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 text-center min-w-[100px]">
                    <span class="text-slate-400 block text-[10px] uppercase">Unit Aset</span>
                    <span class="text-xl font-bold text-white font-mono">{{ $location->assets->count() }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 text-center min-w-[100px]">
                    <span class="text-slate-400 block text-[10px] uppercase">Item Stok</span>
                    <span class="text-xl font-bold text-white font-mono">{{ $location->stockBalances->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Assets in this location -->
    @if($location->assets->count() > 0)
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
            <div class="px-6 py-4 border-b border-slate-800">
                <h2 class="text-sm font-bold text-white">Unit Aset Individual di Titik Ini ({{ $location->assets->count() }} Unit)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">Kode Aset</th>
                            <th class="px-5 py-3.5">Nama Perangkat</th>
                            <th class="px-5 py-3.5">Kondisi Fisik</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($location->assets as $ast)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-5 py-3.5 font-mono font-bold text-indigo-400">
                                    {{ $ast->asset_code }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-white">
                                    {{ $ast->product->name }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($ast->condition === 'available')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Bagus
                                        </span>
                                    @elseif($ast->condition === 'damaged')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            Rusak
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Servis
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-400">
                                    {{ $ast->status === 'in_storage' ? 'Di Gudang' : $ast->status }}
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('assets.show', $ast) }}" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                                        Rincian &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- 2. Stock balances in this location -->
    @if($location->stockBalances->count() > 0)
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
            <div class="px-6 py-4 border-b border-slate-800">
                <h2 class="text-sm font-bold text-white">Saldo Stok Kuantitas di Titik Ini ({{ $location->stockBalances->count() }} Jenis)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">Kode Barang</th>
                            <th class="px-5 py-3.5">Nama Barang</th>
                            <th class="px-5 py-3.5">Kondisi Fisik</th>
                            <th class="px-5 py-3.5 text-right">Kuantitas Tersimpan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($location->stockBalances as $bal)
                            <tr class="hover:bg-slate-800/30">
                                <td class="px-5 py-3.5 font-mono font-bold text-indigo-400">
                                    {{ $bal->product->code }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-white">
                                    {{ $bal->product->name }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($bal->condition === 'available')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Bagus (Tersedia)
                                        </span>
                                    @elseif($bal->condition === 'damaged')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            Rusak
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Dalam Servis
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right font-mono font-bold text-white text-sm">
                                    {{ (float) $bal->quantity }} <span class="text-xs text-slate-400 font-normal">{{ $bal->product->unit->symbol ?? 'unit' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

