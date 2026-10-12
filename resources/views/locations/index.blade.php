@extends('layouts.app')

@section('title', 'Lokasi & Lemari')
@section('page_title', 'Visualisasi Lemari Penyimpanan Gudang Utama')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div>
        <h1 class="text-xl font-bold text-white">Titik Lokasi & Lemari Gudang</h1>
        <p class="text-xs text-slate-400 mt-0.5">Pemetaan fisik 5 zona penyimpanan barang di Gedung Utama Omah Visual LED</p>
    </div>

    <!-- Locations Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($locations as $loc)
            <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl hover:border-indigo-500/40 transition-all flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-md border border-indigo-500/20">
                            {{ $loc->code }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300 capitalize">
                            Zona {{ $loc->type }}
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-white group-hover:text-indigo-300 transition-colors">
                        {{ $loc->name }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        {{ $loc->description ?? 'Titik penyimpanan barang operasional Omah Visual.' }}
                    </p>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-800/80 flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-500 block">Kapasitas Isi:</span>
                        <span class="font-bold text-white font-mono">{{ $loc->assets_count }} unit aset</span>
                        <span class="text-slate-400 text-[11px] block">({{ $loc->stock_balances_count }} item stok)</span>
                    </div>

                    <a href="{{ route('locations.show', $loc) }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-indigo-600 text-slate-200 hover:text-white font-semibold transition-all">
                        Buka Isi Lemari &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

