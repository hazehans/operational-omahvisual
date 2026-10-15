@extends('layouts.app')

@section('title', 'Daftar Unit Aset Terlacak')
@section('page_title', 'Aset Individual & Serial')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Daftar Unit Aset Serial</h1>
            <p class="text-sm text-slate-400 mt-1">Pelacakan unit bernomor seri unik: 25 Processor Novastar, TV, & Toolbox</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-indigo-500/15 text-indigo-400 border border-indigo-500/30">
                Total Terdata: {{ $assets->total() }} Unit
            </span>
            @if(!auth()->user()->hasRole('Teknisi Servis'))
                <a href="{{ route('assets.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-lg shadow-indigo-600/30">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tambah Unit Serial</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md">
        <form method="GET" action="{{ route('assets.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <!-- Search -->
            <div class="sm:col-span-4 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode aset, serial pabrik, produk..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Condition Filter -->
            <div class="sm:col-span-2">
                <select name="condition" class="w-full px-3 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                    <option value="">Semua Kondisi</option>
                    <option value="available" {{ request('condition') == 'available' ? 'selected' : '' }}>Siap / Bagus</option>
                    <option value="damaged" {{ request('condition') == 'damaged' ? 'selected' : '' }}>Rusak</option>
                    <option value="in_service" {{ request('condition') == 'in_service' ? 'selected' : '' }}>Servis</option>
                </select>
            </div>

            <!-- Location Filter -->
            <div class="sm:col-span-3">
                <select name="location_id" class="w-full px-3 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="sm:col-span-2">
                <select name="status" class="w-full px-3 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                    <option value="">Semua Status</option>
                    <option value="in_storage" {{ request('status') == 'in_storage' ? 'selected' : '' }}>Di Gudang</option>
                    <option value="deployed" {{ request('status') == 'deployed' ? 'selected' : '' }}>Dipakai Event</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>

            <!-- Submit Filter -->
            <div class="sm:col-span-1 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold border border-slate-700 transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'condition', 'location_id', 'status']))
                    <a href="{{ route('assets.index') }}" class="p-2.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Assets Grid / Table -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-800/70 text-slate-300 uppercase tracking-wider font-semibold text-xs border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Kode Aset</th>
                        <th class="px-5 py-4">Perangkat & Model</th>
                        <th class="px-5 py-4">Nomor Seri Pabrik</th>
                        <th class="px-5 py-4">Lokasi Penyimpanan</th>
                        <th class="px-5 py-4">Kondisi Fisik</th>
                        <th class="px-5 py-4">Status Penggunaan</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($assets as $ast)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('assets.show', $ast) }}" class="font-mono font-bold text-indigo-400 hover:text-indigo-300 transition-colors">
                                    {{ $ast->asset_code }}
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-white text-sm">{{ $ast->product->name }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $ast->product->model_series ?? $ast->product->brand }}</div>
                            </td>
                            <td class="px-5 py-4 font-mono text-slate-300">
                                {{ $ast->serial_number ?? ($ast->barcode ?? '-') }}
                            </td>
                            <td class="px-5 py-4 text-slate-300 font-medium">
                                {{ $ast->currentLocation->name ?? 'Gedung Utama' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($ast->condition === 'available')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        Bagus / Siap
                                    </span>
                                @elseif($ast->condition === 'damaged')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                        Rusak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/30">
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
                                <a href="{{ route('assets.show', $ast) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold inline-block transition-colors">
                                    Rincian Unit &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                Tidak ada unit aset yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
            <div class="px-5 py-4 border-t border-slate-800">
                {{ $assets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
