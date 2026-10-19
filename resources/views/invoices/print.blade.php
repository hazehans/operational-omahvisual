<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - {{ $invoice->customer->name }} - Omah Visual LED</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            html, body {
                background: white !important;
                color: black !important;
                font-size: 9pt !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .print-shadow-none {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 10mm 12mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased p-4 sm:p-8">

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Detail Invoice</span>
            </a>
            <span class="text-xs text-slate-500 font-mono">{{ $invoice->invoice_number }}</span>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Invoice A4 (Print / PDF)</span>
            </button>
        </div>
    </div>

    <!-- Official A4 Paper Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-md border border-slate-200 print-shadow-none relative">
        <!-- Stamp Overlay if Paid -->
        @if($invoice->status === 'paid')
            <div class="absolute right-12 top-40 pointer-events-none transform rotate-[-12deg] opacity-80 border-4 border-emerald-600 rounded-2xl px-6 py-2 text-center">
                <span class="text-3xl font-black text-emerald-600 uppercase tracking-widest">LUNAS</span>
                <div class="text-[10px] font-bold text-emerald-700 font-mono">PAID IN FULL</div>
            </div>
        @elseif($invoice->status === 'partial')
            <div class="absolute right-12 top-40 pointer-events-none transform rotate-[-12deg] opacity-80 border-4 border-amber-600 rounded-2xl px-6 py-2 text-center">
                <span class="text-2xl font-black text-amber-600 uppercase tracking-widest">DP DITERIMA</span>
                <div class="text-[10px] font-bold text-amber-700 font-mono">PARTIAL PAYMENT</div>
            </div>
        @endif

        <!-- Header Kop Surat -->
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-3">
            <div class="space-y-1">
                <div class="text-xl font-black tracking-tight text-slate-900 uppercase">
                    OMAH VISUAL LED
                </div>
                <div class="text-xs font-bold text-indigo-700 uppercase tracking-wider">
                    CV HAN MEDIA TEKINDO
                </div>
                <div class="text-[11px] text-slate-600 max-w-sm mt-0.5">
                    Gedung Utama Omah Visual LED • Jl. Raya Tlogomas No. 12, Kota Malang, Jawa Timur<br>
                    WhatsApp: +62 812-3456-7890 • Email: finance@omahvisual.com
                </div>
            </div>

            <div class="text-right">
                <h1 class="text-xl font-black text-slate-900 uppercase tracking-wider">FAKTUR TAGIHAN</h1>
                <div class="text-xs font-mono font-bold text-indigo-600 mt-0.5">{{ $invoice->invoice_number }}</div>
                <div class="mt-1 text-xs space-y-0.5 text-slate-600">
                    <div>Tanggal: <span class="font-semibold text-slate-900">{{ $invoice->invoice_date->translatedFormat('d F Y') }}</span></div>
                    <div>Jatuh Tempo: <span class="font-bold text-slate-900">{{ $invoice->due_date->translatedFormat('d F Y') }}</span></div>
                    <div class="uppercase text-[10px] font-bold text-slate-500 mt-0.5">
                        Tipe: {{ str_replace('_', ' ', $invoice->invoice_type) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bill To & Event Reference -->
        <div class="grid grid-cols-2 gap-6 my-3 py-2 border-b border-slate-200 text-xs">
            <!-- Ditagihkan Kepada -->
            <div>
                <span class="font-bold text-[10px] uppercase tracking-wider text-slate-400">DITAGIHKAN KEPADA:</span>
                <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $invoice->customer->name }}</div>
                <div class="font-semibold text-slate-700">{{ $invoice->customer->organization ?? 'Event Organizer / Client' }}</div>
                <div class="text-slate-600 mt-0.5 space-y-0.5">
                    <div>Telepon: {{ $invoice->customer->phone }}</div>
                    @if($invoice->customer->email)
                        <div>Email: {{ $invoice->customer->email }}</div>
                    @endif
                    @if($invoice->customer->address)
                        <div>Alamat: {{ $invoice->customer->address }}</div>
                    @endif
                </div>
            </div>

            <!-- Detail Event -->
            <div>
                <span class="font-bold text-[10px] uppercase tracking-wider text-slate-400">DETAIL ACARA / EVENT:</span>
                @if($invoice->eventOrder)
                    <div class="text-sm font-bold text-slate-900 mt-0.5">{{ $invoice->eventOrder->event_name }}</div>
                    <div class="text-slate-700 mt-0.5 space-y-0.5">
                        <div>No. Pesanan: <span class="font-mono font-bold">{{ $invoice->eventOrder->order_number }}</span></div>
                        <div>Tanggal Acara: <span class="font-semibold">{{ $invoice->eventOrder->event_start_date->translatedFormat('d F Y') }}</span></div>
                        <div>Lokasi Venue: {{ $invoice->eventOrder->venue_name }} {{ $invoice->eventOrder->kabupaten ? '('.$invoice->eventOrder->kabupaten.')' : '' }}</div>
                        <div>Luas Layar: <span class="font-bold">{{ $invoice->eventOrder->total_sqm }} m²</span></div>
                    </div>
                @else
                    <div class="text-slate-500 mt-1">{{ $invoice->title ?? 'Sewa Peralatan Multimedia' }}</div>
                @endif
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="my-3">
            <table class="w-full text-left text-xs border border-slate-200">
                <thead class="bg-slate-100 uppercase text-slate-700 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2 font-bold w-8 text-center border-r border-slate-200">No</th>
                        <th class="px-3 py-2 font-bold border-r border-slate-200">Deskripsi Layanan & Spesifikasi</th>
                        <th class="px-2 py-2 font-bold text-center w-16 border-r border-slate-200">Qty</th>
                        <th class="px-2 py-2 font-bold text-center w-16 border-r border-slate-200">Satuan</th>
                        <th class="px-3 py-2 font-bold text-right w-28 border-r border-slate-200">Harga Satuan (Rp)</th>
                        <th class="px-3 py-2 font-bold text-right w-32">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($invoice->items as $idx => $item)
                        <tr>
                            <td class="px-3 py-1.5 text-center text-slate-500 border-r border-slate-200">{{ $idx + 1 }}</td>
                            <td class="px-3 py-1.5 font-medium text-slate-900 border-r border-slate-200">{{ $item->item_description }}</td>
                            <td class="px-2 py-1.5 text-center font-semibold border-r border-slate-200">{{ number_format($item->quantity, 2) }}</td>
                            <td class="px-2 py-1.5 text-center text-slate-600 uppercase border-r border-slate-200">{{ $item->unit_name }}</td>
                            <td class="px-3 py-1.5 text-right border-r border-slate-200">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-3 py-1.5 text-right font-bold text-slate-900">{{ number_format($item->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Calculation & Terbilang -->
        <div class="flex justify-between items-start gap-6 my-3 text-xs">
            <div class="w-1/2 space-y-2">
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                    <span class="font-bold text-[10px] uppercase tracking-wider text-slate-500">TERBILANG:</span>
                    <div class="italic font-bold text-slate-800 mt-0.5">
                        # {{ App\Models\Invoice::terbilang($invoice->total_amount) }} Rupiah #
                    </div>
                </div>

                @if($invoice->payments->count() > 0)
                    <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-200 text-[11px] space-y-1">
                        <div class="font-bold text-emerald-800 uppercase tracking-wider text-[10px]">RIWAYAT PEMBAYARAN:</div>
                        @foreach($invoice->payments as $p)
                            <div class="flex justify-between text-slate-700">
                                <span>{{ $p->payment_date->format('d/m/Y') }} - {{ $p->method_label }}:</span>
                                <span class="font-bold text-emerald-700">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="border-t border-emerald-200 pt-1 flex justify-between font-bold text-slate-900">
                            <span>Sisa Kurang:</span>
                            <span class="{{ $invoice->balance_due > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="w-1/2 max-w-xs space-y-1.5 border border-slate-200 p-3 rounded-xl bg-slate-50/50">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-slate-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($invoice->discount > 0)
                    <div class="flex justify-between text-rose-600">
                        <span>Diskon:</span>
                        <span>- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if($invoice->tax_percent > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>PPN ({{ $invoice->tax_percent }}%):</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="border-t-2 border-slate-900 pt-1.5 flex justify-between text-sm font-black text-slate-900">
                    <span>TOTAL:</span>
                    <span class="text-indigo-700">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Bank Info & Signatures -->
        <div class="grid grid-cols-2 gap-6 pt-3 border-t border-slate-200 text-xs">
            <div class="space-y-1.5">
                <div class="font-bold text-[10px] uppercase tracking-wider text-slate-500">REKENING PEMBAYARAN RESMI:</div>
                <div class="font-mono text-[10px] text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-200 leading-relaxed whitespace-pre-line">{{ $invoice->bank_info }}</div>
                <div class="text-[10px] text-slate-500 italic mt-0.5">
                    *Harap mencantumkan nomor invoice pada berita transfer bank.
                </div>
            </div>

            <div class="text-center flex flex-col justify-between items-center h-28">
                <div class="text-slate-700">
                    Malang, {{ $invoice->invoice_date->translatedFormat('d F Y') }}<br>
                    <strong>Hormat Kami, Finance Omah Visual LED</strong>
                </div>

                <div>
                    <div class="font-bold text-slate-900 underline uppercase">
                        {{ $invoice->creator->name ?? 'Finance Omah Visual' }}
                    </div>
                    <div class="text-[10px] text-slate-500">CV Han Media Tekindo</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

