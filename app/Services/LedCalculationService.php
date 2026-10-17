<?php

namespace App\Services;

use App\Models\EventOrder;
use App\Models\SpkAssignment;
use App\Models\SpkChecklistItem;

class LedCalculationService
{
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
     * Generate standard SPK Page 2 & Page 3 checklists for an assignment.
     */
    public function generateStandardChecklist(SpkAssignment $assignment, EventOrder $order, string $template = 'essential'): void
    {
        $screens = $order->screens;
        $totalSqm = $screens->sum('total_sqm');
        $total1m = $screens->sum('cabinets_1m');
        $total50cm = $screens->sum('cabinets_50cm');
        $totalCabinets = $screens->sum('total_cabinets');
        $totalHardcases = $screens->sum('total_hardcases');
        $brandSeries = $screens->pluck('brand_series')->unique()->implode(' / ') ?: 'SAGA';
        $processorModel = $screens->pluck('processor_model')->unique()->implode(' / ') ?: 'Novastar VX600';
        $hasHanging = $screens->where('rigging_method', 'hanging')->isNotEmpty();

        // Clear existing checklist items for this assignment
        $assignment->checklistItems()->delete();

        if ($template === 'empty') {
            return;
        }

        if ($template === 'essential') {
            $page2Items = [];
            // Kabinet 1m
            if ($total1m > 0) {
                $hardcases1m = (int) ceil($total1m / 6);
                $page2Items[] = [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => "Kabinet LED 1 Mtr {$brandSeries}",
                    'item_detail' => "TOTAL {$totalSqm} SQM | {$totalHardcases} BOX",
                    'quantity' => $total1m,
                    'unit' => 'pcs',
                ];
                $page2Items[] = [
                    'category' => 'FLIGHT CASE & PACKING',
                    'item_name' => 'Flight Case Kabinet LED 100x50 cm (Isi 6)',
                    'item_detail' => "Muatan {$total1m} Kabinet 1 Mtr ({$hardcases1m} Box)",
                    'quantity' => $hardcases1m,
                    'unit' => 'unit',
                ];
            }
            // Kabinet 50cm
            if ($total50cm > 0) {
                $hardcases50cm = (int) ceil($total50cm / 8);
                $page2Items[] = [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => "Kabinet LED 50cm {$brandSeries}",
                    'item_detail' => 'Kombinasi Baris 50cm',
                    'quantity' => $total50cm,
                    'unit' => 'pcs',
                ];
                $page2Items[] = [
                    'category' => 'FLIGHT CASE & PACKING',
                    'item_name' => 'Flight Case Kabinet LED 50x50 cm (Isi 8)',
                    'item_detail' => "Muatan {$total50cm} Kabinet 50cm ({$hardcases50cm} Box)",
                    'quantity' => $hardcases50cm,
                    'unit' => 'unit',
                ];
            }
            // Jumpers & drops
            if ($totalCabinets > 0) {
                $page2Items[] = [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Power Corn Jumper 1 mtr + 1,5 mtr',
                    'item_detail' => 'Jumper Antar Kabinet',
                    'quantity' => $totalCabinets,
                    'unit' => 'pcs',
                ];
            }
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Power Corn Jumper Panjang 2-6 mtr',
                'item_detail' => 'Jumper Sudut / Baris',
                'quantity' => 6,
                'unit' => 'pcs',
            ];
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Power Awal (Legrand To Corn)',
                'item_detail' => 'Distribusi Power Input',
                'quantity' => max(2, (int) ceil($totalCabinets / 8)),
                'unit' => 'pcs',
            ];
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Power Awal Extender (Legrand Female, Male)',
                'item_detail' => 'Kabel Tarikan Panjang',
                'quantity' => 2,
                'unit' => 'pcs',
            ];
            if ($totalCabinets > 0) {
                $page2Items[] = [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Lan Jumper',
                    'item_detail' => 'Data Interconnect Antar Kabinet',
                    'quantity' => $totalCabinets,
                    'unit' => 'pcs',
                ];
            }
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Lan Awal panjang',
                'item_detail' => 'Main Data Send Port',
                'quantity' => 2,
                'unit' => 'pcs',
            ];
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Lan Awal pendek',
                'item_detail' => 'Port Cadangan',
                'quantity' => 2,
                'unit' => 'pcs',
            ];
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Converter Legrand to Steker',
                'item_detail' => 'Adaptor PLN',
                'quantity' => 2,
                'unit' => 'pcs',
            ];
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Besi Ground dan Kabel Grounding 50 Mtr',
                'item_detail' => 'Safety Grounding',
                'quantity' => 1,
                'unit' => 'roll',
            ];
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Panel Listrik 16A',
                'item_detail' => ($totalSqm > 30) ? '18 MCB' : '12 MCB',
                'quantity' => 1,
                'unit' => 'unit',
            ];
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Kabel Terminal FOH 25 / 50 / 100 mtr',
                'item_detail' => 'FOH Main Control Cable',
                'quantity' => 1,
                'unit' => 'roll',
            ];
            $page2Items[] = [
                'category' => 'KABINET & KELISTRIKAN',
                'item_name' => 'Kabel babok 20-50 Mtr',
                'item_detail' => 'Power Main Feeder',
                'quantity' => 1,
                'unit' => 'roll',
            ];
            if ($totalCabinets > 0) {
                $page2Items[] = [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Kupu2 (Kunci Sambung Panel)',
                    'item_detail' => 'Pengunci Antar Box',
                    'quantity' => $totalCabinets * 2,
                    'unit' => 'pcs',
                ];
            }
            // Ringkas 1 Toolbox standar
            $page2Items[] = [
                'category' => 'TOOLS BOX',
                'item_name' => 'Toolbox Operasional Teknisi (Standar 13 Items)',
                'item_detail' => 'Tang, Obeng, Crimping, Tester LAN, Kunci L/Y, Cutter, dll.',
                'quantity' => 1,
                'unit' => 'box',
            ];

            if ($hasHanging) {
                $page2Items[] = [
                    'category' => 'LED GANTUNG',
                    'item_name' => "Hanging Bar {$brandSeries}",
                    'item_detail' => 'Bar Gantung Atas Layar',
                    'quantity' => (int) ceil($screens->sum('width_m') / 1.0),
                    'unit' => 'batang',
                ];
                $page2Items[] = [
                    'category' => 'LED GANTUNG',
                    'item_name' => 'Takel 10 Mtr (1 TON / 2 TON)',
                    'item_detail' => 'Manual Chain Hoist',
                    'quantity' => 2,
                    'unit' => 'unit',
                ];
            }

            $page3Items = [
                ['category' => 'KIT OPERATOR', 'item_name' => 'Laptop Operator', 'item_detail' => 'Laptop Utama Show', 'quantity' => 1, 'unit' => 'unit'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'Charger + Kabel Power Laptop', 'item_detail' => 'Original Adapter', 'quantity' => 1, 'unit' => 'set'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'Mouse + Mousepad', 'item_detail' => 'Operator Control', 'quantity' => 1, 'unit' => 'set'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'KABEL HDMI 4K 1M (2 Biji)', 'item_detail' => 'High Speed 4K Cable', 'quantity' => 2, 'unit' => 'pcs'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'HDMI Splitter 4 Port', 'item_detail' => 'Distributor Sinyal Video', 'quantity' => 1, 'unit' => 'unit'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'HT 2 Unit + Charger + Pouch', 'item_detail' => 'Komunikasi PIC & Operator', 'quantity' => 2, 'unit' => 'unit'],
                ['category' => 'PROSESOR', 'item_name' => "Prosesor {$processorModel}", 'item_detail' => 'Main Display Controller', 'quantity' => 1, 'unit' => 'unit'],
                ['category' => 'PROSESOR', 'item_name' => 'Kabel AC Power Prosesor', 'item_detail' => 'Kabel Daya Utama', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'PROSESOR', 'item_name' => 'Kabel Setting (USB Type B to A)', 'item_detail' => 'NovaLCT Software Config', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'PROSESOR', 'item_name' => 'Kabel HDMI to HDMI 4K', 'item_detail' => 'Input Display', 'quantity' => 2, 'unit' => 'pcs'],
                [
                    'category' => 'SPARE PART LED',
                    'item_name' => "Modul LED Spare {$brandSeries}",
                    'item_detail' => 'Modul Cadangan (Batch & Kalibrasi Cocok)',
                    'quantity' => max(2, (int) ceil($totalSqm * 0.15)),
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'SPARE PART LED',
                    'item_name' => 'Power Supply Unit (PSU 5V 40A/60A)',
                    'item_detail' => 'Power Supply Cadangan',
                    'quantity' => max(1, (int) ceil($totalSqm * 0.05)),
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'SPARE PART LED',
                    'item_name' => "Tool Service Kit {$brandSeries} Lengkap",
                    'item_detail' => 'Masker, Obeng Khusus, Magnet Vakum',
                    'quantity' => 1,
                    'unit' => 'set',
                ],
                [
                    'category' => 'ATK TAMBAHAN',
                    'item_name' => 'Wrapping Plastik Roll & Isolasi',
                    'item_detail' => 'Marking & Packing Pelindung',
                    'quantity' => 1,
                    'unit' => 'set',
                ],
            ];
        } else {
            // Full template with all 50+ individual items
            $page2Items = [
                // Category: KABINET & KELISTRIKAN
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => "Kabinet LED 1 Mtr {$brandSeries}",
                    'item_detail' => "TOTAL {$totalSqm} SQM | {$totalHardcases} BOX",
                    'quantity' => $total1m,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'FLIGHT CASE & PACKING',
                    'item_name' => 'Flight Case Kabinet LED 100x50 cm (Isi 6)',
                    'item_detail' => "Muatan {$total1m} Kabinet 1 Mtr (".ceil($total1m / 6).' Box)',
                    'quantity' => (int) ceil($total1m / 6),
                    'unit' => 'unit',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => "Kabinet LED 50cm {$brandSeries}",
                    'item_detail' => 'Kombinasi Baris 50cm',
                    'quantity' => $total50cm,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'FLIGHT CASE & PACKING',
                    'item_name' => 'Flight Case Kabinet LED 50x50 cm (Isi 8)',
                    'item_detail' => "Muatan {$total50cm} Kabinet 50cm (".ceil($total50cm / 8).' Box)',
                    'quantity' => (int) ceil($total50cm / 8),
                    'unit' => 'unit',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Power Corn Jumper 1 mtr + 1,5 mtr',
                    'item_detail' => 'Jumper Antar Kabinet',
                    'quantity' => $totalCabinets,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Power Corn Jumper Panjang 2-6 mtr',
                    'item_detail' => 'Jumper Sudut / Baris',
                    'quantity' => 6,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Power Awal (Legrand To Corn)',
                    'item_detail' => 'Distribusi Power Input',
                    'quantity' => max(2, (int) ceil($totalCabinets / 8)),
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Power Awal Extender (Legrand Female, Male)',
                    'item_detail' => 'Kabel Tarikan Panjang',
                    'quantity' => 2,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Lan Jumper',
                    'item_detail' => 'Data Interconnect Antar Kabinet',
                    'quantity' => $totalCabinets,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Lan Awal panjang',
                    'item_detail' => 'Main Data Send Port',
                    'quantity' => 2,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Lan Awal pendek',
                    'item_detail' => 'Port Cadangan',
                    'quantity' => 2,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Converter Legrand to Steker',
                    'item_detail' => 'Adaptor PLN',
                    'quantity' => 2,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Besi Ground dan Kabel Grounding 50 Mtr',
                    'item_detail' => 'Safety Grounding',
                    'quantity' => 1,
                    'unit' => 'roll',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Konektor Kabel panel + terminal Sambung',
                    'item_detail' => 'Terminal Distribusi',
                    'quantity' => 1,
                    'unit' => 'set',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Panel Listrik 16A',
                    'item_detail' => ($totalSqm > 30) ? '18 MCB' : '12 MCB',
                    'quantity' => 1,
                    'unit' => 'unit',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Kabel Terminal FOH 25 / 50 / 100 mtr',
                    'item_detail' => 'FOH Main Control Cable',
                    'quantity' => 1,
                    'unit' => 'roll',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Kabel babok 20-50 Mtr',
                    'item_detail' => 'Power Main Feeder',
                    'quantity' => 1,
                    'unit' => 'roll',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Kupu2 (Kunci Sambung Panel)',
                    'item_detail' => 'Pengunci Antar Box',
                    'quantity' => $totalCabinets * 2,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Baut panjang',
                    'item_detail' => 'Rigging Lock Bolt',
                    'quantity' => 20,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Baut Pendek',
                    'item_detail' => 'Bracket Lock Bolt',
                    'quantity' => 20,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Kabel FO (Fiber Optik)',
                    'item_detail' => ($totalSqm > 40) ? '4 Core (Wajib Tambahan)' : '2 Core Standby',
                    'quantity' => 1,
                    'unit' => 'roll',
                ],
                [
                    'category' => 'KABINET & KELISTRIKAN',
                    'item_name' => 'Terpal Pelindung Layar',
                    'item_detail' => 'Anti Hujan / Debu',
                    'quantity' => 2,
                    'unit' => 'lembar',
                ],

                // Category: TOOLS BOX (13 Items Standar)
                ['category' => 'TOOLS BOX', 'item_name' => 'Tang Kombinasi', 'item_detail' => 'Toolbox Utama', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Tang Potong', 'item_detail' => 'Toolbox Utama', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Obeng + -', 'item_detail' => 'Toolbox Utama', 'quantity' => 1, 'unit' => 'set'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Tang Amper', 'item_detail' => 'Digital Clamp Meter', 'quantity' => 1, 'unit' => 'unit'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Crimping Rj45', 'item_detail' => 'Toolbox Utama', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Rj 45 Konektor', 'item_detail' => 'Spare Konektor LAN', 'quantity' => 10, 'unit' => 'pcs'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Lan Chekker + Baterei', 'item_detail' => 'Tester Kabel Data', 'quantity' => 1, 'unit' => 'set'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Kunci Y', 'item_detail' => 'Kunci Pas Y', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Tang Kupas', 'item_detail' => 'Wire Stripper', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Cutter', 'item_detail' => 'Pisau Cutter', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Kunci L', 'item_detail' => 'Hex Key Set', 'quantity' => 1, 'unit' => 'set'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Linggis 35cm / Orange', 'item_detail' => 'Pengungkit Level Panggung', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Terminal Sambung 200A', 'item_detail' => 'Konektor Genset', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Sarung Tangan 3 pasang', 'item_detail' => 'Safety Gloves', 'quantity' => 3, 'unit' => 'pasang'],
                ['category' => 'TOOLS BOX', 'item_name' => 'Headlamp', 'item_detail' => 'Lampu Kepala Loading Malam', 'quantity' => 2, 'unit' => 'pcs'],

                // Category: Ground Stake Rigging
                ['category' => 'GROUND STAKE', 'item_name' => 'Besi Profil 2 Mtr', 'item_detail' => 'Tiang Tegak Rigging', 'quantity' => 4, 'unit' => 'batang'],
                ['category' => 'GROUND STAKE', 'item_name' => 'Besi Profil 1 Mtr', 'item_detail' => 'Tiang Sambung', 'quantity' => 4, 'unit' => 'batang'],
                ['category' => 'GROUND STAKE', 'item_name' => 'Ganjel Triplek', 'item_detail' => 'Waterpass Leveler', 'quantity' => 10, 'unit' => 'pcs'],
                ['category' => 'GROUND STAKE', 'item_name' => 'Besi H 1,5 Mtr', 'item_detail' => 'Base Footing', 'quantity' => 4, 'unit' => 'batang'],
                ['category' => 'GROUND STAKE', 'item_name' => 'Besi H 2 Mtr', 'item_detail' => 'Base Footing Panjang', 'quantity' => 2, 'unit' => 'batang'],
                ['category' => 'GROUND STAKE', 'item_name' => 'Besi H 1 Mtr', 'item_detail' => 'Base Footing Pendek', 'quantity' => 2, 'unit' => 'batang'],
                ['category' => 'GROUND STAKE', 'item_name' => 'Cat Walk', 'item_detail' => 'Balkon Servis Belakang', 'quantity' => 2, 'unit' => 'unit'],
                ['category' => 'GROUND STAKE', 'item_name' => 'Pemberat Jirgen + Webbing Ungu', 'item_detail' => 'Ballast Water Jug', 'quantity' => 4, 'unit' => 'unit'],
                ['category' => 'GROUND STAKE', 'item_name' => 'Pemberat Beton / Barbel', 'item_detail' => '10KG / 20KG', 'quantity' => 4, 'unit' => 'unit'],
                ['category' => 'GROUND STAKE', 'item_name' => 'Tali Tampar', 'item_detail' => 'Tali Pengikat Belakang', 'quantity' => 2, 'unit' => 'roll'],

                // Category: Stage Level
                ['category' => 'STAGE LEVEL', 'item_name' => 'Panggung Rangka 40cm / 50cm', 'item_detail' => 'Level Panggung Bawah Layar', 'quantity' => 4, 'unit' => 'modul'],
                ['category' => 'STAGE LEVEL', 'item_name' => 'Triplek Alas Stage', 'item_detail' => 'Alas Panggung', 'quantity' => 4, 'unit' => 'lembar'],
                ['category' => 'STAGE LEVEL', 'item_name' => 'Kain Hitam Penutup Stage', 'item_detail' => 'Skirting Stage', 'quantity' => 1, 'unit' => 'set'],
                ['category' => 'STAGE LEVEL', 'item_name' => 'Galon Air Besar', 'item_detail' => 'Pemberat Tambahan', 'quantity' => 2, 'unit' => 'unit'],
            ];

            $page3Items = [
                // Category: KIT OPERATOR
                ['category' => 'KIT OPERATOR', 'item_name' => 'Laptop Operator', 'item_detail' => 'Laptop Utama Show', 'quantity' => 1, 'unit' => 'unit'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'Charger + Kabel Power Laptop', 'item_detail' => 'Original Adapter', 'quantity' => 1, 'unit' => 'set'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'Mouse + Mousepad', 'item_detail' => 'Operator Control', 'quantity' => 1, 'unit' => 'set'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'KABEL HDMI 4K 1M (2 Biji)', 'item_detail' => 'High Speed 4K Cable', 'quantity' => 2, 'unit' => 'pcs'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'HDMI Splitter 4 Port', 'item_detail' => 'Distributor Sinyal Video', 'quantity' => 1, 'unit' => 'unit'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'Capture Card (2 Unit)', 'item_detail' => 'Live Cam & Input Feed', 'quantity' => 2, 'unit' => 'unit'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'Test Pen + Volt Meter + Minyak Wangi', 'item_detail' => 'Safety Check & Comfort', 'quantity' => 1, 'unit' => 'set'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'HT 2 Unit + Charger + Pouch', 'item_detail' => 'Komunikasi PIC & Operator', 'quantity' => 2, 'unit' => 'unit'],
                ['category' => 'KIT OPERATOR', 'item_name' => 'Soundcard + Kabel Audio Jack + Flashdisk', 'item_detail' => 'Audio Feed & Emergency Media', 'quantity' => 1, 'unit' => 'set'],

                // Category: PROSESOR
                ['category' => 'PROSESOR', 'item_name' => "Prosesor {$processorModel}", 'item_detail' => 'Main Display Controller', 'quantity' => 1, 'unit' => 'unit'],
                ['category' => 'PROSESOR', 'item_name' => 'Kabel AC Power Prosesor', 'item_detail' => 'Kabel Daya Utama', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'PROSESOR', 'item_name' => 'Kabel Setting (USB Type B to A)', 'item_detail' => 'NovaLCT Software Config', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'PROSESOR', 'item_name' => 'Kabel HDMI to HDMI 4K', 'item_detail' => 'Input Display', 'quantity' => 2, 'unit' => 'pcs'],
                ['category' => 'PROSESOR', 'item_name' => 'Kabel DVI to HDMI / DP to HDMI', 'item_detail' => 'Video Input Cadangan', 'quantity' => 1, 'unit' => 'pcs'],
                ['category' => 'PROSESOR', 'item_name' => 'Module SFP Fiber Optic', 'item_detail' => 'Optical Transceiver', 'quantity' => ($totalSqm > 40) ? 2 : 1, 'unit' => 'pcs'],

                // Category: ATK TAMBAHAN
                ['category' => 'ATK TAMBAHAN', 'item_name' => 'Wrapping Plastik Roll', 'item_detail' => 'Pelindung Layar Saat Kirim', 'quantity' => 1, 'unit' => 'roll'],
                ['category' => 'ATK TAMBAHAN', 'item_name' => 'Isolasi Kertas Besar & Kecil', 'item_detail' => 'Marking Panggung & Labeling', 'quantity' => 2, 'unit' => 'roll'],
                ['category' => 'ATK TAMBAHAN', 'item_name' => 'Isolasi Hitam Listrik', 'item_detail' => 'Safety Kabel Audio/Video', 'quantity' => 2, 'unit' => 'roll'],
                ['category' => 'ATK TAMBAHAN', 'item_name' => 'Isolasi Bening', 'item_detail' => 'Packing', 'quantity' => 1, 'unit' => 'roll'],
                ['category' => 'ATK TAMBAHAN', 'item_name' => 'Spidol Boardmarker & Snowman', 'item_detail' => 'Label Box & Checklist', 'quantity' => 2, 'unit' => 'pcs'],

                // Category: SPARE PART LED
                [
                    'category' => 'SPARE PART LED',
                    'item_name' => "Modul LED Spare {$brandSeries}",
                    'item_detail' => 'Modul Cadangan (Batch & Kalibrasi Cocok)',
                    'quantity' => max(2, (int) ceil($totalSqm * 0.15)),
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'SPARE PART LED',
                    'item_name' => 'Receiving Card (K8 / K5 / E80)',
                    'item_detail' => 'Receiving Card Cadangan Terprogram',
                    'quantity' => max(1, (int) ceil($totalSqm * 0.05)),
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'SPARE PART LED',
                    'item_name' => 'Power Box / Batok Belakang',
                    'item_detail' => 'Cover Power Cadangan',
                    'quantity' => 1,
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'SPARE PART LED',
                    'item_name' => 'Power Supply Unit (PSU 5V 40A/60A)',
                    'item_detail' => 'Power Supply Cadangan',
                    'quantity' => max(1, (int) ceil($totalSqm * 0.05)),
                    'unit' => 'pcs',
                ],
                [
                    'category' => 'SPARE PART LED',
                    'item_name' => "Tool Service Kit {$brandSeries} Lengkap",
                    'item_detail' => 'Masker, Obeng Khusus, Magnet Vakum',
                    'quantity' => 1,
                    'unit' => 'set',
                ],

                // Category: LED GANTUNG (Hanging Rigging)
                ['category' => 'LED GANTUNG', 'item_name' => "Hanging Bar {$brandSeries}", 'item_detail' => 'Bar Gantung Atas Layar', 'quantity' => $hasHanging ? (int) ceil($screens->sum('width_m') / 1.0) : 0, 'unit' => 'batang'],
                ['category' => 'LED GANTUNG', 'item_name' => 'Takel 10 Mtr (1 TON / 2 TON)', 'item_detail' => 'Manual Chain Hoist', 'quantity' => $hasHanging ? 2 : 0, 'unit' => 'unit'],
                ['category' => 'LED GANTUNG', 'item_name' => 'Webbing Hijau 3 Mtr / Putih 5 Mtr', 'item_detail' => 'Sling Webbing Pengikat Truss', 'quantity' => $hasHanging ? 4 : 0, 'unit' => 'pcs'],
                ['category' => 'LED GANTUNG', 'item_name' => 'Clamp C & Baut Hanging', 'item_detail' => 'Klem Truss Rigging', 'quantity' => $hasHanging ? 8 : 0, 'unit' => 'pcs'],
                ['category' => 'LED GANTUNG', 'item_name' => 'Body Harness & Safety Vest', 'item_detail' => 'APD Bekerja di Ketinggian', 'quantity' => $hasHanging ? 2 : 0, 'unit' => 'set'],
                ['category' => 'LED GANTUNG', 'item_name' => 'Tangga Teleskopik', 'item_detail' => 'Tangga Lipat Aluminium', 'quantity' => 1, 'unit' => 'unit'],

                // Category: PERALATAN TIDUR CREW
                ['category' => 'PERALATAN TIDUR', 'item_name' => 'Hammock', 'item_detail' => 'Istirahat Standby Event', 'quantity' => 2, 'unit' => 'pcs'],
                ['category' => 'PERALATAN TIDUR', 'item_name' => 'Kasur Lipat / Matras', 'item_detail' => 'Istirahat Standby Event', 'quantity' => 2, 'unit' => 'pcs'],
                ['category' => 'PERALATAN TIDUR', 'item_name' => 'Bantal & Sleeping Bag', 'item_detail' => 'Istirahat Standby Event', 'quantity' => 2, 'unit' => 'set'],
                ['category' => 'PERALATAN TIDUR', 'item_name' => 'Ranjang Tidur Lipat', 'item_detail' => 'Velbed Portabel', 'quantity' => 1, 'unit' => 'unit'],
            ];
        }

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
