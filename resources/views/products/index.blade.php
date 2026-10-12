@extends('layouts.app')

@section('title', 'Master Barang')
@section('page_title', 'Katalog Master Produk & Material')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">Master Barang & Spesifikasi</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola data induk kabinet LED, processor, rigging, kabel, dan sparepart</p>
        </div>
        <a href="{{ route('products.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Barang Baru</span>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <!-- Search -->
            <div class="sm:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang, kode, atau seri model..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="sm:col-span-3">
                <select name="category_id" class="w-full px-3 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500 transition-all">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tracking Type Filter -->
            <div class="sm:col-span-2">
                <select name="tracking_type" class="w-full px-3 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500 transition-all">
                    <option value="">Semua Mode</option>
                    <option value="individual" {{ request('tracking_type') == 'individual' ? 'selected' : '' }}>Individual (Serial)</option>
                    <option value="quantity" {{ request('tracking_type') == 'quantity' ? 'selected' : '' }}>Kuantitas (Stok)</option>
                </select>
            </div>

            <!-- Submit Filter -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'category_id', 'tracking_type']))
                    <a href="{{ route('products.index') }}" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Product Table -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Kode & Nama Produk</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Mode Lacak</th>
                        <th class="px-5 py-3.5">Luas / Satuan</th>
                        <th class="px-5 py-3.5 text-center">Aset / Saldo</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-indigo-400 font-bold block">{{ $prod->code }}</span>
                                <a href="{{ route('products.show', $prod) }}" class="font-semibold text-white hover:text-indigo-300">
                                    {{ $prod->name }}
                                </a>
                                @if($prod->model_series)
                                    <span class="block text-[11px] text-slate-500 font-mono">{{ $prod->model_series }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-300">
                                {{ $prod->category->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($prod->tracking_type === 'individual')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                        Per-Unit Serial
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        Kuantitas Stok
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-300">
                                @if($prod->sqm_per_unit)
                                    <span class="font-semibold text-indigo-400">{{ $prod->sqm_per_unit }} m²</span>
                                    <span class="text-slate-500 text-[10px] block">per unit kabinet</span>
                                @else
                                    <span>{{ $prod->unit->name ?? 'Pcs' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($prod->tracking_type === 'individual')
                                    <span class="font-mono font-bold text-white">{{ $prod->assets_count }}</span>
                                    <span class="text-[10px] text-slate-500 block">unit terdaftar</span>
                                @else
                                    <span class="font-mono font-bold text-white">
                                        {{ (float) $prod->stockBalances->where('condition', 'available')->sum('quantity') }}
                                    </span>
                                    <span class="text-[10px] text-emerald-400 block">siap sewa</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('products.show', $prod) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white inline-block" title="Detail">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('products.edit', $prod) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white inline-block" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                Tidak ada produk yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-5 py-3 border-t border-slate-800">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

