<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\Unit;
use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncGAInventoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:sync-ga {--export : Re-run python exporter first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync inventory, vehicles, processors, and assets from GA Excel dataset';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Memulai sinkronisasi master barang dan aset dari data General Affair (GA)...');

        $datasetPath = base_path('database/data/ga_inventory_dataset.json');

        if ($this->option('export') || ! file_exists($datasetPath)) {
            $this->info('Menjalankan script ekstraksi data GA python...');
            exec('python database/data/export_ga_inventory.py');
        }

        if (! file_exists($datasetPath)) {
            $this->error('File database/data/ga_inventory_dataset.json tidak ditemukan!');

            return self::FAILURE;
        }

        $data = json_decode(file_get_contents($datasetPath), true);

        DB::beginTransaction();
        try {
            // 1. Pastikan Kategori & Unit Tersedia
            $catOffice = Category::firstOrCreate(
                ['slug' => 'aset-kantor-mess'],
                ['name' => 'Aset Kantor & Mess', 'description' => 'Aset internal kantor, workshop, dan mess staf']
            );

            $unitPcs = Unit::firstOrCreate(['symbol' => 'pcs'], ['name' => 'Pieces']);
            $unitUnit = Unit::firstOrCreate(['symbol' => 'unit'], ['name' => 'Unit']);
            $unitCabinet = Unit::firstOrCreate(['symbol' => 'cab'], ['name' => 'Cabinet']);
            $unitSet = Unit::firstOrCreate(['symbol' => 'set'], ['name' => 'Set']);
            $unitMeter = Unit::firstOrCreate(['symbol' => 'm'], ['name' => 'Meter']);

            // 2. Pastikan Lokasi Tersedia
            $locMain = Location::firstOrCreate(
                ['code' => 'GU-MAIN'],
                ['name' => 'Gedung Utama Omah Visual LED', 'type' => 'building', 'is_active' => true]
            );

            $locStaging = Location::firstOrCreate(
                ['code' => 'GU-STG-LED'],
                ['name' => 'Area Staging & Flightcase LED', 'type' => 'staging_area', 'is_active' => true]
            );

            $locOffice = Location::firstOrCreate(
                ['code' => 'GU-OFFICE'],
                ['name' => 'Ruang Kantor & Administrasi', 'type' => 'storage_room', 'is_active' => true]
            );

            // 3. Sinkronisasi Armada Kendaraan (8 Unit GA)
            $this->info('Mengimpor data armada kendaraan GA...');
            foreach ($data['vehicles'] as $v) {
                Vehicle::updateOrCreate(
                    ['plate_number' => $v['plate_number']],
                    [
                        'vehicle_code' => $v['vehicle_code'],
                        'name' => $v['name'],
                        'type' => $v['type'],
                        'status' => 'tersedia',
                        'special_condition' => 'Normal (Siap Jalan)',
                        'has_etoll' => true,
                        'has_stnk' => true,
                        'has_kir' => true,
                        'is_active' => true,
                    ]
                );
            }
            $this->info('✓ Sukses sinkronisasi '.count($data['vehicles']).' unit Armada Kendaraan!');

            // 4. Sinkronisasi Video Processors (Individual Assets)
            $this->info('Mengimpor Video Processors NovaStar...');
            $catProc = Category::where('slug', 'sistem-controller')->first()
                ?? Category::firstOrCreate(['slug' => 'sistem-controller'], ['name' => 'Sistem & Controller']);

            foreach ($data['processors'] as $p) {
                $procProduct = Product::firstOrCreate(
                    ['code' => $p['code']],
                    [
                        'name' => $p['name'],
                        'category_id' => $catProc->id,
                        'unit_id' => $unitUnit->id,
                        'brand' => $p['brand'],
                        'model_series' => $p['model'],
                        'tracking_type' => 'individual',
                        'is_rental_item' => true,
                        'is_active' => true,
                        'notes' => 'Master Video Processor dari SPK Mba De',
                    ]
                );

                // Buat 2 unit fisik per model processor
                for ($i = 1; $i <= 2; $i++) {
                    $assetCode = sprintf('AST-%s-%02d', $p['code'], $i);
                    Asset::updateOrCreate(
                        ['asset_code' => $assetCode],
                        [
                            'product_id' => $procProduct->id,
                            'current_location_id' => $locMain->id,
                            'serial_number' => sprintf('%s-SN%04d', $p['model'], $i * 111),
                            'ownership' => 'company',
                            'condition' => 'available',
                            'status' => 'in_storage',
                            'notes' => 'Unit siap pakai operasional',
                        ]
                    );
                }
            }
            $this->info('✓ Sukses sinkronisasi Video Processors & Asset Units!');

            // 5. Sinkronisasi Tool Box & Laptops (Individual Assets)
            $this->info('Mengimpor Tool Box Lapangan & Laptop Operasional...');
            $catTools = Category::where('slug', 'peralatan-tools')->first()
                ?? Category::firstOrCreate(['slug' => 'peralatan-tools'], ['name' => 'Peralatan & Tools']);

            $tbProduct = Product::firstOrCreate(
                ['code' => 'TLS-TBOX-01'],
                [
                    'name' => 'Tool Box Lapangan (Isi 14 Item Wajib)',
                    'category_id' => $catTools->id,
                    'unit_id' => $unitUnit->id,
                    'brand' => 'Deli/Tekiro',
                    'tracking_type' => 'individual',
                    'is_rental_item' => true,
                    'is_active' => true,
                    'notes' => 'Toolbox tim teknisi lapangan lengkap tang, obeng, kunci L/Y, tester, dll',
                ]
            );

            foreach ($data['toolboxes'] as $tb) {
                Asset::updateOrCreate(
                    ['asset_code' => 'AST-'.$tb['code']],
                    [
                        'product_id' => $tbProduct->id,
                        'current_location_id' => $locMain->id,
                        'serial_number' => $tb['code'],
                        'ownership' => 'company',
                        'condition' => $tb['condition'],
                        'status' => $tb['status'],
                        'notes' => $tb['completeness'],
                    ]
                );
            }

            $lapProduct = Product::firstOrCreate(
                ['code' => 'SYS-LAP-OP'],
                [
                    'name' => 'Laptop Operasional Lapangan',
                    'category_id' => $catProc->id,
                    'unit_id' => $unitUnit->id,
                    'brand' => 'Lenovo/Asus',
                    'tracking_type' => 'individual',
                    'is_rental_item' => true,
                    'is_active' => true,
                    'notes' => 'Laptop untuk operator LED lapangan',
                ]
            );

            foreach ($data['laptops'] as $lap) {
                Asset::updateOrCreate(
                    ['asset_code' => 'AST-'.$lap['serial_number']],
                    [
                        'product_id' => $lapProduct->id,
                        'current_location_id' => $locMain->id,
                        'serial_number' => $lap['serial_number'],
                        'ownership' => 'company',
                        'condition' => $lap['condition'],
                        'status' => $lap['status'],
                        'notes' => $lap['name'].' - '.$lap['notes'],
                    ]
                );
            }
            $this->info('✓ Sukses sinkronisasi Tool Box & Laptop!');

            // 6. Sinkronisasi Rental Products (Kabinet, Kabel, Panel, Rigging)
            $this->info('Mengimpor Stok Rental LED, Perkabelan & Rigging GA...');
            $catMap = [
                'Komponen Videotron' => Category::where('slug', 'komponen-videotron')->first()?->id ?? 1,
                'Penyimpanan & Hardcase' => Category::where('slug', 'penyimpanan-hardcase')->first()?->id ?? 13,
                'Perkabelan' => Category::where('slug', 'perkabelan')->first()?->id ?? 6,
                'Kelistrikan & Power' => Category::where('slug', 'kelistrikan')->first()?->id ?? 8,
                'Konstruksi & Rigging' => Category::where('slug', 'konstruksi')->first()?->id ?? 7,
                'Peralatan & Tools' => Category::where('slug', 'peralatan-tools')->first()?->id ?? 12,
            ];

            $importedRental = 0;
            foreach ($data['rental_products'] as $item) {
                $unit = match (strtolower($item['unit'])) {
                    'cabinet' => $unitCabinet,
                    'mtr', 'meter' => $unitMeter,
                    'set' => $unitSet,
                    'unit' => $unitUnit,
                    default => $unitPcs,
                };

                $catId = $catMap[$item['category']] ?? $catMap['Komponen Videotron'];

                // Cari atau buat SKU unik
                $existing = Product::where('name', $item['name'])->first();
                $code = $existing?->code ?? sprintf('GA-%s-%03d', strtoupper(Str::slug(substr($item['name'], 0, 10))), $importedRental + 1);

                $product = Product::updateOrCreate(
                    ['name' => $item['name']],
                    [
                        'code' => $code,
                        'category_id' => $catId,
                        'unit_id' => $unit->id,
                        'tracking_type' => 'quantity',
                        'sqm_per_unit' => $item['sqm_per_unit'],
                        'is_rental_item' => true,
                        'is_active' => true,
                        'notes' => $item['subgroup'].($item['keterangan'] ? ' ('.$item['keterangan'].')' : ''),
                    ]
                );

                // Set StockBalance di Staging atau Gudang Utama
                $locId = ($item['category'] === 'Komponen Videotron') ? $locStaging->id : $locMain->id;
                StockBalance::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'location_id' => $locId,
                        'condition' => 'available',
                        'ownership' => 'company',
                    ],
                    [
                        'quantity' => $item['qty'],
                        'allocated_quantity' => 0,
                    ]
                );
                $importedRental++;
            }
            $this->info("✓ Sukses mengimpor {$importedRental} barang rental operasional!");

            // 7. Sinkronisasi Aset Internal Kantor & Mess (is_rental_item = false)
            $this->info('Mengimpor Aset Internal Kantor & Mess Dormitory...');
            $importedOffice = 0;
            foreach ($data['internal_office_assets'] as $off) {
                $existing = Product::where('name', $off['name'])->first();
                $code = $existing?->code ?? sprintf('INT-%s-%03d', strtoupper(Str::slug(substr($off['name'], 0, 10))), $importedOffice + 1);

                $product = Product::updateOrCreate(
                    ['name' => $off['name']],
                    [
                        'code' => $code,
                        'category_id' => $catOffice->id,
                        'unit_id' => $unitUnit->id,
                        'tracking_type' => 'quantity',
                        'is_rental_item' => false, // ASET INTERNAL KANTOR!
                        'is_active' => true,
                        'notes' => 'Aset Internal GA: '.($off['keterangan'] ?: '-'),
                    ]
                );

                StockBalance::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'location_id' => $locOffice->id,
                        'condition' => 'available',
                        'ownership' => 'company',
                    ],
                    [
                        'quantity' => $off['qty'],
                        'allocated_quantity' => 0,
                    ]
                );
                $importedOffice++;
            }
            $this->info("✓ Sukses mengimpor {$importedOffice} item Aset Internal Kantor & Mess!");

            DB::commit();
            $this->newLine();
            $this->info('🎉 Sinkronisasi seluruh inventaris GA CV Han Media Tekindo BERHASIL!');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Gagal sinkronisasi data GA: '.$e->getMessage());
            $this->error($e->getTraceAsString());

            return self::FAILURE;
        }
    }
}
