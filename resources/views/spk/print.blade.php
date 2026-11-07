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
           ========================================================================= */
        @page {
            size: A4 portrait;
            margin: 6mm 8mm;
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
            line-height: 1.25;
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
                padding: 10px 20px;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
                border-bottom: 2px solid #3b82f6;
            }
            .print-container {
                max-width: 210mm;
                margin: 15px auto;
                padding: 0;
            }
            .page {
                background: #fff;
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto 25px auto;
                padding: 6mm 8mm;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35);
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

        /* TYPOGRAPHY & BOXES */
        .banner-blue {
            background-color: #cfe2ff !important;
            color: #000;
            font-weight: bold;
            font-size: 10.5px;
            padding: 3.5px 6px;
            border: 1px solid #9ec5fe;
        }

        .banner-yellow {
            background-color: #fef08a !important;
            color: #000;
            font-weight: bold;
            font-size: 13px;
            padding: 4px 8px;
            border-bottom: 2px solid #000;
        }

        .banner-pink {
            background-color: #fbcfe8 !important;
            color: #000;
            font-weight: bold;
            font-size: 13px;
            padding: 4px 8px;
            border-bottom: 2px solid #000;
        }

        .banner-softpink {
            background-color: #fce7f3 !important;
            color: #000;
            font-weight: bold;
            font-size: 10.5px;
            padding: 4px;
            text-align: center;
            border: 1px solid #f472b6;
        }

        .banner-softblue {
            background-color: #dbeafe !important;
            color: #000;
            font-weight: bold;
            font-size: 10.5px;
            padding: 4px;
            text-align: center;
            border: 1px solid #93c5fd;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            line-height: 1.2;
        }

        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 2.5px 4px;
        }

        .data-table th {
            background-color: #cfe2ff !important;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
        }

        .cat-row {
            background-color: #dbeafe !important;
            font-weight: bold;
        }

        .cat-row td {
            padding: 2.5px 5px;
            font-size: 9.5px;
        }

        .striped-row:nth-child(even) {
            background-color: #f8fafc;
        }

        /* BUTTONS */
        .btn-print {
            padding: 6px 14px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 11.5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-print:hover { background: #1d4ed8; }

        .btn-sub-print {
            padding: 5px 10px;
            background: #1e293b;
            color: #cbd5e1;
            border: 1px solid #475569;
            border-radius: 6px;
            cursor: pointer;
            font-size: 10.5px;
            font-weight: 600;
        }
        .btn-sub-print:hover { background: #334155; color: #fff; }
    </style>
</head>
<body>

<!-- TOP ACTION BAR (HIDDEN ON PHYSICAL PRINT) -->
<div class="print-btn-bar">
    <div style="display: flex; align-items: center; gap: 10px;">
        <span style="font-size: 14px; font-weight: bold; color: #38bdf8;">Omahvisual LED</span>
        <span style="color: #64748b;">|</span>
        <span style="font-size: 12px; font-weight: 600;">SPK &amp; Operasional • {{ $assignment->spk_number }}</span>
    </div>
    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
        <button type="button" onclick="printAllPages()" class="btn-print" title="Cetak Seluruh Dokumen (Hal 1 s/d 5)">
            🖨️ Cetak Semua (Hal 1-5)
        </button>
        <button type="button" onclick="printSinglePage(1)" class="btn-sub-print" title="Cetak Format SPK Halaman 1 Saja">
            📄 Hal 1 (SPK Resmi)
        </button>
        <button type="button" onclick="printChecklistPages()" class="btn-sub-print" title="Cetak Lembar Checklist Muatan Gudang (Hal 2 & 3)">
            📦 Hal 2-3 (Checklist Muatan)
        </button>
        <button type="button" onclick="printSinglePage(4)" class="btn-sub-print" title="Cetak Lembar Routing Teknis & Panel 16A (Hal 4)">
            📐 Hal 4 (Routing Teknis 4A)
        </button>
        <button type="button" onclick="printSinglePage(5)" class="btn-sub-print" title="Cetak Lembar Berita Acara Serah Terima (Hal 5)">
            📋 Hal 5 (Berita Acara / BAST)
        </button>
        <a href="{{ route('orders.show', $assignment->eventOrder) }}" class="btn-sub-print" style="text-decoration: none;">
            ✕ Tutup
        </a>
    </div>
</div>

<div class="print-container">

    @php
        use App\Services\LedCalculationService;
        $order = $assignment->eventOrder;
        $customer = $order->customer;
        $screens = $order->screens;
        $customerSalutation = $customer->salutation ? $customer->salutation . ' ' : '';
        $picCrew = $assignment->crewAssignments->where('role_label', 'PIC')->first()?->crew;
        $picName = $assignment->crew_pic_event_name ?: ($picCrew ? $picCrew->nickname : '-');
        $picPhone = $picCrew ? $picCrew->phone : ($customer->emergency_phone ?? '-');
        $formattedTotalSqm = LedCalculationService::formatDecimal($order->total_sqm);
        $formattedScreenSizes = $screens->map(fn($s) => LedCalculationService::formatDecimal($s->width_m) . 'x' . LedCalculationService::formatDecimal($s->height_m))->implode(' + ');
        $venueQuery = $order->google_maps_url ?: ('https://www.google.com/maps/search/?api=1&query=' . urlencode($order->venue_name . ' ' . ($order->venue_address ?? '')));
    @endphp

    <!-- ========================================================================= -->
    <!-- HALAMAN 1: SURAT PERINTAH KERJA & IZIN BARANG KELUAR                     -->
    <!-- EXACT MATCH WITH referensi_halaman1_spk.png & spk_dari_mba_de.pdf PAGE 1 -->
    <!-- ========================================================================= -->
    <div class="page" id="page-1">
        <div style="border: 1.5px solid #000; padding: 10px 12px; min-height: 278mm; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <!-- TOP HEADER: LOGO + BANNER TITLE -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <div style="width: 38%;">
                        @if(file_exists(public_path('images/logo_cv_han_media.png')))
                            <img src="{{ asset('images/logo_cv_han_media.png') }}" style="height: 48px; max-width: 100%; object-fit: contain; display: block;" alt="CV. HAN MEDIA TEKINDO">
                        @else
                            <div style="font-weight: 900; font-size: 15px; letter-spacing: -0.5px;">
                                CV. HAN MEDIA<br><span style="font-size: 11px; letter-spacing: 2px;">TEKINDO</span>
                            </div>
                        @endif
                    </div>
                    <div style="width: 60%; background-color: #cfe2ff; border: 1px solid #9ec5fe; text-align: center; padding: 6px 8px;">
                        <div style="font-size: 13.5px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase; color: #000; line-height: 1.2;">
                            SURAT PERINTAH KERJA &amp; IZIN BARANG KELUAR
                        </div>
                    </div>
                </div>

                <!-- NO TRANSAKSI -->
                <div style="display: flex; justify-content: flex-start; margin-bottom: 6px;">
                    <div style="width: 250px; border: 1px solid #cbd5e1;">
                        <div style="background-color: #cfe2ff; font-weight: bold; font-size: 10px; padding: 2px 6px; border-bottom: 1px solid #cbd5e1;">
                            No Transaksi
                        </div>
                        <div style="padding: 2px 6px; font-weight: bold; font-size: 11px; text-align: right;">
                            {{ $order->order_number ?: $order->id }}
                        </div>
                    </div>
                </div>

                <!-- TWO COLUMNS: INFORMASI CUSTOMER & ACARA (LEFT) vs PENANGGUNG JAWAB & QR (RIGHT) -->
                <div style="display: flex; gap: 12px; margin-bottom: 8px;">
                    <!-- LEFT COLUMN -->
                    <div style="flex: 1.15;">
                        <!-- INFORMASI CUSTOMER -->
                        <div class="banner-blue" style="margin-bottom: 4px;">Informasi Customer</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 10.5px; margin-bottom: 8px;">
                            <tr>
                                <td style="width: 125px; padding: 2px 0; vertical-align: top;">Nama Client</td>
                                <td style="width: 10px; vertical-align: top;">:</td>
                                <td style="font-weight: bold; vertical-align: top;">{{ $customerSalutation }}{{ $customer->name }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0; vertical-align: top;">Alamat</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="vertical-align: top;">{{ $customer->address ?: ($order->venue_address ?: '-') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0; vertical-align: top;">Nama Vendor / Instansi</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="vertical-align: top;">{{ $customer->organization ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0; vertical-align: top;">No Telp</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="vertical-align: top;">{{ $customer->phone ?: '-' }}</td>
                            </tr>
                        </table>

                        <!-- INFORMASI ACARA -->
                        <div class="banner-blue" style="margin-bottom: 4px;">Informasi Acara</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                            <tr>
                                <td style="width: 125px; padding: 2px 0; vertical-align: top;">Lokasi Acara</td>
                                <td style="width: 10px; vertical-align: top;">:</td>
                                <td style="font-weight: bold; vertical-align: top;">{{ $order->venue_name }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0; vertical-align: top;">Jenis Event</td>
                                <td style="vertical-align: top;">:</td>
                                <td style="vertical-align: top;">{{ $order->event_type ?: 'Event' }}</td>
                            </tr>
                        </table>

                        <!-- JADWAL LOADING & ACARA (2x2 GRID MATCHING REFERENSI) -->
                        <table style="width: 100%; border-collapse: collapse; font-size: 10.5px; margin-top: 2px;">
                            <tr>
                                <td style="width: 75px; padding: 2px 0;">tgl Loading</td>
                                <td style="width: 10px;">:</td>
                                <td style="width: 120px; font-weight: bold;">{{ $order->loading_date->format('j-M-Y') }}</td>
                                <td style="width: 65px; padding: 2px 0;">tgl Acara</td>
                                <td style="width: 10px;">:</td>
                                <td style="font-weight: bold;">{{ $order->event_start_date->format('j-M-Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0;">Jam Loading</td>
                                <td>:</td>
                                <td style="font-weight: bold;">{{ $order->loading_time ?: '-' }}</td>
                                <td style="padding: 2px 0;">Jam Acara</td>
                                <td>:</td>
                                <td style="font-weight: bold;">{{ $order->event_time ?: ($order->jam_acara ?: '-') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- RIGHT COLUMN: PENANGGUNG JAWAB LAPANGAN & QR CODE -->
                    <div style="flex: 0.85;">
                        <div class="banner-blue" style="margin-bottom: 4px;">Penanggung jawab Lapangan</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 10.5px; margin-bottom: 4px;">
                            <tr>
                                <td style="width: 75px; padding: 2px 0;">Nama PJ</td>
                                <td style="width: 10px;">:</td>
                                <td style="font-weight: bold;">{{ $picName }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 2px 0;">No Telp</td>
                                <td>:</td>
                                <td style="font-weight: bold;">{{ $picPhone }}</td>
                            </tr>
                        </table>

                        <div style="text-align: center; margin-top: 4px;">
                            <div style="font-size: 26px; font-weight: 900; line-height: 1; letter-spacing: 1px;">QR</div>
                            <div style="font-size: 9.5px; font-weight: bold; margin-bottom: 4px;">Share lokasi :</div>
                            <div style="display: inline-block; padding: 4px; border: 1px solid #000; background: #fff;">
                                <div id="page1QrContainer" style="display: flex; justify-content: center; align-items: center;">
                                    <img id="page1QrFallbackImg" src="https://api.qrserver.com/v1/create-qr-code/?size=115x115&data={{ urlencode($venueQuery) }}" alt="QR Lokasi Venue" style="width: 110px; height: 110px; display: block;" crossorigin="anonymous">
                                </div>
                            </div>
                            <div style="font-size: 8px; color: #475569; margin-top: 2px; word-break: break-all;">
                                Scan untuk rute Google Maps venue
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABEL SPESIFIKASI ORDER (STRICTLY NO HARDCASE, NO PROCESSOR, NO KABINET, NO KENDARAAN) -->
                <div style="margin-bottom: 8px;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 32px;">No</th>
                                <th style="width: 42%; text-align: left; padding-left: 8px;">Uraian</th>
                                <th style="text-align: left; padding-left: 8px;">Detail Spesifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center; font-weight: bold;">1</td>
                                <td style="padding-left: 8px; font-weight: 600;">Total Sqm Yang di Order</td>
                                <td style="padding-left: 8px; font-weight: bold;">{{ $formattedTotalSqm }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: center; font-weight: bold;">2</td>
                                <td style="padding-left: 8px; font-weight: 600;">Ukuran LED</td>
                                <td style="padding-left: 8px; font-weight: bold;">{{ $formattedScreenSizes }}</td>
                            </tr>
                            @php
                                $extraRowsCount = max(0, 3 - max(0, $screens->count() - 1));
                            @endphp
                            @if($screens->count() > 1)
                                @foreach($screens->skip(1) as $sIndex => $extraScreen)
                                    <tr>
                                        <td style="text-align: center; font-weight: bold;">{{ $sIndex + 3 }}</td>
                                        <td style="padding-left: 8px; font-weight: 600;">Ukuran LED ({{ $extraScreen->screen_name ?: 'Area ' . ($sIndex + 2) }})</td>
                                        <td style="padding-left: 8px; font-weight: bold;">{{ LedCalculationService::formatDecimal($extraScreen->width_m) }}x{{ LedCalculationService::formatDecimal($extraScreen->height_m) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @for($r = 1; $r <= $extraRowsCount; $r++)
                                <tr class="striped-row">
                                    <td style="text-align: center; color: #94a3b8;">{{ 2 + ($screens->count() > 1 ? $screens->count() - 1 : 0) + $r }}</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <!-- KETERANGAN DETAIL -->
                <div style="margin-bottom: 8px;">
                    <div style="font-weight: bold; font-size: 10px; margin-bottom: 2px;">Keterangan Detail</div>
                    <div style="border: 1px solid #000; padding: 6px 8px; min-height: 28px; font-size: 10px;">
                        {{ $order->notes ?: 'level 1m' }}
                    </div>
                </div>

                <!-- TEAM CREW (10 ROWS EXACT MATCH WITH REFERENSI) -->
                <div>
                    <div style="font-weight: bold; font-size: 10px; margin-bottom: 2px;">Team Crew</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 32px;">No</th>
                                <th style="width: 32%;">Nama</th>
                                <th style="width: 32%;">Jabatan</th>
                                <th>No Telp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $assignedCrews = $assignment->crewAssignments;
                            @endphp
                            @for($c = 1; $c <= 10; $c++)
                                @php
                                    $currentCrewAssigned = $assignedCrews->get($c - 1);
                                @endphp
                                <tr class="{{ $c % 2 === 0 ? 'striped-row' : '' }}">
                                    <td style="text-align: center; font-weight: bold;">{{ $c }}</td>
                                    <td style="padding-left: 6px; font-weight: {{ $currentCrewAssigned ? 'bold' : 'normal' }};">
                                        {{ $currentCrewAssigned ? ($currentCrewAssigned->crew->nickname ?: $currentCrewAssigned->crew->full_name) : '' }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $currentCrewAssigned ? $currentCrewAssigned->role_label : '' }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $currentCrewAssigned ? ($currentCrewAssigned->crew->phone ?: '-') : '' }}
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BOTTOM DOCKED SIGNATURES (3 COLUMNS: PINK, BLUE, PLAIN - NO LEGAL DISCLAIMERS!) -->
            <div style="margin-top: 10px;">
                <table style="width: 100%; border-collapse: collapse; text-align: center;">
                    <tr>
                        <!-- COLUMN 1: PENANGGUNG JAWAB BARANG KELUAR (PINK) -->
                        <td style="width: 33%; vertical-align: top; padding: 0 4px;">
                            <div class="banner-softpink">
                                Penanggung Jawab<br>Barang Keluar
                            </div>
                            <div style="height: 48px;"></div>
                            <div style="font-weight: bold; font-size: 10.5px; border-bottom: 1px solid #000; display: inline-block; min-width: 120px; padding-bottom: 1px;">
                                {{ $assignment->departure_pic_warehouse_name ?: 'Byan' }}
                            </div>
                        </td>

                        <!-- COLUMN 2: PENANGGUNG JAWAB BARANG KEMBALI (BLUE) -->
                        <td style="width: 33%; vertical-align: top; padding: 0 4px;">
                            <div class="banner-softblue">
                                Penanggung Jawab<br>Barang Kembali
                            </div>
                            <div style="height: 48px;"></div>
                            <div style="font-weight: bold; font-size: 10.5px; border-bottom: 1px solid #000; display: inline-block; min-width: 120px; padding-bottom: 1px;">
                                {{ $assignment->return_pic_warehouse_name ?: 'Byan' }}
                            </div>
                        </td>

                        <!-- COLUMN 3: HORMAT KAMI, BAGIAN OPRASIONAL (PLAIN) -->
                        <td style="width: 34%; vertical-align: top; padding: 0 4px;">
                            <div style="font-weight: bold; font-size: 10.5px; padding: 4px; text-align: center;">
                                Hormat kami,<br>Bagian Oprasional
                            </div>
                            <div style="height: 48px;"></div>
                            <div style="font-weight: bold; font-size: 10.5px; border-bottom: 1px solid #000; display: inline-block; min-width: 120px; padding-bottom: 1px;">
                                Dani FT
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 2: CHEKLIST BARANG KERJA TEAM OMAHVISUAL LED                      -->
    <!-- EXACT MATCH WITH spk_dari_mba_de.pdf PAGE 2                               -->
    <!-- ========================================================================= -->
    <div class="page" id="page-2">
        <div style="border: 1.5px solid #000; padding: 8px 10px; min-height: 278mm;">
            <!-- YELLOW HEADER -->
            <div class="banner-yellow" style="margin-bottom: 4px;">
                2 Cheklist Barang Kerja TEAM Omahvisual LED
            </div>

            <!-- EVENT META BAR -->
            <table style="width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 6px; background: #f8fafc; border: 1px solid #cbd5e1;">
                <tr>
                    <td style="padding: 2.5px 6px; width: 33%;"><strong>No Event:</strong> {{ $order->order_number ?: $order->id }}</td>
                    <td style="padding: 2.5px 6px; width: 33%;"><strong>Nama Client:</strong> {{ $customerSalutation }}{{ $customer->name }}</td>
                    <td style="padding: 2.5px 6px; width: 34%;"><strong>PIC Event:</strong> {{ $picName }}</td>
                </tr>
                <tr>
                    <td style="padding: 2.5px 6px;"><strong>Tgl Loading:</strong> {{ $order->loading_date->format('d/m/Y') }}</td>
                    <td style="padding: 2.5px 6px;"><strong>Tgl Acara:</strong> {{ $order->event_start_date->format('d/m/Y') }}</td>
                    <td style="padding: 2.5px 6px;"><strong>Armada Logistik:</strong> {{ $assignment->assignedVehicles->pluck('vehicle_name')->implode(', ') ?: ($assignment->vehicle_name ?: ($order->vehicle_loading_in ?: 'Standar')) }}</td>
                </tr>
            </table>

            <!-- TABLE CHECKLIST PAGE 2 -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 26px;">NO</th>
                        <th>Barang Bawaan KE VENUE</th>
                        <th style="width: 160px;">Detail / Keterangan</th>
                        <th style="width: 55px;">QTY</th>
                        <th style="width: 48px;">Cheklist<br>Keluar</th>
                        <th style="width: 48px;">Cheklist<br>Kembali</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no2 = 1; $cat2 = ''; @endphp
                    @foreach($assignment->checklistItems->where('page_number', 2) as $item2)
                        @if($cat2 !== $item2->category)
                            @php $cat2 = $item2->category; @endphp
                            <tr class="cat-row">
                                <td colspan="6" style="padding: 2.5px 6px; font-weight: bold; background-color: #cfe2ff !important; font-size: 9.5px; text-transform: uppercase;">
                                    {{ $cat2 }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $no2++ }}</td>
                            <td>{{ $item2->item_name }}</td>
                            <td>{{ $item2->item_detail ?: '-' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $item2->quantity }} {{ $item2->unit }}</td>
                            <td style="text-align: center; font-weight: bold; font-size: 11px;">{{ $item2->checked_out ? '✓' : '' }}</td>
                            <td style="text-align: center; font-weight: bold; font-size: 11px;">{{ $item2->checked_in ? '✓' : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 3: CHEKLIST LANJUTAN                                              -->
    <!-- EXACT MATCH WITH spk_dari_mba_de.pdf PAGE 3                               -->
    <!-- ========================================================================= -->
    <div class="page" id="page-3">
        <div style="border: 1.5px solid #000; padding: 8px 10px; min-height: 278mm;">
            <!-- PINK HEADER -->
            <div class="banner-pink" style="margin-bottom: 4px;">
                3 CHEKLIST LANJUTAN
            </div>

            <!-- EVENT META BAR -->
            <table style="width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 6px; background: #f8fafc; border: 1px solid #cbd5e1;">
                <tr>
                    <td style="padding: 2.5px 6px; width: 33%;"><strong>No Event:</strong> {{ $order->order_number ?: $order->id }}</td>
                    <td style="padding: 2.5px 6px; width: 33%;"><strong>Nama Client:</strong> {{ $customerSalutation }}{{ $customer->name }}</td>
                    <td style="padding: 2.5px 6px; width: 34%;"><strong>Venue:</strong> {{ $order->venue_name }}</td>
                </tr>
            </table>

            <!-- TABLE CHECKLIST PAGE 3 -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 26px;">NO</th>
                        <th>Barang Bawaan KE VENUE</th>
                        <th style="width: 160px;">Detail</th>
                        <th style="width: 55px;">QTY</th>
                        <th style="width: 48px;">Cheklist<br>Keluar</th>
                        <th style="width: 48px;">Cheklist<br>Kembali</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no3 = 1; $cat3 = ''; @endphp
                    @foreach($assignment->checklistItems->where('page_number', 3) as $item3)
                        @if($cat3 !== $item3->category)
                            @php $cat3 = $item3->category; @endphp
                            <tr class="cat-row">
                                <td colspan="6" style="padding: 2.5px 6px; font-weight: bold; background-color: #cfe2ff !important; font-size: 9.5px; text-transform: uppercase;">
                                    {{ $cat3 }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td style="text-align: center; font-weight: bold;">{{ $no3++ }}</td>
                            <td>{{ $item3->item_name }}</td>
                            <td>{{ $item3->item_detail ?: '-' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $item3->quantity }} {{ $item3->unit }}</td>
                            <td style="text-align: center; font-weight: bold; font-size: 11px;">{{ $item3->checked_out ? '✓' : '' }}</td>
                            <td style="text-align: center; font-weight: bold; font-size: 11px;">{{ $item3->checked_in ? '✓' : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 4: 4A ROUTING TEKNIS & BLUEPRINT OPERASIONAL                      -->
    <!-- REKAP KESELURUHAN AREA, DIAGRAM PANEL 16A, POWER, DATA, RESOLUSI          -->
    <!-- ========================================================================= -->
    <div class="page" id="page-4">
        <div style="border: 1.5px solid #000; padding: 10px 12px; min-height: 278mm; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <!-- TITLE -->
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 4px; margin-bottom: 6px;">
                    <div>
                        <div style="font-size: 13.5px; font-weight: 900; text-transform: uppercase;">
                            4A ROUTING TEKNIS &amp; BLUEPRINT OPERASIONAL LED
                        </div>
                        <div style="font-size: 9.5px; color: #334155; font-weight: bold;">
                            Diagram Distribusi Panel Listrik 16A, Routing Sinyal Data LAN, &amp; Pixel Mapping VJ
                        </div>
                    </div>
                    <div style="font-size: 9.5px; text-align: right;">
                        <strong>SPK:</strong> {{ $assignment->spk_number }}<br>
                        <strong>Event:</strong> {{ $order->order_number ?: $order->id }}
                    </div>
                </div>

                <!-- 1. TABEL REKAP KESELURUHAN AREA DI SELURUH LAYAR (SINGLE MASTER TABLE) -->
                <div style="margin-bottom: 8px;">
                    <div style="font-weight: bold; font-size: 10px; margin-bottom: 3px; background: #e2e8f0; padding: 2px 6px; border: 1px solid #cbd5e1;">
                        📊 REKAP KESELURUHAN AREA LAYAR &amp; SPESIFIKASI TEKNIS
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 25px;">No</th>
                                <th style="width: 22%;">Area / Posisi Layar</th>
                                <th style="width: 14%;">Dimensi &amp; Luas</th>
                                <th style="width: 16%;">Resolusi Konten VJ</th>
                                <th style="width: 22%;">Routing Data NovaStar</th>
                                <th>Distribusi Daya Panel 16A</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($technicalRouting['screens'] as $rtIndex => $rt)
                                <tr>
                                    <td style="text-align: center; font-weight: bold;">{{ $rt['area_number'] }}</td>
                                    <td>
                                        <strong>{{ $rt['screen_name'] }}</strong><br>
                                        <span style="font-size: 8.5px; color: #475569;">LED {{ $rt['brand_series'] }} ({{ $rt['total_cabinets'] }} Kabinet)</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <strong>{{ $rt['dimension_text'] }}</strong><br>
                                        <span style="font-size: 8.5px; color: #1d4ed8; font-weight: bold;">{{ LedCalculationService::formatDecimal($rt['total_sqm']) }} m²</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <strong>{{ $rt['resolution_text'] }}</strong><br>
                                        <span style="font-size: 8.5px; color: #475569;">128px / 50cm</span>
                                    </td>
                                    <td>
                                        <span style="font-size: 8.5px; font-weight: 600;">{{ $rt['lan_routing_text'] }}</span><br>
                                        <span style="font-size: 8px; color: #16a34a;">Prosesor: {{ $rt['processor_model'] }}</span>
                                    </td>
                                    <td>
                                        <strong style="color: #b91c1c;">{{ $rt['power_routing_text'] }}</strong><br>
                                        <span style="font-size: 8px; color: #475569;">Standar {{ $rt['avg_watt'] }}W / Beban Puncak</span>
                                    </td>
                                </tr>
                            @endforeach
                            <!-- SUMMARY ROW -->
                            <tr style="background-color: #f1f5f9; font-weight: bold;">
                                <td colspan="2" style="text-align: right; padding-right: 8px;">TOTAL KESELURUHAN:</td>
                                <td style="text-align: center; color: #1d4ed8;">{{ LedCalculationService::formatDecimal($technicalRouting['total_sqm']) }} m²</td>
                                <td style="text-align: center;">{{ $technicalRouting['total_screens'] }} Layar Aktif</td>
                                <td>{{ $technicalRouting['total_lan_ports'] }} Port LAN Utama</td>
                                <td style="color: #b91c1c;">{{ number_format($technicalRouting['total_watts']) }} W (~{{ $technicalRouting['total_max_amperes'] }} A)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 2. DIAGRAM PANEL LISTRIK 16A & DISTRIBUSI BEBAN 3-PHASE (R - S - T) -->
                <div style="margin-bottom: 8px; border: 1px solid #000; padding: 6px; background: #fff;">
                    <div style="font-weight: bold; font-size: 10px; margin-bottom: 4px; display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding-bottom: 3px;">
                        <span>⚡ DIAGRAM PEMBAGIAN FASA PANEL LISTRIK 16A (3-PHASE BALANCE)</span>
                        <span style="color: #1d4ed8;">Rekomendasi: {{ $technicalRouting['recommended_panel'] }} • Genset Min {{ $technicalRouting['recommended_genset_kva'] }} KVA</span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-top: 4px;">
                        <!-- FASA R -->
                        <div style="border: 1px solid #ef4444; border-radius: 4px; padding: 4px; background: #fef2f2;">
                            <div style="font-weight: bold; font-size: 9.5px; color: #b91c1c; border-bottom: 1px solid #fca5a5; padding-bottom: 2px;">
                                🔴 FASA R (LINE 1)
                            </div>
                            <div style="font-size: 8.5px; margin-top: 3px; line-height: 1.3;">
                                • MCB 1 - 4 (16A Curve C)<br>
                                • Beban: Area 1 (LED Layar Utama)<br>
                                • Feeder: Legrand 16A to PowerCon<br>
                                • Maksimum: 3.500 Watt / Fasa
                            </div>
                        </div>

                        <!-- FASA S -->
                        <div style="border: 1px solid #eab308; border-radius: 4px; padding: 4px; background: #fefce8;">
                            <div style="font-weight: bold; font-size: 9.5px; color: #a16207; border-bottom: 1px solid #fde047; padding-bottom: 2px;">
                                🟡 FASA S (LINE 2)
                            </div>
                            <div style="font-size: 8.5px; margin-top: 3px; line-height: 1.3;">
                                • MCB 5 - 8 (16A Curve C)<br>
                                • Beban: Area 2 (LED Sayap / Sub-Screen)<br>
                                • Feeder: Legrand 16A to PowerCon<br>
                                • Maksimum: 3.500 Watt / Fasa
                            </div>
                        </div>

                        <!-- FASA T -->
                        <div style="border: 1px solid #3b82f6; border-radius: 4px; padding: 4px; background: #eff6ff;">
                            <div style="font-weight: bold; font-size: 9.5px; color: #1d4ed8; border-bottom: 1px solid #bfdbfe; padding-bottom: 2px;">
                                🔵 FASA T (LINE 3)
                            </div>
                            <div style="font-size: 8.5px; margin-top: 3px; line-height: 1.3;">
                                • MCB 9 - 12 (16A Curve C)<br>
                                • Beban: Prosessor, FOH Control, Laptop VJ<br>
                                • Grounding: Wajib &lt; 2 Volt (Tembaga 50m)<br>
                                • MCB Spare Standby: Siap Cadangan
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. ROUTING POWER & DATA LAN FLOW -->
                <div style="border: 1px solid #000; padding: 6px; background: #fff; margin-bottom: 6px;">
                    <div style="font-weight: bold; font-size: 10px; margin-bottom: 4px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 3px;">
                        🔄 ALUR ROUTING POWER &amp; DATA LAN (DAISY CHAIN PROTOCOL)
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 8.5px;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding-right: 6px; border-right: 1px solid #cbd5e1;">
                                <strong style="color: #b91c1c;">Alur Arus Listrik (Power Flow):</strong><br>
                                1. Sumber Genset / PLN 3-Phase &rarr; Konektor 200A / Babok 50m &rarr; Panel 16A Omah Visual.<br>
                                2. Output Panel 16A &rarr; Legrand to Corn (Power Awal) &rarr; Masuk ke Kabinet Awal Area.<br>
                                3. Daisy-Chain: Power Corn Jumper 1m/1.5m antar kabinet (<strong>Maksimal 6-8 kabinet per jalur power</strong>).<br>
                                4. Grounding: Pasak tembaga 50m ditanam ke tanah basah venue demi mencegah induksi bocor.
                            </td>
                            <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                                <strong style="color: #1d4ed8;">Alur Sinyal Video &amp; Data (Data Flow):</strong><br>
                                1. Source Video (Laptop Operator / Resolume VJ) &rarr; HDMI 4K &rarr; Prosessor NovaStar.<br>
                                2. NovaStar ETH Port 1..N &rarr; Kabel LAN Awal Panjang (Cat6 Shielded) &rarr; Port IN Kabinet 1.<br>
                                3. Daisy-Chain: LAN Jumper menghubungkan Port OUT ke Port IN kabinet berikutnya secara berurutan.<br>
                                4. Alur urutan kabinet: Kiri-ke-Kanan, Bawah-ke-Atas (Ular / Snake Pattern).
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- 4. VISUAL BLUEPRINT THUMBNAIL (TOP MAPPING & FRONT ELEVATION) -->
                @if($order->stage_layout_image || $order->stage_layout_front_image)
                    <div style="border: 1px solid #000; padding: 4px; text-align: center; background: #000; margin-bottom: 4px;">
                        <div style="font-size: 8.5px; font-weight: bold; color: #38bdf8; margin-bottom: 2px; text-transform: uppercase;">
                            {!! 'PEMETAAN PANEL & KABINET LED • ELEVASI TAMPAK DEPAN PANGGUNG' !!}
                        </div>
                        <div style="display: flex; gap: 4px; justify-content: center; align-items: center;">
                            @if($order->stage_layout_image)
                                <img src="{{ $order->stage_layout_image }}" style="max-height: 70mm; max-width: {{ $order->stage_layout_front_image ? '50%' : '100%' }}; object-fit: contain; display: block; margin: 0 auto;">
                            @endif
                            @if($order->stage_layout_front_image)
                                <img src="{{ $order->stage_layout_front_image }}" style="max-height: 70mm; max-width: {{ $order->stage_layout_image ? '50%' : '100%' }}; object-fit: contain; display: block; margin: 0 auto;">
                            @endif
                        </div>
                    </div>
                @else
                    <div style="border: 1px solid #000; padding: 4px; text-align: center; background: #0f172a; margin-bottom: 4px; color: #94a3b8; font-size: 9px;">
                        {!! 'PEMETAAN PANEL & KABINET LED • ELEVASI TAMPAK DEPAN PANGGUNG' !!}
                    </div>
                @endif
            </div>

            <!-- FOOTER SIGN-OFF TEKNISI -->
            <div style="border-top: 1px solid #000; padding-top: 4px; display: flex; justify-content: space-between; font-size: 8.5px; color: #475569;">
                <div>Dokumen Perencanaan Teknis Lapangan • CV. Han Media Tekindo (Omah Visual LED)</div>
                <div>Disetujui Teknisi QC: <strong>{{ $assignment->qc_technician_name ?: $picName }}</strong></div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- HALAMAN 5: BERITA ACARA SERAH TERIMA (BAST / BAT)                         -->
    <!-- OFFICIAL HANDOVER BETWEEN OMAH VISUAL AND CLIENT/VENDOR AT VENUE          -->
    <!-- ========================================================================= -->
    <div class="page" id="page-5">
        <div style="border: 1.5px solid #000; padding: 14px 16px; min-height: 278mm; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <!-- BAST HEADER -->
                <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px;">
                    <div style="font-size: 15px; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase;">
                        BERITA ACARA SERAH TERIMA (BAST)
                    </div>
                    <div style="font-size: 11px; font-weight: bold; color: #334155; margin-top: 2px;">
                        INSTALASI &amp; UJI KELAYAKAN OPERASIONAL LED SCREEN
                    </div>
                    <div style="font-size: 9.5px; color: #64748b; margin-top: 2px;">
                        Nomor: BAST/{{ date('Ymd') }}/{{ $assignment->spk_number }}
                    </div>
                </div>

                <!-- PREAMBLE -->
                <p style="font-size: 10.5px; text-align: justify; margin-bottom: 10px; line-height: 1.4;">
                    Pada hari ini, <strong>{{ \Carbon\Carbon::parse($order->event_start_date)->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::parse($order->event_start_date)->translatedFormat('d F Y') }}</strong>, bertempat di <strong>{{ $order->venue_name }}</strong>, telah dilakukan pemeriksaan fisik, pengujian sinyal, dan serah terima operasional instalasi LED Screen antara pihak-pihak di bawah ini:
                </p>

                <!-- PIHAK PERTAMA & KEDUA -->
                <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 12px; border: 1px solid #cbd5e1;">
                    <tr style="background: #f1f5f9;">
                        <th style="width: 50%; padding: 4px 8px; border: 1px solid #cbd5e1; text-align: left;">PIHAK PERTAMA (PENYEDIA JASA)</th>
                        <th style="width: 50%; padding: 4px 8px; border: 1px solid #cbd5e1; text-align: left;">PIHAK KEDUA (KLIEN / VENDOR)</th>
                    </tr>
                    <tr>
                        <td style="padding: 6px 8px; vertical-align: top; border: 1px solid #cbd5e1; line-height: 1.4;">
                            <strong>CV. HAN MEDIA TEKINDO (Omah Visual LED)</strong><br>
                            Nama PIC Lapangan: <strong>{{ $picName }}</strong><br>
                            Kontak PJ: {{ $picPhone }}<br>
                            Alamat: Yogyakarta &amp; Malang
                        </td>
                        <td style="padding: 6px 8px; vertical-align: top; border: 1px solid #cbd5e1; line-height: 1.4;">
                            Nama Klien: <strong>{{ $customerSalutation }}{{ $customer->name }}</strong><br>
                            Instansi / Vendor: <strong>{{ $customer->organization ?: '-' }}</strong><br>
                            Kontak: {{ $customer->phone ?: '-' }}<br>
                            Lokasi Event: {{ $order->venue_name }}
                        </td>
                    </tr>
                </table>

                <!-- RINCIAN BARANG TERPASANG -->
                <div style="font-weight: bold; font-size: 10.5px; margin-bottom: 4px;">A. Rincian Instalasi Layar LED:</div>
                <table class="data-table" style="margin-bottom: 12px;">
                    <thead>
                        <tr>
                            <th style="width: 30px;">No</th>
                            <th>Posisi Layar</th>
                            <th style="width: 90px;">Ukuran (P × L)</th>
                            <th style="width: 75px;">Luas (m²)</th>
                            <th style="width: 120px;">Tipe LED</th>
                            <th>Metode Pemasangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($screens as $sIdx => $scr)
                            <tr>
                                <td style="text-align: center; font-weight: bold;">{{ $sIdx + 1 }}</td>
                                <td style="font-weight: bold;">{{ $scr->screen_name ?: 'Area ' . ($sIdx + 1) }}</td>
                                <td style="text-align: center;">{{ LedCalculationService::formatDecimal($scr->width_m) }}m × {{ LedCalculationService::formatDecimal($scr->height_m) }}m</td>
                                <td style="text-align: center; font-weight: bold; color: #1d4ed8;">{{ LedCalculationService::formatDecimal($scr->total_sqm) }} m²</td>
                                <td style="text-align: center;">{{ LedCalculationService::obfuscateBrand($scr->brand_series) }} ({{ $scr->pixel_pitch ?: 'P3.9' }})</td>
                                <td style="text-align: center;">{{ $scr->rigging_method === 'hanging' ? 'Truss Gantung (Rigging)' : 'Ground Stack (Panggung)' }}</td>
                            </tr>
                        @endforeach
                        <tr style="background-color: #f1f5f9; font-weight: bold;">
                            <td colspan="3" style="text-align: right; padding-right: 8px;">TOTAL LUAS LAYAR:</td>
                            <td style="text-align: center; color: #1d4ed8;">{{ $formattedTotalSqm }} m²</td>
                            <td colspan="2" style="font-size: 9px; color: #475569;">Waktu Selesai Loading: {{ $order->loading_date->format('d/m/Y') }} {{ $order->loading_time }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- CHECKLIST HASIL UJI KELAYAKAN BERSAMA (JOINT ACCEPTANCE) -->
                <div style="font-weight: bold; font-size: 10.5px; margin-bottom: 4px;">B. Hasil Uji Kelayakan &amp; Kualitas Bersama:</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 12px; border: 1px solid #cbd5e1;">
                    <tr style="background: #f8fafc;">
                        <th style="width: 30px; padding: 4px; border: 1px solid #cbd5e1;">Status</th>
                        <th style="padding: 4px 8px; border: 1px solid #cbd5e1; text-align: left;">Item Pengujian</th>
                        <th style="padding: 4px 8px; border: 1px solid #cbd5e1; text-align: left;">Hasil Pemeriksaan Lapangan</th>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-weight: bold; color: #16a34a; border: 1px solid #cbd5e1;">[✓]</td>
                        <td style="padding: 3px 8px; font-weight: bold; border: 1px solid #cbd5e1;">Konstruksi &amp; Rigging</td>
                        <td style="padding: 3px 8px; border: 1px solid #cbd5e1;">Struktur panggung/rigging kokoh, level permukaan rata, pengunci panel terpasang kuat.</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-weight: bold; color: #16a34a; border: 1px solid #cbd5e1;">[✓]</td>
                        <td style="padding: 3px 8px; font-weight: bold; border: 1px solid #cbd5e1;">Tampilan Visual &amp; Warna</td>
                        <td style="padding: 3px 8px; border: 1px solid #cbd5e1;">Semua modul LED menyala sempurna, tes pola RGB bersih tanpa bintik mati (dead pixel).</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-weight: bold; color: #16a34a; border: 1px solid #cbd5e1;">[✓]</td>
                        <td style="padding: 3px 8px; font-weight: bold; border: 1px solid #cbd5e1;">Sinyal Video &amp; VJ Mapping</td>
                        <td style="padding: 3px 8px; border: 1px solid #cbd5e1;">Resolusi sesuai konten acara, refresh rate stabil 3840Hz tanpa flicker atau kedip sinyal.</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-weight: bold; color: #16a34a; border: 1px solid #cbd5e1;">[✓]</td>
                        <td style="padding: 3px 8px; font-weight: bold; border: 1px solid #cbd5e1;">Kelistrikan &amp; Grounding Safety</td>
                        <td style="padding: 3px 8px; border: 1px solid #cbd5e1;">Jalur MCB 16A terdistribusi seimbang, tegangan netral-ke-tanah aman (&lt; 2 Volt).</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-weight: bold; color: #16a34a; border: 1px solid #cbd5e1;">[✓]</td>
                        <td style="padding: 3px 8px; font-weight: bold; border: 1px solid #cbd5e1;">Kerapihan Kabel &amp; Area Kerja</td>
                        <td style="padding: 3px 8px; border: 1px solid #cbd5e1;">Kabel FOH terlindung rapi dan area panggung bersih siap untuk dimulainya acara.</td>
                    </tr>
                </table>

                <!-- STATEMENT OF HANDOVER -->
                <p style="font-size: 10px; text-align: justify; margin-bottom: 12px; line-height: 1.4;">
                    Demikian Berita Acara Serah Terima ini dibuat dengan sebenarnya dan ditandatangani oleh kedua belah pihak. Dengan penandatanganan ini, layar LED resmi diserahterimakan dalam kondisi prima dan siap digunakan untuk mendukung kelancaran acara.
                </p>
            </div>

            <!-- DUAL OFFICIAL SIGNATURES -->
            <div>
                <table style="width: 100%; border-collapse: collapse; text-align: center;">
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding: 0 10px;">
                            <div style="font-weight: bold; font-size: 10.5px;">PIHAK KEDUA (Penyewa / Client)</div>
                            <div style="font-size: 9.5px; color: #475569;">{{ $customer->organization ?: 'Klien Acara' }}</div>
                            <div style="height: 55px;"></div>
                            <div style="font-weight: bold; font-size: 11px; border-bottom: 1px solid #000; display: inline-block; min-width: 170px; padding-bottom: 2px;">
                                ( {{ $customerSalutation }}{{ $customer->name }} )
                            </div>
                            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">Tanda Tangan &amp; Nama Terang</div>
                        </td>

                        <td style="width: 50%; vertical-align: top; padding: 0 10px;">
                            <div style="font-weight: bold; font-size: 10.5px;">PIHAK PERTAMA (Penyedia)</div>
                            <div style="font-size: 9.5px; color: #475569;">CV. Han Media Tekindo (Omah Visual LED)</div>
                            <div style="height: 55px;"></div>
                            <div style="font-weight: bold; font-size: 11px; border-bottom: 1px solid #000; display: inline-block; min-width: 170px; padding-bottom: 2px;">
                                ( {{ $picName }} )
                            </div>
                            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">PIC Lapangan Omah Visual LED</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- OFFLINE QR CODE SCRIPT -->
<script src="{{ asset('vendor/qrcode.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var qrTarget = "{{ $venueQuery }}";
        var container = document.getElementById("page1QrContainer");

        if (typeof QRCode !== "undefined" && container) {
            // Render offline vector QR Code
            try {
                container.innerHTML = "";
                new QRCode(container, {
                    text: qrTarget,
                    width: 110,
                    height: 110,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M
                });
            } catch(e) {
                console.warn("QRCode local render error, using fallback image:", e);
            }
        }
    });

    // PRINT ACTIONS
    function printAllPages() {
        document.querySelectorAll('.page').forEach(function(el) {
            el.classList.remove('print-hide');
        });
        window.print();
    }

    function printSinglePage(pageNum) {
        document.querySelectorAll('.page').forEach(function(el) {
            el.classList.add('print-hide');
        });
        var targetPage = document.getElementById('page-' + pageNum);
        if (targetPage) {
            targetPage.classList.remove('print-hide');
        }
        window.print();
        // Reset view for browser display
        document.querySelectorAll('.page').forEach(function(el) {
            el.classList.remove('print-hide');
        });
    }

    function printChecklistPages() {
        document.querySelectorAll('.page').forEach(function(el) {
            el.classList.add('print-hide');
        });
        var p2 = document.getElementById('page-2');
        var p3 = document.getElementById('page-3');
        if (p2) p2.classList.remove('print-hide');
        if (p3) p3.classList.remove('print-hide');
        window.print();
        document.querySelectorAll('.page').forEach(function(el) {
            el.classList.remove('print-hide');
        });
    }
</script>
</body>
</html>
