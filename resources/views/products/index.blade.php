@extends('layouts.app')

@section('title', 'Master Barang')
@section('page_title', 'Katalog Master Produk & Material')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Master Barang &amp; Spesifikasi</h1>
            <p class="text-sm text-slate-400 mt-1">Katalog produk, manajemen stok langsung, dan laporan ketersediaan armada LED</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Export CSV -->
            <a href="{{ route('products.export-csv', request()->query()) }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold uppercase tracking-wider border border-slate-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export CSV (Excel)</span>
            </a>

            <!-- Print Monthly Report -->
            <a href="{{ route('products.print-report', request()->query()) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold uppercase tracking-wider border border-slate-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Laporan Ketersediaan</span>
            </a>

            @can('create product')
            <a href="{{ route('products.create') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Barang Baru</span>
            </a>
            @endcan
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shrink-0 font-bold text-lg">
                📦
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Total Katalog SKU</span>
                <div class="text-xl font-black text-white mt-0.5">{{ $totalSku ?? $products->total() }} <span class="text-xs font-normal text-slate-400">item terdaftar</span></div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0 font-bold text-lg">
                ✅
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Status Aktif Beroperasi</span>
                <div class="text-xl font-black text-emerald-400 mt-0.5">{{ $totalActiveSku ?? $products->total() }} <span class="text-xs font-normal text-slate-400">SKU aktif</span></div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-400 shrink-0 font-bold text-lg">
                🖥️
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Kapasitas Layar Siap Sewa</span>
                <div class="text-xl font-black text-sky-400 mt-0.5">{{ $totalSqmReady ?? 0 }} <span class="text-xs font-normal text-slate-400">m² modul LED</span></div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="space-y-3">
        <!-- Item Type Tabs (Operasional vs Internal) -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-3">
            <a href="{{ route('products.index', array_merge(request()->except('item_type', 'page'))) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request('item_type') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800' }}">
                Semua Barang ({{ $totalSku ?? 0 }})
            </a>
            <a href="{{ route('products.index', array_merge(request()->except('page'), ['item_type' => 'rental'])) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('item_type') === 'rental' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800' }}">
                ✨ Barang Sewa / Operasional ({{ $totalRentalSku ?? 0 }})
            </a>
            <a href="{{ route('products.index', array_merge(request()->except('page'), ['item_type' => 'internal'])) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('item_type') === 'internal' ? 'bg-slate-700 text-white shadow-md shadow-slate-700/30' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800' }}">
                🏢 Aset Internal Kantor / Mess ({{ $totalInternalSku ?? 0 }})
            </a>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md">
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                @if(request('item_type'))
                    <input type="hidden" name="item_type" value="{{ request('item_type') }}">
                @endif
                <!-- Search -->
                <div class="sm:col-span-5 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang, kode SKU, atau seri model..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="sm:col-span-3">
                    <select name="category_id" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
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
                    <select name="tracking_type" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                        <option value="">Semua Mode</option>
                        <option value="individual" {{ request('tracking_type') == 'individual' ? 'selected' : '' }}>Individual (Serial)</option>
                        <option value="quantity" {{ request('tracking_type') == 'quantity' ? 'selected' : '' }}>Kuantitas (Stok)</option>
                    </select>
                </div>

                <!-- Submit Filter -->
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold border border-slate-700 transition-all">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'tracking_type', 'item_type']))
                        <a href="{{ route('products.index') }}" class="p-2.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Reset filter">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Product Table -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-800/70 text-slate-300 uppercase tracking-wider font-semibold text-xs border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Foto & Produk</th>
                        <th class="px-5 py-4">Kategori</th>
                        <th class="px-5 py-4">Mode Lacak</th>
                        <th class="px-5 py-4">Luas / Satuan</th>
                        <th class="px-5 py-4 text-center">Aset / Saldo</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-xl bg-slate-950/70 border border-slate-800 flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($prod->image_path)
                                            <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-1.5 mb-0.5">
                                            <span class="font-mono text-xs font-bold text-indigo-400">{{ $prod->code }}</span>
                                            @if($prod->is_rental_item)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">SEWA / OPERASIONAL</span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-700/60 text-slate-300 border border-slate-600/40">INTERNAL KANTOR</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('products.show', $prod) }}" class="font-semibold text-white hover:text-indigo-300 transition-colors block text-sm">
                                            {{ $prod->name }}
                                        </a>
                                        @if($prod->model_series)
                                            <span class="block text-xs text-slate-400 font-mono mt-0.5">{{ $prod->brand ? $prod->brand . ' • ' : '' }}{{ $prod->model_series }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-300 font-medium">
                                {{ $prod->category->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($prod->tracking_type === 'individual')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/15 text-indigo-400 border border-indigo-500/30">
                                        Per-Unit Serial
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-slate-500/15 text-slate-400 border border-slate-500/30">
                                        Kuantitas Stok
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-300">
                                @if($prod->sqm_per_unit)
                                    <span class="font-semibold text-indigo-400">{{ number_format((float) $prod->sqm_per_unit, 2) }} m²</span>
                                    <span class="text-slate-400 text-xs block">per unit kabinet</span>
                                @else
                                    <span>{{ $prod->unit->name ?? 'Pcs' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($prod->tracking_type === 'individual')
                                    @php
                                        $readyCount = $prod->assets->where('condition', 'available')->where('status', 'in_storage')->count();
                                        $serviceCount = $prod->assets->whereIn('condition', ['in_service', 'damaged'])->count();
                                    @endphp
                                    <div class="font-mono font-bold text-emerald-400 text-sm">
                                        {{ $readyCount }} <span class="text-xs font-normal text-slate-400">/ {{ $prod->assets_count }} unit</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 block">Tersedia {{ $readyCount }} dari {{ $prod->assets_count }}</span>
                                    @if($serviceCount > 0)
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                            {{ $serviceCount }} dalam servis
                                        </span>
                                    @endif
                                @else
                                    @php
                                        $readyQty = (float) $prod->stockBalances->where('condition', 'available')->sum('quantity');
                                        $serviceQty = (float) $prod->stockBalances->where('condition', 'in_service')->sum('quantity');
                                        $totalQty = (float) $prod->stockBalances->sum('quantity');
                                    @endphp
                                    <div class="font-mono font-bold text-emerald-400 text-sm">
                                        {{ $readyQty }} <span class="text-xs font-normal text-slate-400">/ {{ $totalQty }} {{ $prod->unit->symbol ?? 'unit' }}</span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 block">Tersedia {{ $readyQty }} dari {{ $totalQty }}</span>
                                    @if($serviceQty > 0)
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                            {{ $serviceQty }} dalam servis
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('products.show', $prod) }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white inline-block transition-colors" title="Detail">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @can('edit product')
                                <a href="{{ route('products.edit', $prod) }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white inline-block transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                @endcan
                                @if(auth()->user()->can('delete product') || auth()->user()->hasRole(['Superuser', 'Owner', 'Warehouse']))
                                <form action="{{ route('products.destroy', $prod) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus master barang {{ $prod->name }} ({{ $prod->code }})? \n\nCatatan: Jika barang memiliki riwayat transaksi operasional aktif, sistem akan mengamankannya dengan mengalihkan status menjadi Nonaktif.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 border border-rose-500/20 transition-colors" title="Hapus Barang">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                Tidak ada produk yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-5 py-4 border-t border-slate-800">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
