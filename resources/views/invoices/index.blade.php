@extends('layouts.app')

@section('title', 'Daftar Invoice & Penagihan Client - Omah Visual')

@section('content')
<div class="space-y-6">
    <!-- Top Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                <span>📄 Invoice & Penagihan Client</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-500/10 text-cyan-500 border border-cyan-500/20">
                    Billing & Finance
                </span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola faktur tagihan sewa LED screen, pembayaran termin DP & pelunasan, serta cetak invoice dan kuitansi resmi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-md shadow-indigo-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Buat Invoice Baru</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Ditagihkan -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Ditagihkan</span>
                <span class="p-2 rounded-xl bg-indigo-500/10 text-indigo-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V9a1 1 0 011-1z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 text-2xl font-extrabold text-slate-900 dark:text-white">
                Rp {{ number_format($metrics['total_invoiced'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-slate-500">Seluruh invoice aktif yang diterbitkan</div>
        </div>

        <!-- Total Pembayaran Masuk -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-500">Total Diterima (Lunas/DP)</span>
                <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                Rp {{ number_format($metrics['total_paid'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-slate-500">Uang masuk via transfer & tunai</div>
        </div>

        <!-- Sisa Piutang Belum Lunas -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-500">Sisa Piutang (Belum Lunas)</span>
                <span class="p-2 rounded-xl bg-rose-500/10 text-rose-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 text-2xl font-extrabold text-rose-600 dark:text-rose-400">
                Rp {{ number_format($metrics['total_balance_due'], 0, ',', '.') }}
            </div>
            <div class="mt-1 text-xs text-slate-500">Menunggu pelunasan dari klien</div>
        </div>

        <!-- Overdue Invoices -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-500">Lewat Jatuh Tempo</span>
                <span class="p-2 rounded-xl bg-amber-500/10 text-amber-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
            </div>
            <div class="mt-3 text-2xl font-extrabold text-amber-600 dark:text-amber-400">
                {{ $metrics['overdue_count'] }} <span class="text-sm font-normal text-slate-500">Invoice</span>
            </div>
            <div class="mt-1 text-xs text-slate-500">Perlu tindak lanjut penagihan</div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <!-- Search -->
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Invoice, Customer, atau Acara..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            <!-- Status Buttons / Select -->
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-semibold">
                    <a href="{{ route('invoices.index', array_merge(request()->query(), ['status' => 'all'])) }}"
                        class="px-3 py-1.5 rounded-lg transition-all {{ !request('status') || request('status') === 'all' ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                        Semua
                    </a>
                    <a href="{{ route('invoices.index', array_merge(request()->query(), ['status' => 'unpaid'])) }}"
                        class="px-3 py-1.5 rounded-lg transition-all {{ request('status') === 'unpaid' ? 'bg-rose-500 text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                        Belum Bayar
                    </a>
                    <a href="{{ route('invoices.index', array_merge(request()->query(), ['status' => 'partial'])) }}"
                        class="px-3 py-1.5 rounded-lg transition-all {{ request('status') === 'partial' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                        DP / Sebagian
                    </a>
                    <a href="{{ route('invoices.index', array_merge(request()->query(), ['status' => 'paid'])) }}"
                        class="px-3 py-1.5 rounded-lg transition-all {{ request('status') === 'paid' ? 'bg-emerald-500 text-white shadow-xs' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white' }}">
                        Lunas
                    </a>
                </div>

                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition-all shadow-xs">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('invoices.index') }}" class="px-3 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 font-semibold">No. Invoice & Tipe</th>
                        <th class="px-6 py-4 font-semibold">Customer / Instansi</th>
                        <th class="px-6 py-4 font-semibold">Acara & Pesanan</th>
                        <th class="px-6 py-4 font-semibold text-right">Total Tagihan</th>
                        <th class="px-6 py-4 font-semibold text-right">Telah Dibayar</th>
                        <th class="px-6 py-4 font-semibold text-right">Sisa Piutang</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                        <th class="px-6 py-4 font-semibold text-center">Jatuh Tempo</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($invoices as $invoice)
                        @php
                            $isOverdue = in_array($invoice->status, ['unpaid', 'partial']) && $invoice->due_date->isPast();
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                            <!-- Invoice Number & Type -->
                            <td class="px-6 py-4">
                                <a href="{{ route('invoices.show', $invoice) }}" class="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $invoice->invoice_date->format('d M Y') }}
                                    <span class="ml-1 px-1.5 py-0.2 rounded text-[10px] uppercase font-semibold
                                        {{ $invoice->invoice_type === 'down_payment' ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' : '' }}
                                        {{ $invoice->invoice_type === 'settlement' ? 'bg-indigo-500/10 text-indigo-500 border border-indigo-500/20' : '' }}
                                        {{ $invoice->invoice_type === 'full' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : '' }}
                                        {{ $invoice->invoice_type === 'standard' || $invoice->invoice_type === 'custom' ? 'bg-slate-500/10 text-slate-500' : '' }}">
                                        {{ str_replace('_', ' ', $invoice->invoice_type) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Customer -->
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900 dark:text-white">
                                    {{ $invoice->customer->name }}
                                </div>
                                <div class="text-xs text-slate-400 truncate max-w-xs">
                                    {{ $invoice->customer->organization ?? 'Individu / EO' }}
                                </div>
                            </td>

                            <!-- Event & Order Link -->
                            <td class="px-6 py-4">
                                @if($invoice->eventOrder)
                                    <a href="{{ route('orders.show', $invoice->eventOrder) }}" class="font-medium text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        {{ $invoice->eventOrder->event_name }}
                                    </a>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $invoice->eventOrder->order_number }} • {{ $invoice->eventOrder->event_start_date->format('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Total Amount -->
                            <td class="px-6 py-4 text-right font-bold text-slate-900 dark:text-white">
                                Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}
                            </td>

                            <!-- Paid Amount -->
                            <td class="px-6 py-4 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format((float) $invoice->paid_amount, 0, ',', '.') }}
                            </td>

                            <!-- Balance Due -->
                            <td class="px-6 py-4 text-right font-bold {{ $invoice->balance_due > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}">
                                Rp {{ number_format((float) $invoice->balance_due, 0, ',', '.') }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold inline-block
                                    {{ $invoice->status === 'paid' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : '' }}
                                    {{ $invoice->status === 'partial' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : '' }}
                                    {{ $invoice->status === 'unpaid' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' : '' }}
                                    {{ $invoice->status === 'cancelled' ? 'bg-slate-500/10 text-slate-500 border border-slate-500/20' : '' }}">
                                    {{ match($invoice->status) {
                                        'paid' => '✓ Lunas',
                                        'partial' => '⏳ DP Diterima',
                                        'unpaid' => 'Belum Lunas',
                                        'cancelled' => 'Dibatalkan',
                                        default => ucfirst($invoice->status)
                                    } }}
                                </span>
                            </td>

                            <!-- Due Date -->
                            <td class="px-6 py-4 text-center">
                                <div class="text-xs font-medium {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                    {{ $invoice->due_date->format('d M Y') }}
                                </div>
                                @if($isOverdue)
                                    <div class="text-[10px] text-rose-500 font-semibold animate-pulse">Lewat Tempo</div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- WhatsApp Share -->
                                    <a href="{{ $invoice->whatsapp_url }}" target="_blank" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors" title="Kirim Tagihan ke WhatsApp Customer">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z" />
                                        </svg>
                                    </a>

                                    <!-- Print A4 -->
                                    <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="Cetak Faktur A4">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>

                                    <!-- View Details -->
                                    <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-xs font-semibold transition-colors">
                                        <span>Kelola</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-full w-12 h-12 mx-auto flex items-center justify-center text-slate-400">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                                        </svg>
                                    </div>
                                    <div class="font-bold text-slate-800 dark:text-slate-200">Belum ada invoice penagihan</div>
                                    <p class="text-xs text-slate-500">Mulai buat invoice pertama Anda untuk menagih biaya sewa LED kepada pelanggan atau EO.</p>
                                    <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-semibold hover:bg-indigo-500 transition-all">
                                        <span>+ Buat Invoice Pertama</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

