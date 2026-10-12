@extends('layouts.app')

@section('title', 'Mutasi Stok')
@section('page_title', 'Histori & Transaksi Mutasi Stok')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">Mutasi Stok Gudang</h1>
            <p class="text-xs text-slate-400 mt-0.5">Catatan audit mutasi barang masuk (Inbound), keluar (Outbound), dan transfer antar lemari</p>
        </div>
        <a href="{{ route('movements.create') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Catat Mutasi Baru</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md">
        <form method="GET" action="{{ route('movements.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor mutasi (MOV-...)..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="sm:col-span-4">
                <select name="type" class="w-full px-3 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500 transition-all">
                    <option value="">Semua Jenis Transaksi</option>
                    <option value="inbound" {{ request('type') == 'inbound' ? 'selected' : '' }}>Barang Masuk (Inbound)</option>
                    <option value="outbound" {{ request('type') == 'outbound' ? 'selected' : '' }}>Barang Keluar (Outbound)</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer Antar Lemari</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Koreksi / Penyesuaian</option>
                    <option value="initial_count" {{ request('type') == 'initial_count' ? 'selected' : '' }}>Saldo Awal Opname</option>
                </select>
            </div>

            <div class="sm:col-span-3 flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'type']))
                    <a href="{{ route('movements.index') }}" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Nomor Transaksi</th>
                        <th class="px-5 py-3.5">Jenis Mutasi</th>
                        <th class="px-5 py-3.5">Asal &rarr; Tujuan</th>
                        <th class="px-5 py-3.5">Rincian Barang</th>
                        <th class="px-5 py-3.5">Petugas</th>
                        <th class="px-5 py-3.5">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($movements as $mov)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-bold text-indigo-400">
                                {{ $mov->movement_number }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($mov->type === 'inbound')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Masuk (Inbound)
                                    </span>
                                @elseif($mov->type === 'outbound')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Keluar (Outbound)
                                    </span>
                                @elseif($mov->type === 'transfer')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                        Transfer Lokasi
                                    </span>
                                @elseif($mov->type === 'adjustment')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Penyesuaian
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                        Saldo Awal
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-300">
                                <span class="text-slate-400">{{ $mov->sourceLocation->name ?? 'Luar Gudang' }}</span>
                                <span class="text-indigo-400 font-bold mx-1">&rarr;</span>
                                <span class="text-white font-medium">{{ $mov->destinationLocation->name ?? 'Luar Gudang' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-300">
                                @foreach($mov->items as $item)
                                    <div>
                                        <span class="font-medium text-white">{{ $item->product->name }}</span>
                                        <span class="font-mono text-indigo-400 font-bold">({{ (float) $item->quantity }} {{ $item->product->unit->symbol ?? 'unit' }})</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $mov->creator->name ?? 'Sistem' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ $mov->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                                Belum ada riwayat transaksi mutasi yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="px-5 py-3 border-t border-slate-800">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

