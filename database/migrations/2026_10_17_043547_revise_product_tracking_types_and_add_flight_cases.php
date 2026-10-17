<?php

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            $unit = Unit::firstOrCreate(['symbol' => 'unit'], ['name' => 'Unit', 'symbol' => 'unit']);

            // Lokasi
            $stagingLocation = Location::where('code', 'GU-STG-LED')->first() ?? Location::first();
            $locAlmari1 = Location::where('code', 'GU-ALM-1')->first() ?? $stagingLocation;
            $locAlmari3 = Location::where('code', 'GU-ALM-3')->first() ?? $stagingLocation;
            $locWorkshop = Location::where('code', 'GU-WS-TB')->first() ?? $stagingLocation;

            // Kategori Flightcase
            $catPendukung = Category::firstOrCreate(['slug' => 'alat-pendukung'], ['name' => 'Alat Pendukung', 'is_active' => true]);
            $subCase = Category::firstOrCreate(
                ['slug' => 'penyimpanan-hardcase'],
                ['name' => 'Penyimpanan & Hardcase', 'parent_id' => $catPendukung->id, 'is_active' => true]
            );

            // 1. Ubah Novastar dari individual ke quantity
            $novastar = Product::where('code', 'SYS-NOV-VX-SERIES')->first();
            if ($novastar) {
                $count = Asset::where('product_id', $novastar->id)->count();
                $novastar->update(['tracking_type' => 'quantity']);
                Asset::where('product_id', $novastar->id)->delete();

                if ($count > 0 && $locAlmari1) {
                    StockBalance::updateOrCreate(
                        [
                            'product_id' => $novastar->id,
                            'location_id' => $locAlmari1->id,
                            'ownership' => 'company',
                            'condition' => 'available',
                        ],
                        ['quantity' => $count]
                    );
                }
            }

            // 2. Ubah Smart TV Samsung dari individual ke quantity
            $tv = Product::where('code', 'TV-DISP-50-SAMS')->first();
            if ($tv) {
                $count = Asset::where('product_id', $tv->id)->count();
                $tv->update(['tracking_type' => 'quantity']);
                Asset::where('product_id', $tv->id)->delete();

                if ($count > 0 && $locAlmari3) {
                    StockBalance::updateOrCreate(
                        [
                            'product_id' => $tv->id,
                            'location_id' => $locAlmari3->id,
                            'ownership' => 'company',
                            'condition' => 'available',
                        ],
                        ['quantity' => $count]
                    );
                }
            }

            // 3. Ubah Toolbox Teknisi dari individual ke quantity
            $toolbox = Product::where('code', 'TLS-BOX-STD')->first();
            if ($toolbox) {
                $count = Asset::where('product_id', $toolbox->id)->count();
                $toolbox->update(['tracking_type' => 'quantity']);
                Asset::where('product_id', $toolbox->id)->delete();

                if ($count > 0 && $locWorkshop) {
                    StockBalance::updateOrCreate(
                        [
                            'product_id' => $toolbox->id,
                            'location_id' => $locWorkshop->id,
                            'ownership' => 'company',
                            'condition' => 'available',
                        ],
                        ['quantity' => $count]
                    );
                }
            }

            // 4. Daftarkan Flight Case Kabinet 100x50 cm (Isi 6) sebagai individual asset
            $fc100 = Product::updateOrCreate(
                ['code' => 'FC-CAB-P39-100X50'],
                [
                    'category_id' => $subCase->id,
                    'unit_id' => $unit->id,
                    'name' => 'Flight Case Kabinet LED 100x50 cm (Isi 6)',
                    'brand' => 'Omah Visual Hardcase',
                    'model_series' => 'FC-100',
                    'specification' => 'Hardcase Roda 4, Kapasitas 6 Kabinet 100x50 cm',
                    'tracking_type' => 'individual',
                    'is_active' => true,
                    'notes' => 'Hardcase beroda standar dengan pelat nomor dan barcode unit unik.',
                ]
            );

            // Buat 12 unit aset Flightcase 100x50 cm (FC-100-01 s/d FC-100-12)
            for ($i = 1; $i <= 12; $i++) {
                $numStr = str_pad($i, 2, '0', STR_PAD_LEFT);
                Asset::updateOrCreate(
                    ['asset_code' => "FC-100-{$numStr}"],
                    [
                        'product_id' => $fc100->id,
                        'current_location_id' => $stagingLocation?->id,
                        'serial_number' => "SN-FC100-{$numStr}",
                        'barcode' => "BC-FC100-{$numStr}",
                        'ownership' => 'company',
                        'condition' => 'available',
                        'status' => 'in_storage',
                        'notes' => "Flight Case Kabinet 100x50 cm Unit #{$i} (Kapasitas 6 Kabinet)",
                    ]
                );
            }

            // 5. Daftarkan Flight Case Kabinet 50x50 cm (Isi 8) sebagai individual asset
            $fc50 = Product::updateOrCreate(
                ['code' => 'FC-CAB-P39-50X50'],
                [
                    'category_id' => $subCase->id,
                    'unit_id' => $unit->id,
                    'name' => 'Flight Case Kabinet LED 50x50 cm (Isi 8)',
                    'brand' => 'Omah Visual Hardcase',
                    'model_series' => 'FC-50',
                    'specification' => 'Hardcase Roda 4, Kapasitas 8 Kabinet 50x50 cm',
                    'tracking_type' => 'individual',
                    'is_active' => true,
                    'notes' => 'Hardcase beroda standar dengan pelat nomor dan barcode unit unik.',
                ]
            );

            // Buat 6 unit aset Flightcase 50x50 cm (FC-050-01 s/d FC-050-06)
            for ($j = 1; $j <= 6; $j++) {
                $numStr = str_pad($j, 2, '0', STR_PAD_LEFT);
                Asset::updateOrCreate(
                    ['asset_code' => "FC-050-{$numStr}"],
                    [
                        'product_id' => $fc50->id,
                        'current_location_id' => $stagingLocation?->id,
                        'serial_number' => "SN-FC050-{$numStr}",
                        'barcode' => "BC-FC050-{$numStr}",
                        'ownership' => 'company',
                        'condition' => 'available',
                        'status' => 'in_storage',
                        'notes' => "Flight Case Kabinet 50x50 cm Unit #{$j} (Kapasitas 8 Kabinet)",
                    ]
                );
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert product tracking types if needed
    }
};
