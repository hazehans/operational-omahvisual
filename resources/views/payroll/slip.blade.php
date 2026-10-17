<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Slip Honor - {{ $assignment->crew->nickname }} - {{ $assignment->eventOrder->order_number }}</title>

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
    <div class="max-w-3xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 no-print">
        <a href="{{ route('crew-payroll.index') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition-colors flex items-center gap-1.5 shadow-xs">
            <span>← Kembali ke Rekap Payroll</span>
        </a>

        @php
            $waMessage = "Halo {$assignment->crew->nickname}, berikut slip honor Anda untuk event:\n"
                . "📌 Event: {$assignment->eventOrder->event_name}\n"
                . "📅 Tanggal: {$assignment->eventOrder->event_start_date->format('d/m/Y')}\n"
                . "📍 Venue: {$assignment->eventOrder->venue_name}\n"
                . "💼 Peran: {$assignment->role_label}\n"
                . "-----------------------------------\n"
                . ($assignment->fee_loading_in > 0 ? "• Loading In: Rp " . number_format($assignment->fee_loading_in, 0, ',', '.') . "\n" : "")
                . ($assignment->fee_loading_out > 0 ? "• Loading Out: Rp " . number_format($assignment->fee_loading_out, 0, ',', '.') . "\n" : "")
                . ($assignment->fee_meal > 0 ? "• Uang Makan: Rp " . number_format($assignment->fee_meal, 0, ',', '.') . "\n" : "")
                . ($assignment->fee_standby > 0 ? "• Standby/Operator: Rp " . number_format($assignment->fee_standby, 0, ',', '.') . "\n" : "")
                . ($assignment->fee_transport > 0 ? "• Transport: Rp " . number_format($assignment->fee_transport, 0, ',', '.') . "\n" : "")
                . ($assignment->fee_bonus > 0 ? "• Bonus/Lembur: Rp " . number_format($assignment->fee_bonus, 0, ',', '.') . "\n" : "")
                . "💰 TOTAL DITERIMA: Rp " . number_format($assignment->total_fee, 0, ',', '.') . "\n"
                . "Rekening: {$assignment->crew->bank_name} - {$assignment->crew->bank_account_number} (a.n {$assignment->crew->bank_account_holder})\n"
                . "Status: " . ($assignment->payment_status === 'paid' ? 'LUNAS' : 'MENUNGGU TRANSFER') . "\n\n"
                . "Terima kasih atas dedikasinya! — Omah Visual LED";
            $waUrl = $assignment->crew->formatWhatsappUrl($waMessage);
        @endphp

        <div class="flex items-center gap-2">
            <a href="{{ $waUrl }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                <span>💬 Kirim ke WhatsApp Kru</span>
            </a>

            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Printable Slip Document Sheet -->
    <div class="slip-sheet max-w-3xl mx-auto bg-white p-8 sm:p-10 rounded-2xl border border-slate-200 shadow-xl space-y-6">
        
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
                    SLIP HONOR KRU
                </div>
                <div class="text-[11px] font-mono text-slate-500 mt-1">
                    No: SLIP/{{ $assignment->eventOrder->order_number }}/{{ str_pad($assignment->id, 4, '0', STR_PAD_LEFT) }}
                </div>
                <div class="text-[11px] text-slate-500">
                    Tanggal: {{ now()->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>

        <!-- 2-Column Specs: Kru & Event -->
        <div class="grid grid-cols-2 gap-6 text-xs">
            <!-- Left: Identitas Kru -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-1">
                    Penerima Honor (Kru / Teknisi)
                </div>
                <div class="flex"><span class="w-28 text-slate-500">Nama Panggilan:</span><span class="font-bold text-slate-900">{{ $assignment->crew->nickname }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Nama Lengkap:</span><span class="font-semibold text-slate-800">{{ $assignment->crew->full_name }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">No. WhatsApp:</span><span>{{ $assignment->crew->phone }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Peran Event:</span><span class="font-bold text-indigo-700 uppercase">{{ $assignment->role_label }}</span></div>
                
                <div class="pt-2 border-t border-slate-200/80">
                    <span class="text-[11px] font-semibold text-slate-700 block mb-0.5">Rekening Pencairan:</span>
                    <span class="font-mono font-bold text-slate-900 block text-xs">
                        {{ $assignment->crew->bank_name ?? 'BANK' }}: {{ $assignment->crew->bank_account_number ?? '-' }}
                    </span>
                    <span class="text-[11px] text-slate-600 block">
                        a.n {{ $assignment->crew->bank_account_holder ?? $assignment->crew->full_name }}
                    </span>
                </div>
            </div>

            <!-- Right: Rincian Event -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-1">
                    Data Event & Penugasan
                </div>
                <div class="flex"><span class="w-28 text-slate-500">No. SPK / Order:</span><span class="font-mono font-bold text-slate-900">{{ $assignment->eventOrder->order_number }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Nama Event:</span><span class="font-bold text-slate-900">{{ $assignment->eventOrder->event_name }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Lokasi / Venue:</span><span class="font-semibold text-slate-800">{{ $assignment->eventOrder->venue_name }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Klien / Vendor:</span><span>{{ $assignment->eventOrder->customer->name }}</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Tgl Loading:</span><span class="font-semibold text-indigo-700">{{ $assignment->eventOrder->loading_date->format('d/m/Y') }} ({{ $assignment->eventOrder->loading_time }})</span></div>
                <div class="flex"><span class="w-28 text-slate-500">Tgl Show Event:</span><span>{{ $assignment->eventOrder->event_start_date->format('d/m/Y') }}</span></div>
            </div>
        </div>

        <!-- Table Rincian Komponen Honor -->
        <div class="border border-slate-300 rounded-xl overflow-hidden">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-300">
                    <tr>
                        <th class="px-4 py-2.5 w-10 text-center">No</th>
                        <th class="px-4 py-2.5">Deskripsi Komponen Pekerjaan / Tugas</th>
                        <th class="px-4 py-2.5 text-right w-36">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @php $rowNo = 1; @endphp

                    @if($assignment->fee_loading_in > 0)
                        <tr>
                            <td class="px-4 py-2.5 text-center text-slate-400 font-mono">{{ $rowNo++ }}</td>
                            <td class="px-4 py-2.5">
                                <div class="font-bold text-slate-900">Honor Bongkar Muat & Pasang (Loading In)</div>
                                <div class="text-[11px] text-slate-500">Instalasi kabinet LED, rigging, cabling, dan setup awal di venue.</div>
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($assignment->fee_loading_in, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    @if($assignment->fee_loading_out > 0)
                        <tr>
                            <td class="px-4 py-2.5 text-center text-slate-400 font-mono">{{ $rowNo++ }}</td>
                            <td class="px-4 py-2.5">
                                <div class="font-bold text-slate-900">Honor Pembongkaran & Muat Pulang (Loading Out)</div>
                                <div class="text-[11px] text-slate-500">Dismantling LED, packing ke hardcase, dan loading kembali ke armada.</div>
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($assignment->fee_loading_out, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    @if($assignment->fee_meal > 0)
                        <tr>
                            <td class="px-4 py-2.5 text-center text-slate-400 font-mono">{{ $rowNo++ }}</td>
                            <td class="px-4 py-2.5">
                                <div class="font-bold text-slate-900">Uang Makan Lapangan</div>
                                <div class="text-[11px] text-slate-500">Alokasi konsumsi kru selama bertugas di lokasi acara.</div>
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($assignment->fee_meal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    @if($assignment->fee_standby > 0)
                        <tr>
                            <td class="px-4 py-2.5 text-center text-slate-400 font-mono">{{ $rowNo++ }}</td>
                            <td class="px-4 py-2.5">
                                <div class="font-bold text-slate-900">Fee Standby / Operator Running Show</div>
                                <div class="text-[11px] text-slate-500">Pengawasan visual switcher, processor Novastar, dan standby selama acara.</div>
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($assignment->fee_standby, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    @if($assignment->fee_transport > 0)
                        <tr>
                            <td class="px-4 py-2.5 text-center text-slate-400 font-mono">{{ $rowNo++ }}</td>
                            <td class="px-4 py-2.5">
                                <div class="font-bold text-slate-900">Uang Transport / Luar Kota</div>
                                <div class="text-[11px] text-slate-500">Bantuan mobilitas kru mandiri atau perjalanan tugas luar kota.</div>
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($assignment->fee_transport, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    @if($assignment->fee_bonus > 0)
                        <tr>
                            <td class="px-4 py-2.5 text-center text-slate-400 font-mono">{{ $rowNo++ }}</td>
                            <td class="px-4 py-2.5">
                                <div class="font-bold text-slate-900">Bonus / Lembur / Insentif Tambahan</div>
                                <div class="text-[11px] text-slate-500">{{ $assignment->notes ?? 'Tambahan apresiasi kinerja khusus event.' }}</div>
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($assignment->fee_bonus, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    <!-- Total Row -->
                    <tr class="bg-slate-100 font-black text-slate-900">
                        <td colspan="2" class="px-4 py-3 text-right uppercase tracking-wider text-xs">
                            TOTAL DITERIMA (NETTO)
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-sm text-indigo-700">
                            Rp {{ number_format($assignment->total_fee, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Status Pembayaran Box -->
        <div class="p-3.5 rounded-xl border {{ $assignment->payment_status === 'paid' ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : 'bg-amber-50 border-amber-300 text-amber-900' }} flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="text-base">{{ $assignment->payment_status === 'paid' ? '✅' : '⏳' }}</span>
                <div>
                    <span class="font-bold uppercase tracking-wider">
                        Status: {{ $assignment->payment_status === 'paid' ? 'LUNAS (SUDAH DITRANSFER / DITERIMA)' : 'BELUM DICAIRKAN (PENDING APPROVAL FINANCE)' }}
                    </span>
                    @if($assignment->payment_status === 'paid')
                        <div class="text-[11px] text-emerald-700">
                            Dibayarkan pada {{ $assignment->payment_date?->translatedFormat('d F Y') }} via {{ strtoupper($assignment->payment_method ?? 'transfer') }}
                            @if($assignment->payment_reference) • Ref: {{ $assignment->payment_reference }} @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="text-right font-mono font-bold text-xs">
                Rp {{ number_format($assignment->total_fee, 0, ',', '.') }}
            </div>
        </div>

        <!-- Tanda Tangan Section -->
        <div class="grid grid-cols-2 gap-10 pt-6 text-center text-xs">
            <div>
                <p class="text-slate-500 font-semibold mb-16">Diserahkan Oleh (Finance / Operasional)</p>
                <div class="border-b border-slate-900 w-44 mx-auto mb-1"></div>
                <p class="font-bold text-slate-900">Omah Visual Management</p>
                <p class="text-[10px] text-slate-400">CV. Han Media Tekindo</p>
            </div>

            <div>
                <p class="text-slate-500 font-semibold mb-16">Diterima Oleh (Kru Pelaksana)</p>
                <div class="border-b border-slate-900 w-44 mx-auto mb-1"></div>
                <p class="font-bold text-slate-900">{{ $assignment->crew->full_name }}</p>
                <p class="text-[10px] text-slate-400 font-mono">{{ $assignment->crew->nickname }} ({{ $assignment->crew->crew_code }})</p>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="pt-4 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Dokumen ini dicetak secara otomatis dari Sistem Informasi Operasional Omah Visual LED.
        </div>
    </div>

</body>
</html>

