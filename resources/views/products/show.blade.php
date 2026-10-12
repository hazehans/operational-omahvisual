@extends('layouts.app')

@section('title', $product->name)
@section('page_title', 'Rincian Master Produk')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Action -->
    <div class="flex items-center justify-between">
        <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Master Barang</span>
        </a>

        <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            <span>Edit Barang</span>
        </a>
    </div>

    <!-- Product Profile Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-md border border-indigo-500/20">
                        {{ $product->code }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300">
                        {{ $product->category->name ?? 'Kategori Umum' }}
                    </span>
                    @if($product->tracking_type === 'individual')
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            Unit Serial Individual
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                            Stok Kuantitas
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl font-extrabold text-white">{{ $product->name }}</h1>
                @if($product->specification)
                    <p class="text-xs text-slate-400 mt-1 max-w-2xl">{{ $product->specification }}</p>
                @endif
            </div>

            <!-- Total Quantity Badge -->
            <div class="flex items-center gap-3">
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 text-right min-w-35">
                    <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Stok Siap Sewa</span>
                    <span class="text-2xl font-extrabold text-emerald-400 font-mono">
                        {{ (float) $totalStockAvailable }}
                    </span>
                    <span class="text-xs text-slate-400 ml-1">{{ $product->unit->symbol ?? 'unit' }}</span>
                </div>
            </div>
        </div>

        <!-- Metric Details -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-800/80 text-xs">
            <div>
                <span class="text-slate-500 block">Brand / Merk:</span>
                <span class="font-semibold text-slate-200">{{ $product->brand ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Model / Seri:</span>
                <span class="font-semibold text-slate-200 font-mono">{{ $product->model_series ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">Luas Meter Persegi:</span>
                <span class="font-semibold text-indigo-400">
                    {{ $product->sqm_per_unit ? $product->sqm_per_unit . ' m² / unit' : 'Bukan Kabinet' }}
                </span>
            </div>
            <div>
                <span class="text-slate-500 block">Kondisi Rusak / Servis:</span>
                <span class="font-semibold text-rose-400">{{ (float) $totalStockDamaged }} rusak</span>
                <span class="text-slate-500">/</span>
                <span class="font-semibold text-amber-400">{{ (float) $totalStockInService }} servis</span>
            </div>
        </div>
    </div>

    <!-- Conditional View: Assets vs Location Balances -->
    @if($product->tracking_type === 'individual')
        <!-- Individual Assets List -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white">Daftar Unit Serial Terdaftar ({{ $product->assets->count() }} Unit)</h2>
                    <p class="text-xs text-slate-400">Unit individual yang memiliki ID fisik tersendiri</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">Kode Aset Internal</th>
                            <th class="px-5 py-3.5">Nomor Seri Pabrik</th>
                            <th class="px-5 py-3.5">Lokasi Penyimpanan</th>
                            <th class="px-5 py-3.5">Kondisi</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Rincian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($product->assets as $ast)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-bold text-indigo-400">
                                    {{ $ast->asset_code }}
                                </td>
                                <td class="px-5 py-3.5 font-mono text-slate-400">
                                    {{ $ast->serial_number ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-300">
                                    {{ $ast->currentLocation->name ?? 'Gedung Utama' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($ast->condition === 'available')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Bagus / Siap
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
                                <td class="px-5 py-3.5">
                                    <span class="text-slate-400">
                                        {{ $ast->status === 'in_storage' ? 'Di Gudang' : ($ast->status === 'deployed' ? 'Dipakai Event' : $ast->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('assets.show', $ast) }}" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                                        Buka Kartu &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                    Belum ada unit individual yang didaftarkan untuk produk ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Stock Balances across Locations -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white">Sebaran Saldo Stok per Lokasi</h2>
                    <p class="text-xs text-slate-400">Rincian saldo fisik di lemari dan area gudang</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">Lokasi / Lemari</th>
                            <th class="px-5 py-3.5">Kepemilikan</th>
                            <th class="px-5 py-3.5">Kondisi Fisik</th>
                            <th class="px-5 py-3.5 text-right">Kuantitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($product->stockBalances as $bal)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-5 py-3.5 font-semibold text-white">
                                    {{ $bal->location->name ?? 'Gedung Utama' }}
                                </td>
                                <td class="px-5 py-3.5 capitalize text-slate-300">
                                    {{ $bal->ownership === 'company' ? 'Aset Omah Visual' : 'Vendor Rekanan' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($bal->condition === 'available')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Tersedia (Bagus)
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
                                    {{ (float) $bal->quantity }} <span class="text-xs text-slate-400 font-normal">{{ $product->unit->symbol ?? 'unit' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-slate-500">
                                    Belum ada saldo stok yang tercatat untuk produk ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

