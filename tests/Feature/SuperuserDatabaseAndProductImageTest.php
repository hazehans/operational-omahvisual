<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperuserDatabaseAndProductImageTest extends TestCase
{
    protected User $owner;

    protected User $warehouse;

    protected User $superuser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::where('email', 'owner@omahvisual.com')->first();
        $this->warehouse = User::where('email', 'warehouse@omahvisual.com')->first();
        $this->superuser = User::where('email', 'super@omahvisual.com')->first();
    }

    public function test_product_image_can_be_uploaded_updated_and_removed(): void
    {
        Storage::fake('public');

        $category = Category::first();
        $unit = Unit::first();
        $code = 'TEST-IMG-'.uniqid();

        $file = UploadedFile::fake()->image('videotron.jpg', 800, 600);

        // 1. Simpan produk baru dengan gambar
        $response = $this->actingAs($this->warehouse)->post('/products', [
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'code' => $code,
            'name' => 'LED Display Test Photo',
            'brand' => 'Absen',
            'model_series' => 'PL2.5 Pro',
            'tracking_type' => 'quantity',
            'image' => $file,
        ]);

        $product = Product::where('code', $code)->first();
        $this->assertNotNull($product);
        $response->assertRedirect('/products/'.$product->id);

        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);

        // 2. Akses halaman detail menampilkan image
        $showResponse = $this->actingAs($this->warehouse)->get('/products/'.$product->id);
        $showResponse->assertStatus(200);
        $showResponse->assertSee($product->image_url);

        // 3. Update produk dengan mengganti foto
        $newFile = UploadedFile::fake()->image('replacement.jpg', 500, 500);
        $oldPath = $product->image_path;

        $updateResponse = $this->actingAs($this->warehouse)->put('/products/'.$product->id, [
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'name' => 'LED Display Test Photo (Updated)',
            'image' => $newFile,
        ]);

        $updateResponse->assertRedirect('/products/'.$product->id);
        $product->refresh();

        $this->assertNotEquals($oldPath, $product->image_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($product->image_path);

        // 4. Hapus foto yang terpasang
        $removeResponse = $this->actingAs($this->warehouse)->put('/products/'.$product->id, [
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'name' => 'LED Display Test Photo (No Image)',
            'remove_image' => '1',
        ]);

        $removeResponse->assertRedirect('/products/'.$product->id);
        $product->refresh();

        $this->assertNull($product->image_path);
    }

    public function test_non_superuser_cannot_access_superuser_database_manager(): void
    {
        // Warehouse dilarang
        $resWarehouse = $this->actingAs($this->warehouse)->get('/superuser/database');
        $resWarehouse->assertStatus(403);

        // Owner dilarang
        $resOwner = $this->actingAs($this->owner)->get('/superuser/database');
        $resOwner->assertStatus(403);
    }

    public function test_superuser_can_access_database_catalog_and_table_browser(): void
    {
        $response = $this->actingAs($this->superuser)->get('/superuser/database');
        $response->assertStatus(200);
        $response->assertSee('Database Explorer');
        $response->assertSee('Master Produk');
        $response->assertSee('Unit Serial Aset');

        $tableResponse = $this->actingAs($this->superuser)->get('/superuser/database/products');
        $tableResponse->assertStatus(200);
        $tableResponse->assertSee('products');
        $tableResponse->assertSee('Master Produk');
    }

    public function test_superuser_can_insert_update_and_delete_raw_record(): void
    {
        // 1. Insert baris baru ke tabel units via Superuser DB manager
        $unitName = 'Box Custom '.uniqid();
        $unitSymbol = 'BX';

        $insertResponse = $this->actingAs($this->superuser)->post('/superuser/database/units', [
            'name' => $unitName,
            'symbol' => $unitSymbol,
            'description' => 'Unit satuan uji coba superuser',
        ]);

        $unit = DB::table('units')->where('name', $unitName)->first();
        $this->assertNotNull($unit);
        $insertResponse->assertRedirect('/superuser/database/units');

        // Verifikasi audit log
        $insertAudit = AuditLog::where('action', 'superuser_db_insert')
            ->where('entity_id', $unit->id)
            ->first();
        $this->assertNotNull($insertAudit);

        // 2. Edit baris data
        $updatedName = $unitName.' (Updated)';
        $updateResponse = $this->actingAs($this->superuser)->put('/superuser/database/units/'.$unit->id, [
            'name' => $updatedName,
            'symbol' => 'BX2',
            'description' => 'Deskripsi diperbarui oleh superuser',
        ]);

        $updateResponse->assertRedirect('/superuser/database/units');
        $unitUpdated = DB::table('units')->where('id', $unit->id)->first();
        $this->assertEquals($updatedName, $unitUpdated->name);
        $this->assertEquals('BX2', $unitUpdated->symbol);

        // 3. Hapus baris data
        $deleteResponse = $this->actingAs($this->superuser)->delete('/superuser/database/units/'.$unit->id);
        $deleteResponse->assertRedirect('/superuser/database/units');

        $unitDeleted = DB::table('units')->where('id', $unit->id)->first();
        $this->assertNull($unitDeleted);

        // Verifikasi audit log penghapusan
        $deleteAudit = AuditLog::where('action', 'superuser_db_delete')
            ->where('entity_id', $unit->id)
            ->first();
        $this->assertNotNull($deleteAudit);
    }
}

