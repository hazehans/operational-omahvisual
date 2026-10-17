<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Honor Bulanan - {{ $crew->nickname }} - {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .slip-sheet {
                box-shadow: none !important;
                border: 1px solid #94a3b8 !important;
                max-width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <!-- Top Action Bar (Screen Only) -->
    <div class="max-w-4xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 no-print">
        <a href="{{ route('crew-payroll.index', ['month' => $month]) }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition-colors flex items-center gap-1.5 shadow-xs">
            <span>← Kembali ke Rekap Payroll</span>
        </a>

        <!-- Filter Month Switcher -->
        <form method="GET" action="{{ route('crew-payroll.crew-monthly-slip', $crew) }}" class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-600">Pilih Periode:</label>
            <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()"
                class="px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-indigo-500">
        </form>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Rekap Bulanan (A4)</span>
            </button>
        </div>
    </div>

    <!-- Printable Slip Document Sheet -->
    <div class="slip-sheet max-w-4xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border border-slate-200 shadow-xl space-y-6">
        
        <!-- Header / Kop -->
        <div class="border-b-2 border-slate-900 pb-5 flex items-start justify-between">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xl tracking-tighter">
                    OV
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-tight text-slate-900 uppercase">OMAH VISUAL LED</h1>
                    <p class="text-xs text-slate-600 font-medium">CV. Han Media Tekindo • Persewaan & Operasional LED Videotron</p>
                    <p class="text-[11px] text-slate-400">Jl. Raya Operasional, Malang, Jawa Timur • WhatsApp: 0812-3456-7890</p>
                </div>
            </div>

            <div class="text-right">
                <div class="inline-block px-3 py-1 rounded-md bg-slate-100 border border-slate-300 font-mono text-xs font-bold text-slate-800">
                    REKAP HONOR BULANAN
                </div>
                <div class="text-[11px] font-bold text-indigo-700 mt-1 uppercase tracking-wide">
                    Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                </div>
                <div class="text-[11px] text-slate-500">
                    Dicetak: {{ now()->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Profil Kru & Rekening -->
        <div class="grid grid-cols-2 gap-6 text-xs p-4 rounded-xl bg-slate-50 border border-slate-200">
            <div class="space-y-1.5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-1">
                    Biodata Kru Lapangan
                </div>
                <div class="flex"><span class="w-28 text-slate-500">Nama Lengkap:</span><span class="font-bold text-slate-900">{{ $crew->full_name }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Nama Panggilan:</span><span class="font-bold text-slate-800">{{ $crew->nickname }} ({{ $crew->crew_code }})</span></div>
                <div class="flex"><span class="w-28 text-slate-500">No. WhatsApp:</span><span>{{ $crew->phone }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Domisili:</span><span>{{ $crew->city ?? 'Malang' }}</span></div>
            </div>

            <div class="space-y-1.5">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-1">
                    Rekening Pencairan Honor
                </div>
                <div class="flex"><span class="w-28 text-slate-500">Nama Bank:</span><span class="font-bold text-slate-900">{{ $crew->bank_name ?? '-' }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">No. Rekening:</span><span class="font-mono font-bold text-slate-900">{{ $crew->bank_account_number ?? 'Belum terdaftar' }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Atas Nama:</span><span class="font-semibold text-slate-800">{{ $crew->bank_account_holder ?? $crew->full_name }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Status Hubungan:</span><span class="capitalize">{{ str_replace('_', ' ', $crew->employment_type) }}</span></div>
            </div>
        </div>

        <!-- Summary KPI Metrics -->
        <div class="grid grid-cols-3 gap-3 text-center text-xs">
            <div class="p-3 rounded-xl bg-indigo-50 border border-indigo-200">
                <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-700">Total Honor Periode Ini</div>
                <div class="text-base font-extrabold text-indigo-900 mt-1">Rp {{ number_format($totalFee, 0, ',', '.') }}</div>
                <div class="text-[10px] text-indigo-600">{{ $assignments->count() }} Event Diikuti</div>
            </div>

            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Telah Dicairkan (Lunas)</div>
                <div class="text-base font-extrabold text-emerald-900 mt-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <div class="text-[10px] text-emerald-600">Sudah ditransfer ke rekening</div>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200">
                <div class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Sisa Belum Dibayar</div>
                <div class="text-base font-extrabold text-amber-900 mt-1">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
                <div class="text-[10px] text-amber-600">Pending pencairan</div>
            </div>
        </div>

        <!-- Table Event List -->
        <div class="border border-slate-300 rounded-xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-300">
                    <tr>
                        <th class="px-3 py-2.5 w-8 text-center">No</th>
                        <th class="px-3 py-2.5">Tanggal</th>
                        <th class="px-3 py-2.5">Event & Venue</th>
                        <th class="px-3 py-2.5">Peran</th>
                        <th class="px-3 py-2.5 text-right">Rincian Komponen</th>
                        <th class="px-3 py-2.5 text-right">Total Fee</th>
                        <th class="px-3 py-2.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($assignments as $idx => $as)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-3 py-2.5 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="px-3 py-2.5 font-semibold text-slate-700 whitespace-nowrap">
                                {{ $as->eventOrder->event_start_date->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="font-bold text-slate-900 leading-tight">{{ $as->eventOrder->event_name }}</div>
                                <div class="text-[10px] text-slate-500">{{ $as->eventOrder->venue_name }} • {{ $as->eventOrder->order_number }}</div>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-300 text-[10px] font-bold text-slate-700">
                                    {{ $as->role_label }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5 text-right text-[10px] text-slate-600">
                                <div class="space-y-0.5 font-mono">
                                    @if($as->fee_loading_in > 0) <span>In: {{ number_format($as->fee_loading_in/1000, 0) }}k</span> @endif
                                    @if($as->fee_loading_out > 0) <span>• Out: {{ number_format($as->fee_loading_out/1000, 0) }}k</span> @endif
                                    @if($as->fee_meal > 0) <span>• Makan: {{ number_format($as->fee_meal/1000, 0) }}k</span> @endif
                                    @if($as->fee_standby > 0) <span>• Op: {{ number_format($as->fee_standby/1000, 0) }}k</span> @endif
                                    @if($as->fee_transport > 0) <span>• Trp: {{ number_format($as->fee_transport/1000, 0) }}k</span> @endif
                                    @if($as->fee_bonus > 0) <span>• Bns: {{ number_format($as->fee_bonus/1000, 0) }}k</span> @endif
                                </div>
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($as->total_fee, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                @if($as->payment_status === 'paid')
                                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800">
                                        Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                Belum ada riwayat penugasan event untuk kru ini pada bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}.
                            </td>
                        </tr>
                    @endforelse

                    <!-- Grand Total Row -->
                    <tr class="bg-slate-100 font-black text-slate-900 border-t-2 border-slate-300">
                        <td colspan="5" class="px-3 py-3 text-right uppercase tracking-wider text-xs">
                            TOTAL REKAPITULASI HONOR BULANAN
                        </td>
                        <td class="px-3 py-3 text-right font-mono text-sm text-indigo-700">
                            Rp {{ number_format($totalFee, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-3 text-center text-[10px] text-slate-500 font-mono">
                            {{ $assignments->count() }} Event
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan Section -->
        <div class="grid grid-cols-2 gap-10 pt-6 text-center text-xs">
            <div>
                <p class="text-slate-500 font-semibold mb-16">Finance & Manajemen Operasional</p>
                <div class="border-b border-slate-900 w-48 mx-auto mb-1"></div>
                <p class="font-bold text-slate-900">Omah Visual Management</p>
                <p class="text-[10px] text-slate-400">CV. Han Media Tekindo</p>
            </div>

            <div>
                <p class="text-slate-500 font-semibold mb-16">Kru / Teknisi Yang Bersangkutan</p>
                <div class="border-b border-slate-900 w-48 mx-auto mb-1"></div>
                <p class="font-bold text-slate-900">{{ $crew->full_name }}</p>
                <p class="text-[10px] text-slate-400 font-mono">{{ $crew->nickname }} ({{ $crew->crew_code }})</p>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="pt-4 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Dicetak secara resmi melalui Portal Sistem Operasional Omah Visual LED.
        </div>
    </div>

</body>
</html>

