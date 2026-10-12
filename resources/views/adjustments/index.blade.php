@extends('layouts.app')

@section('title', 'Penyesuaian & Approval Stok')
@section('page_title', 'Alur Persetujuan Koreksi Stok Owner')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">Penyesuaian Stok Fisik & Approval</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pengajuan koreksi selisih fisik oleh Warehouse wajib disetujui Owner sebelum saldo resmi diperbarui</p>
        </div>
        <a href="{{ route('adjustments.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Ajukan Koreksi Stok</span>
        </a>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
        <a href="{{ route('adjustments.index', ['status' => 'pending_approval']) }}"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $status === 'pending_approval' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'text-slate-400 hover:text-white' }}">
            <span>Menunggu Approval Owner</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-500/20 font-mono">{{ $counts['pending_approval'] }}</span>
        </a>
        <a href="{{ route('adjustments.index', ['status' => 'approved']) }}"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'text-slate-400 hover:text-white' }}">
            <span>Disetujui</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-500/20 font-mono">{{ $counts['approved'] }}</span>
        </a>
        <a href="{{ route('adjustments.index', ['status' => 'rejected']) }}"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all flex items-center gap-2 {{ $status === 'rejected' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'text-slate-400 hover:text-white' }}">
            <span>Ditolak</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-rose-500/20 font-mono">{{ $counts['rejected'] }}</span>
        </a>
        <a href="{{ route('adjustments.index', ['status' => 'all']) }}"
            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $status === 'all' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
            Semua
        </a>
    </div>

    <!-- Adjustments Cards / Table -->
    <div class="space-y-4">
        @forelse($adjustments as $adj)
            <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-md border border-indigo-500/20">
                            {{ $adj->adjustment_number }}
                        </span>

                        @if($adj->status === 'pending_approval')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                Menunggu Persetujuan Owner
                            </span>
                        @elseif($adj->status === 'approved')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Disetujui & Saldo Resmi Terposting
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                Ditolak
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-400">
                        Diajukan oleh <span class="text-slate-200 font-semibold">{{ $adj->requester->name }}</span> • {{ $adj->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

                <!-- Item Rincian -->
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/80">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div>
                            @foreach($adj->items as $item)
                                <div class="font-bold text-white text-sm">{{ $item->product->name }}</div>
                                <div class="text-slate-400 mt-0.5">
                                    Lokasi: <span class="text-slate-200 font-semibold">{{ $adj->location->name }}</span> | 
                                    Kondisi: <span class="capitalize text-slate-200">{{ $item->condition }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Perhitungan Selisih -->
                        @foreach($adj->items as $item)
                            <div class="flex items-center gap-4 text-right">
                                <div>
                                    <span class="text-slate-500 block text-[10px] uppercase">Saldo Sistem</span>
                                    <span class="font-mono font-bold text-slate-300 text-sm">{{ (float) $item->current_quantity }}</span>
                                </div>
                                <div class="text-slate-600">&rarr;</div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] uppercase">Temuan Fisik</span>
                                    <span class="font-mono font-bold text-white text-sm">{{ (float) $item->adjusted_quantity }}</span>
                                </div>
                                <div class="pl-2 border-l border-slate-800">
                                    <span class="text-slate-500 block text-[10px] uppercase">Koreksi</span>
                                    <span class="font-mono font-bold text-sm {{ $item->difference >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $item->difference >= 0 ? '+' : '' }}{{ (float) $item->difference }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Alasan -->
                    <div class="mt-3 pt-3 border-t border-slate-800/60 text-xs">
                        <span class="text-slate-500 font-semibold">Alasan Penyesuaian:</span>
                        <p class="text-slate-300 mt-0.5">{{ $adj->reason }}</p>
                        @if($adj->rejection_reason)
                            <p class="text-rose-400 mt-1 font-medium">Alasan Penolakan: {{ $adj->rejection_reason }}</p>
                        @endif
                    </div>
                </div>

                <!-- Approval Actions (Hanya jika status pending_approval) -->
                @if($adj->status === 'pending_approval')
                    <div class="pt-2 flex flex-wrap items-center justify-end gap-3 border-t border-slate-800/80">
                        <!-- Form Tolak -->
                        <form action="{{ route('adjustments.reject', $adj) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input type="text" name="rejection_reason" required placeholder="Alasan penolakan..."
                                class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 text-xs font-semibold transition-all">
                                Tolak
                            </button>
                        </form>

                        <!-- Form Setujui (Approve) -->
                        <form action="{{ route('adjustments.approve', $adj) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menyetujui koreksi ini? Saldo resmi sistem akan otomatis diselaraskan secara permanen.');">
                            @csrf
                            <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Setujui (Approve & Posting Saldo)</span>
                            </button>
                        </form>
                    </div>
                @elseif($adj->approved_by)
                    <div class="text-[11px] text-slate-500 text-right">
                        Diproses oleh {{ $adj->approver->name }} pada {{ $adj->approved_at?->format('d/m/Y H:i') }}
                    </div>
                @endif
            </div>
        @empty
            <div class="p-12 text-center bg-slate-900/40 border border-slate-800 rounded-2xl text-slate-500 text-xs">
                Tidak ada data penyesuaian stok pada tab ini.
            </div>
        @endforelse

        @if($adjustments->hasPages())
            <div class="pt-2">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

