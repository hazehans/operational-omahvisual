@extends('layouts.app')

@section('title', 'Tabel: ' . $table)
@section('page_title', 'Eksplorasi Mentah: ' . $table)

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('superuser.database.index') }}" class="text-sm font-semibold text-slate-400 hover:text-white flex items-center gap-1.5 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Katalog Tabel DB</span>
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('superuser.database.create', $table) }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Baris Baru</span>
            </a>
        </div>
    </div>

    <!-- Table Header Info Card -->
    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-mono text-sm font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-0.5 rounded border border-indigo-500/20">
                        {{ $table }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-800 text-slate-300">
                        {{ $meta['category'] }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-white">{{ $meta['name'] }}</h1>
                <p class="text-sm text-slate-400 mt-0.5">{{ $meta['description'] }}</p>
            </div>

            <div class="flex items-center gap-3 text-right">
                <div class="px-4 py-2.5 rounded-xl bg-slate-950/70 border border-slate-800">
                    <span class="text-xs text-slate-400 block">Total Data Terfilter</span>
                    <span class="text-xl font-extrabold text-white font-mono">{{ number_format($rows->total()) }}</span>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mt-6 pt-5 border-t border-slate-800/80">
            <form method="GET" action="{{ route('superuser.database.show', $table) }}" class="flex items-center gap-3">
                <div class="relative flex-1">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari string atau ID di seluruh kolom tabel {{ $table }}..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950/70 border border-slate-800 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold border border-slate-700 transition-all">
                    Cari
                </button>
                @if(request()->filled('q'))
                    <a href="{{ route('superuser.database.show', $table) }}" class="p-2.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-sm font-semibold text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    <!-- Raw Data Grid -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-slate-800/80 text-slate-300 uppercase tracking-wider font-semibold border-b border-slate-800 sticky top-0 z-10 backdrop-blur-md">
                    <tr>
                        <th class="px-4 py-3 text-center w-20">Aksi</th>
                        @foreach($columns as $col)
                            <th class="px-4 py-3">
                                <a href="{{ route('superuser.database.show', array_merge(['table' => $table], request()->query(), ['sort' => $col['name'], 'dir' => ($sortBy === $col['name'] && $sortDir === 'asc') ? 'desc' : 'asc'])) }}"
                                   class="inline-flex items-center gap-1 hover:text-white transition-colors">
                                    <span>{{ $col['name'] }}</span>
                                    <span class="text-[10px] text-slate-500 lowercase font-normal">({{ $col['type_name'] }})</span>
                                    @if($sortBy === $col['name'])
                                        <span class="text-indigo-400 font-bold">{{ $sortDir === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    @forelse($rows as $row)
                        @php
                            $rowArray = (array) $row;
                            $rowId = $rowArray['id'] ?? null;
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-4 py-2.5 text-center whitespace-nowrap font-sans">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($rowId !== null)
                                        <a href="{{ route('superuser.database.edit', ['table' => $table, 'id' => $rowId]) }}"
                                           class="p-1.5 rounded-lg bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 hover:text-white transition-colors" title="Edit Baris">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>

                                        <form action="{{ route('superuser.database.destroy', ['table' => $table, 'id' => $rowId]) }}" method="POST"
                                              onsubmit="return confirm('PERINGATAN SUPERUSER:\nApakah Anda yakin ingin menghapus data baris ID #{{ $rowId }} dari tabel [{{ $table }}]? Tindakan ini tidak dapat dibatalkan.');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 hover:text-white transition-colors" title="Hapus Baris">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-600 text-[10px]">No PK</span>
                                    @endif
                                </div>
                            </td>
                            @foreach($columns as $col)
                                @php
                                    $val = $rowArray[$col['name']] ?? null;
                                @endphp
                                <td class="px-4 py-2.5 max-w-xs truncate">
                                    @if(is_null($val))
                                        <span class="text-slate-600 italic">NULL</span>
                                    @elseif(is_bool($val))
                                        <span class="{{ $val ? 'text-emerald-400' : 'text-rose-400' }} font-bold">
                                            {{ $val ? 'true' : 'false' }}
                                        </span>
                                    @elseif(is_array($val) || is_object($val))
                                        <span class="text-amber-300">{{ json_encode($val) }}</span>
                                    @elseif($col['name'] === 'id')
                                        <span class="font-bold text-indigo-400">#{{ $val }}</span>
                                    @else
                                        <span class="text-slate-200">{{ Str::limit((string) $val, 40) }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="px-5 py-12 text-center text-slate-500 font-sans">
                                Tidak ada data yang ditemukan di tabel <span class="font-mono text-slate-400">[{{ $table }}]</span>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rows->hasPages())
            <div class="px-5 py-4 border-t border-slate-800">
                {{ $rows->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

