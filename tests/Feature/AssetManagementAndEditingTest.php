<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AssetManagementAndEditingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $warehouse;

    protected User $owner;

    protected Product $product;

    protected Location $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warehouse = User::where('email', 'warehouse@omahvisual.com')->first();
        $this->owner = User::where('email', 'owner@omahvisual.com')->first();

        $category = Category::firstOrCreate(['slug' => 'cat-asset-test'], ['name' => 'Category Asset Test']);
        $unit = Unit::firstOrCreate(['symbol' => 'UNIT'], ['name' => 'Unit']);
        $this->location = Location::firstOrCreate(['code' => 'LOC-AST-TEST'], ['name' => 'Lokasi Test Aset']);

        $this->product = Product::firstOrCreate(
            ['tracking_type' => 'individual'],
            [
                'code' => 'PRD-AST-'.uniqid(),
                'name' => 'Videotron Individual Test',
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'is_active' => true,
            ]
        );
    }

    public function test_user_can_edit_serial_number_and_barcode_of_asset(): void
    {
        $asset = Asset::create([
            'product_id' => $this->product->id,
            'current_location_id' => $this->location->id,
            'asset_code' => 'OVL-TEST-'.uniqid(),
            'serial_number' => 'BOX-LAMA-01',
            'ownership' => 'company',
            'condition' => 'available',
            'status' => 'in_storage',
        ]);

        $response = $this->actingAs($this->warehouse)->put(route('assets.update-status', $asset), [
            'serial_number' => 'BOX-BARU-01-HIJAU',
            'barcode' => 'BC-BOX-01',
            'ownership' => 'company',
            'condition' => 'available',
            'status' => 'in_storage',
            'current_location_id' => $this->location->id,
            'notes' => 'Nomor box direvisi menjadi Box Hijau 01',
        ]);

        $response->assertRedirect(route('assets.show', $asset));
        $response->assertSessionHas('success');

        $asset->refresh();
        $this->assertEquals('BOX-BARU-01-HIJAU', $asset->serial_number);
        $this->assertEquals('BC-BOX-01', $asset->barcode);
        $this->assertEquals('Nomor box direvisi menjadi Box Hijau 01', $asset->notes);
    }

    public function test_user_can_delete_unused_asset(): void
    {
        $asset = Asset::create([
            'product_id' => $this->product->id,
            'current_location_id' => $this->location->id,
            'asset_code' => 'OVL-DEL-'.uniqid(),
            'serial_number' => 'SALAH-INPUT-01',
            'ownership' => 'company',
            'condition' => 'available',
            'status' => 'in_storage',
        ]);

        $response = $this->actingAs($this->warehouse)->delete(route('assets.destroy', $asset));

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
    }
}
