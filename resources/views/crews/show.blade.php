@extends('layouts.app')

@section('title', 'Dompet & Profil Kru — ' . $crew->nickname)
@section('page_title', 'Dompet & Profil Kru')

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumbs & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('crews.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Master Kru</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('crew-payroll.crew-monthly-slip', $crew) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all flex items-center gap-1.5 shadow-xs">
                <span>🧾 Slip Rekap Bulanan</span>
            </a>
            <a href="{{ route('crews.edit', $crew) }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all flex items-center gap-1.5 shadow-md shadow-indigo-600/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span>Edit Data</span>
            </a>
        </div>
    </div>

    <!-- Profile & Overview Card -->
    <div class="rounded-3xl bg-linear-to-r from-slate-900 via-indigo-950/60 to-slate-900 border border-slate-800 p-6 md:p-8 shadow-xl text-white">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-2xl overflow-hidden bg-slate-800 border-2 border-indigo-500/40 shrink-0 flex items-center justify-center font-black text-xl text-indigo-300 shadow-md">
                    @if($crew->photo_path)
                        <img src="{{ asset('storage/' . $crew->photo_path) }}" alt="{{ $crew->nickname }}" class="w-full h-full object-cover">
                    @else
                        <span>{{ strtoupper(substr($crew->nickname, 0, 2)) }}</span>
                    @endif
                </div>

                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-black text-white tracking-tight">{{ $crew->nickname }}</h1>
                        <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-bold">
                            {{ $crew->crew_code }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $crew->is_active ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                            {{ $crew->is_active ? 'Aktif Bertugas' : 'Non-Aktif' }}
                        </span>
                    </div>

                    <div class="text-sm font-semibold text-slate-300">{{ $crew->full_name }}</div>
                    
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 pt-1">
                        <span class="text-indigo-400 font-medium">💼 {{ is_array($crew->positions) ? implode(', ', $crew->positions) : ($crew->position ?? 'Kru') }}</span>
                        <span>•</span>
                        <span>🏷️ {{ ucwords(str_replace('_', ' ', $crew->employment_type)) }}</span>
                        @if($crew->city)
                            <span>•</span>
                            <span>📍 {{ $crew->city }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- WhatsApp & Emergency Contact -->
            <div class="shrink-0 space-y-2 text-xs">
                @if($crew->phone)
                    <a href="{{ $crew->whatsapp_url }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600/30 hover:bg-emerald-600/50 text-emerald-300 border border-emerald-500/30 font-bold flex items-center gap-2 transition-all">
                        <span>💬 Chat WhatsApp: {{ $crew->phone }}</span>
                    </a>
                @endif
                @if($crew->emergency_phone)
                    <div class="text-[11px] text-slate-400">
                        Kontak Darurat: <span class="font-mono text-slate-300">{{ $crew->emergency_phone }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- DOMPET & SALDO HONOR KRU (CREW WALLET SECTION) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xl">💰</span>
                <h2 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Dompet &amp; Saldo Honor</h2>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-500 dark:text-amber-300 border border-amber-500/30">
                    Sistem Saldo Terpadu
                </span>
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Total Partisipasi: <strong class="text-slate-900 dark:text-white">{{ $totalJobs }} Event</strong>
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Saldo Belum Dicairkan (Pending) -->
            <div class="p-5 rounded-2xl bg-linear-to-br from-amber-500/10 via-amber-500/5 to-slate-900 border border-amber-500/30 shadow-lg space-y-2 relative overflow-hidden">
                <div class="flex items-center justify-between text-xs font-semibold text-amber-500 dark:text-amber-400">
                    <span>Saldo Belum Dicairkan</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                </div>
                <div class="text-2xl md:text-3xl font-black text-amber-500 dark:text-amber-300 tracking-tight">
                    Rp {{ number_format($totalPending, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Honor dari event yang belum dibayarkan oleh Finance.
                </p>
            </div>

            <!-- Saldo Sudah Dicairkan (Paid) -->
            <div class="p-5 rounded-2xl bg-linear-to-br from-emerald-500/10 via-emerald-500/5 to-slate-900 border border-emerald-500/30 shadow-lg space-y-2">
                <div class="flex items-center justify-between text-xs font-semibold text-emerald-500 dark:text-emerald-400">
                    <span>Sudah Dicairkan</span>
                    <span class="text-xs">✓ Lunas</span>
                </div>
                <div class="text-2xl md:text-3xl font-black text-emerald-500 dark:text-emerald-400 tracking-tight">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Total dana honor yang telah sukses ditransfer ke rekening kru.
                </p>
            </div>

            <!-- Total Akumulasi Honor (Lifetime) -->
            <div class="p-5 rounded-2xl bg-linear-to-br from-indigo-500/10 via-indigo-500/5 to-slate-900 border border-indigo-500/30 shadow-lg space-y-2">
                <div class="flex items-center justify-between text-xs font-semibold text-indigo-400">
                    <span>Total Akumulasi Honor</span>
                    <span class="text-xs">💼 Lifetime</span>
                </div>
                <div class="text-2xl md:text-3xl font-black text-white tracking-tight">
                    Rp {{ number_format($totalEarned, 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    Akumulasi seluruh pendapatan dari {{ $totalJobs }} job panggung.
                </p>
            </div>
        </div>

        <!-- Withdrawal & Bank Account Action Banner -->
        <div class="p-5 rounded-3xl bg-slate-900 border border-slate-800 shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
            <div class="flex items-start gap-4">
                <div class="p-3 rounded-2xl bg-indigo-500/20 text-indigo-400 shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-white">Rekening Pencairan Terdaftar</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300 font-mono font-bold">{{ $crew->bank_name ?? 'Belum Diisi' }}</span>
                    </div>
                    <div class="text-xs text-slate-300">
                        No. Rek: <strong class="font-mono text-white text-sm tracking-wider">{{ $crew->bank_account_number ?? '-' }}</strong>
                        <span class="text-slate-500">|</span> 
                        a.n: <strong class="text-slate-200">{{ $crew->bank_account_holder ?? $crew->full_name }}</strong>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Withdrawal Request Button -->
            <div class="shrink-0 w-full md:w-auto">
                @if($totalPending > 0)
                    <a href="{{ $withdrawalWhatsappUrl }}" target="_blank" class="w-full md:w-auto px-5 py-3 rounded-2xl bg-linear-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-xs shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                        <span>💬 Ajukan Pencairan Honor ke Finance</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @else
                    <div class="px-4 py-2.5 rounded-2xl bg-slate-800 text-slate-400 text-xs font-semibold text-center border border-slate-700/60">
                        ✓ Seluruh Honor Sudah Dicairkan Lunas
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Job & Assignment History Table -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Riwayat Job &amp; Penugasan Event</h3>
            <span class="text-xs text-slate-500">Halaman {{ $assignments->currentPage() }} dari {{ $assignments->lastPage() }}</span>
        </div>

        <div class="bg-white dark:bg-slate-900/60 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-950/80 uppercase text-[10px] text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">Tanggal &amp; Event</th>
                            <th class="px-5 py-3.5">Venue / Lokasi</th>
                            <th class="px-5 py-3.5">Peran Ditugaskan</th>
                            <th class="px-5 py-3.5">Rincian Komponen Fee</th>
                            <th class="px-5 py-3.5 text-right">Total Honor</th>
                            <th class="px-5 py-3.5 text-center">Status ACC</th>
                            <th class="px-5 py-3.5 text-center">Pencairan</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($assignments as $ca)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <!-- Tanggal & Event -->
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white text-xs">
                                        {{ $ca->eventOrder?->event_name ?? 'Event #'.$ca->event_order_id }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $ca->eventOrder?->event_start_date?->format('d M Y') ?? '-' }}
                                    </div>
                                </td>

                                <!-- Venue -->
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $ca->eventOrder?->venue_name ?? '-' }}
                                    </div>
                                </td>

                                <!-- Peran -->
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                        {{ $ca->role_label }}
                                    </span>
                                </td>

                                <!-- Komponen Fee -->
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1 text-[10px]">
                                        @if($ca->fee_loading_in > 0) <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">In: {{ number_format($ca->fee_loading_in/1000, 0) }}k</span> @endif
                                        @if($ca->fee_loading_out > 0) <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Out: {{ number_format($ca->fee_loading_out/1000, 0) }}k</span> @endif
                                        @if($ca->fee_meal > 0) <span class="px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300">Makan: {{ number_format($ca->fee_meal/1000, 0) }}k</span> @endif
                                        @if($ca->fee_standby > 0) <span class="px-1.5 py-0.5 rounded bg-cyan-50 dark:bg-cyan-950/50 text-cyan-700 dark:text-cyan-300">Op: {{ number_format($ca->fee_standby/1000, 0) }}k</span> @endif
                                        @if($ca->fee_transport > 0) <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300">Trp: {{ number_format($ca->fee_transport/1000, 0) }}k</span> @endif
                                        @if($ca->fee_bonus > 0) <span class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300">Bns: {{ number_format($ca->fee_bonus/1000, 0) }}k</span> @endif
                                    </div>
                                </td>

                                <!-- Total Honor -->
                                <td class="px-5 py-4 text-right">
                                    <span class="font-extrabold text-slate-900 dark:text-white text-xs">
                                        Rp {{ number_format($ca->total_fee, 0, ',', '.') }}
                                    </span>
                                </td>

                                <!-- Status ACC -->
                                <td class="px-5 py-4 text-center">
                                    @if($ca->status === 'acc')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">
                                            ✓ ACC Siap
                                        </span>
                                    @elseif($ca->status === 'offered')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400">
                                            ⏳ Ditawarkan
                                        </span>
                                    @elseif($ca->status === 'completed')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400">
                                            Selesai
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400">
                                            Menolak
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Bayar -->
                                <td class="px-5 py-4 text-center">
                                    @if($ca->payment_status === 'paid')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">
                                            ✅ Lunas
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400">
                                            ⏳ Pending
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ $ca->whatsapp_reminder_url }}" target="_blank" class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-600 dark:text-emerald-400" title="Reminder WA">
                                            ⏰
                                        </a>
                                        <a href="{{ route('crew-payroll.slip', $ca) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 text-slate-600 dark:text-slate-300" title="Cetak Slip Gaji">
                                            🧾
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-slate-500">
                                    Belum ada catatan riwayat penugasan event untuk kru ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assignments->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

