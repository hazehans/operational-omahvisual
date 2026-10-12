<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InventoryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions
        $permissions = [
            'view inventory',
            'create product',
            'edit product',
            'delete product',
            'manage locations',
            'record movement',
            'request stock adjustment',
            'approve stock adjustment',
            'conduct stock count',
            'verify stock count',
            'view audit logs',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
        $ownerRole->syncPermissions(Permission::all());

        $branchManagerRole = Role::firstOrCreate(['name' => 'Branch Manager']);
        $branchManagerRole->syncPermissions([
            'view inventory', 'create product', 'edit product', 'manage locations',
            'record movement', 'request stock adjustment', 'approve stock adjustment',
            'conduct stock count', 'verify stock count', 'view audit logs'
        ]);

        $warehouseRole = Role::firstOrCreate(['name' => 'Warehouse']);
        $warehouseRole->syncPermissions([
            'view inventory', 'create product', 'edit product', 'manage locations',
            'record movement', 'request stock adjustment', 'conduct stock count',
            'verify stock count'
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'Admin LED']);
        $adminRole->syncPermissions(['view inventory', 'record movement']);

        $superuserRole = Role::firstOrCreate(['name' => 'Superuser']);
        $superuserRole->syncPermissions(Permission::all());

        // 2. Users
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@omahvisual.com'],
            ['name' => 'Owner / Branch Manager', 'password' => Hash::make('password123')]
        );
        $ownerUser->assignRole([$ownerRole, $branchManagerRole]);

        $warehouseUser = User::firstOrCreate(
            ['email' => 'warehouse@omahvisual.com'],
            ['name' => 'Tim Warehouse', 'password' => Hash::make('password123')]
        );
        $warehouseUser->assignRole($warehouseRole);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@omahvisual.com'],
            ['name' => 'Admin LED', 'password' => Hash::make('password123')]
        );
        $adminUser->assignRole($adminRole);

        $superUser = User::firstOrCreate(
            ['email' => 'super@omahvisual.com'],
            ['name' => 'Super Administrator', 'password' => Hash::make('password123')]
        );
        $superUser->assignRole($superuserRole);

        // 3. Units
        $unitsData = [
            ['name' => 'Unit', 'symbol' => 'unit'],
            ['name' => 'Pieces', 'symbol' => 'pcs'],
            ['name' => 'Roll', 'symbol' => 'roll'],
            ['name' => 'Set', 'symbol' => 'set'],
            ['name' => 'Batang', 'symbol' => 'btg'],
            ['name' => 'Meter Persegi', 'symbol' => 'm2'],
            ['name' => 'Pax', 'symbol' => 'pax'],
            ['name' => 'Box', 'symbol' => 'box'],
            ['name' => 'Meter', 'symbol' => 'm'],
        ];
        $unitMap = [];
        foreach ($unitsData as $u) {
            $unitMap[$u['symbol']] = Unit::firstOrCreate(['symbol' => $u['symbol']], $u);
        }

        // 4. Locations (Gedung Utama)
        $mainBuilding = Location::firstOrCreate(
            ['code' => 'GU-MAIN'],
            ['name' => 'Gedung Utama Omah Visual LED', 'type' => 'building', 'description' => 'Gedung pusat operasional']
        );

        $locAlmari1 = Location::firstOrCreate(
            ['code' => 'GU-ALM-1'],
            ['name' => 'Lemari 1 (Sparepart & Power)', 'parent_id' => $mainBuilding->id, 'type' => 'cabinet', 'description' => 'PSU, Powerbox, Modul, Receiving Card']
        );

        $locAlmari2 = Location::firstOrCreate(
            ['code' => 'GU-ALM-2'],
            ['name' => 'Lemari 2 (Display & Tools)', 'parent_id' => $mainBuilding->id, 'type' => 'cabinet', 'description' => 'Kabel display, konverter, capture card, spare tools']
        );

        $locAlmari3 = Location::firstOrCreate(
            ['code' => 'GU-ALM-3'],
            ['name' => 'Lemari 3 (Perangkat TV & Extender)', 'parent_id' => $mainBuilding->id, 'type' => 'cabinet', 'description' => 'STB, Remote TV, HDMI Extender TX/RX, Splitter']
        );

        $locStaging = Location::firstOrCreate(
            ['code' => 'GU-STG-LED'],
            ['name' => 'Area Staging & Flightcase LED', 'parent_id' => $mainBuilding->id, 'type' => 'staging_area', 'description' => 'Kabinet LED, Hanging Bar, Rigging Besi H']
        );

        $locWorkshop = Location::firstOrCreate(
            ['code' => 'GU-WS-TB'],
            ['name' => 'Area Workshop Toolbox', 'parent_id' => $mainBuilding->id, 'type' => 'locker', 'description' => 'Penyimpanan Toolbox 1 s/d 10 teknisi']
        );

        // 5. Categories
        $catVideotron = Category::firstOrCreate(['slug' => 'komponen-videotron'], ['name' => 'Komponen Videotron']);
        $catTv = Category::firstOrCreate(['slug' => 'televisi-display'], ['name' => 'Televisi & Display']);
        $catPendukung = Category::firstOrCreate(['slug' => 'alat-pendukung'], ['name' => 'Alat Pendukung']);
        $catApd = Category::firstOrCreate(['slug' => 'keselamatan-kerja'], ['name' => 'Keselamatan Kerja']);

        // Subcategories
        $subInti = Category::firstOrCreate(['slug' => 'komponen-inti'], ['name' => 'Komponen Inti', 'parent_id' => $catVideotron->id]);
        $subKabel = Category::firstOrCreate(['slug' => 'perkabelan'], ['name' => 'Perkabelan', 'parent_id' => $catVideotron->id]);
        $subKonstruksi = Category::firstOrCreate(['slug' => 'konstruksi'], ['name' => 'Konstruksi & Rigging', 'parent_id' => $catVideotron->id]);
        $subListrik = Category::firstOrCreate(['slug' => 'kelistrikan'], ['name' => 'Kelistrikan & Power', 'parent_id' => $catVideotron->id]);
        $subSistem = Category::firstOrCreate(['slug' => 'sistem-controller'], ['name' => 'Sistem & Controller', 'parent_id' => $catVideotron->id]);

        $subTvLayar = Category::firstOrCreate(['slug' => 'layar-tv'], ['name' => 'Layar Televisi', 'parent_id' => $catTv->id]);
        $subTvAlat = Category::firstOrCreate(['slug' => 'perangkat-pendukung-tv'], ['name' => 'Perangkat Pendukung TV', 'parent_id' => $catTv->id]);

        $subTools = Category::firstOrCreate(['slug' => 'peralatan-tools'], ['name' => 'Peralatan & Tools', 'parent_id' => $catPendukung->id]);
        $subCase = Category::firstOrCreate(['slug' => 'penyimpanan-hardcase'], ['name' => 'Penyimpanan & Hardcase', 'parent_id' => $catPendukung->id]);

        // 6. Products & Assets / Stock Balances

        // A. Video Processors (Individual tracking)
        $prodNovastar = Product::firstOrCreate(
            ['code' => 'SYS-NOV-VX-SERIES'],
            [
                'category_id' => $subSistem->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Video Processor Novastar (All Series)',
                'brand' => 'Novastar',
                'tracking_type' => 'individual',
                'specification' => 'Processor Utama Videotron',
            ]
        );

        $processorModels = [
            1 => 'VX20M', 2 => 'VX10', 3 => 'VX10', 4 => 'VX10', 5 => 'VX10', 6 => 'VX10',
            7 => 'VX8M', 8 => 'VX6 QL', 9 => 'VX 4E QL', 10 => 'VX 4E QL', 11 => 'VX 4E QL',
            12 => 'VX4M', 13 => 'VX4M', 14 => 'VX4M', 15 => 'VX4M', 16 => 'VX4M', 17 => 'VX4M',
            18 => 'VX4s', 19 => 'VX2S', 20 => 'VX2M', 21 => 'VX4M', 22 => 'VX2M', 23 => 'VX2M',
            24 => 'VX2M', 25 => 'VX2M'
        ];

        foreach ($processorModels as $no => $model) {
            $code = 'OVL-PROC-' . str_pad($no, 3, '0', STR_PAD_LEFT);
            Asset::firstOrCreate(
                ['asset_code' => $code],
                [
                    'product_id' => $prodNovastar->id,
                    'current_location_id' => $locAlmari1->id,
                    'serial_number' => 'SN-NOVA-' . $model . '-' . str_pad($no, 2, '0', STR_PAD_LEFT),
                    'ownership' => 'company',
                    'condition' => 'available',
                    'status' => 'in_storage',
                    'notes' => 'Nomor Unit ' . $no . ' (' . $model . ')',
                ]
            );
        }

        // B. LED Cabinets (with sqm calculation!)
        $cabP39Big = Product::firstOrCreate(
            ['code' => 'LED-CAB-P39-100X50'],
            [
                'category_id' => $subInti->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Cabinet LED P3.9 (100x50 cm)',
                'brand' => 'Qiangli',
                'model_series' => 'P3.9',
                'specification' => '100x50 cm (0.50 sqm/unit)',
                'tracking_type' => 'quantity',
                'sqm_per_unit' => 0.5000,
            ]
        );
        StockBalance::firstOrCreate(
            ['product_id' => $cabP39Big->id, 'location_id' => $locStaging->id, 'ownership' => 'company', 'condition' => 'available'],
            ['quantity' => 72.00] // 72 unit = 36 m²
        );

        $cabP39Small = Product::firstOrCreate(
            ['code' => 'LED-CAB-P39-50X50'],
            [
                'category_id' => $subInti->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Cabinet LED P3.9 (50x50 cm)',
                'brand' => 'Qiangli',
                'model_series' => 'P3.9',
                'specification' => '50x50 cm (0.25 sqm/unit)',
                'tracking_type' => 'quantity',
                'sqm_per_unit' => 0.2500,
            ]
        );
        StockBalance::firstOrCreate(
            ['product_id' => $cabP39Small->id, 'location_id' => $locStaging->id, 'ownership' => 'company', 'condition' => 'available'],
            ['quantity' => 36.00] // 36 unit = 9 m²
        );

        $cabP26 = Product::firstOrCreate(
            ['code' => 'LED-CAB-P26-100X50'],
            [
                'category_id' => $subInti->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Cabinet LED P2.6 Indoor Lampro',
                'brand' => 'Lampro',
                'model_series' => 'P2.6',
                'specification' => '100x50 cm (0.50 sqm/unit)',
                'tracking_type' => 'quantity',
                'sqm_per_unit' => 0.5000,
            ]
        );
        StockBalance::firstOrCreate(
            ['product_id' => $cabP26->id, 'location_id' => $locStaging->id, 'ownership' => 'company', 'condition' => 'available'],
            ['quantity' => 42.00] // 42 unit = 21 m²
        );

        // C. Televisi & Display (Individual)
        $prodTv50 = Product::firstOrCreate(
            ['code' => 'TV-DISP-50-SAMS'],
            [
                'category_id' => $subTvLayar->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Smart TV 50 inch Samsung 4K',
                'brand' => 'Samsung',
                'tracking_type' => 'individual',
                'specification' => '50 inch + Flightcase 125x14x76 cm',
            ]
        );
        for ($i = 1; $i <= 5; $i++) {
            Asset::firstOrCreate(
                ['asset_code' => 'OVL-TV50-' . str_pad($i, 2, '0', STR_PAD_LEFT)],
                [
                    'product_id' => $prodTv50->id,
                    'current_location_id' => $locAlmari3->id,
                    'serial_number' => 'SAMS-50-' . rand(100000, 999999),
                    'ownership' => 'company',
                    'condition' => 'available',
                    'status' => 'in_storage',
                    'notes' => 'Unit TV 50" #' . $i . ' Hardcase Standar',
                ]
            );
        }

        // D. Cables & Rigging (Quantity)
        $kabelLanJumper = Product::firstOrCreate(
            ['code' => 'CBL-LAN-JMP-15'],
            [
                'category_id' => $subKabel->id,
                'unit_id' => $unitMap['pcs']->id,
                'name' => 'Kabel LAN Jumper 1.5m',
                'tracking_type' => 'quantity',
            ]
        );
        StockBalance::firstOrCreate(
            ['product_id' => $kabelLanJumper->id, 'location_id' => $locStaging->id, 'ownership' => 'company', 'condition' => 'available'],
            ['quantity' => 130.00]
        );

        $kabelPowerJumper = Product::firstOrCreate(
            ['code' => 'CBL-PWR-JMP-SHT'],
            [
                'category_id' => $subKabel->id,
                'unit_id' => $unitMap['pcs']->id,
                'name' => 'Kabel Powercon Jumper Pendek',
                'tracking_type' => 'quantity',
            ]
        );
        StockBalance::firstOrCreate(
            ['product_id' => $kabelPowerJumper->id, 'location_id' => $locStaging->id, 'ownership' => 'company', 'condition' => 'available'],
            ['quantity' => 98.00]
        );

        // E. Sparepart dengan Kondisi Rusak & Bagus (Pemisahan Kondisi Nyata)
        $psuLite = Product::firstOrCreate(
            ['code' => 'SP-PSU-LITE'],
            [
                'category_id' => $subListrik->id,
                'unit_id' => $unitMap['pcs']->id,
                'name' => 'Power Supply Unit (PSU) Lite',
                'tracking_type' => 'quantity',
            ]
        );
        // Sesuai catatan di Excel Lemari 1: Total 3, Rusak 1, Bagus 2
        StockBalance::firstOrCreate(
            ['product_id' => $psuLite->id, 'location_id' => $locAlmari1->id, 'ownership' => 'company', 'condition' => 'available'],
            ['quantity' => 2.00]
        );
        StockBalance::firstOrCreate(
            ['product_id' => $psuLite->id, 'location_id' => $locAlmari1->id, 'ownership' => 'company', 'condition' => 'damaged'],
            ['quantity' => 1.00]
        );

        // F. Toolboxes TB-01 s/d TB-10 (Assets)
        $prodToolbox = Product::firstOrCreate(
            ['code' => 'TLS-BOX-STD'],
            [
                'category_id' => $subTools->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Toolbox Operasional Teknisi (Standar 13 Items)',
                'tracking_type' => 'individual',
                'specification' => 'Isi Tang kombinasi, potong, obeng +/-, kunci L/Y, cutter, dll',
            ]
        );
        for ($t = 1; $t <= 10; $t++) {
            Asset::firstOrCreate(
                ['asset_code' => 'TB-' . str_pad($t, 2, '0', STR_PAD_LEFT)],
                [
                    'product_id' => $prodToolbox->id,
                    'current_location_id' => $locWorkshop->id,
                    'ownership' => 'company',
                    'condition' => 'available',
                    'status' => 'in_storage',
                    'notes' => 'Toolbox Unit ' . $t . ' Standar Kerja Lapangan',
                ]
            );
        }
    }
}

