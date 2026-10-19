<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPK - {{ $assignment->spk_number }} - Omahvisual LED</title>
    <style>
        /* =========================================================================
           A4 PRINT STYLESHEET (UNIFORM A4 PORTRAIT CALIBRATION)
           Calibrated for A4 (210mm x 297mm) with zero margin double-padding.
           Pages 1-4: Standard A4 Portrait for consistent print & PDF export.
           ========================================================================= */
        @page {
            size: A4 portrait;
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
            font-size: 11.5px;
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
                max-width: 210mm;
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
            font-size: 16px;
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
            font-size: 11px;
            text-align: center;
            padding: 3.5px 6px;
            margin-top: 8px;
            border: 1px solid #7a9cb5;
        }

        .banner-red {
            background-color: #f7b2b7 !important;
            color: #000;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            padding: 3.5px 6px;
            margin-top: 8px;
            border: 1px solid #d88188;
        }

        .sign-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin-top: 3px;
            margin-bottom: 4px;
        }

        .sign-table td {
            width: 33.3%;
            padding: 4px 6px;
            font-size: 10.5px;
            vertical-align: top;
        }

        /* READABLE DATA TABLE FOR CHECKLIST (53 & 48 ITEMS WITHOUT SPILL) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            line-height: 1.25;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 2.5px 4px;
        }

        .data-table th {
            background-color: #e2e8f0 !important;
            font-weight: bold;
            text-align: center;
            padding: 3.5px 4px;
            font-size: 10.5px;
        }

        .cat-row {
            background-color: #bcd4e6 !important;
            font-weight: bold;
        }

        .cat-row td {
            padding: 2.5px 5px;
            font-size: 10px;
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
        <button type="button" onclick="printAllPages()" class="btn-print" title="Cetak Seluruh Lembar (1 s/d 5)">
            🖨️ Cetak Semua (Hal 1-5)
        </button>
        <button type="button" onclick="printSinglePage(1)" class="btn-sub-print" title="Cetak Lembar Serah Terima & Surat Jalan Saja">
            📄 Hal 1 (Surat Jalan)
        </button>
        <button type="button" onclick="printChecklistPages()" class="btn-sub-print" title="Cetak Lembar Checklist Muatan Gudang (Hal 2 & 3)">
            📦 Hal 2-3 (Checklist)
        </button>
        <button type="button" onclick="printBlueprintPages()" class="btn-sub-print" title="Cetak Lembar Blueprint Teknis Panggung (Hal 4 & 5)">
            📐 Hal 4-5 (Blueprint HD)
        </button>
        <a href="{{ route('orders.show', $assignment->eventOrder) }}" class="btn-sub-print" style="text-decoration: none; display: inline-flex; align-items: center;">
            ✕ Tutup
        </a>
    </div>
</div>

<div class="print-container">

    <!-- ========================================================================= -->
    <!-- HALAMAN 1: SERAH TERIMA & PENUGASAN ARMADA (PORTRAIT)                      -->
    <!-- Signature block is docked flush to the bottom like a grounded footer       -->
    <!-- ========================================================================= -->
    <div class="page" id="page-1">
        <div style="border: 1.5px solid #000; padding: 12px 14px; min-height: 275mm; display: flex; flex-direction: column; justify-content: space-between;">
            <!-- TOP SECTION -->
            <div>
                <div class="header-title">
                    <span>☑️ 1 SERAH TERIMA</span>
                </div>

                <div style="background: #e2e8f0; padding: 5px 8px; font-weight: bold; font-size: 11px; border: 1px solid #94a3b8; margin-bottom: 10px;">
                    Penugasan Team lapangan untuk memenuhi kebutuhan event client dengan detail :
                </div>

                <table style="width: 100%; border-collapse: collapse; font-size: 11.5px;">
                    <tr>
                        <td style="width: 58%; vertical-align: top; padding-right: 14px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 11.5px;">
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

                            <div style="margin-top: 10px;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                    @if($assignment->crewAssignments->count() > 0)
                                        @foreach($assignment->crewAssignments->take(7) as $idx => $ca)
                                            <tr>
                                                <td style="width: 75px; padding: 2px 0;">Crew {{ $idx + 1 }}</td>
                                                <td>: <strong>{{ $ca->crew->nickname }}</strong> ({{ $ca->role_label }})</td>
                                            </tr>
                                        @endforeach
                                        @for($i = $assignment->crewAssignments->count() + 1; $i <= 7; $i++)
                                            <tr>
                                                <td style="width: 75px; padding: 2px 0;">Crew {{ $i }}</td>
                                                <td>: ............................................</td>
                                            </tr>
                                        @endfor
                                    @else
                                        @for($i = 1; $i <= 7; $i++)
                                            @php $field = "crew_{$i}_name"; @endphp
                                            <tr>
                                                <td style="width: 75px; padding: 2.5px 0;">Crew {{ $i }}</td>
                                                <td>: {{ $assignment->$field ?? '............................................' }}</td>
                                            </tr>
                                        @endfor
                                    @endif
                                </table>
                            </div>
                        </td>

                        <td style="width: 42%; vertical-align: top; border-left: 1px dashed #94a3b8; padding-left: 14px;">
                            <!-- 2D STAGE & LED BLUEPRINT DIAGRAM THUMBNAIL -->
                            <div style="border: 1.5px solid #000; padding: 4px; margin-bottom: 8px; text-align: center; background: #fff;">
                                <div style="font-weight: bold; font-size: 9.5px; text-transform: uppercase; background: #e2e8f0; padding: 2.5px; margin-bottom: 4px; border: 1px solid #cbd5e1;">
                                    📐 BLUEPRINT LAYOUT PANGGUNG 2D
                                </div>
                                @if($assignment->eventOrder->stage_layout_image)
                                    <img id="printPage1ThumbImg" src="{{ $assignment->eventOrder->stage_layout_image }}" style="width: 100%; max-height: 120px; object-fit: contain; display: block; margin: 0 auto; border: 1px solid #e2e8f0;">
                                @else
                                    <img id="printPage1ThumbImg" src="" style="width: 100%; max-height: 120px; object-fit: contain; display: none; margin: 0 auto; border: 1px solid #e2e8f0;">
                                    <div id="printPage1ThumbFallback" style="height: 105px; border: 1px dashed #94a3b8; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; font-size: 9.5px; color: #475569;">
                                        <div style="font-weight: bold; font-size: 10.5px; color: #000;">
                                            Panggung: {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m
                                        </div>
                                        <div style="margin-top: 2px; font-weight: bold; color: #1e3a8a;">
                                            {{ $assignment->eventOrder->screens->map(fn($s) => $s->screen_name . ' (' . $s->width_m . 'x' . $s->height_m . 'm)')->implode(' + ') }}
                                        </div>
                                        <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">
                                            ▼ Bibir Panggung Menghadap Penonton ▼
                                        </div>
                                    </div>
                                @endif
                                <div style="font-size: 8.5px; color: #334155; margin-top: 2px; font-weight: bold;">
                                    Dimensi: {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m (Level {{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m)
                                </div>
                                <div style="font-size: 8.5px; color: #1e40af; margin-top: 2px; font-weight: bold; background: #eff6ff; padding: 2px; border: 1px solid #bfdbfe;">
                                    📌 Detail Teknis Lengkap: Lihat Lembar 4 (Blueprint)
                                </div>
                            </div>

                            @if($assignment->assignedVehicles->count() > 0)
                                <div style="margin-top: 6px; font-size: 10px;">
                                    <div style="font-weight: bold; font-size: 10.5px; border-bottom: 1px solid #000; padding-bottom: 2px; margin-bottom: 4px;">
                                        Armada Kendaraan ({{ $assignment->assignedVehicles->count() }} Unit):
                                    </div>
                                    @foreach($assignment->assignedVehicles as $vIndex => $veh)
                                        <div style="margin-bottom: 4px; padding: 3px 5px; background: #f8fafc; border: 1px solid #cbd5e1; font-size: 10px;">
                                            <div><strong>{{ $vIndex + 1 }}. {{ $veh->vehicle_name }}</strong> ({{ $veh->plate_number ?? '-' }}) • Driver: <strong>{{ $veh->driver_name ?? '-' }}</strong></div>
                                            <div style="font-size: 9px; color: #334155; margin-top: 1px;">
                                                E-Toll: <strong>{{ $veh->has_etoll ? 'Ada' : 'Belum' }}</strong> • Kunci: <strong>{{ $veh->has_kunci_ban ? 'Ada' : 'Belum' }}</strong> • STNK: <strong>{{ $veh->has_stnk ? 'Ada' : 'Belum' }}</strong> • KIR: <strong>{{ $veh->has_kir ? 'Ada' : 'Belum' }}</strong> • BBM: <strong>{{ $veh->has_barcode_bbm ? 'Ada' : 'Belum' }}</strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 8px;">
                                    <tr><td style="width: 95px; padding: 2.5px 0;">Kendaraan</td><td>: <strong>{{ $assignment->vehicle_name ?? 'TRAGA 1' }}</strong></td></tr>
                                    <tr><td style="padding: 2.5px 0;">E toll</td><td>: {{ $assignment->has_etoll ? 'Ada / Siap' : '.......' }}</td></tr>
                                    <tr><td style="padding: 2.5px 0;">Kunci Ban</td><td>: {{ $assignment->has_kunci_ban ? 'Ada / Siap' : '.......' }}</td></tr>
                                    <tr><td style="padding: 2.5px 0;">STNK</td><td>: {{ $assignment->has_stnk ? 'Ada / Siap' : '.......' }}</td></tr>
                                    <tr><td style="padding: 2.5px 0;">Kartu KIR</td><td>: {{ $assignment->has_kir ? 'Ada / Siap' : '.......' }}</td></tr>
                                    <tr><td style="padding: 2.5px 0;">BardCode BBM</td><td>: {{ $assignment->has_barcode_bbm ? 'Ada / Siap' : '.......' }}</td></tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- BOTTOM DOCKED SIGNATURE SECTION -->
            <div style="margin-top: auto; padding-top: 10px;">
                <p style="font-size: 10px; margin: 1px 0; color: #1e293b; font-weight: 500;">- Dengan menandatangani checklist ini, berarti kamu bertanggung jawab penuh atas isi checklist dan menyatakan bahwa semua kelengkapan sudah dicek dan diserahterimakan dengan teliti dan benar.</p>
                <p style="font-size: 10px; margin: 1px 0; color: #1e293b; font-weight: 500;">- Terdapat 2 Lembar cheklist yang sangat Crusial , dan wajib teliti</p>

                <div class="banner-blue">KEBERANGKATAN</div>
                <table class="sign-table">
                    <tr>
                        <td style="height: 44px; vertical-align: top;">TTD PIC EVENT</td>
                        <td style="vertical-align: top;">JAM DAN TGL<br><span style="font-weight: bold;">{{ $assignment->departure_datetime?->format('d/m/Y H:i') ?? '..................................' }}</span></td>
                        <td style="vertical-align: top;">TTD PIC GUDANG</td>
                    </tr>
                    <tr>
                        <td>( {{ $assignment->departure_pic_event_name ?? 'Nama Terang' }} )</td>
                        <td></td>
                        <td>( {{ $assignment->departure_pic_warehouse_name ?? 'Nama Terang' }} )</td>
                    </tr>
                </table>

                <div style="background-color: #d1fae5; color: #065f46; font-weight: bold; font-size: 10px; text-align: center; padding: 2.5px 6px; margin-top: 5px; border: 1px solid #6ee7b7;">
                    UJI KELAYAKAN INSTALASI VENUE & BAST (QC LAPANGAN)
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 9px; border: 1px solid #6ee7b7; margin-bottom: 2px; background: #f0fdf4;">
                    <tr>
                        <td style="padding: 2.5px 6px; width: 33%;">Grounding: <strong>{{ $assignment->qc_grounding_safe ? '[✓] Aman (<2V)' : '[ ] Aman' }}</strong></td>
                        <td style="padding: 2.5px 6px; width: 34%;">RGB Test: <strong>{{ $assignment->qc_test_rgb ? '[✓] Bersih' : '[ ] Bersih' }}</strong></td>
                        <td style="padding: 2.5px 6px; width: 33%;">Sinyal: <strong>{{ $assignment->qc_video_signal_ok ? '[✓] 3840Hz Stabil' : '[ ] 3840Hz' }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding: 2.5px 6px;">Teknisi QC: <strong>{{ $assignment->qc_technician_name ?? '..................................' }}</strong> ({{ $assignment->qc_verified_at?->format('d/m/Y H:i') ?? 'Tgl/Jam' }})</td>
                        <td style="padding: 2.5px 6px; text-align: right;">Status: <strong>{{ $assignment->qc_status === 'passed' ? 'LOLOS SIAP SHOW' : ($assignment->qc_status === 'issues_found' ? 'ADA KENDALA' : 'MENUNGGU TES') }}</strong></td>
                    </tr>
                </table>

                <div style="text-align: center; font-size: 10px; font-weight: bold; margin: 4px 0; color: #b91c1c;">
                    PC Kepulangan Barang Wajib Orang yg berbeda
                </div>

                <div class="banner-red">KEPULANGAN</div>
                <table class="sign-table">
                    <tr>
                        <td style="height: 44px; vertical-align: top;">TTD PIC EVENT</td>
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
            <div style="font-size: 15px; font-weight: bold;">
                2 Cheklist Barang Kerja TEAM Omahvisual LED
            </div>
            <div style="font-size: 10px; text-align: right;">
                No Event : {{ $assignment->eventOrder->order_number }}<br>
                Nama Client : <strong>{{ $assignment->eventOrder->customer->name }}</strong>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 5px; background: #f8fafc; padding: 4px 6px; border: 1px solid #e2e8f0;">
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
                            <td colspan="6" style="padding: 2.5px 5px; font-size: 10px;">
                                <strong>{{ $currentCat }}</strong>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: center; font-size: 10px;">{{ $no++ }}</td>
                        <td style="font-size: 10px;">{{ $item->item_name }}</td>
                        <td style="font-size: 10px;">{{ $item->item_detail ?? '' }}</td>
                        <td style="text-align: center; font-weight: bold; font-size: 10px;">{{ $item->quantity }} {{ $item->unit }}</td>
                        <td style="text-align: center; font-size: 10px;">{{ $item->checked_out ? '✓' : '' }}</td>
                        <td style="text-align: center; font-size: 10px;">{{ $item->checked_in ? '✓' : '' }}</td>
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
            <div style="font-size: 15px; font-weight: bold;">
                3 CHEKLIST PIC Omahvisual LED
            </div>
            <div style="font-size: 10px; text-align: right;">
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
                            <td colspan="6" style="padding: 2.5px 5px; font-size: 10px;">
                                <strong>{{ $currentCat }}</strong>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: center; font-size: 10px;">{{ $no++ }}</td>
                        <td style="font-size: 10px;">{{ $item->item_name }}</td>
                        <td style="font-size: 10px;">{{ $item->item_detail ?? '' }}</td>
                        <td style="text-align: center; font-weight: bold; font-size: 10px;">{{ $item->quantity }} {{ $item->unit }}</td>
                        <td style="text-align: center; font-size: 10px;">{{ $item->checked_out ? '✓' : '' }}</td>
                        <td style="text-align: center; font-size: 10px;">{{ $item->checked_in ? '✓' : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 4: BLUEPRINT TEKNIS PANGGUNG & LED (A4 PORTRAIT ATAS-BAWAH)       -->
    <!-- Diagrams stacked vertically (Atas - Bawah) for maximum wide visual scope   -->
    <!-- ========================================================================= -->
    <!-- PAGE 4: BLUEPRINT 1 - PEMETAAN PANEL & KABINET LED (MAPPING BLUEPRINT) -->
    <div class="page" id="page-4">
        <!-- Title & Event Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 6px;">
            <div>
                <div style="font-size: 14px; font-weight: bold; text-transform: uppercase;">
                    4A BLUEPRINT: PEMETAAN PANEL &amp; KABINET LED (MAPPING &amp; RESOLUSI VJ)
                </div>
                <div style="font-size: 10px; color: #334155; font-weight: bold;">
                    Nomor Urut Kabinet, Resolusi Piksel Konten &amp; Rute Kabel • Omahvisual LED
                </div>
            </div>
            <div style="font-size: 10px; text-align: right;">
                <strong>No. SPK:</strong> {{ $assignment->spk_number }}<br>
                <strong>No. Event:</strong> {{ $assignment->eventOrder->order_number }}
            </div>
        </div>

        <!-- Project Meta Strip -->
        <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 6px; background: #f8fafc; border: 1px solid #cbd5e1;">
            <tr>
                <td style="padding: 3px 6px; width: 33%;"><strong>Client:</strong> {{ $assignment->eventOrder->customer->name }}</td>
                <td style="padding: 3px 6px; width: 33%;"><strong>Venue:</strong> {{ $assignment->eventOrder->venue_name }}</td>
                <td style="padding: 3px 6px; width: 34%;"><strong>Tgl Event:</strong> {{ $assignment->eventOrder->event_start_date->format('d/m/Y') }} (Load: {{ $assignment->eventOrder->loading_date->format('d/m/Y') }})</td>
            </tr>
            <tr>
                <td style="padding: 3px 6px;"><strong>Ukuran Panggung:</strong> {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m (Level +{{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m)</td>
                <td style="padding: 3px 6px;"><strong>Total Layar:</strong> {{ $assignment->eventOrder->screens->count() }} Unit ({{ $assignment->eventOrder->total_sqm }} m²)</td>
                <td style="padding: 3px 6px;"><strong>PIC Event:</strong> {{ $assignment->crew_pic_event_name ?? '-' }}</td>
            </tr>
        </table>

        <!-- DIAGRAM 1: PEMETAAN PANEL & KABINET LED (RESOLUME & VJ MAPPING) -->
        <div style="border: 1.5px solid #000; background: #fff; margin-bottom: 8px;">
            <div style="background: #1e293b; color: #fff; padding: 4px 10px; font-size: 11px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                <span>🔲 DIAGRAM PEMETAAN PANEL &amp; NOMOR KABINET LED</span>
                <span style="font-size: 10px; color: #38bdf8;">Panduan Teknisi Lapangan &amp; Operator VJ</span>
            </div>
            <div style="padding: 0; text-align: center; background: #070b14; overflow: hidden;">
                @if($assignment->eventOrder->stage_layout_image)
                    <img id="printBlueprintTopImg" src="{{ $assignment->eventOrder->stage_layout_image }}" style="width: 100%; max-height: 140mm; object-fit: contain; display: block; margin: 0 auto;">
                @else
                    <img id="printBlueprintTopImg" src="" style="width: 100%; max-height: 140mm; object-fit: contain; display: none; margin: 0 auto;">
                    <div id="printBlueprintTopFallback" style="height: 120mm; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-size: 13px;">
                        <span style="font-size: 16px; font-weight: bold; color: #e2e8f0; margin-bottom: 6px;">Diagram Pemetaan Panel &amp; Kabinet LED</span>
                        <span style="font-size: 14px; color: #38bdf8; font-weight: bold;">{{ $assignment->eventOrder->screens->map(fn($s) => $s->screen_name . ' (' . $s->width_m . 'x' . $s->height_m . 'm - ' . $s->total_cabinets . ' Kabinet)')->implode(' • ') }}</span>
                        <span style="margin-top: 8px; color: #facc15; font-weight: bold; font-size: 13px;">PANDUAN SUSUNAN MODUL TEKNISI &amp; RESOLUSI PIKSEL VJ</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabel Rincian Formasi Layar LED -->
        <table class="data-table" style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 30px;">NO</th>
                    <th>Nama Posisi Layar</th>
                    <th style="width: 85px;">Panjang × Lebar</th>
                    <th style="width: 65px;">Luas (m²)</th>
                    <th style="width: 95px;">Total Kabinet</th>
                    <th style="width: 85px;">Hardcase</th>
                    <th style="width: 130px;">Prosesor Video</th>
                    <th>Spesifikasi Panggung / Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignment->eventOrder->screens as $idx => $sc)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                        <td style="font-weight: bold;">{{ $sc->screen_name }}</td>
                        <td style="text-align: center;">{{ $sc->width_m }}m × {{ $sc->height_m }}m</td>
                        <td style="text-align: center; font-weight: bold; color: #1e40af;">{{ $sc->total_sqm }} m²</td>
                        <td style="text-align: center;">{{ $sc->total_cabinets }} Kabinet ({{ $sc->cabinets_1m ?? $sc->total_cabinets }}x 1m, {{ $sc->cabinets_50cm ?? 0 }}x 50cm)</td>
                        <td style="text-align: center;">{{ $sc->total_hardcases }} Flightcase</td>
                        <td style="font-size: 9px; font-weight: 600;">{{ $sc->processor_model }}</td>
                        <td style="font-size: 9px;">{{ $sc->stage_specification ?? 'Standar panggung Omah Visual' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="font-size: 8.5px; color: #64748b; margin-top: 4px;">
            *Alur nomor kabinet: kiri-ke-kanan, atas-ke-bawah (standar NovaStar). Pastikan kabel power dan LAN jumper mengikuti urutan nomor kabinet di atas.
        </div>
    </div>

    <!-- PAGE 5: BLUEPRINT 2 - ELEVASI TAMPAK DEPAN & SAFETY RIGGING -->
    <div class="page" id="page-5">
        <!-- Title & Event Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 6px;">
            <div>
                <div style="font-size: 14px; font-weight: bold; text-transform: uppercase;">
                    4B BLUEPRINT: ELEVASI TAMPAK DEPAN PANGGUNG &amp; SAFETY RIGGING
                </div>
                <div style="font-size: 10px; color: #334155; font-weight: bold;">
                    Perspektif Penonton, Level Tinggi Panggung, &amp; Sign-Off Keselamatan • Omahvisual LED
                </div>
            </div>
            <div style="font-size: 10px; text-align: right;">
                <strong>No. SPK:</strong> {{ $assignment->spk_number }}<br>
                <strong>No. Event:</strong> {{ $assignment->eventOrder->order_number }}
            </div>
        </div>

        <!-- Project Meta Strip -->
        <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 6px; background: #f8fafc; border: 1px solid #cbd5e1;">
            <tr>
                <td style="padding: 3px 6px; width: 33%;"><strong>Client:</strong> {{ $assignment->eventOrder->customer->name }}</td>
                <td style="padding: 3px 6px; width: 33%;"><strong>Venue:</strong> {{ $assignment->eventOrder->venue_name }}</td>
                <td style="padding: 3px 6px; width: 34%;"><strong>Tgl Event:</strong> {{ $assignment->eventOrder->event_start_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 3px 6px;"><strong>Ukuran Panggung:</strong> {{ $assignment->eventOrder->stage_width_m ?? 10 }}m × {{ $assignment->eventOrder->stage_depth_m ?? 6 }}m (Level +{{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m)</td>
                <td style="padding: 3px 6px;"><strong>Kelistrikan:</strong> {{ $assignment->eventOrder->kelistrikan ?? 'Genset' }} ({{ $assignment->eventOrder->kelistrikan_keterangan ?? '-' }})</td>
                <td style="padding: 3px 6px;"><strong>PIC Rigging:</strong> {{ $assignment->crew_pic_event_name ?? '-' }}</td>
            </tr>
        </table>

        <!-- DIAGRAM 2: TAMPAK DEPAN (FRONT ELEVATION / PERSPEKTIF PENONTON) -->
        <div style="border: 1.5px solid #000; background: #fff; margin-bottom: 8px;">
            <div style="background: #1e293b; color: #fff; padding: 4px 10px; font-size: 11px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                <span>🎭 DIAGRAM ELEVASI TAMPAK DEPAN (PERSPEKTIF PENONTON &amp; PANGGUNG)</span>
                <span style="font-size: 10px; color: #facc15;">Tinggi Level Panggung &amp; Truss Rigging</span>
            </div>
            <div style="padding: 0; text-align: center; background: #070b14; overflow: hidden;">
                @if($assignment->eventOrder->stage_layout_front_image)
                    <img id="printBlueprintFrontImg" src="{{ $assignment->eventOrder->stage_layout_front_image }}" style="width: 100%; max-height: 140mm; object-fit: contain; display: block; margin: 0 auto;">
                @else
                    <img id="printBlueprintFrontImg" src="" style="width: 100%; max-height: 140mm; object-fit: contain; display: none; margin: 0 auto;">
                    <div id="printBlueprintFrontFallback" style="height: 120mm; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; font-size: 13px;">
                        <span style="font-size: 16px; font-weight: bold; color: #e2e8f0; margin-bottom: 6px;">Elevasi Panggung: Level +{{ $assignment->eventOrder->stage_height_m ?? 0.6 }}m</span>
                        <span style="font-size: 14px; color: #38bdf8; font-weight: bold;">Ground Stack / Rigging Truss Gantung</span>
                        <span style="margin-top: 8px; color: #94a3b8; font-size: 12px;">(Render blueprint elevasi otomatis saat simpan layout)</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- BOTTOM SECTION: RIGGING TABLE & SAFETY SIGN-OFF (SIDE-BY-SIDE) -->
        <div style="display: flex; gap: 8px; align-items: stretch;">
            <!-- Left 60%: Tabel Spesifikasi Teknik Pemasangan & Rigging -->
            <div style="flex: 1.5;">
                <table class="data-table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 25px;">NO</th>
                            <th>Nama Posisi Layar</th>
                            <th style="width: 75px;">Dimensi</th>
                            <th style="width: 55px;">Luas</th>
                            <th style="width: 90px;">Metode Pasang</th>
                            <th style="width: 95px;">Prosesor &amp; Sinyal</th>
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
                                <td style="text-align: center; font-weight: bold;">
                                    {{ $sc->rigging_method === 'hanging' ? 'Truss Gantung' : 'Ground Stack' }}
                                </td>
                                <td style="font-size: 8.5px;">{{ $sc->processor_model }}</td>
                                <td style="font-size: 8.5px;">{{ $sc->stage_specification ?? 'Perhatikan kerataan level & ballast' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Right 40%: Rigging Statement & Sign-off Box -->
            <div style="flex: 1; border: 1px solid #000; padding: 6px 8px; background: #fff; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="font-size: 9.5px; font-weight: bold; margin-bottom: 3px; text-transform: uppercase;">
                        Pernyataan Kelayakan Instalasi Rigging:
                    </div>
                    <div style="font-size: 8.5px; color: #334155; line-height: 1.3;">
                        1. Kerataan lantai level panggung dicek waterpass sebelum kabinet dipasang.<br>
                        2. Quick-lock kabinet &amp; safety wire terpasang lengkap sesuai standar safety.<br>
                        3. Beban daya listrik dibagi merata dan grounding terpasang aman.
                    </div>
                </div>
                <table class="sign-table" style="margin-bottom: 0; margin-top: 6px;">
                    <tr>
                        <td style="height: 35px; vertical-align: top; font-size: 8.5px; padding: 1px;">TTD PIC RIGGING</td>
                        <td style="vertical-align: top; font-size: 8.5px; padding: 1px;">TTD OPERATOR VISUAL</td>
                        <td style="vertical-align: top; font-size: 8.5px; padding: 1px;">TTD PIC EVENT</td>
                    </tr>
                    <tr>
                        <td style="font-size: 8.5px; padding: 1px;">( .................... )</td>
                        <td style="font-size: 8.5px; padding: 1px;">( .................... )</td>
                        <td style="font-size: 8.5px; padding: 1px;">( {{ $assignment->crew_pic_event_name ?? '..................' }} )</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- INTERACTIVE PRINT SCRIPTS & REAL-TIME HD BLUEPRINT ENGINE -->
@php
    $screensForJs = $assignment->eventOrder->stage_layout_json;
    if (empty($screensForJs)) {
        $screensForJs = [];
        $screenCount = $assignment->eventOrder->screens->count();
        $stageW = (float) ($assignment->eventOrder->stage_width_m ?? 10.0);
        $stageD = (float) ($assignment->eventOrder->stage_depth_m ?? 6.0);
        foreach ($assignment->eventOrder->screens as $idx => $sc) {
            $w = (float) $sc->width_m;
            $h = (float) $sc->height_m;
            $posX = 0.0;
            $posZ = -($stageD / 4);
            $rot = 0;
            if ($screenCount === 3) {
                if ($idx === 1) {
                    $posX = -($stageW / 4);
                    $rot = 20;
                } elseif ($idx === 2) {
                    $posX = ($stageW / 4);
                    $rot = -20;
                }
            } elseif ($screenCount > 1) {
                $step = $stageW / ($screenCount + 1);
                $posX = -($stageW / 2) + ($step * ($idx + 1));
            }
            $screensForJs[] = [
                'id' => $sc->id,
                'name' => $sc->screen_name,
                'width' => $w,
                'height' => $h,
                'x' => round($posX, 2),
                'z' => round($posZ, 2),
                'rotY' => $rot,
                'rigging' => $sc->rigging_method,
            ];
        }
    }
@endphp

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

    function printBlueprintPages() {
        document.querySelectorAll('.page').forEach((p, idx) => {
            if (idx + 1 === 4 || idx + 1 === 5) {
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

    // Dynamic High-Definition Blueprint Renderer (1200x600 Ultra-Crisp & Giant Typography)
    function renderPrintBlueprints() {
        const stageConfig = {
            width: {{ (float) ($assignment->eventOrder->stage_width_m ?? 10.0) }},
            depth: {{ (float) ($assignment->eventOrder->stage_depth_m ?? 6.0) }},
            height: {{ (float) ($assignment->eventOrder->stage_height_m ?? 0.6) }}
        };

        const screensData = @json($screensForJs);
        if (!screensData || screensData.length === 0) return;

        // 1. PEMETAAN PANEL & KABINET LED (LED MAPPING BLUEPRINT - 1200 x 600)
        const topCanvas = document.createElement('canvas');
        topCanvas.width = 1200;
        topCanvas.height = 600;
        const ctxTop = topCanvas.getContext('2d');

        // Dark Blueprint Background with subtle grid lines
        ctxTop.fillStyle = '#070b14';
        ctxTop.fillRect(0, 0, topCanvas.width, topCanvas.height);

        ctxTop.strokeStyle = '#0f172a';
        ctxTop.lineWidth = 1;
        for (let x = 0; x <= topCanvas.width; x += 40) {
            ctxTop.beginPath(); ctxTop.moveTo(x, 0); ctxTop.lineTo(x, topCanvas.height); ctxTop.stroke();
        }
        for (let y = 0; y <= topCanvas.height; y += 40) {
            ctxTop.beginPath(); ctxTop.moveTo(0, y); ctxTop.lineTo(topCanvas.width, y); ctxTop.stroke();
        }

        const palettes = [
            { name: 'blue', even: '#1e3a8a', odd: '#2563eb', border: '#3b82f6', badge: '#38bdf8' },
            { name: 'red', even: '#991b1b', odd: '#dc2626', border: '#ef4444', badge: '#f87171' },
            { name: 'green', even: '#166534', odd: '#16a34a', border: '#22c55e', badge: '#4ade80' },
            { name: 'purple', even: '#6b21a8', odd: '#9333ea', border: '#a855f7', badge: '#c084fc' },
            { name: 'amber', even: '#9a3412', odd: '#ea580c', border: '#f97316', badge: '#fb923c' },
            { name: 'cyan', even: '#115e59', odd: '#0d9488', border: '#14b8a6', badge: '#2dd4bf' }
        ];

        const totalCabinetsAll = screensData.reduce((acc, sc) => {
            const cols = Math.max(1, Math.round(sc.width / 0.5));
            const rows = Math.max(1, Math.round(sc.height / 0.5));
            return acc + (cols * rows);
        }, 0);
        const totalSqmAll = screensData.reduce((acc, sc) => acc + (sc.width * sc.height), 0);

        // Header Block
        ctxTop.fillStyle = 'rgba(15, 23, 42, 0.96)';
        ctxTop.fillRect(20, 16, topCanvas.width - 40, 70);
        ctxTop.strokeStyle = '#38bdf8';
        ctxTop.lineWidth = 1.5;
        ctxTop.strokeRect(20, 16, topCanvas.width - 40, 70);

        ctxTop.fillStyle = '#38bdf8';
        ctxTop.font = 'bold 16px Arial, sans-serif';
        ctxTop.textAlign = 'left';
        ctxTop.fillText('OMAH VISUAL — DIAGRAM PEMETAAN PANEL & KABINET LED (MAPPING BLUEPRINT)', 36, 40);

        ctxTop.fillStyle = '#e2e8f0';
        ctxTop.font = '13px Arial, sans-serif';
        ctxTop.fillText(`Total Formasi: ${screensData.length} Layar • ${totalCabinetsAll} Kabinet (${totalSqmAll.toFixed(1)} m²) • Standar Modul: 50 × 50 cm`, 36, 59);

        ctxTop.fillStyle = '#94a3b8';
        ctxTop.font = '12px Arial, sans-serif';
        ctxTop.fillText('Pitch P3.9 (128×128 px/kabinet) • Format Sinyal & VJ Mapping: Resolume Arena / NovaStar NovaLCT', 36, 76);

        // Header Right Badge
        const tagText = 'PANDUAN TEKNISI & VJ KONTEN';
        ctxTop.font = 'bold 11px Arial, sans-serif';
        const tagW = ctxTop.measureText(tagText).width + 24;
        const tagX = topCanvas.width - tagW - 36;
        ctxTop.fillStyle = 'rgba(56, 189, 248, 0.15)';
        ctxTop.fillRect(tagX, 36, tagW, 28);
        ctxTop.strokeStyle = '#38bdf8';
        ctxTop.lineWidth = 1;
        ctxTop.strokeRect(tagX, 36, tagW, 28);
        ctxTop.fillStyle = '#38bdf8';
        ctxTop.textAlign = 'center';
        ctxTop.fillText(tagText, tagX + tagW / 2, 54);

        // Sort screens from left to right according to X position
        const sortedScreens = [...screensData].sort((a, b) => (a.x || 0) - (b.x || 0));

        // Layout calculation to fit screens within canvas
        const gapMeters = 0.4;
        const totalMetersW = sortedScreens.reduce((sum, sc) => sum + sc.width, 0) + (sortedScreens.length - 1) * gapMeters;
        const maxMeterH = sortedScreens.reduce((max, sc) => Math.max(max, sc.height), 1);

        const marginX = 40;
        const availW = topCanvas.width - marginX * 2;
        const availH = 430;
        const scale = Math.min(availW / Math.max(totalMetersW, 5), availH / (maxMeterH + 0.8));

        const gapPx = 20;
        const totalScreensPxW = sortedScreens.reduce((sum, sc) => sum + (sc.width * scale), 0) + (sortedScreens.length - 1) * gapPx;
        let currentDrawX = marginX + Math.max(0, (availW - totalScreensPxW) / 2);
        const topAreaY = 102;

        sortedScreens.forEach((sc, idx) => {
            const scW_px = sc.width * scale;
            const scH_px = sc.height * scale;
            const scY = topAreaY + (availH - 55 - scH_px) / 2;

            const palette = palettes[idx % palettes.length];
            const cols = Math.max(1, Math.round(sc.width / 0.5));
            const rows = Math.max(1, Math.round(sc.height / 0.5));
            const totalPanels = cols * rows;

            const cellW = scW_px / cols;
            const cellH = scH_px / rows;

            const resW = cols * 128;
            const resH = rows * 128;

            // 1. Checkered numbered panels
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    const pNum = r * cols + c + 1;
                    const cellX = currentDrawX + c * cellW;
                    const cellY = scY + r * cellH;

                    ctxTop.fillStyle = (c + r) % 2 === 0 ? palette.even : palette.odd;
                    ctxTop.fillRect(cellX, cellY, cellW, cellH);

                    // Subtle inner cabinet border
                    ctxTop.strokeStyle = 'rgba(255, 255, 255, 0.22)';
                    ctxTop.lineWidth = 1;
                    ctxTop.strokeRect(cellX, cellY, cellW, cellH);

                    // Sequential panel number (01, 02, ... 120)
                    const numStr = pNum < 10 ? '0' + pNum : '' + pNum;
                    const fontSize = Math.max(9, Math.min(cellW * 0.38, cellH * 0.42, 22));
                    ctxTop.fillStyle = 'rgba(255, 255, 255, 0.78)';
                    ctxTop.font = `bold ${fontSize}px Arial, sans-serif`;
                    ctxTop.textAlign = 'center';
                    ctxTop.textBaseline = 'middle';
                    ctxTop.fillText(numStr, cellX + cellW / 2, cellY + cellH / 2);
                }
            }

            // 2. Diagonal Dashed Guide Lines ('X' through screen)
            ctxTop.save();
            ctxTop.setLineDash([7, 7]);
            ctxTop.strokeStyle = 'rgba(255, 255, 255, 0.45)';
            ctxTop.lineWidth = 2;
            ctxTop.beginPath();
            ctxTop.moveTo(currentDrawX, scY); ctxTop.lineTo(currentDrawX + scW_px, scY + scH_px);
            ctxTop.moveTo(currentDrawX, scY + scH_px); ctxTop.lineTo(currentDrawX + scW_px, scY);
            ctxTop.stroke();

            // 3. Center Crosshair (Horizontal & Vertical)
            const midX = currentDrawX + scW_px / 2;
            const midY = scY + scH_px / 2;
            ctxTop.beginPath();
            ctxTop.moveTo(currentDrawX, midY); ctxTop.lineTo(currentDrawX + scW_px, midY);
            ctxTop.moveTo(midX, scY); ctxTop.lineTo(midX, scY + scH_px);
            ctxTop.stroke();

            // Center reticle ring
            ctxTop.beginPath();
            ctxTop.arc(midX, midY, Math.min(36, cellH * 0.75), 0, Math.PI * 2);
            ctxTop.stroke();
            ctxTop.restore();

            // 4. Outer Boundary Dashed White Border
            ctxTop.save();
            ctxTop.setLineDash([8, 6]);
            ctxTop.strokeStyle = '#ffffff';
            ctxTop.lineWidth = 3.5;
            ctxTop.strokeRect(currentDrawX, scY, scW_px, scH_px);
            ctxTop.restore();

            // 5. Top-Right Corner Pitch Badge ([P3.9] 50 x 50 cm)
            if (scW_px >= 120) {
                const cornerBadgeW = Math.min(135, scW_px * 0.45);
                const cornerBadgeH = 24;
                const cornerBadgeX = currentDrawX + scW_px - cornerBadgeW - 6;
                const cornerBadgeY = scY + 6;

                ctxTop.fillStyle = 'rgba(0, 0, 0, 0.85)';
                ctxTop.fillRect(cornerBadgeX, cornerBadgeY, cornerBadgeW, cornerBadgeH);
                ctxTop.strokeStyle = '#22c55e';
                ctxTop.lineWidth = 1;
                ctxTop.strokeRect(cornerBadgeX, cornerBadgeY, cornerBadgeW, cornerBadgeH);

                ctxTop.fillStyle = '#22c55e';
                ctxTop.fillRect(cornerBadgeX + 3, cornerBadgeY + 3, 38, cornerBadgeH - 6);
                ctxTop.fillStyle = '#052e16';
                ctxTop.font = 'bold 10px Arial, sans-serif';
                ctxTop.textAlign = 'center';
                ctxTop.textBaseline = 'middle';
                ctxTop.fillText('P3.9', cornerBadgeX + 22, cornerBadgeY + cornerBadgeH / 2);

                ctxTop.fillStyle = '#ffffff';
                ctxTop.font = 'bold 10px Arial, sans-serif';
                ctxTop.fillText('50 × 50 cm', cornerBadgeX + 85, cornerBadgeY + cornerBadgeH / 2);
            }

            // 6. Big Screen Name in Center
            const cleanName = (sc.name || `SCREEN ${idx + 1}`).toUpperCase();
            ctxTop.save();
            const nameFontSize = Math.max(16, Math.min(scW_px * 0.12, scH_px * 0.22, 42));
            ctxTop.font = `900 ${nameFontSize}px Arial Black, Arial, sans-serif`;
            ctxTop.textAlign = 'center';
            ctxTop.textBaseline = 'middle';
            ctxTop.shadowColor = 'rgba(0, 0, 0, 0.95)';
            ctxTop.shadowBlur = 10;
            ctxTop.shadowOffsetX = 2;
            ctxTop.shadowOffsetY = 2;
            ctxTop.fillStyle = '#ffffff';
            ctxTop.fillText(cleanName, midX, midY - (scH_px > 100 ? 14 : 0));
            ctxTop.restore();

            // 7. Center Resolution Pill Badge (e.g. W 1920 x H 1024)
            if (scH_px > 90) {
                const resText = `W ${resW} x H ${resH}`;
                const resFontSz = Math.max(11, Math.min(nameFontSize * 0.48, 18));
                ctxTop.font = `bold ${resFontSz}px Arial, sans-serif`;
                const resMetrics = ctxTop.measureText(resText);
                const resPillW = resMetrics.width + 24;
                const resPillH = resFontSz + 12;
                const resPillX = midX - resPillW / 2;
                const resPillY = midY + 12;

                ctxTop.fillStyle = 'rgba(0, 0, 0, 0.88)';
                ctxTop.fillRect(resPillX, resPillY, resPillW, resPillH);
                ctxTop.strokeStyle = 'rgba(255, 255, 255, 0.4)';
                ctxTop.lineWidth = 1;
                ctxTop.strokeRect(resPillX, resPillY, resPillW, resPillH);

                ctxTop.fillStyle = '#f8fafc';
                ctxTop.textAlign = 'center';
                ctxTop.textBaseline = 'middle';
                ctxTop.fillText(resText, midX, resPillY + resPillH / 2);
            }

            // 8. Info Strip Below Screen
            const infoY = scY + scH_px + 10;
            ctxTop.fillStyle = 'rgba(15, 23, 42, 0.9)';
            ctxTop.fillRect(currentDrawX, infoY, scW_px, 26);
            ctxTop.strokeStyle = palette.border;
            ctxTop.lineWidth = 1;
            ctxTop.strokeRect(currentDrawX, infoY, scW_px, 26);

            ctxTop.fillStyle = '#f8fafc';
            ctxTop.font = 'bold 11px Arial, sans-serif';
            ctxTop.textAlign = 'center';
            ctxTop.textBaseline = 'middle';
            const infoText = `${sc.name}: ${sc.width}m × ${sc.height}m (${cols}×${rows} = ${totalPanels} Kabinet) • ${resW}×${resH} px`;
            ctxTop.fillText(infoText, midX, infoY + 13);

            currentDrawX += scW_px + gapPx;
        });

        // Bottom Technical Instruction Legend
        ctxTop.fillStyle = '#94a3b8';
        ctxTop.font = '11px Arial, sans-serif';
        ctxTop.textAlign = 'center';
        ctxTop.textBaseline = 'alphabetic';
        ctxTop.fillText('ℹ️ Alur nomor kabinet: kiri-ke-kanan, atas-ke-bawah (standar NovaStar). Pastikan perkabelan data & catu daya mengikuti urutan nomor di atas.', topCanvas.width / 2, topCanvas.height - 14);

        // 2. FRONT ELEVATION BLUEPRINT (1200 x 600)
        const frontCanvas = document.createElement('canvas');
        frontCanvas.width = 1200;
        frontCanvas.height = 600;
        const ctxFront = frontCanvas.getContext('2d');

        ctxFront.fillStyle = '#0a0f1d';
        ctxFront.fillRect(0, 0, frontCanvas.width, frontCanvas.height);

        const maxScreenH = screensData.reduce((max, sc) => Math.max(max, sc.height || 3), 3);
        const hasHanging = screensData.some(sc => sc.rigging === 'hanging');
        const totalHM = stageConfig.height + maxScreenH + (hasHanging ? 1.4 : 0.5) + 0.6;
        const contentWFront = Math.max(stageConfig.width + 1.0, 6);

        const scaleFront = Math.min((frontCanvas.width - marginX * 2) / contentWFront, (frontCanvas.height - marginY * 2) / totalHM);
        const centerFrontX = frontCanvas.width / 2;
        const groundY = frontCanvas.height - 42;

        const stageWFront_px = stageConfig.width * scaleFront;
        const stageHFront_px = Math.max(24, stageConfig.height * scaleFront);
        const stageFrontX = centerFrontX - stageWFront_px / 2;
        const stageTopY = groundY - stageHFront_px;

        ctxFront.fillStyle = '#1e293b';
        ctxFront.fillRect(stageFrontX, stageTopY, stageWFront_px, stageHFront_px);

        ctxFront.strokeStyle = '#1e293b';
        ctxFront.lineWidth = 1;
        for (let x = centerFrontX; x <= frontCanvas.width; x += scaleFront) {
            ctxFront.beginPath(); ctxFront.moveTo(x, 0); ctxFront.lineTo(x, frontCanvas.height); ctxFront.stroke();
        }
        for (let x = centerFrontX - scaleFront; x >= 0; x -= scaleFront) {
            ctxFront.beginPath(); ctxFront.moveTo(x, 0); ctxFront.lineTo(x, frontCanvas.height); ctxFront.stroke();
        }
        for (let y = groundY; y >= 0; y -= scaleFront) {
            ctxFront.beginPath(); ctxFront.moveTo(0, y); ctxFront.lineTo(frontCanvas.width, y); ctxFront.stroke();
        }
        for (let y = groundY + scaleFront; y <= frontCanvas.height; y += scaleFront) {
            ctxFront.beginPath(); ctxFront.moveTo(0, y); ctxFront.lineTo(frontCanvas.width, y); ctxFront.stroke();
        }

        ctxFront.strokeStyle = '#334155';
        ctxFront.lineWidth = 1.5;
        for (let x = centerFrontX; x <= stageFrontX + stageWFront_px; x += scaleFront) {
            ctxFront.beginPath(); ctxFront.moveTo(x, stageTopY); ctxFront.lineTo(x, groundY); ctxFront.stroke();
        }
        for (let x = centerFrontX - scaleFront; x >= stageFrontX; x -= scaleFront) {
            ctxFront.beginPath(); ctxFront.moveTo(x, stageTopY); ctxFront.lineTo(x, groundY); ctxFront.stroke();
        }

        // Ground Level
        ctxFront.strokeStyle = '#475569';
        ctxFront.lineWidth = 4;
        ctxFront.beginPath();
        ctxFront.moveTo(0, groundY);
        ctxFront.lineTo(frontCanvas.width, groundY);
        ctxFront.stroke();

        ctxFront.fillStyle = '#cbd5e1';
        ctxFront.font = 'bold 14px Arial';
        ctxFront.textAlign = 'right';
        ctxFront.fillText('LANTAI DASAR / GROUND LEVEL (±0.00m)', frontCanvas.width - 25, groundY + 22);

        ctxFront.strokeStyle = '#64748b';
        ctxFront.lineWidth = 2.5;
        ctxFront.strokeRect(stageFrontX, stageTopY, stageWFront_px, stageHFront_px);

        ctxFront.strokeStyle = '#334155';
        ctxFront.lineWidth = 1;
        for (let sx = stageFrontX + 25; sx < stageFrontX + stageWFront_px; sx += 30) {
            ctxFront.beginPath(); ctxFront.moveTo(sx, stageTopY); ctxFront.lineTo(sx, groundY); ctxFront.stroke();
        }

        ctxFront.fillStyle = '#facc15';
        ctxFront.font = 'bold 15px Arial';
        ctxFront.textAlign = 'center';
        ctxFront.fillText(`LEVEL PANGGUNG (+${stageConfig.height}m) • LEBAR: ${stageConfig.width}m`, centerFrontX, stageTopY + (stageHFront_px > 30 ? stageHFront_px / 2 + 5 : -10));

        ctxFront.strokeStyle = '#38bdf8';
        ctxFront.lineWidth = 2;
        ctxFront.setLineDash([6, 6]);
        ctxFront.beginPath();
        ctxFront.moveTo(centerFrontX, 90);
        ctxFront.lineTo(centerFrontX, groundY);
        ctxFront.stroke();
        ctxFront.setLineDash([]);

        // Front Screens
        screensData.forEach((sc, idx) => {
            const rotRad = (sc.rotY || 0) * (Math.PI / 180);
            const scW_px = Math.max(50, sc.width * scaleFront * Math.max(0.65, Math.abs(Math.cos(rotRad))));
            const scH_px = sc.height * scaleFront;
            const scX = centerFrontX + (sc.x * scaleFront);

            const isHanging = sc.rigging === 'hanging';
            const clearanceM = isHanging ? 0.6 : 0.0;
            const clearance_px = clearanceM * scaleFront;
            const scBottomY = stageTopY - clearance_px;
            const scTopY = scBottomY - scH_px;

            if (isHanging) {
                const trussY = Math.max(70, scTopY - 45);
                const trussW = scW_px + 40;
                ctxFront.fillStyle = '#334155';
                ctxFront.fillRect(scX - trussW / 2, trussY, trussW, 18);
                ctxFront.strokeStyle = '#94a3b8';
                ctxFront.lineWidth = 2;
                ctxFront.strokeRect(scX - trussW / 2, trussY, trussW, 18);

                for (let tx = scX - trussW / 2 + 10; tx < scX + trussW / 2; tx += 20) {
                    ctxFront.beginPath(); ctxFront.moveTo(tx, trussY); ctxFront.lineTo(tx + 10, trussY + 18); ctxFront.stroke();
                }

                ctxFront.strokeStyle = '#e2e8f0';
                ctxFront.lineWidth = 3;
                ctxFront.beginPath(); ctxFront.moveTo(scX - scW_px * 0.4, trussY + 18); ctxFront.lineTo(scX - scW_px * 0.4, scTopY); ctxFront.stroke();
                ctxFront.beginPath(); ctxFront.moveTo(scX + scW_px * 0.4, trussY + 18); ctxFront.lineTo(scX + scW_px * 0.4, scTopY); ctxFront.stroke();

                ctxFront.fillStyle = '#94a3b8';
                ctxFront.font = 'bold 12px Arial';
                ctxFront.textAlign = 'center';
                ctxFront.fillText('TRUSS RIGGING GANTUNG', scX, trussY - 7);
            } else {
                ctxFront.fillStyle = '#475569';
                ctxFront.fillRect(scX - scW_px * 0.45, stageTopY - 7, 24, 7);
                ctxFront.fillRect(scX + scW_px * 0.45 - 24, stageTopY - 7, 24, 7);
                if (scW_px > 140) {
                    ctxFront.fillRect(scX - 12, stageTopY - 7, 24, 7);
                }
            }

            ctxFront.fillStyle = idx === 0 ? '#1d4ed8' : '#0284c7';
            ctxFront.fillRect(scX - scW_px / 2, scTopY, scW_px, scH_px);
            ctxFront.strokeStyle = '#38bdf8';
            ctxFront.lineWidth = 3;
            ctxFront.strokeRect(scX - scW_px / 2, scTopY, scW_px, scH_px);

            const cabStepX = scaleFront * 0.5;
            ctxFront.strokeStyle = idx === 0 ? '#1e40af' : '#0369a1';
            ctxFront.lineWidth = 1;
            for (let cx = scX - scW_px / 2 + cabStepX; cx < scX + scW_px / 2; cx += cabStepX) {
                ctxFront.beginPath(); ctxFront.moveTo(cx, scTopY); ctxFront.lineTo(cx, scBottomY); ctxFront.stroke();
            }
            for (let cy = scTopY + cabStepX; cy < scBottomY; cy += cabStepX) {
                ctxFront.beginPath(); ctxFront.moveTo(scX - scW_px / 2, cy); ctxFront.lineTo(scX + scW_px / 2, cy); ctxFront.stroke();
            }

            // Big Bold Screen Labels
            ctxFront.fillStyle = '#ffffff';
            ctxFront.font = 'bold 18px Arial';
            ctxFront.textAlign = 'center';
            ctxFront.fillText(sc.name, scX, scTopY + (scH_px / 2) - 16);

            ctxFront.fillStyle = '#fef08a';
            ctxFront.font = 'bold 16px Arial';
            ctxFront.fillText(`${sc.width}m × ${sc.height}m`, scX, scTopY + (scH_px / 2) + 6);

            ctxFront.fillStyle = '#38bdf8';
            ctxFront.font = 'bold 13px Arial';
            ctxFront.fillText(`${isHanging ? 'Rigging Truss Gantung' : 'Ground Stack'} • ${(sc.width * sc.height).toFixed(1)} m²`, scX, scTopY + (scH_px / 2) + 26);

            const dimLineX = scX - scW_px / 2 - 16;
            ctxFront.strokeStyle = '#94a3b8';
            ctxFront.lineWidth = 1.5;
            ctxFront.beginPath(); ctxFront.moveTo(dimLineX, scTopY); ctxFront.lineTo(dimLineX, scBottomY); ctxFront.stroke();
            ctxFront.beginPath(); ctxFront.moveTo(dimLineX - 4, scTopY); ctxFront.lineTo(dimLineX + 4, scTopY);
            ctxFront.moveTo(dimLineX - 4, scBottomY); ctxFront.lineTo(dimLineX + 4, scBottomY);
            ctxFront.stroke();

            ctxFront.save();
            ctxFront.translate(dimLineX - 8, scTopY + scH_px / 2);
            ctxFront.rotate(-Math.PI / 2);
            ctxFront.fillStyle = '#e2e8f0';
            ctxFront.font = 'bold 13px Arial';
            ctxFront.textAlign = 'center';
            ctxFront.fillText(`Tinggi: ${sc.height}m`, 0, 0);
            ctxFront.restore();

            const totalPeak = (stageConfig.height + sc.height + clearanceM).toFixed(2);
            ctxFront.fillStyle = '#38bdf8';
            ctxFront.font = 'bold 14px Arial';
            ctxFront.textAlign = 'center';
            ctxFront.fillText(`▲ Puncak: +${totalPeak}m`, scX, scTopY - 12);
        });

        ctxFront.fillStyle = 'rgba(15, 23, 42, 0.95)';
        ctxFront.fillRect(20, 20, 480, 70);
        ctxFront.strokeStyle = '#facc15';
        ctxFront.lineWidth = 1.5;
        ctxFront.strokeRect(20, 20, 480, 70);

        ctxFront.fillStyle = '#facc15';
        ctxFront.font = 'bold 16px Arial';
        ctxFront.textAlign = 'left';
        ctxFront.fillText('OMAH VISUAL - BLUEPRINT ELEVASI TAMPAK DEPAN', 32, 44);
        ctxFront.fillStyle = '#e2e8f0';
        ctxFront.font = '13px Arial';
        ctxFront.fillText(`Format: Tampak Depan Penonton • Total Layar: ${screensData.length} Unit`, 32, 63);
        ctxFront.fillStyle = '#94a3b8';
        ctxFront.font = '12px Arial';
        ctxFront.fillText(`Level Panggung: +${stageConfig.height}m • Skala Dinamis: ${scaleFront.toFixed(1)} px/m`, 32, 80);

        // Inject high-res data URLs
        const topData = topCanvas.toDataURL('image/png');
        const frontData = frontCanvas.toDataURL('image/png');

        const topImg = document.getElementById('printBlueprintTopImg');
        const frontImg = document.getElementById('printBlueprintFrontImg');
        const thumbImg = document.getElementById('printPage1ThumbImg');

        if (topImg) {
            topImg.src = topData;
            topImg.style.display = 'block';
            document.getElementById('printBlueprintTopFallback')?.remove();
        }
        if (frontImg) {
            frontImg.src = frontData;
            frontImg.style.display = 'block';
            document.getElementById('printBlueprintFrontFallback')?.remove();
        }
        if (thumbImg) {
            thumbImg.src = topData;
            thumbImg.style.display = 'block';
            document.getElementById('printPage1ThumbFallback')?.remove();
        }
    }

    // Auto-render on load
    window.addEventListener('DOMContentLoaded', renderPrintBlueprints);
</script>

</body>
</html>
