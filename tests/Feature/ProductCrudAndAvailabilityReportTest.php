<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\ServiceTicket;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductCrudAndAvailabilityReportTest extends TestCase
{
    use DatabaseTransactions;

    protected User $owner;

    protected User $warehouse;

    protected Category $category;

    protected Unit $unit;

    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::where('email', 'owner@omahvisual.com')->first();
        $this->warehouse = User::where('email', 'warehouse@omahvisual.com')->first();

        $this->category = Category::firstOrCreate(
            ['slug' => 'kategori-testing'],
            ['name' => 'Kategori Testing', 'is_active' => true]
        );

        $this->unit = Unit::firstOrCreate(
            ['symbol' => 'PCS'],
            ['name' => 'Pieces']
        );

        $this->location = Location::firstOrCreate(
            ['code' => 'GU-TEST'],
            ['name' => 'Gudang Testing', 'is_active' => true]
        );
    }

    public function test_user_can_view_product_catalog_with_availability_stats(): void
    {
        $response = $this->actingAs($this->owner)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Master Barang &amp; Spesifikasi', false);
        $response->assertSee('Export CSV (Excel)');
        $response->assertSee('Cetak Laporan Ketersediaan');
        $response->assertSee('Total Katalog SKU');
    }

    public function test_user_can_create_product_pure_catalog(): void
    {
        $code = 'TEST-CAB-'.uniqid();

        $response = $this->actingAs($this->warehouse)->post(route('products.store'), [
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'code' => $code,
            'name' => 'Modul LED P3.9 Katalog Baru',
            'brand' => 'Novastar',
            'model_series' => 'VX-Test',
            'tracking_type' => 'quantity',
            'sqm_per_unit' => 0.5,
            'specification' => 'Pitch 3.9mm High Refresh Rate 3840Hz',
            'notes' => 'Master katalog produk baru siap operasional',
        ]);

        $product = Product::where('code', $code)->first();
        $this->assertNotNull($product);

        $response->assertRedirect(route('products.show', $product));
        $this->assertEquals('Modul LED P3.9 Katalog Baru', $product->name);
        $this->assertTrue($product->is_active);
    }

    public function test_user_can_delete_unused_product_permanently(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'code' => 'DEL-PROD-'.uniqid(),
            'name' => 'Barang Salah Input Mau Dihapus',
            'tracking_type' => 'quantity',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->owner)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_with_active_service_ticket_is_deactivated_instead_of_deleted(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'unit_id' => $this->unit->id,
            'code' => 'SVC-PROD-'.uniqid(),
            'name' => 'Barang Sedang Ada Tiket Servis',
            'tracking_type' => 'quantity',
            'is_active' => true,
        ]);

        ServiceTicket::create([
            'ticket_number' => 'TCK-TEST-'.uniqid(),
            'product_id' => $product->id,
            'reported_by' => $this->owner->id,
            'status' => 'open',
            'issue_description' => 'Modul blank butuh perbaikan IC',
        ]);

        $response = $this->actingAs($this->owner)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('warning');

        // Barang tidak terhapus permanen melainkan dinonaktifkan
        $product->refresh();
        $this->assertFalse($product->is_active);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_user_can_export_inventory_availability_as_csv(): void
    {
        $response = $this->actingAs($this->owner)->get(route('products.export-csv'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('laporan-ketersediaan-omahvisual', $response->headers->get('Content-Disposition'));
    }

    public function test_user_can_render_printable_monthly_availability_report(): void
    {
        $response = $this->actingAs($this->owner)->get(route('products.print-report'));

        $response->assertStatus(200);
        $response->assertSee('LAPORAN KETERSEDIAAN ASET');
        $response->assertSee('OMAH VISUAL LED');
        $response->assertSee('CV HAN MEDIA TEKINDO');
        $response->assertSee('Indeks Kesiapan Armada');
        $response->assertSee('Kepala Logistik &amp; Gudang', false);
    }
}
