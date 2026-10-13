@extends('layouts.app')

@section('title', 'Lokasi & Lemari')
@section('page_title', 'Visualisasi Titik Lokasi & Lemari Gudang')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-white tracking-tight">Titik Lokasi & Lemari Gudang</h1>
            <p class="text-xs md:text-sm text-slate-400 mt-1">Pemetaan fisik zona penyimpanan barang di Gedung Utama Omah Visual LED lengkap dengan foto visual.</p>
        </div>

        @can('manage locations')
        <div>
            <a href="{{ route('locations.create') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Titik Lokasi</span>
            </a>
        </div>
        @endcan
    </div>

    <!-- Locations Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($locations as $loc)
            <div class="rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl hover:border-indigo-500/40 transition-all flex flex-col justify-between overflow-hidden group">
                
                <!-- Foto Visual Lokasi (Top Card) -->
                <div class="relative w-full h-44 bg-slate-950 overflow-hidden border-b border-slate-800/80">
                    @if($loc->image_url)
                        <img src="{{ $loc->image_url }}" alt="{{ $loc->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-linear-to-t from-slate-950/80 via-transparent to-transparent"></div>
                    @else
                        <!-- Visual Fallback Placeholder Sesuai Tipe Lokasi -->
                        <div class="w-full h-full flex flex-col items-center justify-center bg-linear-to-br from-slate-900 via-indigo-950/30 to-slate-950 p-6 text-center">
                            @if($loc->type === 'cabinet')
                                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center mb-2 shadow-inner">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @elseif($loc->type === 'staging_area')
                                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center mb-2 shadow-inner">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4" />
                                    </svg>
                                </div>
                            @elseif($loc->type === 'locker')
                                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mb-2 shadow-inner">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                    </svg>
                                </div>
                            @else
                                <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            @endif
                            <span class="text-[11px] font-medium text-slate-500">Belum ada foto fisik</span>
                        </div>
                    @endif

                    <!-- Badges overlay -->
                    <div class="absolute top-3 left-3 flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-white bg-slate-950/80 backdrop-blur-md px-2.5 py-1 rounded-md border border-slate-700/80 shadow-md">
                            {{ $loc->code }}
                        </span>
                    </div>

                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-950/80 backdrop-blur-md text-indigo-300 border border-slate-700/80 capitalize">
                            Zona {{ $loc->type }}
                        </span>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h2 class="text-base font-bold text-white group-hover:text-indigo-300 transition-colors">
                            {{ $loc->name }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-1.5 leading-relaxed line-clamp-2">
                            {{ $loc->description ?? 'Titik penyimpanan barang operasional Omah Visual.' }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs">
                        <div>
                            <span class="text-slate-500 block text-[11px]">Kapasitas Muatan:</span>
                            <span class="font-bold text-white font-mono">{{ $loc->assets_count }} unit aset</span>
                            <span class="text-slate-400 text-[11px] block">({{ $loc->stock_balances_count }} item stok)</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @can('manage locations')
                            <a href="{{ route('locations.edit', $loc) }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all" title="Edit / Ganti Foto Lokasi">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            @endcan

                            <a href="{{ route('locations.show', $loc) }}" class="px-3.5 py-2 rounded-xl bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white font-semibold transition-all flex items-center gap-1.5">
                                <span>Buka Isi</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
</div>
@endsection
