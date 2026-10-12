<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Inventory — Omah Visual LED</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen">
    <!-- Navbar -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 flex items-center justify-center font-bold text-slate-950 text-xl shadow-lg shadow-orange-500/20">
                    OV
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight text-white">Omah Visual LED</h1>
                    <p class="text-xs text-slate-400">Inventory Management System • Gedung Utama</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                    PostgreSQL 18 Connected
                </span>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        <!-- Hero Welcome -->
        <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-800/80 to-slate-800/40 border border-slate-700/60 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-400">Core Inventory Foundation • MVP Draft 0.2</span>
                <h2 class="text-2xl font-bold text-white mt-1">Sistem Inventori Berhasil Diinisialisasi! 🎉</h2>
                <p class="text-sm text-slate-300 mt-1 max-w-2xl">
                    Struktur database, dual-tracking mode (individual unit vs kuantitas saldo), perhitungan otomatis luasan LED ($m^2$), dan pemisahan kondisi rusak/tersedia telah aktif di database PostgreSQL lokal Anda (<code class="text-amber-300">inventory_db</code>).
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                <span class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-xs text-slate-300 text-center">
                    Gedung Utama Omah Visual
                </span>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <p class="text-xs font-medium text-slate-400">Master Barang (Products)</p>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-white">{{ $totalProducts }}</span>
                    <span class="text-xs text-slate-400">Kategori Aktif</span>
                </div>
            </div>

            <div class="p-5 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <p class="text-xs font-medium text-slate-400">Unit Terlacak Individual (Assets)</p>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-amber-400">{{ $totalAssets }}</span>
                    <span class="text-xs text-slate-400">Processors, TV & Boxes</span>
                </div>
            </div>

            <div class="p-5 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <p class="text-xs font-medium text-slate-400">Titik Penyimpanan (Lokasi)</p>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-sky-400">{{ $totalLocations }}</span>
                    <span class="text-xs text-slate-400">Lemari 1-3 & Staging</span>
                </div>
            </div>

            <div class="p-5 rounded-xl bg-slate-800/60 border border-slate-700/50">
                <p class="text-xs font-medium text-slate-400">Total Akun & Role (RBAC)</p>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-emerald-400">{{ $totalUsers }}</span>
                    <span class="text-xs text-slate-400">Owner, Warehouse, dll</span>
                </div>
            </div>
        </div>

        <!-- Inventory Preview Table -->
        <div class="rounded-2xl bg-slate-800/40 border border-slate-700/60 overflow-hidden shadow-xl">
            <div class="p-5 border-b border-slate-700/60 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-white text-base">Sampel Data Inventaris Terintegrasi</h3>
                    <p class="text-xs text-slate-400">Hasil pemetaan dari data Excel (Komponen Videotron, TV, Processor, Tools)</p>
                </div>
                <span class="text-xs text-slate-400 bg-slate-900/60 px-3 py-1 rounded-full border border-slate-700">
                    Dual Tracking: Individual vs Quantity
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-700/60">
                        <tr>
                            <th class="px-5 py-3">Kode</th>
                            <th class="px-5 py-3">Nama Barang</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Mode Pelacakan</th>
                            <th class="px-5 py-3">Stok Fisik</th>
                            <th class="px-5 py-3">Stok Tersedia</th>
                            <th class="px-5 py-3">Kalkulasi Luas / Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        @foreach($products as $prod)
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="px-5 py-3.5 font-mono text-xs text-amber-400">{{ $prod->code }}</td>
                            <td class="px-5 py-3.5 font-medium text-white">
                                {{ $prod->name }}
                                @if($prod->brand)
                                    <span class="text-xs text-slate-400 block">{{ $prod->brand }} {{ $prod->specification ? '• ' . $prod->specification : '' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-300">{{ $prod->category->name ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                @if($prod->isIndividual())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Individual (Barcode/Asset ID)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                        Quantity (Kuantitas)
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-bold text-white">
                                {{ $prod->total_physical_stock }} {{ $prod->unit->symbol ?? '' }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-emerald-400">
                                {{ $prod->total_available_stock }} {{ $prod->unit->symbol ?? '' }}
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-400">
                                @if($prod->sqm_per_unit)
                                    <span class="text-amber-300 font-semibold">
                                        {{ $prod->calculateTotalSqm($prod->total_available_stock) }} m² total
                                    </span>
                                    <span class="block text-[11px] text-slate-500">({{ (float) $prod->sqm_per_unit }} m²/unit)</span>
                                @elseif($prod->isIndividual())
                                    <span>{{ $prod->assets()->count() }} Unit Terdaftar</span>
                                @else
                                    <span>Saldo Kuantitas</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Akun Pengguna & Role Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-5 rounded-2xl bg-slate-800/40 border border-slate-700/60">
                <h4 class="font-bold text-white text-sm mb-3">Akun Siap Pakai (Untuk Pengujian):</h4>
                <div class="space-y-2 text-xs">
                    <div class="p-2.5 rounded-lg bg-slate-900/60 border border-slate-700/40 flex justify-between items-center">
                        <div>
                            <span class="font-semibold text-amber-400">Owner & Branch Manager:</span>
                            <span class="text-slate-300 block">owner@omahvisual.com</span>
                        </div>
                        <span class="font-mono text-slate-400">password: password123</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-slate-900/60 border border-slate-700/40 flex justify-between items-center">
                        <div>
                            <span class="font-semibold text-sky-400">Warehouse Team:</span>
                            <span class="text-slate-300 block">warehouse@omahvisual.com</span>
                        </div>
                        <span class="font-mono text-slate-400">password: password123</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-slate-900/60 border border-slate-700/40 flex justify-between items-center">
                        <div>
                            <span class="font-semibold text-emerald-400">Admin LED:</span>
                            <span class="text-slate-300 block">admin@omahvisual.com</span>
                        </div>
                        <span class="font-mono text-slate-400">password: password123</span>
                    </div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-800/40 border border-slate-700/60">
                <h4 class="font-bold text-white text-sm mb-3">Catatan Integrasi Operasional:</h4>
                <ul class="space-y-2 text-xs text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="text-amber-400 font-bold">✓</span>
                        <span><strong>25 Unit Novastar Processors:</strong> Sudah terdaftar bernomor unik (<code class="text-amber-300">OVL-PROC-001</code> s/d <code class="text-amber-300">OVL-PROC-025</code>) siap untuk SPK / Surat Jalan event.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-400 font-bold">✓</span>
                        <span><strong>Pemisahan Kondisi:</strong> PSU Lite rusak 1 unit tercatat otomatis tanpa mengurangi stok fisik keseluruhan namun tidak dihitung di stok tersedia.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-400 font-bold">✓</span>
                        <span><strong>Formula Meter Persegi ($m^2$):</strong> Tim gudang menghitung 72 unit kabinet 100x50, sistem otomatis mengonversi menjadi 36.0 $m^2$ untuk sewa.</span>
                    </li>
                </ul>
            </div>
        </div>
    </main>
</body>
</html>
