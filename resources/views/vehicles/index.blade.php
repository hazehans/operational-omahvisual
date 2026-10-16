@extends('layouts.app')

@section('title', 'Master Data Armada & Kendaraan')
@section('page_title', 'Master Armada Kendaraan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-indigo-500 dark:text-indigo-400 mb-1">
                <span>Master Data</span>
                <span>•</span>
                <span>Logistik & Transportasi</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Armada Kendaraan Operasional</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar kendaraan angkut modul LED, status ketersediaan live, kondisi khusus armada, dan arsip foto STNK, E-Toll, serta barcode BBM.</p>
        </div>

        <a href="{{ route('vehicles.create') }}" class="px-5 py-2.5 rounded-xl bg-linear-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Armada Baru</span>
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('vehicles.index') }}" class="w-full sm:w-auto flex flex-wrap items-center gap-2 flex-1">
            <div class="relative flex-1 sm:max-w-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari armada, plat nomor, atau kondisi..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300">
                <option value="">Semua Ketersediaan</option>
                <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia / Siap</option>
                <option value="dipakai" {{ request('status') === 'dipakai' ? 'selected' : '' }}>Sedang Dipakai Event</option>
                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance / Bengkel</option>
                <option value="standby" {{ request('status') === 'standby' ? 'selected' : '' }}>Standby / Cadangan</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                Filter
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('vehicles.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>

        <div class="text-xs text-slate-500">
            Total Armada: <span class="font-bold text-slate-900 dark:text-white">{{ $vehicles->total() }}</span>
        </div>
    </div>

    <!-- Vehicles Table -->
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase text-[10px] text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Armada & Foto</th>
                        <th class="px-4 py-3">Plat Nomor</th>
                        <th class="px-4 py-3">Status Ketersediaan</th>
                        <th class="px-4 py-3">Kondisi Khusus</th>
                        <th class="px-4 py-3 text-center">Kelengkapan SPK</th>
                        <th class="px-4 py-3 text-center">Dokumen Foto</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($vehicles as $veh)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-9 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300">
                                        @if($veh->photo_path)
                                            <img src="{{ asset('storage/' . $veh->photo_path) }}" alt="{{ $veh->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span>🚛</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $veh->name }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">
                                            {{ $veh->vehicle_code ?? ('VH-' . str_pad($veh->id, 3, '0', STR_PAD_LEFT)) }} • {{ $veh->type ?: 'Pick Up' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $veh->plate_number ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($veh->status === 'tersedia')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        ✓ Tersedia / Siap
                                    </span>
                                @elseif($veh->status === 'dipakai')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                        ⏱️ Dipakai Event
                                    </span>
                                @elseif($veh->status === 'maintenance')
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                        🔧 Bengkel / Servis
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                        ⏸️ Standby
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-slate-800 dark:text-slate-200 font-medium">{{ $veh->special_condition ?: 'Normal' }}</div>
                                @if($veh->notes)
                                    <div class="text-[10px] text-slate-400 truncate max-w-xs">{{ $veh->notes }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex flex-wrap gap-1 items-center justify-center text-[10px]">
                                    <span class="px-1.5 py-0.5 rounded {{ $veh->has_stnk ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">STNK</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $veh->has_kir ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">KIR</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $veh->has_etoll ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">E-Toll</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $veh->has_kunci_ban ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">Kunci</span>
                                    <span class="px-1.5 py-0.5 rounded {{ $veh->has_barcode_bbm ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">BBM</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($veh->stnk_photo_path)
                                        <a href="{{ asset('storage/' . $veh->stnk_photo_path) }}" target="_blank" class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 text-[10px] font-semibold text-slate-700 dark:text-slate-300" title="Foto STNK">📄 STNK</a>
                                    @endif
                                    @if($veh->etoll_photo_path)
                                        <a href="{{ asset('storage/' . $veh->etoll_photo_path) }}" target="_blank" class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 text-[10px] font-semibold text-slate-700 dark:text-slate-300" title="Foto E-Toll">💳 Toll</a>
                                    @endif
                                    @if($veh->barcode_bbm_photo_path)
                                        <a href="{{ asset('storage/' . $veh->barcode_bbm_photo_path) }}" target="_blank" class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 text-[10px] font-semibold text-emerald-700 dark:text-emerald-300" title="Foto Barcode BBM">⛽ BBM</a>
                                    @endif
                                    @if(!$veh->stnk_photo_path && !$veh->etoll_photo_path && !$veh->barcode_bbm_photo_path)
                                        <span class="text-slate-400 text-[10px]">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('vehicles.edit', $veh) }}" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit Armada">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('vehicles.destroy', $veh) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus armada {{ $veh->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-900/30 text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Hapus Armada">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data armada kendaraan terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
