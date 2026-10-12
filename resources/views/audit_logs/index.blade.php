@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page_title', 'Jejak Audit Aktivitas Sistem')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-white">Log Audit Trail Terpusat</h1>
            <p class="text-xs text-slate-400 mt-0.5">Catatan jejak aktivitas immutable: siapa, melakukan aksi apa, pada entitas apa, dan kapan</p>
        </div>
        <span class="px-3 py-1.5 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 text-xs font-semibold">
            Khusus Owner & Superuser
        </span>
    </div>

    <!-- Filter -->
    <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="flex items-center gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan, nama user, atau alamat IP..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <button type="submit" class="py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all">
                Cari
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('audit-logs.index') }}" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white" title="Reset filter">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            @endif
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Waktu</th>
                        <th class="px-5 py-3.5">Pengguna</th>
                        <th class="px-5 py-3.5">Aksi</th>
                        <th class="px-5 py-3.5">Entitas</th>
                        <th class="px-5 py-3.5">Keterangan</th>
                        <th class="px-5 py-3.5">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5 text-slate-400 font-mono">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-white">
                                {{ $log->user->name ?? 'Guest' }}
                            </td>
                            <td class="px-5 py-3.5 font-mono text-indigo-400 font-bold">
                                {{ $log->action }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-300">
                                {{ $log->entity_type }} #{{ $log->entity_id }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-300 max-w-md">
                                {{ $log->description }}
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-500">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                Belum ada log aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-5 py-3 border-t border-slate-800">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

