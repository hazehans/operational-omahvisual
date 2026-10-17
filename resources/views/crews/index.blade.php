@extends('layouts.app')

@section('title', 'Master Data Kru & Teknisi Lapangan')
@section('page_title', 'Master Kru & Teknisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-indigo-500 dark:text-indigo-400 mb-1">
                <span>Master Data</span>
                <span>•</span>
                <span>Sumber Daya Lapangan</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Kru & Teknisi Lapangan</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar roster kru lapangan Omahvisual, PIC teknisi, rigger, supir armada, operator, dan arsip dokumen KTP/Rate Card.</p>
        </div>

        <a href="{{ route('crews.create') }}" class="px-5 py-2.5 rounded-xl bg-linear-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Kru Baru</span>
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('crews.index') }}" class="w-full sm:w-auto flex flex-wrap items-center gap-2 flex-1">
            <div class="relative flex-1 sm:max-w-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, panggilan, atau telepon..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="employment_type" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300">
                <option value="">Semua Status Kerja</option>
                <option value="freelance_outsourcing" {{ request('employment_type') === 'freelance_outsourcing' ? 'selected' : '' }}>Freelance / Outsourcing</option>
                <option value="karyawan_tetap" {{ request('employment_type') === 'karyawan_tetap' ? 'selected' : '' }}>Karyawan Tetap</option>
                <option value="magang" {{ request('employment_type') === 'magang' ? 'selected' : '' }}>Anak Magang</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                Filter
            </button>
            @if(request('search') || request('employment_type'))
                <a href="{{ route('crews.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>

        <div class="text-xs text-slate-500">
            Total Kru Terdaftar: <span class="font-bold text-slate-900 dark:text-white">{{ $crews->total() }}</span>
        </div>
    </div>

    <!-- Crews Table -->
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase text-[10px] text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Kru & Foto</th>
                        <th class="px-4 py-3">Nama Lengkap & HRIS</th>
                        <th class="px-4 py-3">Jabatan / Role</th>
                        <th class="px-4 py-3">Status Kerja</th>
                        <th class="px-4 py-3">Kontak WhatsApp</th>
                        <th class="px-4 py-3 text-center">Dokumen</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($crews as $crew)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300">
                                        @if($crew->photo_path)
                                            <img src="{{ asset('storage/' . $crew->photo_path) }}" alt="{{ $crew->nickname }}" class="w-full h-full object-cover">
                                        @else
                                            <span>{{ strtoupper(substr($crew->nickname, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $crew->nickname }}</div>
                                        <div class="font-mono text-[10px] text-indigo-600 dark:text-indigo-400 font-bold">{{ $crew->crew_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $crew->full_name }}</div>
                                @if($crew->hris_employee_id)
                                    <div class="text-[10px] text-cyan-600 dark:text-cyan-400 font-mono font-medium">HRIS ID: {{ $crew->hris_employee_id }}</div>
                                @endif
                                @if($crew->city)
                                    <div class="text-[10px] text-slate-400">{{ $crew->city }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @php
                                        $roles = is_array($crew->positions) && count($crew->positions) > 0 ? $crew->positions : explode(', ', (string)$crew->position);
                                    @endphp
                                    @foreach($roles as $r)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                            {{ $r }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($crew->employment_type === 'karyawan_tetap')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        Karyawan Tetap
                                    </span>
                                @elseif($crew->employment_type === 'magang')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                        Anak Magang
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                        Outsourcing / Freelance
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ $crew->whatsapp_url }}" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold flex items-center gap-1">
                                    <span>💬</span>
                                    <span>{{ $crew->formatted_phone ?: $crew->phone }}</span>
                                </a>
                                @if($crew->emergency_phone)
                                    <div class="text-[10px] text-slate-400">Darurat: {{ $crew->emergency_phone }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($crew->ktp_photo_path)
                                        <a href="{{ asset('storage/' . $crew->ktp_photo_path) }}" target="_blank" class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-slate-700 dark:text-slate-300 text-[10px] font-semibold border border-slate-300 dark:border-slate-700" title="Lihat Foto KTP">
                                            🪪 KTP
                                        </a>
                                    @endif
                                    @if($crew->rate_card_photo_path)
                                        <a href="{{ asset('storage/' . $crew->rate_card_photo_path) }}" target="_blank" class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 text-[10px] font-semibold border border-emerald-300 dark:border-emerald-800" title="Lihat Foto Rate Card">
                                            💰 Rate
                                        </a>
                                    @endif
                                    @if(!$crew->ktp_photo_path && !$crew->rate_card_photo_path)
                                        <span class="text-slate-400 text-[10px]">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $crew->is_active ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                    {{ $crew->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('crew-payroll.crew-monthly-slip', $crew) }}" target="_blank" class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 text-emerald-600 dark:text-emerald-400 transition-colors" title="Lihat Rekap Slip Bulanan Kru">
                                        💰
                                    </a>
                                    <a href="{{ route('crews.edit', $crew) }}" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit Kru">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('crews.destroy', $crew) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kru {{ $crew->nickname }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-900/30 text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Hapus Kru">
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
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data kru terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($crews->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $crews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
