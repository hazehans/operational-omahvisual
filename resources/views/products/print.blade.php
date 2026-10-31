<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Ketersediaan Aset {{ $periodText }} - Omah Visual LED</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            html, body {
                background: white !important;
                color: black !important;
                font-size: 8.5pt !important;
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
            tr {
                page-break-inside: avoid;
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
    <div class="no-print max-w-5xl mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Master Barang</span>
            </a>
            <span class="text-xs text-slate-500 font-medium">Laporan Ketersediaan: <strong>{{ $periodText }}</strong></span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('products.export-csv') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold border border-slate-300 transition-all">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh CSV (Excel)</span>
            </a>

            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md shadow-indigo-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Simpan PDF (A4)</span>
            </button>
        </div>
    </div>

    <!-- Official A4 Paper Container -->
    <div class="max-w-5xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-md border border-slate-200 print-shadow-none">
        
        <!-- Header Kop Surat -->
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-4">
            <div class="space-y-1">
                <div class="text-2xl font-black tracking-tight text-slate-900 uppercase">
                    OMAH VISUAL LED
                </div>
                <div class="text-xs font-bold text-indigo-700 uppercase tracking-wider">
                    CV HAN MEDIA TEKINDO
                </div>
                <div class="text-[11px] text-slate-600 max-w-md mt-0.5 leading-relaxed">
                    Gedung Utama Omah Visual LED • Jl. Raya Tlogomas No. 12, Kota Malang, Jawa Timur<br>
                    Layanan Rental Videotron Indoor/Outdoor, Novastar Processor &amp; Production Rigging
                </div>
            </div>

            <div class="text-right">
                <h1 class="text-lg font-black text-slate-900 uppercase tracking-wider">LAPORAN KETERSEDIAAN ASET</h1>
                <div class="text-xs font-bold text-indigo-600 mt-0.5">PERIODE: {{ strtoupper($periodText) }}</div>
                <div class="text-[10px] text-slate-500 mt-1">
                    Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB oleh {{ auth()->user()->name }}
                </div>
            </div>
        </div>

        <!-- Executive Summary Cards -->
        <div class="grid grid-cols-4 gap-3 my-5">
            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50">
                <span class="text-[10px] uppercase font-bold text-slate-500 block">Katalog SKU Terdata</span>
                <div class="text-lg font-black text-slate-900 mt-0.5">{{ $totalProducts }} <span class="text-xs font-normal text-slate-600">item</span></div>
                <span class="text-[10px] text-emerald-600 font-semibold">{{ $totalActiveProducts }} item aktif beroperasi</span>
            </div>

            <div class="p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/50">
                <span class="text-[10px] uppercase font-bold text-emerald-800 block">Layar LED Siap Sewa</span>
                <div class="text-lg font-black text-emerald-700 mt-0.5">{{ number_format($totalSqmReady, 2) }} <span class="text-xs font-normal">m²</span></div>
                <span class="text-[10px] text-emerald-700 font-semibold">Total {{ $totalCabinetsReady }} unit kabinet</span>
            </div>

            <div class="p-3.5 rounded-xl border border-amber-200 bg-amber-50/50">
                <span class="text-[10px] uppercase font-bold text-amber-800 block">Servis &amp; Perbaikan</span>
                <div class="text-lg font-black text-amber-700 mt-0.5">{{ number_format($totalSqmInService + $totalSqmDamaged, 2) }} <span class="text-xs font-normal">m²</span></div>
                <span class="text-[10px] text-amber-700 font-semibold">{{ $totalServiceQuantity + $totalDamagedQuantity }} unit butuh tindak lanjut</span>
            </div>

            <div class="p-3.5 rounded-xl border border-indigo-200 bg-indigo-50/50">
                <span class="text-[10px] uppercase font-bold text-indigo-800 block">Indeks Kesiapan Armada</span>
                <div class="text-lg font-black text-indigo-700 mt-0.5">{{ $readinessPercentage }}%</div>
                <span class="text-[10px] text-indigo-700 font-semibold">Status: {{ $readinessPercentage >= 85 ? 'PRIMA / SIAP EVENT' : 'PERLU PERBAIKAN' }}</span>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="border border-slate-300 rounded-xl overflow-hidden mb-6">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-900 text-white uppercase text-[10px] tracking-wider font-bold">
                    <tr>
                        <th class="py-2.5 px-3 text-center w-8">No</th>
                        <th class="py-2.5 px-3">Kode SKU</th>
                        <th class="py-2.5 px-3">Nama Produk &amp; Seri</th>
                        <th class="py-2.5 px-3">Kategori</th>
                        <th class="py-2.5 px-2 text-center">Luas/Unit</th>
                        <th class="py-2.5 px-2 text-center text-emerald-300">Ready</th>
                        <th class="py-2.5 px-2 text-center text-amber-300">Servis</th>
                        <th class="py-2.5 px-2 text-center text-rose-300">Rusak</th>
                        <th class="py-2.5 px-2 text-center">Total</th>
                        <th class="py-2.5 px-3 text-right">Luas Ready</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-800">
                    @forelse($products as $idx => $prod)
                        @php
                            if ($prod->isIndividual()) {
                                $ready = $prod->assets->where('condition', 'available')->where('status', 'in_storage')->count();
                                $service = $prod->assets->where('condition', 'in_service')->count();
                                $damaged = $prod->assets->where('condition', 'damaged')->count();
                            } else {
                                $ready = (float) $prod->stockBalances->where('condition', 'available')->sum('quantity');
                                $service = (float) $prod->stockBalances->where('condition', 'in_service')->sum('quantity');
                                $damaged = (float) $prod->stockBalances->where('condition', 'damaged')->sum('quantity');
                            }
                            $total = $ready + $service + $damaged;
                            $sqmReady = $prod->sqm_per_unit ? round($ready * (float)$prod->sqm_per_unit, 2) : 0;
                        @endphp
                        <tr class="{{ $idx % 2 === 1 ? 'bg-slate-50' : 'bg-white' }}">
                            <td class="py-2 px-3 text-center text-slate-500 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-2 px-3 font-mono font-bold text-slate-900 whitespace-nowrap">{{ $prod->code }}</td>
                            <td class="py-2 px-3">
                                <span class="font-bold text-slate-900 block">{{ $prod->name }}</span>
                                @if($prod->brand || $prod->model_series)
                                    <span class="text-[10px] text-slate-500">{{ $prod->brand }} {{ $prod->model_series ? '• '.$prod->model_series : '' }}</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-slate-600">{{ $prod->category->name ?? '-' }}</td>
                            <td class="py-2 px-2 text-center font-mono">
                                {{ $prod->sqm_per_unit ? number_format((float)$prod->sqm_per_unit, 2).' m²' : '-' }}
                            </td>
                            <td class="py-2 px-2 text-center font-mono font-bold text-emerald-700 bg-emerald-50/40">
                                {{ $ready }}
                            </td>
                            <td class="py-2 px-2 text-center font-mono {{ $service > 0 ? 'font-bold text-amber-700 bg-amber-50/40' : 'text-slate-400' }}">
                                {{ $service }}
                            </td>
                            <td class="py-2 px-2 text-center font-mono {{ $damaged > 0 ? 'font-bold text-rose-700 bg-rose-50/40' : 'text-slate-400' }}">
                                {{ $damaged }}
                            </td>
                            <td class="py-2 px-2 text-center font-mono font-bold text-slate-900">
                                {{ $total }} <span class="text-[10px] font-normal text-slate-500">{{ $prod->unit->symbol ?? 'unit' }}</span>
                            </td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-indigo-700">
                                {{ $sqmReady > 0 ? number_format($sqmReady, 2).' m²' : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-6 text-center text-slate-400">Tidak ada data produk yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-100 font-bold text-slate-900 border-t-2 border-slate-300">
                    <tr>
                        <td colspan="5" class="py-2.5 px-3 text-right uppercase text-[10px] tracking-wider">Total Kesiapan Armada:</td>
                        <td class="py-2.5 px-2 text-center font-mono font-black text-emerald-700 text-sm">{{ $totalReadyQuantity }}</td>
                        <td class="py-2.5 px-2 text-center font-mono font-black text-amber-700 text-sm">{{ $totalServiceQuantity }}</td>
                        <td class="py-2.5 px-2 text-center font-mono font-black text-rose-700 text-sm">{{ $totalDamagedQuantity }}</td>
                        <td class="py-2.5 px-2 text-center font-mono font-black text-slate-900 text-sm">{{ $totalAllStock }}</td>
                        <td class="py-2.5 px-3 text-right font-mono font-black text-indigo-700 text-sm">{{ number_format($totalSqmReady, 2) }} m²</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Catatan & Pengesahan Dokumen -->
        <div class="grid grid-cols-3 gap-6 pt-6 border-t border-slate-300 text-center text-xs">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Dibuat Oleh:</span>
                <div class="h-16 flex items-end justify-center font-bold text-slate-800">
                    ( .................................................. )
                </div>
                <span class="text-[11px] text-slate-600 block mt-1 font-semibold">Kepala Logistik &amp; Gudang</span>
                <span class="text-[10px] text-slate-400">CV Han Media Tekindo</span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Diperiksa Oleh:</span>
                <div class="h-16 flex items-end justify-center font-bold text-slate-800">
                    ( .................................................. )
                </div>
                <span class="text-[11px] text-slate-600 block mt-1 font-semibold">Manager Operasional</span>
                <span class="text-[10px] text-slate-400">Omah Visual LED</span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-500 block mb-1">Disetujui Oleh:</span>
                <div class="h-16 flex items-end justify-center font-bold text-slate-800">
                    ( Hans / Direktur Utama )
                </div>
                <span class="text-[11px] text-slate-600 block mt-1 font-semibold">Owner &amp; Managing Director</span>
                <span class="text-[10px] text-slate-400">CV Han Media Tekindo</span>
            </div>
        </div>

    </div>

</body>
</html>

