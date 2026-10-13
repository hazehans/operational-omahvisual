<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPK - {{ $assignment->spk_number }} - Omahvisual LED</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.3;
        }
        .page {
            page-break-after: always;
            break-after: page;
            min-height: 275mm;
            position: relative;
        }
        .page:last-child {
            page-break-after: avoid;
            break-after: avoid;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
        }
        .box-container {
            border: 1.5px solid #000;
            padding: 10px;
            margin-bottom: 12px;
        }
        .banner-blue {
            background-color: #bcd4e6;
            color: #000;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            margin-top: 15px;
            border: 1px solid #7a9cb5;
        }
        .banner-red {
            background-color: #f7b2b7;
            color: #000;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            margin-top: 15px;
            border: 1px solid #d88188;
        }
        .sign-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin-top: 4px;
            margin-bottom: 10px;
        }
        .sign-table td {
            width: 33.3%;
            padding: 6px;
            font-size: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 2.5px 4px;
        }
        .data-table th {
            background-color: #e6e6e6;
            font-weight: bold;
            text-align: center;
        }
        .cat-row {
            background-color: #f0f4f8;
            font-weight: bold;
        }
        .print-btn-bar {
            background: #f1f5f9;
            padding: 10px 20px;
            border-bottom: 1px solid #cbd5e1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media print {
            .print-btn-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="print-btn-bar">
    <div>
        <strong>Surat Perintah Kerja (SPK) & Cargo List</strong> • {{ $assignment->spk_number }}
    </div>
    <div>
        <button onclick="window.print()" style="padding: 6px 16px; background: #4f46e5; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak ke PDF / Printer Fisik
        </button>
    </div>
</div>

<div style="max-width: 210mm; margin: 0 auto; padding: 10mm;">

    <!-- ========================================== -->
    <!-- HALAMAN 1: SERAH TERIMA & PENUGASAN        -->
    <!-- ========================================== -->
    <div class="page">
        <div class="header-title">
            <span>☑️ 1 SERAH TERIMA</span>
        </div>

        <div style="background: #e2e8f0; padding: 4px 8px; font-weight: bold; font-size: 10px; border: 1px solid #94a3b8; margin-bottom: 12px;">
            Penugasan Team lapangan untuk memenuhi kebutuhan event client dengan detail :
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <tr>
                <td style="width: 58%; vertical-align: top; padding-right: 15px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="width: 110px; padding: 3px 0;">Nama Client</td><td>: {{ $assignment->eventOrder->customer->name }} ({{ $assignment->eventOrder->customer->organization ?? '-' }})</td></tr>
                        <tr><td style="padding: 3px 0;">Tgl Event</td><td>: {{ $assignment->eventOrder->event_start_date->format('d/m/Y') }}</td></tr>
                        <tr><td style="padding: 3px 0;">Jam Event</td><td>: {{ $assignment->eventOrder->event_time ?? '-' }}</td></tr>
                        <tr><td style="padding: 3px 0; font-weight: bold;">Tgl Loading</td><td style="font-weight: bold;">: {{ $assignment->eventOrder->loading_date->format('d/m/Y') }}</td></tr>
                        <tr><td style="padding: 3px 0; font-weight: bold;">Jam Loading</td><td style="font-weight: bold;">: {{ $assignment->eventOrder->loading_time }}</td></tr>
                        <tr><td style="padding: 3px 0;">Lokasi Event</td><td>: {{ $assignment->eventOrder->venue_name }}</td></tr>
                        <tr><td style="padding: 3px 0;">Ukuran LED</td><td>: {{ $assignment->eventOrder->screens->map(fn($s) => $s->width_m.'x'.$s->height_m.'m '.$s->brand_series)->implode(', ') }}</td></tr>
                        <tr><td style="padding: 3px 0; font-weight: bold;">Jumlah SQM</td><td style="font-weight: bold;">: {{ $assignment->eventOrder->total_sqm }} SQM</td></tr>
                        <tr><td style="padding: 3px 0;">Keterangan Lain</td><td>: {{ $assignment->eventOrder->notes ?? '-' }}</td></tr>
                    </table>

                    <div style="margin-top: 25px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            @for($i = 1; $i <= 7; $i++)
                                @php $field = "crew_{$i}_name"; @endphp
                                <tr>
                                    <td style="width: 80px; padding: 2px 0;">Crew {{ $i }}</td>
                                    <td>: {{ $assignment->$field ?? '............................................' }}</td>
                                </tr>
                            @endfor
                        </table>
                    </div>
                </td>

                <td style="width: 42%; vertical-align: top; border-left: 1px dashed #ccc; padding-left: 15px;">
                    <!-- 2D STAGE & LED BLUEPRINT DIAGRAM -->
                    <div style="border: 1.5px solid #000; padding: 4px; margin-bottom: 8px; text-align: center; background: #fff;">
                        <div style="font-weight: bold; font-size: 9px; text-transform: uppercase; background: #e2e8f0; padding: 2px; margin-bottom: 4px; border: 1px solid #cbd5e1;">
                            📐 BLUEPRINT LAYOUT PANGGUNG 2D
                        </div>
                        @if($assignment->eventOrder->stage_layout_image)
                            <img src="{{ $assignment->eventOrder->stage_layout_image }}" style="width: 100%; max-height: 135px; object-fit: contain; display: block; margin: 0 auto; border: 1px solid #e2e8f0;">
                        @else
                            <div style="height: 110px; border: 1px dashed #94a3b8; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; font-size: 9px; color: #475569;">
                                <div style="font-weight: bold; font-size: 10px; color: #000;">
                                    Panggung: {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m
                                </div>
                                <div style="margin-top: 4px; font-weight: bold; color: #1e3a8a;">
                                    {{ $assignment->eventOrder->screens->map(fn($s) => $s->screen_name . ' (' . $s->width_m . 'x' . $s->height_m . 'm)')->implode(' + ') }}
                                </div>
                                <div style="font-size: 8px; color: #64748b; margin-top: 4px;">
                                    ▼ Bibir Panggung Menghadap Penonton ▼
                                </div>
                            </div>
                        @endif
                        <div style="font-size: 8px; color: #334155; margin-top: 2px; font-weight: bold;">
                            Dimensi: {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m (Level {{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m)
                        </div>
                        <div style="font-size: 7.5px; color: #1e40af; margin-top: 2px; font-weight: bold; background: #eff6ff; padding: 2px; border: 1px solid #bfdbfe;">
                            📌 Detail Teknis Tampak Atas & Depan: Lihat Lembar 4
                        </div>
                    </div>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <tr><td style="width: 100px; padding: 3px 0;">Kendaraan</td><td>: {{ $assignment->vehicle_name ?? 'TRAGA 1' }}</td></tr>
                        <tr><td style="padding: 3px 0;">E toll</td><td>: {{ $assignment->has_etoll ? 'Ada / Siap' : '.......' }}</td></tr>
                        <tr><td style="padding: 3px 0;">Kunci Ban</td><td>: {{ $assignment->has_kunci_ban ? 'Ada / Siap' : '.......' }}</td></tr>
                        <tr><td style="padding: 3px 0;">STNK</td><td>: {{ $assignment->has_stnk ? 'Ada / Siap' : '.......' }}</td></tr>
                        <tr><td style="padding: 3px 0;">Kartu KIR</td><td>: {{ $assignment->has_kir ? 'Ada / Siap' : '.......' }}</td></tr>
                        <tr><td style="padding: 3px 0;">BardCode BBM</td><td>: {{ $assignment->has_barcode_bbm ? 'Ada / Siap' : '.......' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Signature Section -->
        <div style="margin-top: 50px;">
            <p style="font-size: 10px; margin: 2px 0;">- Dengan menandatangani checklist ini, berarti kamu bertanggung jawab penuh atas isi checklist dan menyatakan bahwa semua kelengkapan sudah dicek dan diserahterimakan dengan teliti dan benar.</p>
            <p style="font-size: 10px; margin: 2px 0;">- Terdapat 2 Lembar cheklist yang sangat Crusial , dan wajib teliti</p>

            <div class="banner-blue">KEBERANGKATAN</div>
            <table class="sign-table">
                <tr>
                    <td style="height: 50px; vertical-align: top;">TTD PIC EVENT</td>
                    <td style="vertical-align: top;">JAM DAN TGL<br><br><span style="font-weight: bold;">{{ $assignment->departure_datetime?->format('d/m/Y H:i') ?? '..................................' }}</span></td>
                    <td style="vertical-align: top;">TTD PIC GUDANG</td>
                </tr>
                <tr>
                    <td>( {{ $assignment->departure_pic_event_name ?? 'Nama Terang' }} )</td>
                    <td></td>
                    <td>( {{ $assignment->departure_pic_warehouse_name ?? 'Nama Terang' }} )</td>
                </tr>
            </table>

            <div style="text-align: center; font-size: 10px; font-weight: bold; margin: 8px 0; color: #b91c1c;">
                PC Kepulangan Barang Wajib Orang yg berbeda
            </div>

            <div class="banner-red">KEPULANGAN</div>
            <table class="sign-table">
                <tr>
                    <td style="height: 50px; vertical-align: top;">TTD PIC EVENT</td>
                    <td style="vertical-align: top;">JAM DAN TGL<br><br><span style="font-weight: bold;">{{ $assignment->return_datetime?->format('d/m/Y H:i') ?? '..................................' }}</span></td>
                    <td style="vertical-align: top;">TTD PIC GUDANG</td>
                </tr>
                <tr>
                    <td>( {{ $assignment->return_pic_event_name ?? 'Nama Terang' }} )</td>
                    <td></td>
                    <td>( {{ $assignment->return_pic_warehouse_name ?? 'Nama Terang' }} )</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- HALAMAN 2: CHECKLIST BARANG KERJA TEAM     -->
    <!-- ========================================== -->
    <div class="page">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 6px;">
            <div style="font-size: 13px; font-weight: bold;">
                2 Cheklist Barang Kerja TEAM Omahvisual LED
            </div>
            <div style="font-size: 9px; text-align: right;">
                No Event : {{ $assignment->eventOrder->order_number }}<br>
                Nama Client : {{ $assignment->eventOrder->customer->name }}
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 9.5px; margin-bottom: 6px;">
            <div>Nama Pic Event : {{ $assignment->crew_pic_event_name ?? '..................' }}</div>
            <div>Nama Pic Gudang : {{ $assignment->departure_pic_warehouse_name ?? '..................' }}</div>
            <div>Tgl Load : {{ $assignment->eventOrder->loading_date->format('d/m/Y') }}</div>
            <div>Tgl Event : {{ $assignment->eventOrder->event_start_date->format('d/m/Y') }}</div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th>Barang Bawaan KE VENUE</th>
                    <th style="width: 140px;">Detail</th>
                    <th style="width: 50px;">QTY</th>
                    <th style="width: 45px;">Keluar</th>
                    <th style="width: 45px;">Kembali</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; $currentCat = ''; @endphp
                @foreach($assignment->checklistItems->where('page_number', 2) as $item)
                    @if($currentCat !== $item->category)
                        @php $currentCat = $item->category; @endphp
                        <tr class="cat-row">
                            <td colspan="6" style="padding: 2px 5px; font-size: 9px;">
                                <strong>{{ $currentCat }}</strong>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->item_detail ?? '' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $item->quantity }} {{ $item->unit }}</td>
                        <td style="text-align: center;">{{ $item->checked_out ? '✓' : '' }}</td>
                        <td style="text-align: center;">{{ $item->checked_in ? '✓' : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ========================================== -->
    <!-- HALAMAN 3: CHECKLIST PIC (KIT & SPARES)    -->
    <!-- ========================================== -->
    <div class="page">
        <div style="border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 8px;">
            <div style="font-size: 13px; font-weight: bold; text-align: center;">
                3 CHEKLIST PIC Omahvisual LED
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th>Barang Bawaan KE VENUE</th>
                    <th style="width: 140px;">Detail</th>
                    <th style="width: 50px;">QTY</th>
                    <th style="width: 45px;">Keluar</th>
                    <th style="width: 45px;">Kembali</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; $currentCat = ''; @endphp
                @foreach($assignment->checklistItems->where('page_number', 3) as $item)
                    @if($currentCat !== $item->category)
                        @php $currentCat = $item->category; @endphp
                        <tr class="cat-row">
                            <td colspan="6" style="padding: 2px 5px; font-size: 9px;">
                                <strong>{{ $currentCat }}</strong>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->item_detail ?? '' }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $item->quantity }} {{ $item->unit }}</td>
                        <td style="text-align: center;">{{ $item->checked_out ? '✓' : '' }}</td>
                        <td style="text-align: center;">{{ $item->checked_in ? '✓' : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 4: BLUEPRINT TEKNIS TATA LETAK PANGGUNG & FORMASI LAYAR LED       -->
    <!-- ========================================================================= -->
    <div class="page">
        <!-- Title & Event Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 8px;">
            <div>
                <div style="font-size: 13px; font-weight: bold; text-transform: uppercase;">
                    4 BLUEPRINT TEKNIS: TATA LETAK PANGGUNG &amp; FORMASI LAYAR LED
                </div>
                <div style="font-size: 9px; color: #334155; font-weight: bold;">
                    Engineering Stage Plot &amp; LED Screen Rigging Blueprint • Omahvisual LED
                </div>
            </div>
            <div style="font-size: 9px; text-align: right;">
                <strong>No. SPK:</strong> {{ $assignment->spk_number }}<br>
                <strong>No. Event:</strong> {{ $assignment->eventOrder->order_number }}
            </div>
        </div>

        <!-- Project Meta Strip -->
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 8px; background: #f8fafc; border: 1px solid #cbd5e1;">
            <tr>
                <td style="padding: 4px 6px; width: 33%;"><strong>Client:</strong> {{ $assignment->eventOrder->customer->name }}</td>
                <td style="padding: 4px 6px; width: 33%;"><strong>Venue:</strong> {{ $assignment->eventOrder->venue_name }}</td>
                <td style="padding: 4px 6px; width: 34%;"><strong>Tgl Event:</strong> {{ $assignment->eventOrder->event_start_date->format('d/m/Y') }} (Load: {{ $assignment->eventOrder->loading_date->format('d/m/Y') }})</td>
            </tr>
            <tr>
                <td style="padding: 4px 6px;"><strong>Ukuran Panggung:</strong> {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m (Level +{{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m)</td>
                <td style="padding: 4px 6px;"><strong>Total Layar:</strong> {{ $assignment->eventOrder->screens->count() }} Unit ({{ $assignment->eventOrder->total_sqm }} m²)</td>
                <td style="padding: 4px 6px;"><strong>PIC Event:</strong> {{ $assignment->crew_pic_event_name ?? '-' }}</td>
            </tr>
        </table>

        <!-- DIAGRAM 1: TAMPAK ATAS (TOP-DOWN 2D FLOOR PLAN) -->
        <div style="border: 1.5px solid #000; margin-bottom: 8px; background: #fff;">
            <div style="background: #1e293b; color: #fff; padding: 3px 8px; font-size: 10px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                <span>📐 1. TAMPAK ATAS (TOP-DOWN FLOOR PLAN) — BIDANG PANGGUNG & FORMASI LAYAR TERHADAP PENONTON</span>
                <span style="font-size: 8.5px; color: #38bdf8;">Grid Skala: 1 Kotak = 1 Meter</span>
            </div>
            <div style="padding: 4px; text-align: center; background: #0f172a;">
                @if($assignment->eventOrder->stage_layout_image)
                    <img src="{{ $assignment->eventOrder->stage_layout_image }}" style="width: 100%; max-height: 230px; object-fit: contain; display: block; margin: 0 auto;">
                @else
                    <div style="height: 150px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-size: 10px;">
                        <span style="font-size: 11px; font-weight: bold; color: #e2e8f0; margin-bottom: 4px;">Panggung: {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m</span>
                        <span>{{ $assignment->eventOrder->screens->map(fn($s) => $s->screen_name . ' (' . $s->width_m . 'x' . $s->height_m . 'm)')->implode(' • ') }}</span>
                        <span style="margin-top: 6px; color: #facc15;">▼ BIBIR PANGGUNG MENGHADAP PENONTON ▼</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- DIAGRAM 2: TAMPAK DEPAN (FRONT ELEVATION / PERSPEKTIF PENONTON) -->
        <div style="border: 1.5px solid #000; margin-bottom: 8px; background: #fff;">
            <div style="background: #1e293b; color: #fff; padding: 3px 8px; font-size: 10px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                <span>🎭 2. TAMPAK DEPAN (FRONT ELEVATION) — LEVEL TINGGI PANGGUNG, ELEVASI LAYAR & TRUSS GANTUNG</span>
                <span style="font-size: 8.5px; color: #facc15;">Perspektif Penonton</span>
            </div>
            <div style="padding: 4px; text-align: center; background: #0f172a;">
                @if($assignment->eventOrder->stage_layout_front_image)
                    <img src="{{ $assignment->eventOrder->stage_layout_front_image }}" style="width: 100%; max-height: 230px; object-fit: contain; display: block; margin: 0 auto;">
                @else
                    <div style="height: 150px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-size: 10px;">
                        <span style="font-size: 11px; font-weight: bold; color: #e2e8f0; margin-bottom: 4px;">Elevasi Panggung: Level +{{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m</span>
                        <span>Ground Stack / Rigging Truss Gantung</span>
                        <span style="margin-top: 6px; color: #38bdf8;">(Render blueprint elevasi otomatis saat simpan layout di sistem)</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- TABEL SPESIFIKASI TEKNIK PEMASANGAN & RIGGING -->
        <table class="data-table" style="margin-bottom: 8px;">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th>Nama Posisi Layar</th>
                    <th style="width: 75px;">Dimensi Fisik</th>
                    <th style="width: 60px;">Luas SQM</th>
                    <th style="width: 80px;">Total Kabinet</th>
                    <th style="width: 100px;">Metode Pasang</th>
                    <th style="width: 110px;">Prosesor & Sinyal</th>
                    <th>Catatan Rigging & Safety</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignment->eventOrder->screens as $idx => $sc)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                        <td style="font-weight: bold;">{{ $sc->screen_name }}</td>
                        <td style="text-align: center;">{{ $sc->width_m }}m × {{ $sc->height_m }}m</td>
                        <td style="text-align: center; font-weight: bold; color: #1e40af;">{{ $sc->total_sqm }} m²</td>
                        <td style="text-align: center;">{{ $sc->total_cabinets }} Kabinet ({{ $sc->total_hardcases }} Box)</td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $sc->rigging_method === 'hanging' ? 'Truss Gantung' : 'Ground Stack' }}
                        </td>
                        <td style="font-size: 8.5px;">{{ $sc->processor_model }}</td>
                        <td style="font-size: 8.5px;">{{ $sc->stage_specification ?? 'Perhatikan level kerataan & ballast panggung' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- RIGGING CHECK & SIGN-OFF BOX -->
        <div style="border: 1px solid #000; padding: 6px; background: #fff;">
            <div style="font-size: 9px; font-weight: bold; margin-bottom: 4px; text-transform: uppercase;">
                Pernyataan Kelayakan Instalasi Rigging & Visual Panggung:
            </div>
            <div style="font-size: 8.5px; color: #334155; line-height: 1.25; margin-bottom: 6px;">
                1. Kerataan lantai level panggung telah dicek menggunakan waterpass sebelum modul kabinet pertama dipasang.<br>
                2. Sistem penguncian kabinet (quick-lock) dan safety wire terpasang lengkap sesuai standar keselamatan kerja.<br>
                3. Distribusi beban daya listrik telah dibagi merata dan grounding terpasang aman.
            </div>
            <table class="sign-table" style="margin-bottom: 0;">
                <tr>
                    <td style="height: 35px; vertical-align: top;">TTD PIC RIGGING / STAGE LEAD</td>
                    <td style="vertical-align: top;">TTD OPERATOR VISUAL / SYSTEM</td>
                    <td style="vertical-align: top;">TTD PIC EVENT (LAPANGAN)</td>
                </tr>
                <tr>
                    <td>( .................................................. )</td>
                    <td>( .................................................. )</td>
                    <td>( {{ $assignment->crew_pic_event_name ?? '..................................' }} )</td>
                </tr>
            </table>
        </div>
    </div>

</div>

</body>
</html>

