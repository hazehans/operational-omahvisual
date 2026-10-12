@extends('layouts.app')

@section('title', 'Superuser Database Manager')
@section('page_title', 'Raw Database Explorer & CRUD')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20 mb-2">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                Area Khusus Superuser (Raw Data Control)
            </div>
            <h1 class="text-2xl font-bold text-white">Database Explorer & CRUD Manager</h1>
            <p class="text-sm text-slate-400 mt-1">
                Akses level rendah setara Django Admin untuk inspeksi, modifikasi langsung, dan perbaikan data mentah PostgreSQL.
            </p>
        </div>
        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 text-xs text-slate-400 max-w-xs self-start sm:self-auto">
            <span class="font-bold text-slate-300">Catatan:</span> Seluruh aksi insert, update, dan delete di sini dicatat otomatis pada audit trail.
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-sm font-semibold text-emerald-300 flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tables Grouped by Category -->
    @php
        $grouped = collect($tables)->groupBy('category');
    @endphp

    <div class="space-y-8">
        @foreach($grouped as $category => $items)
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-200 uppercase tracking-wider text-xs">{{ $category }}</h2>
                    <div class="flex-1 h-px bg-slate-800"></div>
                    <span class="text-xs text-slate-500 font-mono">{{ count($items) }} Tabel</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($items as $item)
                        @php
                            $actualTable = $item['table'];
                        @endphp
                        <a href="{{ route('superuser.database.show', $actualTable) }}"
                           class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-indigo-500/60 hover:bg-slate-800/40 transition-all flex flex-col justify-between group shadow-md">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="font-mono text-xs font-bold text-indigo-400 group-hover:text-indigo-300 transition-colors">
                                        {{ $actualTable }}
                                    </span>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700/60">
                                        {{ number_format($item['count']) }} baris
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-white group-hover:text-indigo-200 transition-colors">
                                    {{ $item['name'] }}
                                </h3>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $item['description'] }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs font-semibold text-slate-400 group-hover:text-indigo-400 transition-colors">
                                <span>Buka Tabel Mentah</span>
                                <span>&rarr;</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

