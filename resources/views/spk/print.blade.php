<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPK - {{ $assignment->spk_number }} - Omahvisual LED</title>
    <style>
        /* =========================================================================
           A4 PRINT STYLESHEET (PORTRAIT & LANDSCAPE HYBRID CALIBRATION)
           Calibrated for A4 (210mm x 297mm) with zero margin double-padding.
           Pages 1-3: A4 Portrait
           Page 4: A4 Landscape (Dedicated Wide Stage Blueprint & Rigging Plot)
           ========================================================================= */
        @page {
            size: A4 portrait;
            margin: 8mm 10mm;
        }

        @page landscape-sheet {
            size: A4 landscape;
            margin: 8mm 10mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background: #94a3b8;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.3;
        }

        /* SCREEN PREVIEW CONTAINER */
        @media screen {
            body {
                background: #64748b;
                padding: 15px 0 60px 0;
            }
            .print-btn-bar {
                position: sticky;
                top: 0;
                z-index: 100;
                background: #0f172a;
                color: #fff;
                padding: 12px 24px;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                border-bottom: 2px solid #3b82f6;
            }
            .print-container {
                max-width: 297mm;
                margin: 20px auto;
                padding: 0;
            }
            .page {
                background: #fff;
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto 30px auto;
                padding: 8mm 10mm;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35), 0 8px 10px -6px rgba(0, 0, 0, 0.35);
                position: relative;
            }
            .page-landscape {
                width: 297mm !important;
                min-height: 210mm !important;
                max-width: 297mm !important;
                padding: 8mm 10mm !important;
            }
        }

        /* PHYSICAL PRINT / PDF EXPORT RULES */
        @media print {
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .print-btn-bar {
                display: none !important;
            }
            .print-container {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .page {
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .page-landscape {
                page: landscape-sheet !important;
                page-break-before: always !important;
                break-before: page !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .page:last-child {
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
            .print-hide {
                display: none !important;
            }
        }

        /* HEADER & TYPOGRAPHY */
        .header-title {
            font-size: 15px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .banner-blue {
            background-color: #bcd4e6 !important;
            color: #000;
            font-weight: bold;
            font-size: 10.5px;
            text-align: center;
            padding: 3px 6px;
            margin-top: 10px;
            border: 1px solid #7a9cb5;
        }

        .banner-red {
            background-color: #f7b2b7 !important;
            color: #000;
            font-weight: bold;
            font-size: 10.5px;
            text-align: center;
            padding: 3px 6px;
            margin-top: 8px;
            border: 1px solid #d88188;
        }

        .sign-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin-top: 3px;
            margin-bottom: 6px;
        }

        .sign-table td {
            width: 33.3%;
            padding: 4px 6px;
            font-size: 10px;
            vertical-align: top;
        }

        /* READABLE DATA TABLE FOR CHECKLIST (53 & 48 ITEMS WITHOUT SPILL) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            line-height: 1.25;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 2px 4px;
        }

        .data-table th {
            background-color: #e2e8f0 !important;
            font-weight: bold;
            text-align: center;
            padding: 3px 4px;
            font-size: 9.5px;
        }

        .cat-row {
            background-color: #bcd4e6 !important;
            font-weight: bold;
        }

        .cat-row td {
            padding: 2px 5px;
            font-size: 9.5px;
        }

        /* ACTION BUTTONS */
        .btn-print {
            padding: 7px 16px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .btn-print:hover {
            background: #4338ca;
        }
        .btn-sub-print {
            padding: 6px 12px;
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #475569;
            border-radius: 6px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-sub-print:hover {
            background: #334155;
            color: #fff;
        }
    </style>
</head>
<body>

<!-- TOP ACTION BAR (HIDDEN ON PRINT) -->
<div class="print-btn-bar">
    <div style="display: flex; align-items: center; gap: 12px;">
        <span style="font-size: 15px; font-weight: bold; color: #38bdf8;">Omahvisual LED</span>
        <span style="color: #64748b;">|</span>
        <span style="font-size: 13px; font-weight: 600;">SPK &amp; Cargo List • {{ $assignment->spk_number }}</span>
    </div>
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <button type="button" onclick="printAllPages()" class="btn-print" title="Cetak Seluruh Lembar (1 s/d 4)">
            🖨️ Cetak Semua (Hal 1-4)
        </button>
        <button type="button" onclick="printSinglePage(1)" class="btn-sub-print" title="Cetak Lembar Serah Terima & Surat Jalan Saja">
            📄 Hal 1 (Surat Jalan)
        </button>
        <button type="button" onclick="printChecklistPages()" class="btn-sub-print" title="Cetak Lembar Checklist Muatan Gudang (Hal 2 & 3)">
            📦 Hal 2-3 (Checklist)
        </button>
        <button type="button" onclick="printSinglePage(4)" class="btn-sub-print" title="Cetak Lembar Blueprint Teknis Panggung (Landscape)">
            📐 Hal 4 (Blueprint Landscape)
        </button>
        <a href="{{ route('orders.show', $assignment->eventOrder) }}" class="btn-sub-print" style="text-decoration: none; display: inline-flex; align-items: center;">
            ✕ Tutup
        </a>
    </div>
</div>

<div class="print-container">

    <!-- ========================================================================= -->
    <!-- HALAMAN 1: SERAH TERIMA & PENUGASAN ARMADA (PORTRAIT)                      -->
    <!-- ========================================================================= -->
    <div class="page" id="page-1">
        <div style="border: 1.5px solid #000; padding: 10px 12px;">
            <div class="header-title">
                <span>☑️ 1 SERAH TERIMA</span>
            </div>

            <div style="background: #e2e8f0; padding: 4px 8px; font-weight: bold; font-size: 10.5px; border: 1px solid #94a3b8; margin-bottom: 10px;">
                Penugasan Team lapangan untuk memenuhi kebutuhan event client dengan detail :
            </div>

            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <tr>
                    <td style="width: 58%; vertical-align: top; padding-right: 14px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                            <tr><td style="width: 105px; padding: 2.5px 0;">Nama Client</td><td>: <strong>{{ $assignment->eventOrder->customer->name }}</strong> ({{ $assignment->eventOrder->customer->organization ?? '-' }})</td></tr>
                            <tr><td style="padding: 2.5px 0;">Tgl Event</td><td>: {{ $assignment->eventOrder->event_start_date->format('d/m/Y') }}</td></tr>
                            <tr><td style="padding: 2.5px 0;">Jam Event</td><td>: {{ $assignment->eventOrder->event_time ?? '-' }}</td></tr>
                            <tr><td style="padding: 2.5px 0; font-weight: bold;">Tgl Loading</td><td style="font-weight: bold;">: {{ $assignment->eventOrder->loading_date->format('d/m/Y') }}</td></tr>
                            <tr><td style="padding: 2.5px 0; font-weight: bold;">Jam Loading</td><td style="font-weight: bold;">: {{ $assignment->eventOrder->loading_time }}</td></tr>
                            <tr><td style="padding: 2.5px 0;">Lokasi Event</td><td>: {{ $assignment->eventOrder->venue_name }}</td></tr>
                            <tr><td style="padding: 2.5px 0;">Ukuran LED</td><td>: {{ $assignment->eventOrder->screens->map(fn($s) => $s->width_m.'x'.$s->height_m.'m '.$s->brand_series)->implode(', ') }}</td></tr>
                            <tr><td style="padding: 2.5px 0; font-weight: bold;">Jumlah SQM</td><td style="font-weight: bold; color: #1e40af;">: {{ $assignment->eventOrder->total_sqm }} SQM</td></tr>
                            <tr><td style="padding: 2.5px 0;">Keterangan Lain</td><td>: {{ $assignment->eventOrder->notes ?? '-' }}</td></tr>
                        </table>

                        <div style="margin-top: 14px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
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

                    <td style="width: 42%; vertical-align: top; border-left: 1px dashed #94a3b8; padding-left: 14px;">
                        <!-- 2D STAGE & LED BLUEPRINT DIAGRAM THUMBNAIL -->
                        <div style="border: 1.5px solid #000; padding: 4px; margin-bottom: 8px; text-align: center; background: #fff;">
                            <div style="font-weight: bold; font-size: 9px; text-transform: uppercase; background: #e2e8f0; padding: 2px; margin-bottom: 4px; border: 1px solid #cbd5e1;">
                                📐 BLUEPRINT LAYOUT PANGGUNG 2D
                            </div>
                            @if($assignment->eventOrder->stage_layout_image)
                                <img src="{{ $assignment->eventOrder->stage_layout_image }}" style="width: 100%; max-height: 120px; object-fit: contain; display: block; margin: 0 auto; border: 1px solid #e2e8f0;">
                            @else
                                <div style="height: 100px; border: 1px dashed #94a3b8; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; font-size: 9px; color: #475569;">
                                    <div style="font-weight: bold; font-size: 10px; color: #000;">
                                        Panggung: {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m
                                    </div>
                                    <div style="margin-top: 2px; font-weight: bold; color: #1e3a8a;">
                                        {{ $assignment->eventOrder->screens->map(fn($s) => $s->screen_name . ' (' . $s->width_m . 'x' . $s->height_m . 'm)')->implode(' + ') }}
                                    </div>
                                    <div style="font-size: 8px; color: #64748b; margin-top: 2px;">
                                        ▼ Bibir Panggung Menghadap Penonton ▼
                                    </div>
                                </div>
                            @endif
                            <div style="font-size: 8px; color: #334155; margin-top: 2px; font-weight: bold;">
                                Dimensi: {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m (Level {{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m)
                            </div>
                            <div style="font-size: 8px; color: #1e40af; margin-top: 2px; font-weight: bold; background: #eff6ff; padding: 2px; border: 1px solid #bfdbfe;">
                                📌 Detail Teknis Lengkap: Lihat Lembar 4 (Landscape)
                            </div>
                        </div>

                        <table style="width: 100%; border-collapse: collapse; font-size: 10.5px; margin-top: 6px;">
                            <tr><td style="width: 90px; padding: 2.5px 0;">Kendaraan</td><td>: <strong>{{ $assignment->vehicle_name ?? 'TRAGA 1' }}</strong></td></tr>
                            <tr><td style="padding: 2.5px 0;">E toll</td><td>: {{ $assignment->has_etoll ? 'Ada / Siap' : '.......' }}</td></tr>
                            <tr><td style="padding: 2.5px 0;">Kunci Ban</td><td>: {{ $assignment->has_kunci_ban ? 'Ada / Siap' : '.......' }}</td></tr>
                            <tr><td style="padding: 2.5px 0;">STNK</td><td>: {{ $assignment->has_stnk ? 'Ada / Siap' : '.......' }}</td></tr>
                            <tr><td style="padding: 2.5px 0;">Kartu KIR</td><td>: {{ $assignment->has_kir ? 'Ada / Siap' : '.......' }}</td></tr>
                            <tr><td style="padding: 2.5px 0;">BardCode BBM</td><td>: {{ $assignment->has_barcode_bbm ? 'Ada / Siap' : '.......' }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Signature Section -->
            <div style="margin-top: 14px;">
                <p style="font-size: 9.5px; margin: 1px 0; color: #1e293b;">- Dengan menandatangani checklist ini, berarti kamu bertanggung jawab penuh atas isi checklist dan menyatakan bahwa semua kelengkapan sudah dicek dan diserahterimakan dengan teliti dan benar.</p>
                <p style="font-size: 9.5px; margin: 1px 0; color: #1e293b;">- Terdapat 2 Lembar cheklist yang sangat Crusial , dan wajib teliti</p>

                <div class="banner-blue">KEBERANGKATAN</div>
                <table class="sign-table">
                    <tr>
                        <td style="height: 42px; vertical-align: top;">TTD PIC EVENT</td>
                        <td style="vertical-align: top;">JAM DAN TGL<br><span style="font-weight: bold;">{{ $assignment->departure_datetime?->format('d/m/Y H:i') ?? '..................................' }}</span></td>
                        <td style="vertical-align: top;">TTD PIC GUDANG</td>
                    </tr>
                    <tr>
                        <td>( {{ $assignment->departure_pic_event_name ?? 'Nama Terang' }} )</td>
                        <td></td>
                        <td>( {{ $assignment->departure_pic_warehouse_name ?? 'Nama Terang' }} )</td>
                    </tr>
                </table>

                <div style="text-align: center; font-size: 9.5px; font-weight: bold; margin: 4px 0; color: #b91c1c;">
                    PC Kepulangan Barang Wajib Orang yg berbeda
                </div>

                <div class="banner-red">KEPULANGAN</div>
                <table class="sign-table">
                    <tr>
                        <td style="height: 42px; vertical-align: top;">TTD PIC EVENT</td>
                        <td style="vertical-align: top;">JAM DAN TGL<br><span style="font-weight: bold;">{{ $assignment->return_datetime?->format('d/m/Y H:i') ?? '..................................' }}</span></td>
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
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 2: CHECKLIST BARANG KERJA TEAM (53 ITEMS EXACT CALIBRATION)        -->
    <!-- ========================================================================= -->
    <div class="page" id="page-2">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 5px;">
            <div style="font-size: 14px; font-weight: bold;">
                2 Cheklist Barang Kerja TEAM Omahvisual LED
            </div>
            <div style="font-size: 9.5px; text-align: right;">
                No Event : {{ $assignment->eventOrder->order_number }}<br>
                Nama Client : <strong>{{ $assignment->eventOrder->customer->name }}</strong>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 9.5px; margin-bottom: 5px; background: #f8fafc; padding: 3px 6px; border: 1px solid #e2e8f0;">
            <div>Nama Pic Event : <strong>{{ $assignment->crew_pic_event_name ?? '..................' }}</strong></div>
            <div>Nama Pic Gudang : <strong>{{ $assignment->departure_pic_warehouse_name ?? '..................' }}</strong></div>
            <div>Tgl Load : <strong>{{ $assignment->eventOrder->loading_date->format('d/m/Y') }}</strong></div>
            <div>Tgl Event : <strong>{{ $assignment->eventOrder->event_start_date->format('d/m/Y') }}</strong></div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th>Barang Bawaan KE VENUE</th>
                    <th style="width: 155px;">Detail</th>
                    <th style="width: 55px;">QTY</th>
                    <th style="width: 48px;">Keluar</th>
                    <th style="width: 48px;">Kembali</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; $currentCat = ''; @endphp
                @foreach($assignment->checklistItems->where('page_number', 2) as $item)
                    @if($currentCat !== $item->category)
                        @php $currentCat = $item->category; @endphp
                        <tr class="cat-row">
                            <td colspan="6" style="padding: 2px 5px; font-size: 9.5px;">
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
    <!-- HALAMAN 3: CHECKLIST PIC (KIT & SPARES - 48 ITEMS EXACT CALIBRATION)       -->
    <!-- ========================================================================= -->
    <div class="page" id="page-3">
        <div style="border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 14px; font-weight: bold;">
                3 CHEKLIST PIC Omahvisual LED
            </div>
            <div style="font-size: 9.5px; text-align: right;">
                No Event : {{ $assignment->eventOrder->order_number }} • Client : <strong>{{ $assignment->eventOrder->customer->name }}</strong>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th>Barang Bawaan KE VENUE</th>
                    <th style="width: 155px;">Detail</th>
                    <th style="width: 55px;">QTY</th>
                    <th style="width: 48px;">Keluar</th>
                    <th style="width: 48px;">Kembali</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; $currentCat = ''; @endphp
                @foreach($assignment->checklistItems->where('page_number', 3) as $item)
                    @if($currentCat !== $item->category)
                        @php $currentCat = $item->category; @endphp
                        <tr class="cat-row">
                            <td colspan="6" style="padding: 2px 5px; font-size: 9.5px;">
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
    <!-- HALAMAN 4: BLUEPRINT TEKNIS PANGGUNG & LED (A4 LANDSCAPE ORIENTATION)     -->
    <!-- ========================================================================= -->
    <div class="page page-landscape" id="page-4">
        <!-- Title & Event Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 6px;">
            <div>
                <div style="font-size: 14px; font-weight: bold; text-transform: uppercase;">
                    4 BLUEPRINT TEKNIS: TATA LETAK PANGGUNG &amp; FORMASI LAYAR LED
                </div>
                <div style="font-size: 9.5px; color: #334155; font-weight: bold;">
                    Engineering Stage Plot &amp; LED Screen Rigging Blueprint • Omahvisual LED (A4 Landscape View)
                </div>
            </div>
            <div style="font-size: 9.5px; text-align: right;">
                <strong>No. SPK:</strong> {{ $assignment->spk_number }}<br>
                <strong>No. Event:</strong> {{ $assignment->eventOrder->order_number }}
            </div>
        </div>

        <!-- Project Meta Strip -->
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 8px; background: #f8fafc; border: 1px solid #cbd5e1;">
            <tr>
                <td style="padding: 3.5px 6px; width: 33%;"><strong>Client:</strong> {{ $assignment->eventOrder->customer->name }}</td>
                <td style="padding: 3.5px 6px; width: 33%;"><strong>Venue:</strong> {{ $assignment->eventOrder->venue_name }}</td>
                <td style="padding: 3.5px 6px; width: 34%;"><strong>Tgl Event:</strong> {{ $assignment->eventOrder->event_start_date->format('d/m/Y') }} (Load: {{ $assignment->eventOrder->loading_date->format('d/m/Y') }})</td>
            </tr>
            <tr>
                <td style="padding: 3.5px 6px;"><strong>Ukuran Panggung:</strong> {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m (Level +{{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m)</td>
                <td style="padding: 3.5px 6px;"><strong>Total Layar:</strong> {{ $assignment->eventOrder->screens->count() }} Unit ({{ $assignment->eventOrder->total_sqm }} m²)</td>
                <td style="padding: 3.5px 6px;"><strong>PIC Event:</strong> {{ $assignment->crew_pic_event_name ?? '-' }}</td>
            </tr>
        </table>

        <!-- DUAL BLUEPRINTS SIDE-BY-SIDE (WIDE HORIZONTAL FORMAT) -->
        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
            <!-- DIAGRAM 1: TAMPAK ATAS (TOP-DOWN 2D FLOOR PLAN) -->
            <div style="flex: 1; border: 1.5px solid #000; background: #fff;">
                <div style="background: #1e293b; color: #fff; padding: 3px 6px; font-size: 9.5px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                    <span>📐 1. TAMPAK ATAS (TOP-DOWN FLOOR PLAN) — BIDANG PANGGUNG &amp; FORMASI LAYAR</span>
                    <span style="font-size: 8px; color: #38bdf8;">Grid: 1 Kotak = 1 Meter</span>
                </div>
                <div style="padding: 3px; text-align: center; background: #0f172a;">
                    @if($assignment->eventOrder->stage_layout_image)
                        <img src="{{ $assignment->eventOrder->stage_layout_image }}" style="width: 100%; max-height: 220px; object-fit: contain; display: block; margin: 0 auto;">
                    @else
                        <div style="height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-size: 10px;">
                            <span style="font-size: 11px; font-weight: bold; color: #e2e8f0; margin-bottom: 3px;">Panggung: {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m</span>
                            <span>{{ $assignment->eventOrder->screens->map(fn($s) => $s->screen_name . ' (' . $s->width_m . 'x' . $s->height_m . 'm)')->implode(' • ') }}</span>
                            <span style="margin-top: 4px; color: #facc15;">▼ BIBIR PANGGUNG MENGHADAP PENONTON ▼</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- DIAGRAM 2: TAMPAK DEPAN (FRONT ELEVATION / PERSPEKTIF PENONTON) -->
            <div style="flex: 1; border: 1.5px solid #000; background: #fff;">
                <div style="background: #1e293b; color: #fff; padding: 3px 6px; font-size: 9.5px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                    <span>🎭 2. TAMPAK DEPAN (FRONT ELEVATION) — LEVEL TINGGI PANGGUNG &amp; TRUSS GANTUNG</span>
                    <span style="font-size: 8px; color: #facc15;">Perspektif Penonton</span>
                </div>
                <div style="padding: 3px; text-align: center; background: #0f172a;">
                    @if($assignment->eventOrder->stage_layout_front_image)
                        <img src="{{ $assignment->eventOrder->stage_layout_front_image }}" style="width: 100%; max-height: 220px; object-fit: contain; display: block; margin: 0 auto;">
                    @else
                        <div style="height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-size: 10px;">
                            <span style="font-size: 11px; font-weight: bold; color: #e2e8f0; margin-bottom: 3px;">Elevasi Panggung: Level +{{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m</span>
                            <span>Ground Stack / Rigging Truss Gantung</span>
                            <span style="margin-top: 4px; color: #38bdf8;">(Render blueprint elevasi otomatis saat simpan layout)</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- BOTTOM SECTION: RIGGING TABLE & SAFETY SIGN-OFF (SIDE-BY-SIDE IN LANDSCAPE) -->
        <div style="display: flex; gap: 8px; align-items: stretch;">
            <!-- Left 62%: Tabel Spesifikasi Teknik Pemasangan & Rigging -->
            <div style="flex: 1.6;">
                <table class="data-table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 25px;">NO</th>
                            <th>Nama Posisi Layar</th>
                            <th style="width: 75px;">Dimensi</th>
                            <th style="width: 55px;">Luas</th>
                            <th style="width: 80px;">Total Kabinet</th>
                            <th style="width: 90px;">Metode Pasang</th>
                            <th style="width: 100px;">Prosesor &amp; Sinyal</th>
                            <th>Catatan Rigging &amp; Safety</th>
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
                                <td style="font-size: 8px;">{{ $sc->processor_model }}</td>
                                <td style="font-size: 8px;">{{ $sc->stage_specification ?? 'Perhatikan kerataan level & ballast' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Right 38%: Rigging Statement & Sign-off Box -->
            <div style="flex: 1; border: 1px solid #000; padding: 4px 6px; background: #fff; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 8.5px; font-weight: bold; margin-bottom: 2px; text-transform: uppercase;">
                        Pernyataan Kelayakan Instalasi Rigging:
                    </div>
                    <div style="font-size: 7.5px; color: #334155; line-height: 1.2;">
                        1. Kerataan lantai level panggung dicek waterpass sebelum kabinet dipasang.<br>
                        2. Quick-lock kabinet &amp; safety wire terpasang lengkap sesuai standar safety.<br>
                        3. Beban daya listrik dibagi merata dan grounding terpasang aman.
                    </div>
                </div>
                <table class="sign-table" style="margin-bottom: 0; margin-top: 4px;">
                    <tr>
                        <td style="height: 26px; vertical-align: top; font-size: 8px; padding: 1px;">TTD PIC RIGGING</td>
                        <td style="vertical-align: top; font-size: 8px; padding: 1px;">TTD OPERATOR VISUAL</td>
                        <td style="vertical-align: top; font-size: 8px; padding: 1px;">TTD PIC EVENT</td>
                    </tr>
                    <tr>
                        <td style="font-size: 8px; padding: 1px;">( .................... )</td>
                        <td style="font-size: 8px; padding: 1px;">( .................... )</td>
                        <td style="font-size: 8px; padding: 1px;">( {{ $assignment->crew_pic_event_name ?? '..................' }} )</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- INTERACTIVE PRINT SCRIPTS -->
<script>
    function printAllPages() {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('print-hide'));
        window.print();
    }

    function printSinglePage(pageNo) {
        document.querySelectorAll('.page').forEach((p, idx) => {
            if (idx + 1 === pageNo) {
                p.classList.remove('print-hide');
            } else {
                p.classList.add('print-hide');
            }
        });
        window.print();
        setTimeout(() => {
            document.querySelectorAll('.page').forEach(p => p.classList.remove('print-hide'));
        }, 500);
    }

    function printChecklistPages() {
        document.querySelectorAll('.page').forEach((p, idx) => {
            if (idx + 1 === 2 || idx + 1 === 3) {
                p.classList.remove('print-hide');
            } else {
                p.classList.add('print-hide');
            }
        });
        window.print();
        setTimeout(() => {
            document.querySelectorAll('.page').forEach(p => p.classList.remove('print-hide'));
        }, 500);
    }
</script>

</body>
</html>
