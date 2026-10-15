@extends('layouts.app')

@section('page_title', 'Servis & Pemeliharaan LED')

@section('content')
<div class="space-y-6">

    <!-- Top Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-1">
                <span>Bengkel & Workshop</span>
                <span>•</span>
                <span>Karantina Inventori</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Servis & Pemeliharaan Unit</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Perbaikan modul LED, kalibrasi prosesor Novastar, pemakaian sparepart, dan uji kelayakan QC.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('service-tickets.create') }}" class="px-5 py-2.5 rounded-xl bg-linear-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-bold text-sm shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buka Tiket Servis Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
        <!-- Antrean Masuk -->
        <a href="{{ route('service-tickets.index', ['status' => 'pending_inspection']) }}" class="p-4 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md hover:border-amber-500/50 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Antrean Masuk</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-2xl md:text-3xl font-black text-amber-500 dark:text-amber-400 font-mono">
                {{ $metrics['pending_inspection'] }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Menunggu dicek teknisi</p>
        </a>

        <!-- Sedang Dikerjakan -->
        <a href="{{ route('service-tickets.index', ['status' => 'in_progress']) }}" class="p-4 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md hover:border-indigo-500/50 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengerjaan</span>
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-2xl md:text-3xl font-black text-indigo-600 dark:text-indigo-400 font-mono">
                {{ $metrics['in_progress'] }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Sedang disolder / dites</p>
        </a>

        <!-- Menunggu Sparepart -->
        <a href="{{ route('service-tickets.index', ['status' => 'waiting_parts']) }}" class="p-4 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md hover:border-purple-500/50 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tunggu Part</span>
                <span class="w-2.5 h-2.5 rounded-full bg-purple-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-2xl md:text-3xl font-black text-purple-600 dark:text-purple-400 font-mono">
                {{ $metrics['waiting_parts'] }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Menunggu IC / suku cadang</p>
        </a>

        <!-- Uji Coba QC -->
        <a href="{{ route('service-tickets.index', ['status' => 'quality_control']) }}" class="p-4 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md hover:border-cyan-500/50 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Uji Coba (QC)</span>
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-2xl md:text-3xl font-black text-cyan-600 dark:text-cyan-400 font-mono">
                {{ $metrics['quality_control'] }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Aging test & pola warna</p>
        </a>

        <!-- Selesai Bulan Ini -->
        <a href="{{ route('service-tickets.index', ['status' => 'completed']) }}" class="p-4 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md hover:border-emerald-500/50 transition-all group">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Selesai Bulan Ini</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-2xl md:text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono">
                {{ $metrics['completed_this_month'] }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Kembali ke rak siap sewa</p>
        </a>
    </div>

    <!-- Search & Filter Controls -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md">
        <form action="{{ route('service-tickets.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tiket, nama modul, kode aset, atau gejala..." class="w-full pl-10 pr-4 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <!-- Status Filter -->
            <select name="status" class="w-full sm:w-48 py-2 px-3 rounded-xl text-sm bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Status</option>
                <option value="pending_inspection" {{ request('status') === 'pending_inspection' ? 'selected' : '' }}>Antrean Masuk</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                <option value="waiting_parts" {{ request('status') === 'waiting_parts' ? 'selected' : '' }}>Menunggu Part</option>
                <option value="quality_control" {{ request('status') === 'quality_control' ? 'selected' : '' }}>Uji Coba & QC</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Siap Pakai)</option>
                <option value="scrapped" {{ request('status') === 'scrapped' ? 'selected' : '' }}>Afkir / Rusak Total</option>
            </select>

            <!-- Priority Filter -->
            <select name="priority" class="w-full sm:w-40 py-2 px-3 rounded-xl text-sm bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Prioritas</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>🔴 Darurat</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>🟠 Tinggi</option>
                <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>🔵 Normal</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>⚪ Rendah</option>
            </select>

            <button type="submit" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold transition-all">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status', 'priority']))
                <a href="{{ route('service-tickets.index') }}" class="w-full sm:w-auto text-center px-3 py-2 text-sm text-slate-500 hover:text-slate-800 dark:hover:text-slate-200">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Ticket Table List -->
    <div class="bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 rounded-2xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 dark:bg-slate-800/70 text-slate-500 dark:text-slate-300 uppercase tracking-wider font-semibold text-xs border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Nomor & Tanggal</th>
                        <th class="px-5 py-4">Barang / Aset</th>
                        <th class="px-5 py-4">Gejala & Kerusakan</th>
                        <th class="px-5 py-4 text-center">Prioritas</th>
                        <th class="px-5 py-4">Teknisi PJ</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                    @forelse($tickets as $t)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <a href="{{ route('service-tickets.show', $t) }}" class="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline block text-sm">
                                    {{ $t->ticket_number }}
                                </a>
                                <span class="text-xs text-slate-400 block mt-0.5">
                                    {{ $t->intake_date ? $t->intake_date->format('d/m/Y H:i') : $t->created_at->format('d/m/Y') }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-900 dark:text-white leading-tight">
                                    {{ $t->product->name }}
                                </div>
                                @if($t->asset)
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                            {{ $t->asset->asset_code }}
                                        </span>
                                        @if($t->asset->serial_number)
                                            <span class="text-xs text-slate-400 font-mono">SN: {{ $t->asset->serial_number }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 font-medium mt-0.5 block">
                                        Jumlah: <strong class="text-slate-200">{{ (float) $t->quantity }}</strong> {{ $t->product->unit->symbol ?? 'unit' }}
                                    </span>
                                @endif
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">
                                    Asal: {{ $t->sourceLocation->name ?? '-' }}
                                </span>
                            </td>

                            <td class="px-5 py-4 max-w-xs">
                                <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-2 leading-relaxed">
                                    {{ $t->issue_description }}
                                </p>
                                @if($t->spareparts->count() > 0)
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/15 text-purple-400 border border-purple-500/20">
                                        ⚙️ {{ $t->spareparts->count() }} sparepart terpasang
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $t->priority_badge_class }}">
                                    {{ $t->priority_label }}
                                </span>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($t->technician)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-linear-to-tr from-indigo-500 to-cyan-500 text-white font-bold text-[10px] flex items-center justify-center">
                                            {{ strtoupper(substr($t->technician->name, 0, 1)) }}
                                        </div>
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ $t->technician->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ditentukan</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $t->status_badge_class }}">
                                    {{ $t->status_label }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('service-tickets.show', $t) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition-colors inline-flex items-center gap-1.5 shadow-xs">
                                    <span>Buka Tiket</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-800/80 text-slate-400 flex items-center justify-center mx-auto text-xl font-bold">
                                        🔧
                                    </div>
                                    <p class="font-bold text-slate-200">Tidak ada tiket servis yang sesuai filter</p>
                                    <p class="text-xs text-slate-400">Seluruh unit modul dan perlengkapan LED dalam kondisi prima atau filter pencarian tidak menemukan hasil.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

