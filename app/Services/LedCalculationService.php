<?php

namespace App\Services;

use App\Models\EventOrder;
use App\Models\SpkAssignment;
use App\Models\SpkChecklistItem;

class LedCalculationService
{
    /**
     * Obfuscate internal brand names for field documents and SPK.
     * SAGA -> SG, MAVIC -> MV, LITE -> LT, LAMPRO -> LP.
     */
    public static function obfuscateBrand(?string $brand): string
    {
        if (empty($brand)) {
            return 'SG';
        }

        $map = [
            'SAGA' => 'SG',
            'MAVIC' => 'MV',
            'LITE' => 'LT',
            'LAMPRO' => 'LP',
            'Saga' => 'SG',
            'Mavic' => 'MV',
            'Lite' => 'LT',
            'Lampro' => 'LP',
        ];

        return str_ireplace(array_keys($map), array_values($map), $brand);
    }

    /**
     * Format decimal numbers cleanly without trailing zeroes (e.g. 4.5 -> 4,5; 12.0 -> 12).
     */
    public static function formatDecimal(float|int|string|null $value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        $num = (float) $value;
        if (floor($num) == $num) {
            return (string) (int) $num;
        }

        return str_replace('.', ',', rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.'));
    }

    /**
     * Calculate screen specifications and technical equipment requirements.
     *
     * @return array<string, mixed>
     */
    public function calculateScreen(float $width, float $height, string $pitch = 'P3.9', string $brand = 'SAGA'): array
    {
        $sqm = round($width * $height, 2);

        // Standard cabinet width is 0.5m (500mm)
        $columns = (int) round($width / 0.5);

        // Calculate 1m cabinets and 50cm cabinets based on height
        $wholeMeters = (int) floor($height);
        $remainderHeight = round($height - $wholeMeters, 2);

        $cabinets1m = $columns * $wholeMeters;
        $cabinets50cm = 0;

        if ($remainderHeight >= 0.4) {
            $cabinets50cm = $columns * 1;
        }

        $totalCabinets = $cabinets1m + $cabinets50cm;

        // Hardcase calculation: 1m hardcase holds 6 cabinets, 50cm hardcase holds 8 cabinets
        $hardcases1m = (int) ceil($cabinets1m / 6);
        $hardcases50cm = (int) ceil($cabinets50cm / 8);
        $totalHardcases = $hardcases1m + $hardcases50cm;

        // Pixel count calculation
        $pixelsPerCabinet = ($pitch === 'P2.6') ? 73728 : 32768; // P3.9 is 128x256 per 500x1000mm cabinet
        $totalPixels = $totalCabinets * $pixelsPerCabinet;

        // Recommend Novastar processor based on pixel capacity
        $recommendedProcessor = match (true) {
            $totalPixels <= 1300000 => 'Novastar VX400 / VX4S',
            $totalPixels <= 2600000 => 'Novastar VX600',
            $totalPixels <= 3900000 => 'Novastar VX1000',
            default => 'Novastar VX1000 Pro / VX20 + Fiber Optic',
        };

        // Jumpers & drops
        $powerJumpers = $totalCabinets;
        $lanJumpers = $totalCabinets;
        $powerDrops = (int) ceil($totalCabinets / 8);
        $lanDrops = (int) ceil($totalCabinets / 16);

        // Spare parts
        $spareModules = max(2, (int) ceil($sqm * 0.15));
        $spareReceivingCards = max(1, (int) ceil($sqm * 0.05));
        $sparePsu = max(1, (int) ceil($sqm * 0.05));

        return [
            'width_m' => $width,
            'height_m' => $height,
            'total_sqm' => $sqm,
            'brand_series' => $brand,
            'pixel_pitch' => $pitch,
            'columns' => $columns,
            'cabinets_1m' => $cabinets1m,
            'cabinets_50cm' => $cabinets50cm,
            'total_cabinets' => $totalCabinets,
            'hardcases_1m' => $hardcases1m,
            'hardcases_50cm' => $hardcases50cm,
            'total_hardcases' => $totalHardcases,
            'total_pixels' => $totalPixels,
            'processor_model' => $recommendedProcessor,
            'power_jumpers_qty' => $powerJumpers,
            'lan_jumpers_qty' => $lanJumpers,
            'power_drops_qty' => $powerDrops,
            'lan_drops_qty' => $lanDrops,
            'spare_modules_qty' => $spareModules,
            'spare_receiving_cards_qty' => $spareReceivingCards,
            'spare_psu_qty' => $sparePsu,
        ];
    }

    /**
     * Calculate comprehensive technical routing for all screens in an event order (Section 4A).
     *
     * @return array<string, mixed>
     */
    public function calculateTechnicalRouting(EventOrder $order): array
    {
        $screens = $order->screens;
        $routingDetails = [];
        $totalWatts = 0;
        $totalMaxAmperes = 0;
        $totalLanPorts = 0;

        $phases = ['Fasa R', 'Fasa S', 'Fasa T'];
        $phaseIndex = 0;
        $currentMcb = 1;

        foreach ($screens as $index => $screen) {
            $sqm = (float) $screen->total_sqm;
            // LED consumption: ~300W/sqm average, ~600W/sqm peak
            $avgWatt = (int) round($sqm * 300);
            $maxWatt = (int) round($sqm * 600);
            $maxAmp = round($maxWatt / 220, 1);

            $totalWatts += $maxWatt;
            $totalMaxAmperes += $maxAmp;

            // Pixel mapping
            $pixelsX = (int) round(((float) $screen->width_m / 0.5) * 128); // 128 px per 50cm
            $pixelsY = (int) round(((float) $screen->height_m / 0.5) * 128);
            $totalScreenPixels = $pixelsX * $pixelsY;

            // LAN ports (NovaStar port limit is 650,000 pixels per port)
            $lanPortsNeeded = max(1, (int) ceil($totalScreenPixels / 650000));
            $totalLanPorts += $lanPortsNeeded;

            // MCBs on 16A panel (each MCB can safely hold ~2200-2500 Watt)
            $mcbsNeeded = max(1, (int) ceil($maxWatt / 2200));
            $mcbStart = $currentMcb;
            $mcbEnd = $currentMcb + $mcbsNeeded - 1;
            $currentMcb = $mcbEnd + 1;

            $assignedPhase = $phases[$phaseIndex % 3];
            $phaseIndex++;

            $obfuscatedBrand = self::obfuscateBrand($screen->brand_series);

            $routingDetails[] = [
                'area_number' => $index + 1,
                'screen_id' => $screen->id,
                'screen_name' => $screen->screen_name ?: 'Area '.($index + 1),
                'brand_series' => $obfuscatedBrand,
                'width_m' => $screen->width_m,
                'height_m' => $screen->height_m,
                'dimension_text' => self::formatDecimal($screen->width_m).'m × '.self::formatDecimal($screen->height_m).'m',
                'total_sqm' => $sqm,
                'total_cabinets' => $screen->total_cabinets,
                'cabinets_detail' => ($screen->cabinets_1m ?? 0).'x 1m, '.($screen->cabinets_50cm ?? 0).'x 50cm',
                'pixels_w' => $pixelsX,
                'pixels_h' => $pixelsY,
                'total_pixels' => $totalScreenPixels,
                'resolution_text' => "{$pixelsX} × {$pixelsY} px",
                'lan_ports_needed' => $lanPortsNeeded,
                'lan_routing_text' => "Port {$lanPortsNeeded}x NovaStar ETH (Daisy-Chain Kabinet 1-{$screen->total_cabinets})",
                'max_watt' => $maxWatt,
                'avg_watt' => $avgWatt,
                'max_ampere' => $maxAmp,
                'assigned_phase' => $assignedPhase,
                'assigned_mcb' => "MCB {$mcbStart}".($mcbEnd > $mcbStart ? " s/d {$mcbEnd}" : ''),
                'power_routing_text' => "{$assignedPhase} (MCB {$mcbStart}".($mcbEnd > $mcbStart ? "-{$mcbEnd}" : '').") • {$maxWatt}W / ~{$maxAmp}A",
                'rigging_method' => $screen->rigging_method === 'hanging' ? 'Truss Gantung' : 'Ground Stack',
                'processor_model' => $screen->processor_model ?: 'NovaStar VX Series',
            ];
        }

        // Panel sizing recommendation
        $recommendedPanel = match (true) {
            $currentMcb <= 6 => 'Panel Listrik 16A (6 MCB)',
            $currentMcb <= 12 => 'Panel Listrik 16A (12 MCB)',
            default => 'Panel Listrik 16A (18 MCB / Dual Panel)',
        };

        // Genset sizing recommendation (KVA)
        $recommendedKva = max(10, (int) ceil(($totalWatts * 1.3) / 800));

        return [
            'screens' => $routingDetails,
            'total_screens' => count($routingDetails),
            'total_sqm' => $screens->sum('total_sqm'),
            'total_cabinets' => $screens->sum('total_cabinets'),
            'total_watts' => $totalWatts,
            'total_max_amperes' => round($totalMaxAmperes, 1),
            'total_lan_ports' => $totalLanPorts,
            'total_mcbs' => $currentMcb - 1,
            'recommended_panel' => $recommendedPanel,
            'recommended_genset_kva' => $recommendedKva,
        ];
    }

    /**
     * Generate standard SPK Page 2 & Page 3 checklists for an assignment.
     * Strictly aligns with spk_dari_mba_de.pdf and user guidelines:
     * - Obfuscates brand names (SAGA -> SG, MAVIC -> MV, etc.)
     * - Groups Power Jumpers and LED per area/screen
     * - Matches 14-item standard toolbox and complete accessories
     */
    public function generateStandardChecklist(SpkAssignment $assignment, EventOrder $order, string $template = 'essential'): void
    {
        $screens = $order->screens;
        $totalSqm = (float) $screens->sum('total_sqm');
        $totalCabinets = (int) $screens->sum('total_cabinets');
        $hasHanging = $screens->where('rigging_method', 'hanging')->isNotEmpty();

        // Clear existing checklist items for this assignment
        $assignment->checklistItems()->delete();

        if ($template === 'empty') {
            return;
        }

        $page2Items = [];
        $page3Items = [];

        // =========================================================================
        // HALAMAN 2: CHECKLIST BARANG KERJA TEAM OMAHVISUAL LED
        // =========================================================================

        // 1. LED & Power Jumpers (Organized PER SCREEN / AREA)
        $total1m = (int) $screens->sum('cabinets_1m');
        $total50cm = (int) $screens->sum('cabinets_50cm');

        if ($total1m > 0) {
            $hardcases1m = (int) ceil($total1m / 6);
            $page2Items[] = [
                'category' => 'FLIGHT CASE & PACKING',
                'item_name' => 'Flight Case Kabinet LED 100x50 cm (Isi 6)',
                'item_detail' => "Muatan {$total1m} Kabinet 1 Mtr ({$hardcases1m} Box)",
                'quantity' => $hardcases1m,
                'unit' => 'unit',
            ];
        }

        if ($total50cm > 0) {
            $hardcases50cm = (int) ceil($total50cm / 8);
            $page2Items[] = [
                'category' => 'FLIGHT CASE & PACKING',
                'item_name' => 'Flight Case Kabinet LED 50x50 cm (Isi 8)',
                'item_detail' => "Muatan {$total50cm} Kabinet 50cm ({$hardcases50cm} Box)",
                'quantity' => $hardcases50cm,
                'unit' => 'unit',
            ];
        }

        foreach ($screens as $idx => $screen) {
            $areaNum = $idx + 1;
            $areaLabel = $screen->screen_name ? "Area {$areaNum} ({$screen->screen_name})" : "Area {$areaNum}";
            $brandCode = self::obfuscateBrand($screen->brand_series);
            $screenSqm = self::formatDecimal($screen->total_sqm);
            $cabinetsCount = (int) $screen->total_cabinets;

            // LED Main item
            $page2Items[] = [
                'category' => 'LED & PERKABELAN UTAMA',
                'item_name' => "LED {$brandCode} - {$areaLabel}",
                'item_detail' => "No HC | {$screenSqm} SQM ({$screen->width_m}x{$screen->height_m}m)",
                'quantity' => $cabinetsCount > 0 ? $cabinetsCount : (int) ceil((float) $screen->total_sqm),
                'unit' => 'kabinet',
            ];

            // Power jumper per area
            $page2Items[] = [
                'category' => 'LED & PERKABELAN UTAMA',
                'item_name' => "Power Corn Jumper 1 mtr / 1,5 mtr ({$areaLabel})",
                'item_detail' => "Jumper Antar Kabinet {$brandCode}",
                'quantity' => max(1, $cabinetsCount),
                'unit' => 'pcs',
            ];
        }

        // Shared Power & Network Feeds for LED
        $powerAwalQty = max(2, (int) ceil($totalCabinets / 8));
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Power Awal (Legrand To Corn)',
            'item_detail' => 'Distribusi Power Input',
            'quantity' => $powerAwalQty,
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Lan Jumper',
            'item_detail' => 'Data Interconnect Antar Kabinet',
            'quantity' => max(1, $totalCabinets),
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Lan Awal panjang',
            'item_detail' => 'Main Data Send Port ke Prosessor',
            'quantity' => max(2, count($screens)),
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Lan Awal pendek',
            'item_detail' => 'Port Cadangan',
            'quantity' => 2,
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Besi Ground dan Kabel Grounding 50 Mtr',
            'item_detail' => 'T / B (Grounding Safety Venue)',
            'quantity' => 1,
            'unit' => 'roll',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Konektor Kabel panel',
            'item_detail' => 'Terminal Distribusi Genset/PLN',
            'quantity' => 1,
            'unit' => 'set',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Panel Listrik 16A',
            'item_detail' => ($totalSqm > 30) ? '18 MCB' : '12 MCB',
            'quantity' => 1,
            'unit' => 'unit',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Kabel Terminal FOH 25 / 50 / 100 mtr',
            'item_detail' => 'FOH Main Control Cable',
            'quantity' => 1,
            'unit' => 'roll',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Kabel babok 20-50 Mtr',
            'item_detail' => 'Power Main Feeder 3-Phase',
            'quantity' => 1,
            'unit' => 'roll',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Kupu2 (Kunci Sambung Panel)',
            'item_detail' => 'P2 / P3',
            'quantity' => max(10, $totalCabinets * 2),
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'LED & PERKABELAN UTAMA',
            'item_name' => 'Baut panjang',
            'item_detail' => 'Rigging Lock Bolt',
            'quantity' => 20,
            'unit' => 'pcs',
        ];

        // 2. TOOLS BOX No : isi 14 ITEM #WAJIB
        if ($template === 'essential') {
            $page2Items[] = [
                'category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB',
                'item_name' => 'Toolbox Operasional Teknisi (Standar 13 Items)',
                'item_detail' => 'Toolbox Operasional Lengkap 14 Item GA',
                'quantity' => 1,
                'unit' => 'box',
            ];
        }

        if ($template === 'full') {
            $page2Items[] = ['category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB', 'item_name' => 'Tang Kombinasi', 'item_detail' => 'Tang Kombinasi Toolbox', 'quantity' => 1, 'unit' => 'pcs'];
            $page2Items[] = ['category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB', 'item_name' => 'Tang Potong', 'item_detail' => 'Tang Potong Kabel', 'quantity' => 1, 'unit' => 'pcs'];
            $page2Items[] = ['category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB', 'item_name' => 'Tang Kupas', 'item_detail' => 'Wire Stripper', 'quantity' => 1, 'unit' => 'pcs'];
        }

        $page2Items[] = [
            'category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB',
            'item_name' => 'Tang Kombinasi ,Tang Kupas, Tang Potong',
            'item_detail' => 'Toolbox Operasional',
            'quantity' => 1,
            'unit' => 'set',
        ];
        $page2Items[] = [
            'category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB',
            'item_name' => 'Obeng +- , Linggis 35cm / Orange',
            'item_detail' => 'Toolbox Operasional',
            'quantity' => 1,
            'unit' => 'set',
        ];
        $page2Items[] = [
            'category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB',
            'item_name' => 'Tang Amper',
            'item_detail' => 'Digital Clamp Meter Ampere',
            'quantity' => 1,
            'unit' => 'unit',
        ];
        $page2Items[] = [
            'category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB',
            'item_name' => 'Creping+ Rj 45 Konektor +Lan Chekker + Baterei',
            'item_detail' => 'Tester & Crimping Set',
            'quantity' => 1,
            'unit' => 'set',
        ];
        $page2Items[] = [
            'category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB',
            'item_name' => 'Kunci Y , Kunci L ,Cuter',
            'item_detail' => 'Hex & Y-Wrench Set',
            'quantity' => 1,
            'unit' => 'set',
        ];
        $page2Items[] = [
            'category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB',
            'item_name' => 'Terminal Sambung 200A',
            'item_detail' => 'Heavy Duty Joint Terminal',
            'quantity' => 1,
            'unit' => 'unit',
        ];
        $page2Items[] = [
            'category' => 'TOOLS BOX No : isi 14 ITEM #WAJIB',
            'item_name' => 'Sarung Tangan 3 pasang',
            'item_detail' => 'Safety Gloves Crew',
            'quantity' => 1,
            'unit' => 'set',
        ];

        // 3. SPARE PART LED #WAJIB
        $page2Items[] = [
            'category' => 'SPARE PART LED #WAJIB',
            'item_name' => 'Modul LED',
            'item_detail' => 'SG / LT / LP',
            'quantity' => max(2, (int) ceil($totalSqm * 0.15)),
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'SPARE PART LED #WAJIB',
            'item_name' => 'Riciving Card',
            'item_detail' => 'K8 / K5 / E80',
            'quantity' => max(1, (int) ceil($totalSqm * 0.05)),
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'SPARE PART LED #WAJIB',
            'item_name' => 'Power BOX / Batok Belakang',
            'item_detail' => 'SG / LP',
            'quantity' => 1,
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'SPARE PART LED #WAJIB',
            'item_name' => 'Power Supply Unit',
            'item_detail' => 'SG , LT',
            'quantity' => max(1, (int) ceil($totalSqm * 0.05)),
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'SPARE PART LED #WAJIB',
            'item_name' => 'KIT LP , Modul + PSU + RC K8 LENGKAP',
            'item_detail' => 'LENGKAP',
            'quantity' => 1,
            'unit' => 'set',
        ];
        $page2Items[] = [
            'category' => 'SPARE PART LED #WAJIB',
            'item_name' => 'KIT SG , Modul + PSU + RC K5 LENGKAP',
            'item_detail' => 'LENGKAP',
            'quantity' => 1,
            'unit' => 'set',
        ];
        $page2Items[] = [
            'category' => 'SPARE PART LED #WAJIB',
            'item_name' => 'KIT LT , Modul + PSU + RC E8 LENGKAP',
            'item_detail' => 'LENGKAP',
            'quantity' => 1,
            'unit' => 'set',
        ];
        $page2Items[] = [
            'category' => 'SPARE PART LED #WAJIB',
            'item_name' => 'KIT MV , Modul + PSU + RC K6 LENGKAP',
            'item_detail' => 'LENGKAP',
            'quantity' => 1,
            'unit' => 'set',
        ];

        // 4. PROSESOR
        $processorModel = $screens->pluck('processor_model')->filter()->first() ?: 'NovaStar VX600';
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => "PROSESOR 1 ({$processorModel})",
            'item_detail' => 'Vx 2 / Vx 4 / Vx 6 / Vx 8 / VX1000',
            'quantity' => 1,
            'unit' => 'unit',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'PROSESOR 2 (Cadangan / Secondary)',
            'item_detail' => 'Vx 10',
            'quantity' => 1,
            'unit' => 'unit',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'PROSESOR 3',
            'item_detail' => 'X20M',
            'quantity' => 1,
            'unit' => 'unit',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'Kabel AC Power',
            'item_detail' => 'Power Main Controller',
            'quantity' => 2,
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'Kabel Setting',
            'item_detail' => 'USB Type-B / Ethernet Config',
            'quantity' => 1,
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'Hdmi To Hdmi',
            'item_detail' => 'Input Sinyal Video',
            'quantity' => 2,
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'Kabel Dvi to Hdmi',
            'item_detail' => 'Input Video Sekunder',
            'quantity' => 1,
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'Converter DVI to Hdmi',
            'item_detail' => 'Adapter Sinyal Display',
            'quantity' => 1,
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'KABEL HDMI TO HDMI 4K',
            'item_detail' => 'High Speed Ultra HD Video',
            'quantity' => 2,
            'unit' => 'pcs',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'Kabel FO 2 Core / 4 Core',
            'item_detail' => '2 Core / 4 Core Fiber Optic',
            'quantity' => 1,
            'unit' => 'roll',
        ];
        $page2Items[] = [
            'category' => 'PROSESOR',
            'item_name' => 'FSP 20G',
            'item_detail' => 'Fiber SFP Transceiver Module',
            'quantity' => 1,
            'unit' => 'unit',
        ];

        // =========================================================================
        // HALAMAN 3: CHEKLIST LANJUTAN
        // =========================================================================

        // 1. ATK Tambahan #WAJIB
        $page3Items[] = ['category' => 'ATK Tambahan #WAJIB', 'item_name' => 'Warping', 'item_detail' => 'Plastik Roll Pelindung', 'quantity' => 1, 'unit' => 'roll'];
        $page3Items[] = ['category' => 'ATK Tambahan #WAJIB', 'item_name' => 'Isolasi kertas Besar , Kecil', 'item_detail' => 'Marking Posisi', 'quantity' => 2, 'unit' => 'roll'];
        $page3Items[] = ['category' => 'ATK Tambahan #WAJIB', 'item_name' => 'Isolasi Hitam', 'item_detail' => 'Safety Kabel Listrik', 'quantity' => 2, 'unit' => 'roll'];
        $page3Items[] = ['category' => 'ATK Tambahan #WAJIB', 'item_name' => 'Isolasi Bening', 'item_detail' => 'Packing Box', 'quantity' => 1, 'unit' => 'roll'];
        $page3Items[] = ['category' => 'ATK Tambahan #WAJIB', 'item_name' => 'Spidol', 'item_detail' => 'Boardmarker & Snowman', 'quantity' => 2, 'unit' => 'pcs'];

        // 2. Ground Stake
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Tali Tampar', 'item_detail' => 'Tali Pengikat Belakang', 'quantity' => 2, 'unit' => 'roll'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Ganjel Triplek', 'item_detail' => 'Waterpass Leveler', 'quantity' => 10, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Besi Profil 2 Mtr', 'item_detail' => 'Tiang Tegak Rigging', 'quantity' => 4, 'unit' => 'batang'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Besi Profil 1Mtr', 'item_detail' => 'Tiang Sambung', 'quantity' => 4, 'unit' => 'batang'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Besi H 1,5', 'item_detail' => 'Base Footing', 'quantity' => 4, 'unit' => 'batang'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Besi H 1 Mtr', 'item_detail' => 'Base Footing Pendek', 'quantity' => 2, 'unit' => 'batang'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Besi H 2 Mtr', 'item_detail' => 'Base Footing Panjang', 'quantity' => 2, 'unit' => 'batang'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Cat Walk', 'item_detail' => 'Balkon Servis Belakang', 'quantity' => 2, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Pemberat Jirgen + Webbing Ungu', 'item_detail' => 'Ballast Water Jug', 'quantity' => 4, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Pemberat beton', 'item_detail' => 'Ballast Beton', 'quantity' => 4, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'Ground Stake', 'item_name' => 'Pemberat Barbel', 'item_detail' => '5KG / 10KG / 20KG', 'quantity' => 4, 'unit' => 'unit'];

        // 3. LED Gantung
        $brandCodeAny = self::obfuscateBrand($screens->pluck('brand_series')->first() ?: 'SAGA');
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => "Hanging Bar {$brandCodeAny} / LT / MV", 'item_detail' => 'Bar Gantung Atas Layar', 'quantity' => $hasHanging ? (int) ceil($screens->sum('width_m')) : 0, 'unit' => 'batang'];
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => 'Takel 10 mtr', 'item_detail' => '1 TON / 2 TON', 'quantity' => $hasHanging ? 2 : 0, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => 'Webing Hijau 3 Mtr', 'item_detail' => 'Sling Webbing', 'quantity' => $hasHanging ? 2 : 0, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => 'Webing Putih 5 Mtr', 'item_detail' => 'Sling Webbing', 'quantity' => $hasHanging ? 2 : 0, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => 'Webing Ungu 1 Mtr', 'item_detail' => 'Sling Webbing', 'quantity' => $hasHanging ? 2 : 0, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => 'Clam C', 'item_detail' => 'Klem Truss Rigging', 'quantity' => $hasHanging ? 4 : 0, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => 'Baut Hanging / Pendek', 'item_detail' => 'Baut Pengunci Rigging', 'quantity' => $hasHanging ? 8 : 0, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => 'Body Harnes', 'item_detail' => 'APD Ketinggian', 'quantity' => $hasHanging ? 2 : 0, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => 'Body Fest Safety', 'item_detail' => 'Rompi Keselamatan K3', 'quantity' => $hasHanging ? 2 : 0, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'LED Gantung', 'item_name' => 'Tangga Teleskopik #WAJIB', 'item_detail' => 'Tangga Lipat Aluminium', 'quantity' => 1, 'unit' => 'unit'];

        // 4. PERALATAN TIDUR
        $page3Items[] = ['category' => 'PERALATAN TIDUR', 'item_name' => 'Hammock', 'item_detail' => 'Istirahat Standby Crew', 'quantity' => 2, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'PERALATAN TIDUR', 'item_name' => 'Kasur Lipat', 'item_detail' => 'Istirahat Standby Crew', 'quantity' => 2, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'PERALATAN TIDUR', 'item_name' => 'Bantal', 'item_detail' => 'Istirahat Standby Crew', 'quantity' => 2, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'PERALATAN TIDUR', 'item_name' => 'Sleeping bag', 'item_detail' => 'Istirahat Standby Crew', 'quantity' => 2, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'PERALATAN TIDUR', 'item_name' => 'Ranjang tidur lipat', 'item_detail' => 'Velbed Portabel', 'quantity' => 1, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'PERALATAN TIDUR', 'item_name' => 'Matras', 'item_detail' => 'Alas Tidur', 'quantity' => 2, 'unit' => 'pcs'];

        // 5. Stage Level
        $page3Items[] = ['category' => 'Stage Level', 'item_name' => 'Ukuran Stage', 'item_detail' => ($order->stage_width_m ?? 10).'m × '.($order->stage_depth_m ?? 6).'m', 'quantity' => 1, 'unit' => 'set'];
        $page3Items[] = ['category' => 'Stage Level', 'item_name' => 'Triplek', 'item_detail' => 'Alas Panggung', 'quantity' => 4, 'unit' => 'lembar'];
        $page3Items[] = ['category' => 'Stage Level', 'item_name' => 'Kain', 'item_detail' => 'Skirting Hitam Penutup Panggung', 'quantity' => 1, 'unit' => 'set'];
        $page3Items[] = ['category' => 'Stage Level', 'item_name' => 'Alas Kaki Stage', 'item_detail' => 'Peredam & Pelindung Lantai', 'quantity' => 4, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'Stage Level', 'item_name' => 'Trek Belt', 'item_detail' => 'Pengikat Rigging Panggung', 'quantity' => 2, 'unit' => 'roll'];
        $page3Items[] = ['category' => 'Stage Level', 'item_name' => 'GALON AIR BESAR', 'item_detail' => '1 / 2 (Konsumsi & Ballast)', 'quantity' => 1, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'Stage Level', 'item_name' => 'Panggung rangka 50cm 2meter', 'item_detail' => 'Modul Panggung 50cm', 'quantity' => 4, 'unit' => 'modul'];
        $page3Items[] = ['category' => 'Stage Level', 'item_name' => 'Panggung rangka 40cm 2meter', 'item_detail' => 'Modul Panggung 40cm', 'quantity' => 4, 'unit' => 'modul'];

        // 6. LAPTOP
        $page3Items[] = [
            'category' => 'LAPTOP',
            'item_name' => $template === 'essential' ? 'Laptop Operator' : 'Laptop + Charger + Kabel Power',
            'item_detail' => 'No Laptop: LAPTOP-001 / Operator Show (Charger & Kabel Power)',
            'quantity' => 1,
            'unit' => 'set',
        ];

        // 7. KIT OPERATOR NO :
        $page3Items[] = ['category' => 'KIT OPERATOR NO :', 'item_name' => 'Mouse + Pad', 'item_detail' => 'Operator Kit', 'quantity' => 1, 'unit' => 'set'];
        $page3Items[] = ['category' => 'KIT OPERATOR NO :', 'item_name' => 'KABEL HDMI 4K 1 M 2 Biji', 'item_detail' => 'High Speed 4K', 'quantity' => 2, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'KIT OPERATOR NO :', 'item_name' => 'HDMI Spliter isi 4', 'item_detail' => 'Distributor Video', 'quantity' => 1, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'KIT OPERATOR NO :', 'item_name' => 'Capture Card 2 Unit', 'item_detail' => 'USB 3.0 Cam Feed', 'quantity' => 2, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'KIT OPERATOR NO :', 'item_name' => 'Test Pen + Volt Meter +Minyak wangi', 'item_detail' => 'Electrical Test & Hygiene', 'quantity' => 1, 'unit' => 'set'];
        $page3Items[] = ['category' => 'KIT OPERATOR NO :', 'item_name' => 'Ht 2 Unit + Charger + Pouch', 'item_detail' => 'Komunikasi Tim', 'quantity' => 2, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'KIT OPERATOR NO :', 'item_name' => 'Sound card + Kabel jack Audio + FD', 'item_detail' => 'Audio Emergency USB', 'quantity' => 1, 'unit' => 'set'];

        // 8. Tambahan Extra Perkabelan
        $page3Items[] = ['category' => 'Tambahan Extra Perkabelan', 'item_name' => 'Converter Legrand to Steker', 'item_detail' => 'Steker PLN Cadangan', 'quantity' => 2, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'Tambahan Extra Perkabelan', 'item_name' => 'Power Corn Jumper Panjang 2-6 mtr', 'item_detail' => 'Jumper Sudut / Ekstensi', 'quantity' => 4, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'Tambahan Extra Perkabelan', 'item_name' => 'Power Awal Extender (Legrand Female, Male)', 'item_detail' => 'Tarikan Jauh', 'quantity' => 2, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'Tambahan Extra Perkabelan', 'item_name' => 'Power con Extender Female To Female 25 Cm', 'item_detail' => 'Kopler PowerCon', 'quantity' => 2, 'unit' => 'pcs'];
        $page3Items[] = ['category' => 'Tambahan Extra Perkabelan', 'item_name' => 'terminal Sambung Listrik', 'item_detail' => 'Terminal Sambung Arus', 'quantity' => 1, 'unit' => 'unit'];
        $page3Items[] = ['category' => 'Tambahan Extra Perkabelan', 'item_name' => 'Terpal', 'item_detail' => 'Cover Hujan Layar & Panggung', 'quantity' => 2, 'unit' => 'lembar'];

        foreach ($page2Items as $item) {
            SpkChecklistItem::create([
                'spk_assignment_id' => $assignment->id,
                'page_number' => 2,
                'category' => $item['category'],
                'item_name' => $item['item_name'],
                'item_detail' => $item['item_detail'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'checked_out' => false,
                'checked_in' => false,
            ]);
        }

        foreach ($page3Items as $item) {
            SpkChecklistItem::create([
                'spk_assignment_id' => $assignment->id,
                'page_number' => 3,
                'category' => $item['category'],
                'item_name' => $item['item_name'],
                'item_detail' => $item['item_detail'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'checked_out' => false,
                'checked_in' => false,
            ]);
        }
    }
}
