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
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InventoryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions (Based on roles/permissions.json and role_revised.xlsx)
        $permissions = [
            // Dashboard
            'view dashboard',
            'view financial stats',
            // Orders
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'cancel orders',
            // SPK & Operations
            'view spk',
            'print spk',
            'manage spk crews',
            'manage spk vehicles',
            'sign spk operational',
            'approve spk qc',
            'view spk live',
            'upload spk qc',
            // Warehouse & Gate
            'manage spk departure',
            'manage spk return',
            'sign spk warehouse',
            'report spk discrepancy',
            // Service & Workshop
            'view service tickets',
            'create service ticket',
            'assign service ticket',
            'execute service ticket',
            'approve service ticket',
            'manage service spareparts',
            // Inventory & Assets
            'view inventory',
            'create product',
            'edit product',
            'delete product',
            'manage locations',
            'record movement',
            'conduct stock count',
            'verify stock count',
            'request stock adjustment',
            'approve stock adjustment',
            // Invoices & Billing
            'view invoices',
            'create invoice',
            'edit invoice',
            'delete invoice',
            'record invoice payment',
            'delete invoice payment',
            'print invoice',
            'print receipt',
            // Payroll & Fees
            'view payroll',
            'manage crew fees',
            'manage operational costs',
            'mark payroll paid',
            'print payroll slip',
            // Master Data
            'view customers',
            'manage customers',
            'view crews',
            'manage crews',
            'view vehicles',
            'manage vehicles',
            // System Administration
            'manage users',
            'view audit logs',
            'access superuser database',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 1.1 Superuser & Owner (Full Control)
        $superuserRole = Role::firstOrCreate(['name' => 'Superuser']);
        $superuserRole->syncPermissions(Permission::all());

        $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
        $ownerRole->syncPermissions(Permission::all());

        // 1.2 Manager Operasional
        $managerOpsRole = Role::firstOrCreate(['name' => 'Manager Operasional']);
        $managerOpsRole->syncPermissions([
            'view dashboard',
            'view orders',
            'create orders',
            'edit orders',
            'cancel orders',
            'view spk',
            'print spk',
            'manage spk crews',
            'manage spk vehicles',
            'sign spk operational',
            'approve spk qc',
            'view spk live',
            'upload spk qc',
            'create service ticket',
            'report spk discrepancy',
            'view inventory',
            'view customers',
            'manage customers',
            'view crews',
            'manage crews',
            'view vehicles',
            'manage vehicles',
            'view payroll',
            'manage operational costs',
        ]);

        // 1.3 Admin LED
        $adminRole = Role::firstOrCreate(['name' => 'Admin LED']);
        $adminRole->syncPermissions([
            'view dashboard',
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'cancel orders',
            'view spk',
            'print spk',
            'manage spk crews',
            'manage spk vehicles',
            'view spk live',
            'upload spk qc',
            'view customers',
            'manage customers',
            'view crews',
            'view vehicles',
            'view inventory',
            'print invoice',
        ]);

        // 1.4 Finance
        $financeRole = Role::firstOrCreate(['name' => 'Finance']);
        $financeRole->syncPermissions([
            'view dashboard',
            'view financial stats',
            'view orders',
            'view spk',
            'print spk',
            'view invoices',
            'create invoice',
            'edit invoice',
            'delete invoice',
            'record invoice payment',
            'delete invoice payment',
            'print invoice',
            'print receipt',
            'view payroll',
            'manage crew fees',
            'manage operational costs',
            'mark payroll paid',
            'print payroll slip',
            'view customers',
            'view crews',
            'manage crews',
        ]);

        // 1.5 Warehouse
        $warehouseRole = Role::firstOrCreate(['name' => 'Warehouse']);
        $warehouseRole->syncPermissions([
            'view dashboard',
            'view orders',
            'view spk',
            'print spk',
            'manage spk departure',
            'manage spk return',
            'sign spk warehouse',
            'report spk discrepancy',
            'view inventory',
            'create product',
            'edit product',
            'manage locations',
            'record movement',
            'conduct stock count',
            'verify stock count',
            'request stock adjustment',
            'create service ticket',
            'view vehicles',
        ]);

        // 1.6 Kepala Servis
        $kepalaServisRole = Role::firstOrCreate(['name' => 'Kepala Servis']);
        $kepalaServisRole->syncPermissions([
            'view dashboard',
            'view service tickets',
            'create service ticket',
            'assign service ticket',
            'execute service ticket',
            'approve service ticket',
            'manage service spareparts',
            'view inventory',
            'manage locations',
            'record movement',
            'request stock adjustment',
        ]);

        // 1.7 Teknisi Servis
        $teknisiRole = Role::firstOrCreate(['name' => 'Teknisi Servis']);
        $teknisiRole->syncPermissions([
            'view dashboard',
            'view service tickets',
            'execute service ticket',
            'manage service spareparts',
        ]);

        // 2. Default Seeded Users
        $superUser = User::firstOrCreate(
            ['email' => 'super@omahvisual.com'],
            ['name' => 'Super Administrator', 'password' => Hash::make('password123')]
        );
        $superUser->syncRoles([$superuserRole]);

        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@omahvisual.com'],
            ['name' => 'Owner Omahvisual', 'password' => Hash::make('password123')]
        );
        $ownerUser->syncRoles([$ownerRole]);

        $managerOpsUser = User::firstOrCreate(
            ['email' => 'manager.ops@omahvisual.com'],
            ['name' => 'Dani FT (Manager Operasional)', 'password' => Hash::make('password123'), 'phone' => '087775995930']
        );
        $managerOpsUser->syncRoles([$managerOpsRole]);

        // User Admin doubles as Finance for current business stage
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@omahvisual.com'],
            ['name' => 'Admin LED & Finance', 'password' => Hash::make('password123'), 'phone' => '085785193041']
        );
        $adminUser->syncRoles([$adminRole, $financeRole]);

        $financeUser = User::firstOrCreate(
            ['email' => 'finance@omahvisual.com'],
            ['name' => 'Staf Finance', 'password' => Hash::make('password123'), 'phone' => '081234567891']
        );
        $financeUser->syncRoles([$financeRole]);

        $warehouseUser = User::firstOrCreate(
            ['email' => 'warehouse@omahvisual.com'],
            ['name' => 'Byan (Tim Warehouse)', 'password' => Hash::make('password123'), 'phone' => '081234567892']
        );
        $warehouseUser->syncRoles([$warehouseRole]);

        $kepalaServisUser = User::firstOrCreate(
            ['email' => 'kepala.servis@omahvisual.com'],
            ['name' => 'Kepala Ruang Servis', 'password' => Hash::make('password123'), 'phone' => '081234567893']
        );
        $kepalaServisUser->syncRoles([$kepalaServisRole]);

        $teknisiUser = User::firstOrCreate(
            ['email' => 'teknisi@omahvisual.com'],
            ['name' => 'Teknisi Workshop Servis', 'password' => Hash::make('password123'), 'phone' => '081234567894']
        );
        $teknisiUser->syncRoles([$teknisiRole]);

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

        // A. Video Processors (Quantity tracking)
        $prodNovastar = Product::firstOrCreate(
            ['code' => 'SYS-NOV-VX-SERIES'],
            [
                'category_id' => $subSistem->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Video Processor Novastar (All Series)',
                'brand' => 'Novastar',
                'tracking_type' => 'quantity',
                'specification' => 'Processor Utama Videotron',
            ]
        );
        StockBalance::firstOrCreate(
            ['product_id' => $prodNovastar->id, 'location_id' => $locAlmari1->id, 'ownership' => 'company', 'condition' => 'available'],
            ['quantity' => 25.00]
        );

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

        // C. Televisi & Display (Quantity)
        $prodTv50 = Product::firstOrCreate(
            ['code' => 'TV-DISP-50-SAMS'],
            [
                'category_id' => $subTvLayar->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Smart TV 50 inch Samsung 4K',
                'brand' => 'Samsung',
                'tracking_type' => 'quantity',
                'specification' => '50 inch + Flightcase 125x14x76 cm',
            ]
        );
        StockBalance::firstOrCreate(
            ['product_id' => $prodTv50->id, 'location_id' => $locAlmari3->id, 'ownership' => 'company', 'condition' => 'available'],
            ['quantity' => 5.00]
        );

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

        // F. Toolboxes TB-01 s/d TB-10 (Quantity)
        $prodToolbox = Product::firstOrCreate(
            ['code' => 'TLS-BOX-STD'],
            [
                'category_id' => $subTools->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Toolbox Operasional Teknisi (Standar 13 Items)',
                'tracking_type' => 'quantity',
                'specification' => 'Isi Tang kombinasi, potong, obeng +/-, kunci L/Y, cutter, dll',
            ]
        );
        StockBalance::firstOrCreate(
            ['product_id' => $prodToolbox->id, 'location_id' => $locWorkshop->id, 'ownership' => 'company', 'condition' => 'available'],
            ['quantity' => 10.00]
        );

        // G. Flight Case Kabinet LED (Satu-satunya kategori ber-ID / Aset Serial Unik)
        $fc100 = Product::firstOrCreate(
            ['code' => 'FC-CAB-P39-100X50'],
            [
                'category_id' => $subCase->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Flight Case Kabinet LED 100x50 cm (Isi 6)',
                'brand' => 'Omah Visual Hardcase',
                'model_series' => 'FC-100',
                'specification' => 'Hardcase Roda 4, Kapasitas 6 Kabinet 100x50 cm',
                'tracking_type' => 'individual',
            ]
        );
        for ($i = 1; $i <= 12; $i++) {
            $numStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            Asset::firstOrCreate(
                ['asset_code' => "FC-100-{$numStr}"],
                [
                    'product_id' => $fc100->id,
                    'current_location_id' => $locStaging->id,
                    'serial_number' => "SN-FC100-{$numStr}",
                    'barcode' => "BC-FC100-{$numStr}",
                    'ownership' => 'company',
                    'condition' => 'available',
                    'status' => 'in_storage',
                    'notes' => "Flight Case Kabinet 100x50 cm Unit #{$i} (Kapasitas 6 Kabinet)",
                ]
            );
        }

        $fc50 = Product::firstOrCreate(
            ['code' => 'FC-CAB-P39-50X50'],
            [
                'category_id' => $subCase->id,
                'unit_id' => $unitMap['unit']->id,
                'name' => 'Flight Case Kabinet LED 50x50 cm (Isi 8)',
                'brand' => 'Omah Visual Hardcase',
                'model_series' => 'FC-50',
                'specification' => 'Hardcase Roda 4, Kapasitas 8 Kabinet 50x50 cm',
                'tracking_type' => 'individual',
            ]
        );
        for ($j = 1; $j <= 6; $j++) {
            $numStr = str_pad($j, 2, '0', STR_PAD_LEFT);
            Asset::firstOrCreate(
                ['asset_code' => "FC-050-{$numStr}"],
                [
                    'product_id' => $fc50->id,
                    'current_location_id' => $locStaging->id,
                    'serial_number' => "SN-FC050-{$numStr}",
                    'barcode' => "BC-FC050-{$numStr}",
                    'ownership' => 'company',
                    'condition' => 'available',
                    'status' => 'in_storage',
                    'notes' => "Flight Case Kabinet 50x50 cm Unit #{$j} (Kapasitas 8 Kabinet)",
                ]
            );
        }
    }
}
