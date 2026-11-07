@extends('layouts.app')

@section('title', 'Master Data Customer & EO')
@section('page_title', 'Master Customer & Klien')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-indigo-500 dark:text-indigo-400 mb-1">
                <span>Master Data</span>
                <span>•</span>
                <span>Operasional LED</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Data Customer & EO</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar klien, vendor event organizer, instansi, kontak darurat, dan verifikasi company untuk pemesanan layar LED.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Link Registrasi Mandiri Klien -->
            <a href="{{ route('customers.public-register') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold uppercase tracking-wider border border-slate-300 dark:border-slate-700 transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span>Buka Form Self-Register</span>
            </a>

            <a href="{{ route('customers.create') }}" class="px-5 py-2.5 rounded-xl bg-linear-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Customer Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
        <a href="{{ route('customers.index', request()->except('status', 'page')) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request('status') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
            Semua Customer ({{ $totalCustomers ?? 0 }})
        </a>
        <a href="{{ route('customers.index', array_merge(request()->except('page'), ['status' => 'verified'])) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') === 'verified' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
            ✓ Terverifikasi Company ({{ $verifiedCount ?? 0 }})
        </a>
        <a href="{{ route('customers.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }}">
            ⏳ Menunggu Verifikasi ({{ $pendingCount ?? 0 }})
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('customers.index') }}" class="w-full sm:w-96 flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, instansi, telepon, kota, dll..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                Cari
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('customers.index') }}" class="text-xs text-rose-500 hover:underline">Reset</a>
            @endif
        </form>

        <div class="text-xs text-slate-500">
            Menampilkan: <span class="font-bold text-slate-900 dark:text-white">{{ $customers->total() }}</span> customer
        </div>
    </div>

    <!-- Customer Table -->
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 uppercase text-[10px] text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Customer &amp; Foto</th>
                        <th class="px-4 py-3">Organisasi / Vendor</th>
                        <th class="px-4 py-3">No. WhatsApp &amp; Darurat</th>
                        <th class="px-4 py-3">Domisili</th>
                        <th class="px-4 py-3 text-center">Verifikasi</th>
                        <th class="px-4 py-3 text-center">Order</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($customers as $c)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <!-- Customer & Foto -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($c->photo_path)
                                            <img src="{{ asset('storage/' . $c->photo_path) }}" alt="{{ $c->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xs font-bold text-slate-500">{{ strtoupper(substr($c->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-mono text-[10px] font-bold text-indigo-600 dark:text-indigo-400 block">{{ $c->customer_code }}</span>
                                        <a href="{{ route('customers.show', $c) }}" class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            {{ $c->salutation ? $c->salutation . ' ' : '' }}{{ $c->name }}
                                        </a>
                                        @if($c->full_name && $c->full_name !== $c->name)
                                            <span class="block text-[11px] text-slate-500">{{ $c->full_name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Organisasi -->
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $c->organization ?: 'Personal / EO' }}</div>
                                @if($c->category)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                        {{ $c->category }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kontak WhatsApp & Darurat -->
                            <td class="px-4 py-3">
                                <a href="{{ $c->whatsapp_url }}" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold flex items-center gap-1">
                                    <span>💬</span>
                                    <span>{{ $c->phone }}</span>
                                </a>
                                @if($c->pic_secondary_phone)
                                    <div class="text-[11px] text-amber-600 dark:text-amber-400 flex items-center gap-1 mt-0.5" title="Kontak Darurat">
                                        <span>🚨</span>
                                        <span>{{ $c->pic_secondary_phone }} ({{ $c->pic_secondary_name ?: 'Darurat' }})</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Domisili -->
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $c->city ?: '-' }}</div>
                                @if($c->province)
                                    <div class="text-[10px] text-slate-400">{{ $c->province }}</div>
                                @endif
                            </td>

                            <!-- Verifikasi Status -->
                            <td class="px-4 py-3 text-center">
                                @if($c->verification_status === 'verified')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                                        ✓ Verified
                                    </span>
                                @else
                                    <form action="{{ route('customers.verify', $c) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-500/15 hover:bg-emerald-500 hover:text-white text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 transition-colors" title="Klik untuk verifikasi klien ini">
                                            <span>⏳ Verifikasi?</span>
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <!-- Order Count -->
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $c->orders_count > 0 ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-500' }}">
                                    {{ $c->orders_count }} Event
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('customers.show', $c) }}" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Detail Profile">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('customers.edit', $c) }}" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit Customer">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    @if($c->orders_count == 0)
                                        <form action="{{ route('customers.destroy', $c) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus customer {{ $c->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-900/30 text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Hapus Customer">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data customer terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
