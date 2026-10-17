@extends('layouts.app')

@section('title', 'Honor & Payroll Kru Freelance - Omah Visual')
@section('page_title', 'Honor & Payroll Kru Freelance')

@section('content')
<div class="space-y-6" x-data="{
    selectedIds: [],
    selectAll: false,
    showSinglePayModal: false,
    showBatchPayModal: false,
    singlePayData: { id: null, crewName: '', amount: 0, formattedAmount: '' },
    openSinglePayModal(id, crewName, amount, formattedAmount) {
        this.singlePayData = { id, crewName, amount, formattedAmount };
        this.showSinglePayModal = true;
    },
    toggleSelectAll(event) {
        if (event.target.checked) {
            this.selectedIds = Array.from(document.querySelectorAll('.assignment-checkbox:not(:disabled)')).map(el => el.value);
        } else {
            this.selectedIds = [];
        }
    }
}">
    <!-- Header with Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Keuangan & Operasional</span>
                <span>/</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Honor & Payroll Kru</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                <span>💰 Rekap Honor & Payroll Kru Freelance</span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola pencairan honor per event (Loading In, Loading Out, Uang Makan, Operator) & cetak slip gaji kru mandiri.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('crews.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-colors">
                <span>👥 Master Kru & Rate Card</span>
            </a>
            <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all">
                <span>📑 Kelola SPK Event</span>
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-base">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Financial KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Belum Dibayar -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block mb-1">Total Belum Dibayar</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-400 block mt-1">Periode {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold">
                ⏳
            </div>
        </div>

        <!-- Sudah Lunas -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mb-1">Total Sudah Lunas</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-400 block mt-1">Telah dicairkan ke rekening kru</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold">
                ✅
            </div>
        </div>

        <!-- Total Penugasan -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 block mb-1">Total Penugasan Job</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $totalAssignments }} Penugasan</span>
                <span class="text-[11px] text-slate-400 block mt-1">Kru & teknisi terlibat</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-bold">
                🎯
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white dark:bg-slate-900/60 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
        <form method="GET" action="{{ route('crew-payroll.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Filter Bulan -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Bulan Event</label>
                <input type="month" name="month" value="{{ $month }}"
                    class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Filter Kru -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kru / Teknisi</label>
                <select name="crew_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Kru</option>
                    @foreach($crews as $c)
                        <option value="{{ $c->id }}" {{ request('crew_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nickname }} ({{ $c->full_name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Penugasan -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Job</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status Job</option>
                    <option value="offered" {{ request('status') === 'offered' ? 'selected' : '' }}>Ditawarkan</option>
                    <option value="acc" {{ request('status') === 'acc' ? 'selected' : '' }}>ACC (Konfirmasi)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="declined" {{ request('status') === 'declined' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Filter Status Bayar -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Pembayaran</label>
                <select name="payment_status" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status Bayar</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>⏳ Belum Lunas (Unpaid)</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>✅ Lunas (Paid)</option>
                </select>
            </div>

            <!-- Pencarian Teks & Tombol -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Event / Kru</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama event/kru..."
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-all">
                    Filter
                </button>
                @if(request()->anyFilled(['crew_id', 'status', 'payment_status', 'search']) || (request('month') && request('month') !== now()->format('Y-m')))
                    <a href="{{ route('crew-payroll.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white text-xs font-bold transition-colors" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Batch Actions Toolbar (Shown when items selected) -->
    <div x-show="selectedIds.length > 0" x-cloak class="p-3.5 rounded-xl bg-indigo-600 text-white flex items-center justify-between shadow-lg transition-all animate-fade-in">
        <div class="flex items-center gap-2 text-xs font-semibold">
            <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center font-bold" x-text="selectedIds.length"></span>
            <span>Penugasan kru dipilih untuk dicairkan bersama.</span>
        </div>
        <button type="button" @click="showBatchPayModal = true" class="px-4 py-1.5 rounded-lg bg-white text-indigo-700 hover:bg-indigo-50 text-xs font-bold shadow-xs transition-colors">
            Bayar Sekaligus (Batch Lunas)
        </button>
    </div>

    <!-- Main Table -->
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-400">
                <thead class="bg-slate-50 dark:bg-slate-950/60 text-slate-500 dark:text-slate-400 uppercase font-bold border-b border-slate-200 dark:border-slate-800 text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-8">
                            <input type="checkbox" @change="toggleSelectAll($event)" class="rounded text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-3">Kru & Rekening Bank</th>
                        <th class="px-4 py-3">Event & Jadwal</th>
                        <th class="px-4 py-3">Peran (Role)</th>
                        <th class="px-4 py-3">Rincian Komponen Fee</th>
                        <th class="px-4 py-3 text-right">Total Honor</th>
                        <th class="px-4 py-3 text-center">Status Job</th>
                        <th class="px-4 py-3 text-center">Status Bayar</th>
                        <th class="px-4 py-3 text-right">Aksi & Slip</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($assignments as $assignment)
                        @php
                            $waMessage = "Halo {$assignment->crew->nickname}, berikut rincian honor untuk event:\n"
                                . "📌 Event: {$assignment->eventOrder->event_name}\n"
                                . "📅 Tanggal: {$assignment->eventOrder->event_start_date->format('d/m/Y')}\n"
                                . "📍 Lokasi: {$assignment->eventOrder->venue_name}\n"
                                . "💼 Peran: {$assignment->role_label}\n"
                                . "-----------------------------------\n"
                                . ($assignment->fee_loading_in > 0 ? "• Loading In: Rp " . number_format($assignment->fee_loading_in, 0, ',', '.') . "\n" : "")
                                . ($assignment->fee_loading_out > 0 ? "• Loading Out: Rp " . number_format($assignment->fee_loading_out, 0, ',', '.') . "\n" : "")
                                . ($assignment->fee_meal > 0 ? "• Uang Makan: Rp " . number_format($assignment->fee_meal, 0, ',', '.') . "\n" : "")
                                . ($assignment->fee_standby > 0 ? "• Standby/Operator: Rp " . number_format($assignment->fee_standby, 0, ',', '.') . "\n" : "")
                                . ($assignment->fee_transport > 0 ? "• Transport: Rp " . number_format($assignment->fee_transport, 0, ',', '.') . "\n" : "")
                                . ($assignment->fee_bonus > 0 ? "• Bonus/Lembur: Rp " . number_format($assignment->fee_bonus, 0, ',', '.') . "\n" : "")
                                . "💰 TOTAL HONOR: Rp " . number_format($assignment->total_fee, 0, ',', '.') . "\n"
                                . "Status Pembayaran: " . ($assignment->payment_status === 'paid' ? 'LUNAS' : 'BELUM DIBAYAR') . "\n"
                                . "Terima kasih atas kerja kerasnya! — Omah Visual LED";
                            $waUrl = $assignment->crew->formatWhatsappUrl($waMessage);
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                            <!-- Checkbox -->
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" value="{{ $assignment->id }}" x-model="selectedIds"
                                    class="assignment-checkbox rounded text-indigo-600 focus:ring-indigo-500"
                                    {{ $assignment->payment_status === 'paid' ? 'disabled' : '' }}>
                            </td>

                            <!-- Kru & Rekening -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-slate-600 dark:text-slate-300 text-xs">
                                        @if($assignment->crew->photo_path)
                                            <img src="{{ asset('storage/' . $assignment->crew->photo_path) }}" alt="{{ $assignment->crew->nickname }}" class="w-full h-full object-cover">
                                        @else
                                            <span>{{ strtoupper(substr($assignment->crew->nickname, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-xs flex items-center gap-1.5">
                                            <span>{{ $assignment->crew->nickname }}</span>
                                            <span class="text-[10px] text-slate-400 font-normal">({{ $assignment->crew->full_name }})</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                            <span>🏦</span>
                                            @if($assignment->crew->bank_account_number)
                                                <span>{{ $assignment->crew->bank_name }}: <strong class="font-mono text-slate-800 dark:text-slate-200">{{ $assignment->crew->bank_account_number }}</strong> (a.n {{ $assignment->crew->bank_account_holder }})</span>
                                            @else
                                                <span class="italic text-slate-400">Belum input no. rekening</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Event & Jadwal -->
                            <td class="px-4 py-3">
                                <a href="{{ route('spk.show', $assignment->spkAssignment) }}" class="font-bold text-indigo-600 dark:text-indigo-400 hover:underline block leading-tight">
                                    {{ $assignment->eventOrder->event_name }}
                                </a>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ $assignment->eventOrder->event_start_date->format('d M Y') }} • {{ $assignment->eventOrder->venue_name }}
                                </div>
                                <div class="text-[10px] font-mono text-slate-400">
                                    {{ $assignment->eventOrder->order_number }}
                                </div>
                            </td>

                            <!-- Peran -->
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                    {{ $assignment->role_label }}
                                </span>
                            </td>

                            <!-- Rincian Fee -->
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1 max-w-xs text-[10px]">
                                    @if($assignment->fee_loading_in > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" title="Loading In">In: {{ number_format($assignment->fee_loading_in/1000, 0) }}k</span>
                                    @endif
                                    @if($assignment->fee_loading_out > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" title="Loading Out">Out: {{ number_format($assignment->fee_loading_out/1000, 0) }}k</span>
                                    @endif
                                    @if($assignment->fee_meal > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300" title="Uang Makan">Makan: {{ number_format($assignment->fee_meal/1000, 0) }}k</span>
                                    @endif
                                    @if($assignment->fee_standby > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-cyan-50 dark:bg-cyan-950/50 text-cyan-700 dark:text-cyan-300" title="Fee Standby / Operator">Op: {{ number_format($assignment->fee_standby/1000, 0) }}k</span>
                                    @endif
                                    @if($assignment->fee_transport > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300" title="Transport Luar Kota">Trp: {{ number_format($assignment->fee_transport/1000, 0) }}k</span>
                                    @endif
                                    @if($assignment->fee_bonus > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300" title="Bonus / Lembur">Bns: {{ number_format($assignment->fee_bonus/1000, 0) }}k</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Total Honor -->
                            <td class="px-4 py-3 text-right">
                                <span class="font-extrabold text-slate-900 dark:text-white text-xs">
                                    Rp {{ number_format($assignment->total_fee, 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Status Job -->
                            <td class="px-4 py-3 text-center">
                                @if($assignment->status === 'acc')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        ✓ ACC
                                    </span>
                                @elseif($assignment->status === 'offered')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                        Ditawarkan
                                    </span>
                                @elseif($assignment->status === 'completed')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                        Menolak
                                    </span>
                                @endif
                            </td>

                            <!-- Status Pembayaran -->
                            <td class="px-4 py-3 text-center">
                                @if($assignment->payment_status === 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 flex items-center justify-center gap-1">
                                        <span>✅ Lunas</span>
                                    </span>
                                    <div class="text-[9px] text-slate-400 mt-0.5">
                                        {{ $assignment->payment_date?->format('d/m/Y') }} ({{ ucfirst($assignment->payment_method ?? 'transfer') }})
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">
                                        ⏳ Belum Lunas
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi & Slip -->
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- WA Share -->
                                    <a href="{{ $waUrl }}" target="_blank" class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-600 dark:text-emerald-400 transition-colors" title="Kirim Rincian via WhatsApp">
                                        💬
                                    </a>

                                    <!-- Tombol Tandai Lunas -->
                                    @if($assignment->payment_status === 'unpaid')
                                        <button type="button" @click="openSinglePayModal('{{ $assignment->id }}', '{{ $assignment->crew->nickname }}', {{ $assignment->total_fee }}, 'Rp {{ number_format($assignment->total_fee, 0, ',', '.') }}')"
                                            class="px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[10px] shadow-xs transition-colors" title="Tandai Sudah Transfer / Lunas">
                                            Bayar
                                        </button>
                                    @endif

                                    <!-- Slip Satuan -->
                                    <a href="{{ route('crew-payroll.slip', $assignment) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-slate-600 dark:text-slate-300 hover:text-indigo-600 transition-colors" title="Lihat & Cetak Slip Gaji Event">
                                        🧾
                                    </a>

                                    <!-- Slip Rekap Bulanan Kru -->
                                    <a href="{{ route('crew-payroll.crew-monthly-slip', ['crew' => $assignment->crew_id, 'month' => $month]) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-purple-50 dark:hover:bg-purple-900/40 text-slate-600 dark:text-slate-300 hover:text-purple-600 transition-colors" title="Rekap Slip Bulanan Kru Ini">
                                        📅
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-slate-400">
                                <p class="text-sm">Tidak ada data penugasan atau honor kru pada filter yang dipilih.</p>
                                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan bulan atau bersihkan kata kunci pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL 1: TANDAI LUNAS SATUAN -->
    <div x-show="showSinglePayModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showSinglePayModal" @click="showSinglePayModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-300 dark:border-slate-700">
                <form :action="'{{ url('/crew-payroll') }}/' + singlePayData.id + '/mark-paid'" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Pencairan Honor Kru</h3>
                                <p class="text-xs text-slate-500">Tandai pembayaran telah selesai ditransfer/tunai.</p>
                            </div>
                            <button type="button" @click="showSinglePayModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-1">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nama Kru:</span>
                                <span class="font-bold text-slate-900 dark:text-white" x-text="singlePayData.crewName"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Nominal Honor:</span>
                                <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm" x-text="singlePayData.formattedAmount"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Metode Pembayaran *</label>
                            <select name="payment_method" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                                <option value="transfer">Bank Transfer (BCA / Mandiri / BRI / dll)</option>
                                <option value="cash">Tunai / Cash Lapangan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Tanggal Bayar / Transfer *</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">No. Referensi / Bukti Transfer (Opsional)</label>
                            <input type="text" name="payment_reference" placeholder="Misal: Ref #98231 atau No Transaksi m-Banking"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="notes" placeholder="Catatan internal finance..."
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showSinglePayModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md">
                            Konfirmasi Pembayaran Lunas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: TANDAI LUNAS MASSAL (BATCH MARK PAID) -->
    <div x-show="showBatchPayModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showBatchPayModal" @click="showBatchPayModal = false" class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white dark:bg-slate-900 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-300 dark:border-slate-700">
                <form action="{{ route('crew-payroll.batch-mark-paid') }}" method="POST">
                    @csrf
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="assignment_ids[]" :value="id">
                    </template>

                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">Pencairan Honor Massal</h3>
                                <p class="text-xs text-slate-500">Tandai sekaligus <span class="font-bold text-indigo-600" x-text="selectedIds.length"></span> penugasan kru sebagai LUNAS.</p>
                            </div>
                            <button type="button" @click="showBatchPayModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Metode Pembayaran *</label>
                            <select name="payment_method" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                                <option value="transfer">Bank Transfer (Payroll Batch / KlikBCA / Mandiri MCM)</option>
                                <option value="cash">Tunai / Kas Kecil</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Tanggal Bayar / Transfer *</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs font-semibold text-slate-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">No. Batch / Referensi Payroll</label>
                            <input type="text" name="payment_reference" placeholder="Misal: Batch Payroll Mingguan #2026-10-W3"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white">
                        </div>
                    </div>

                    <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" @click="showBatchPayModal = false" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md">
                            Bayar Semua yang Dipilih
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

