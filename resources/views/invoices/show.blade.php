@extends('layouts.app')

@section('title', 'Detail Invoice ' . $invoice->invoice_number . ' - Omah Visual')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ showPaymentModal: false }">
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('invoices.index') }}" class="hover:text-indigo-600 transition-colors">Invoice</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200 font-mono">{{ $invoice->invoice_number }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                <span class="font-mono">{{ $invoice->invoice_number }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                    {{ $invoice->status === 'paid' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : '' }}
                    {{ $invoice->status === 'partial' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : '' }}
                    {{ $invoice->status === 'unpaid' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20' : '' }}
                    {{ $invoice->status === 'cancelled' ? 'bg-slate-500/10 text-slate-500 border border-slate-500/20' : '' }}">
                    {{ match($invoice->status) {
                        'paid' => '✓ Lunas',
                        'partial' => '⏳ DP Diterima',
                        'unpaid' => 'Belum Dibayar',
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst($invoice->status)
                    } }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase">
                    {{ str_replace('_', ' ', $invoice->invoice_type) }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Diterbitkan pada {{ $invoice->invoice_date->translatedFormat('d F Y') }} • Jatuh Tempo {{ $invoice->due_date->translatedFormat('d F Y') }}
                @if($invoice->creator)
                    • Dibuat oleh {{ $invoice->creator->name }}
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Kirim WhatsApp Button -->
            <a href="{{ $invoice->whatsapp_url }}" target="_blank"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-md shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z" />
                </svg>
                <span>Bagikan ke WhatsApp</span>
            </a>

            <!-- Print A4 Button -->
            <a href="{{ route('invoices.print', $invoice) }}" target="_blank"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold transition-all shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Faktur A4</span>
            </a>

            <!-- Edit Button -->
            <a href="{{ route('invoices.edit', $invoice) }}"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit</span>
            </a>

            <!-- Delete Form -->
            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice {{ $invoice->invoice_number }} ini?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2 rounded-xl border border-rose-200 dark:border-rose-900/40 bg-rose-50/50 hover:bg-rose-100 dark:bg-rose-950/20 text-rose-600 transition-colors shadow-xs" title="Hapus Invoice">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Payment Progress Overview Card -->
    @php
        $percentPaid = $invoice->total_amount > 0 ? min(100, round(($invoice->paid_amount / $invoice->total_amount) * 100)) : 0;
    @endphp
    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Progres Pembayaran</span>
                <div class="text-lg font-bold text-slate-900 dark:text-white mt-0.5">
                    {{ $percentPaid }}% Terbayar
                    <span class="text-xs font-normal text-slate-500">
                        (Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }} dari Rp {{ number_format($invoice->total_amount, 0, ',', '.') }})
                    </span>
                </div>
            </div>

            <!-- Catat Pembayaran Button -->
            @if($invoice->balance_due > 0)
                <button type="button" @click="showPaymentModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>+ Catat Pembayaran Masuk</span>
                </button>
            @else
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-600 font-bold text-xs border border-emerald-500/20">
                    <span>✓ Tagihan Telah Lunas Penuh</span>
                </div>
            @endif
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-slate-100 dark:bg-slate-800 h-3 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $invoice->status === 'paid' ? 'bg-emerald-500' : ($invoice->status === 'partial' ? 'bg-amber-500' : 'bg-rose-500') }}"
                style="width: {{ $percentPaid }}%;"></div>
        </div>

        <!-- Amount Badges -->
        <div class="grid grid-cols-3 gap-3 pt-2 text-center text-xs">
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                <div class="text-slate-400">Total Tagihan</div>
                <div class="font-extrabold text-slate-900 dark:text-white mt-0.5">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
            </div>
            <div class="p-3 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/30">
                <div class="text-emerald-600 dark:text-emerald-400 font-semibold">Telah Diterima</div>
                <div class="font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
            </div>
            <div class="p-3 rounded-xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/30">
                <div class="text-rose-600 dark:text-rose-400 font-semibold">Sisa Belum Bayar</div>
                <div class="font-extrabold text-rose-600 dark:text-rose-400 mt-0.5">Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Main Content: Left & Right Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Customer, Event, Items Table -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Client & Event Card -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Client Info -->
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Ditagihkan Kepada</div>
                        <div class="text-base font-bold text-slate-900 dark:text-white">
                            <a href="{{ route('customers.show', $invoice->customer) }}" class="hover:text-indigo-600 transition-colors">
                                {{ $invoice->customer->name }}
                            </a>
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            {{ $invoice->customer->organization ?? 'Instansi / EO' }}
                        </div>
                        <div class="text-xs text-slate-600 dark:text-slate-400 mt-2 space-y-0.5">
                            <div>📞 {{ $invoice->customer->phone }}</div>
                            @if($invoice->customer->email)
                                <div>✉️ {{ $invoice->customer->email }}</div>
                            @endif
                            @if($invoice->customer->address)
                                <div class="text-slate-500">📍 {{ $invoice->customer->address }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- Event Reference -->
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Referensi Acara & Pesanan</div>
                        @if($invoice->eventOrder)
                            <div class="text-base font-bold text-slate-900 dark:text-white">
                                <a href="{{ route('orders.show', $invoice->eventOrder) }}" class="hover:text-indigo-600 transition-colors">
                                    {{ $invoice->eventOrder->event_name }}
                                </a>
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                No. Pesanan: <span class="font-mono font-semibold">{{ $invoice->eventOrder->order_number }}</span>
                            </div>
                            <div class="text-xs text-slate-600 dark:text-slate-400 mt-2 space-y-0.5">
                                <div>📅 Tanggal: {{ $invoice->eventOrder->event_start_date->format('d M Y') }}</div>
                                <div>📍 Venue: {{ $invoice->eventOrder->venue_name }}</div>
                                <div>📐 Luas Layar: <span class="font-bold">{{ $invoice->eventOrder->total_sqm }} m²</span></div>
                            </div>
                        @else
                            <div class="text-sm text-slate-500">Pesanan mandiri tanpa referensi SPK.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Line Items Table Card -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Rincian Layanan & Barang</h3>
                    @if($invoice->title)
                        <div class="text-xs text-slate-500 mt-0.5">{{ $invoice->title }}</div>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 uppercase text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-3 py-2.5 font-bold w-8 text-center">No</th>
                                <th class="px-3 py-2.5 font-bold">Deskripsi Layanan / Barang</th>
                                <th class="px-3 py-2.5 font-bold text-center w-20">Kuantitas</th>
                                <th class="px-3 py-2.5 font-bold text-center w-16">Satuan</th>
                                <th class="px-3 py-2.5 font-bold text-right w-28">Harga (Rp)</th>
                                <th class="px-3 py-2.5 font-bold text-right w-32">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($invoice->items as $idx => $item)
                                <tr>
                                    <td class="px-3 py-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="px-3 py-3 font-medium text-slate-900 dark:text-white">{{ $item->item_description }}</td>
                                    <td class="px-3 py-3 text-center font-semibold text-slate-700 dark:text-slate-300">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="px-3 py-3 text-center text-slate-500 uppercase">{{ $item->unit_name }}</td>
                                    <td class="px-3 py-3 text-right text-slate-700 dark:text-slate-300">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-right font-bold text-slate-900 dark:text-white">{{ number_format($item->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Financial Calculation Breakdown -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start gap-4">
                    <!-- Terbilang -->
                    <div class="text-xs text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-800 max-w-sm">
                        <div class="font-bold text-[10px] uppercase tracking-wider text-slate-400">Terbilang (IDR):</div>
                        <div class="italic font-medium text-slate-800 dark:text-slate-200 mt-0.5">
                            # {{ App\Models\Invoice::terbilang($invoice->total_amount) }} Rupiah #
                        </div>
                    </div>

                    <div class="w-full sm:w-64 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Subtotal:</span>
                            <span class="font-semibold text-slate-900 dark:text-white">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                        </div>

                        @if($invoice->discount > 0)
                            <div class="flex items-center justify-between text-rose-500">
                                <span>Diskon:</span>
                                <span>- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if($invoice->tax_percent > 0)
                            <div class="flex items-center justify-between text-slate-500">
                                <span>PPN ({{ $invoice->tax_percent }}%):</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700 text-sm font-extrabold text-slate-900 dark:text-white">
                            <span>Grand Total:</span>
                            <span class="text-indigo-600 dark:text-indigo-400">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes & Terms -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Info Bank -->
                <div class="p-4 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-1">
                    <div class="font-bold uppercase tracking-wider text-slate-400">Rekening Pembayaran Resmi</div>
                    <pre class="font-mono text-[11px] text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $invoice->bank_info }}</pre>
                </div>

                <!-- Syarat Ketentuan -->
                <div class="p-4 rounded-xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-1">
                    <div class="font-bold uppercase tracking-wider text-slate-400">Syarat & Ketentuan</div>
                    <pre class="font-sans text-[11px] text-slate-600 dark:text-slate-400 whitespace-pre-line">{{ $invoice->terms_conditions }}</pre>
                </div>
            </div>
        </div>

        <!-- Right: Riwayat Pembayaran Masuk (Payment Ledger) -->
        <div class="space-y-6">
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>💵 Riwayat Pembayaran</span>
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600">
                        {{ $invoice->payments->count() }} Transaksi
                    </span>
                </div>

                @forelse($invoice->payments as $payment)
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-xs text-indigo-600 dark:text-indigo-400">
                                {{ $payment->payment_number }}
                            </span>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="text-[11px] text-slate-500 space-y-0.5">
                            <div>📅 Tanggal: {{ $payment->payment_date->translatedFormat('d M Y') }}</div>
                            <div>🏦 Metode: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $payment->method_label }}</span></div>
                            @if($payment->reference_number)
                                <div>🔖 Ref: <span class="font-mono">{{ $payment->reference_number }}</span></div>
                            @endif
                            @if($payment->receiver)
                                <div>👤 Diterima oleh: {{ $payment->receiver->name }}</div>
                            @endif
                            @if($payment->notes)
                                <div class="italic text-slate-400">"{{ $payment->notes }}"</div>
                            @endif
                        </div>

                        <!-- Proof & Actions -->
                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700 flex items-center justify-between">
                            <div>
                                @if($payment->proof_image)
                                    <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        <span>📷 Lihat Bukti Transfer</span>
                                    </a>
                                @else
                                    <span class="text-[10px] text-slate-400">Tanpa Bukti Gambar</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1">
                                <!-- Print Kuitansi -->
                                <a href="{{ route('invoices.payments.receipt', $payment) }}" target="_blank" class="p-1 text-slate-500 hover:text-slate-900 dark:hover:text-white" title="Cetak Kuitansi Resmi">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>

                                <!-- Delete Payment -->
                                <form action="{{ route('invoices.payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Hapus catatan pembayaran ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-rose-500 hover:text-rose-700" title="Hapus Pembayaran">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-dashed border-slate-200 dark:border-slate-700 text-center text-xs text-slate-400 space-y-2">
                        <p>Belum ada catatan pembayaran yang diterima untuk invoice ini.</p>
                        @if($invoice->balance_due > 0)
                            <button type="button" @click="showPaymentModal = true" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                + Catat Pembayaran Sekarang
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- MODAL CATAT PEMBAYARAN MASUK -->
    <div x-show="showPaymentModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 space-y-4" @click.outside="showPaymentModal = false">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    Catat Pembayaran Masuk
                </h3>
                <button type="button" @click="showPaymentModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Tanggal Bayar -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Tanggal Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white">
                </div>

                <!-- Jumlah Bayar -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Nominal Pembayaran (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="amount" min="1" max="{{ $invoice->balance_due }}" value="{{ $invoice->balance_due }}" required
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-indigo-600 dark:text-indigo-400">
                    <p class="text-[11px] text-slate-400 mt-1">Sisa tagihan saat ini: Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}</p>
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Metode Pembayaran <span class="text-rose-500">*</span>
                    </label>
                    <select name="payment_method" required
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white">
                        <option value="transfer_bca">Transfer Bank BCA (4390865177 a/n M. Burhanudin)</option>
                        <option value="transfer_bca">Transfer Bank BCA (3660399282 a/n Balqist Hamada)</option>
                        <option value="transfer_bri">Transfer Bank BRI</option>
                        <option value="transfer_mandiri">Transfer Bank Mandiri</option>
                        <option value="cash">Tunai / Cash Lapangan</option>
                        <option value="qris">QRIS Omah Visual</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <!-- Nomor Referensi / Bukti Transfer -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        No. Referensi / No. Transaksi Bank
                    </label>
                    <input type="text" name="reference_number" placeholder="Contoh: TRF-BCA-98124..."
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white">
                </div>

                <!-- Upload Bukti Transfer -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Upload Foto Bukti Transfer (Opsional)
                    </label>
                    <input type="file" name="proof_image" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                </div>

                <!-- Catatan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1">
                        Catatan Pembayaran
                    </label>
                    <input type="text" name="notes" placeholder="Contoh: Pembayaran DP 50% via m-BCA..."
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showPaymentModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-xs">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

