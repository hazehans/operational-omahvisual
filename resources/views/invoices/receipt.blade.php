<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi {{ $payment->payment_number }} - Omah Visual LED</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 20mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased p-4 sm:p-8">

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="no-print max-w-3xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.show', $payment->invoice) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Invoice</span>
            </a>
            <span class="text-xs text-slate-500 font-mono">{{ $payment->payment_number }}</span>
        </div>

        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Cetak Kuitansi Resmi (Print / PDF)</span>
        </button>
    </div>

    <!-- Official Kuitansi Container -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-md border-2 border-slate-900 relative">
        <!-- Watermark / Stamp -->
        <div class="absolute right-12 top-28 pointer-events-none transform rotate-[-8deg] opacity-75 border-4 border-emerald-600 rounded-2xl px-6 py-2 text-center">
            <span class="text-3xl font-black text-emerald-600 uppercase tracking-widest">LUNAS / DITERIMA</span>
            <div class="text-[10px] font-bold text-emerald-700 font-mono">{{ $payment->payment_date->format('d/m/Y') }}</div>
        </div>

        <!-- Header Kop Kuitansi -->
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-5">
            <div>
                <div class="text-2xl font-black tracking-tight text-slate-900 uppercase">
                    OMAH VISUAL LED
                </div>
                <div class="text-xs font-bold text-indigo-700 uppercase tracking-wider">
                    CV HAN MEDIA TEKINDO
                </div>
                <div class="text-[11px] text-slate-500 mt-1">
                    Gedung Utama Omah Visual LED • Jl. Raya Tlogomas No. 12, Kota Malang, Jawa Timur
                </div>
            </div>

            <div class="text-right">
                <h1 class="text-xl font-black text-slate-900 uppercase tracking-wider">KUITANSI PEMBAYARAN</h1>
                <div class="text-xs font-mono font-bold text-indigo-600 mt-0.5">{{ $payment->payment_number }}</div>
                <div class="text-xs text-slate-600 mt-1">Tanggal: <span class="font-bold text-slate-900">{{ $payment->payment_date->translatedFormat('d F Y') }}</span></div>
            </div>
        </div>

        <!-- Kuitansi Body -->
        <div class="my-8 space-y-4 text-xs sm:text-sm">
            <div class="grid grid-cols-4 gap-2 items-baseline py-2 border-b border-slate-100">
                <span class="text-slate-500 font-semibold">Telah Diterima Dari:</span>
                <span class="col-span-3 font-bold text-slate-900 text-base">
                    {{ $payment->invoice->customer->name }}
                    @if($payment->invoice->customer->organization)
                        <span class="text-xs font-normal text-slate-500">({{ $payment->invoice->customer->organization }})</span>
                    @endif
                </span>
            </div>

            <div class="grid grid-cols-4 gap-2 items-baseline py-2 border-b border-slate-100">
                <span class="text-slate-500 font-semibold">Uang Sejumlah:</span>
                <span class="col-span-3 text-lg font-black text-indigo-700">
                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                </span>
            </div>

            <div class="grid grid-cols-4 gap-2 items-baseline py-2 border-b border-slate-100">
                <span class="text-slate-500 font-semibold">Terbilang:</span>
                <span class="col-span-3 italic font-bold text-slate-800 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                    # {{ App\Models\Invoice::terbilang($payment->amount) }} Rupiah #
                </span>
            </div>

            <div class="grid grid-cols-4 gap-2 items-baseline py-2 border-b border-slate-100">
                <span class="text-slate-500 font-semibold">Untuk Pembayaran:</span>
                <span class="col-span-3 text-slate-800 font-medium space-y-1">
                    <div>
                        Pembayaran Invoice <span class="font-mono font-bold text-indigo-600">{{ $payment->invoice->invoice_number }}</span>
                        ({{ ucfirst(str_replace('_', ' ', $payment->invoice->invoice_type)) }})
                    </div>
                    @if($payment->eventOrder)
                        <div class="text-slate-600">
                            Acara: <strong>{{ $payment->eventOrder->event_name }}</strong> ({{ $payment->eventOrder->event_start_date->format('d M Y') }})
                        </div>
                    @endif
                    @if($payment->notes)
                        <div class="text-xs text-slate-500 italic">"{{ $payment->notes }}"</div>
                    @endif
                </span>
            </div>

            <div class="grid grid-cols-4 gap-2 items-baseline py-2 border-b border-slate-100">
                <span class="text-slate-500 font-semibold">Metode & Referensi:</span>
                <span class="col-span-3 text-slate-800 font-medium">
                    <span class="font-bold">{{ $payment->method_label }}</span>
                    @if($payment->reference_number)
                        <span class="font-mono text-xs text-slate-500 ml-2">(Ref: {{ $payment->reference_number }})</span>
                    @endif
                </span>
            </div>
        </div>

        <!-- Footer & Signatures -->
        <div class="grid grid-cols-2 gap-8 pt-6 border-t-2 border-slate-900 text-xs mt-8">
            <div class="space-y-1">
                <div class="text-[11px] text-slate-500 font-mono">
                    Total Tagihan Invoice: Rp {{ number_format($payment->invoice->total_amount, 0, ',', '.') }}<br>
                    Telah Dibayar Total: Rp {{ number_format($payment->invoice->paid_amount, 0, ',', '.') }}<br>
                    Sisa Tagihan: <strong class="{{ $payment->invoice->balance_due > 0 ? 'text-rose-600' : 'text-emerald-600' }}">Rp {{ number_format($payment->invoice->balance_due, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="text-center flex flex-col justify-between items-center h-32">
                <div class="text-slate-700">
                    Malang, {{ $payment->payment_date->translatedFormat('d F Y') }}<br>
                    <strong>Penerima Pembayaran,</strong>
                </div>

                <div>
                    <div class="font-bold text-slate-900 underline uppercase">
                        {{ $payment->receiver->name ?? 'Finance Omah Visual LED' }}
                    </div>
                    <div class="text-[10px] text-slate-500">CV Han Media Tekindo</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

