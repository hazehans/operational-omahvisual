@extends('layouts.app')

@section('title', $product->name)
@section('page_title', 'Rincian Master Produk')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Action -->
    <div class="flex items-center justify-between">
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-slate-400 hover:text-white flex items-center gap-1.5 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Master Barang</span>
        </a>

        <a href="{{ route('products.edit', $product) }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold border border-slate-700 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            <span>Edit Barang</span>
        </a>
    </div>

    <!-- Product Profile Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
            
            <div class="flex flex-col sm:flex-row items-start gap-6 flex-1">
                <!-- Foto Produk -->
                <div class="w-32 h-32 md:w-36 md:h-36 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden shadow-inner">
                    @if($product->image_path)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center text-slate-600">
                            <svg class="w-10 h-10 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-[11px] font-medium text-slate-500">Tanpa Foto</span>
                        </div>
                    @endif
                </div>

                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-lg border border-indigo-500/20">
                            SKU: {{ $product->code }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300">
                            {{ $product->category->name ?? 'Kategori Umum' }}
                        </span>
                        @if($product->tracking_type === 'individual')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/15 text-indigo-400 border border-indigo-500/30">
                                Unit Serial Individual
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-500/15 text-slate-400 border border-slate-500/30">
                                Stok Kuantitas
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl md:text-3xl font-extrabold text-white">{{ $product->name }}</h1>
                    @if($product->specification)
                        <p class="text-sm text-slate-300 max-w-2xl leading-relaxed">{{ $product->specification }}</p>
                    @endif
                    @if($product->notes)
                        <div class="p-3 rounded-xl bg-slate-950/40 border border-slate-800 text-xs text-slate-400 max-w-2xl">
                            <span class="font-semibold text-slate-300">Catatan Internal:</span> {{ $product->notes }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Total Quantity Badge -->
            <div class="flex items-center gap-3 shrink-0">
                <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 text-right min-w-40 shadow-inner">
                    <span class="text-xs uppercase tracking-wider font-semibold text-slate-400 block mb-1">Stok Siap Sewa</span>
                    <span class="text-3xl font-extrabold text-emerald-400 font-mono">
                        {{ (float) $totalStockAvailable }}
                    </span>
                    <span class="text-sm text-slate-400 ml-1.5">{{ $product->unit->symbol ?? 'unit' }}</span>
                </div>
            </div>
        </div>

        <!-- Metric Details -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-800/80 text-sm">
            <div>
                <span class="text-slate-400 text-xs block mb-0.5">Brand / Merk:</span>
                <span class="font-semibold text-slate-200">{{ $product->brand ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-xs block mb-0.5">Model / Seri:</span>
                <span class="font-semibold text-slate-200 font-mono">{{ $product->model_series ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-xs block mb-0.5">Luas Meter Persegi:</span>
                <span class="font-semibold text-indigo-400">
                    {{ $product->sqm_per_unit ? $product->sqm_per_unit . ' m² / unit' : 'Bukan Kabinet' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 text-xs block mb-0.5">Kondisi Rusak / Servis:</span>
                <span class="font-semibold text-rose-400">{{ (float) $totalStockDamaged }} rusak</span>
                <span class="text-slate-500 mx-1">/</span>
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
                    <h2 class="text-base font-bold text-white">Daftar Unit Serial Terdaftar ({{ $product->assets->count() }} Unit)</h2>
                    <p class="text-sm text-slate-400">Unit individual yang memiliki ID fisik dan status tersendiri</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-800/70 text-slate-300 uppercase tracking-wider font-semibold text-xs border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-4">Kode Aset Internal</th>
                            <th class="px-5 py-4">Nomor Seri Pabrik</th>
                            <th class="px-5 py-4">Lokasi Penyimpanan</th>
                            <th class="px-5 py-4">Kondisi</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Rincian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($product->assets as $ast)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-4 font-mono font-bold text-indigo-400 text-sm">
                                    {{ $ast->asset_code }}
                                </td>
                                <td class="px-5 py-4 font-mono text-slate-300 text-sm">
                                    {{ $ast->serial_number ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-300 font-medium">
                                    {{ $ast->currentLocation->name ?? 'Gedung Utama' }}
                                </td>
                                <td class="px-5 py-4">
                                    @if($ast->condition === 'available')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                            Bagus / Siap
                                        </span>
                                    @elseif($ast->condition === 'damaged')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                            Rusak
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                            Servis
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-slate-300 font-medium">
                                        {{ $ast->status === 'in_storage' ? 'Di Gudang' : ($ast->status === 'deployed' ? 'Dipakai Event' : $ast->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('assets.show', $ast) }}" class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors">
                                        Buka Kartu &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-slate-400">
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
                    <h2 class="text-base font-bold text-white">Sebaran Saldo Stok per Lokasi</h2>
                    <p class="text-sm text-slate-400">Rincian saldo fisik di lemari dan area gudang</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-800/70 text-slate-300 uppercase tracking-wider font-semibold text-xs border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-4">Lokasi / Lemari</th>
                            <th class="px-5 py-4">Kepemilikan</th>
                            <th class="px-5 py-4">Kondisi Fisik</th>
                            <th class="px-5 py-4 text-right">Kuantitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($product->stockBalances as $bal)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-4 font-semibold text-white">
                                    {{ $bal->location->name ?? 'Gedung Utama' }}
                                </td>
                                <td class="px-5 py-4 capitalize text-slate-300 font-medium">
                                    {{ $bal->ownership === 'company' ? 'Aset Omah Visual' : 'Vendor Rekanan' }}
                                </td>
                                <td class="px-5 py-4">
                                    @if($bal->condition === 'available')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                            Tersedia (Bagus)
                                        </span>
                                    @elseif($bal->condition === 'damaged')
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                            Rusak
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                            Dalam Servis
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right font-mono font-bold text-white text-base">
                                    {{ (float) $bal->quantity }} <span class="text-xs text-slate-400 font-normal">{{ $product->unit->symbol ?? 'unit' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-slate-400">
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
