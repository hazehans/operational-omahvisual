<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdjustmentLocationFilterAndDecimalTest extends TestCase
{
    use DatabaseTransactions;

    protected User $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warehouse = User::where('email', 'warehouse@omahvisual.com')->first();
    }

    public function test_adjustment_create_view_receives_location_products_mapping(): void
    {
        $response = $this->actingAs($this->warehouse)->get(route('adjustments.create'));

        $response->assertStatus(200);
        $response->assertViewHas('locations');
        $response->assertViewHas('products');
        $response->assertViewHas('productsList');
        $response->assertViewHas('locationProductsMap');
        $response->assertSee('adjustmentForm');
        $response->assertSee('syncProductOptions');
    }

    public function test_product_sqm_per_unit_casts_to_two_decimal_places(): void
    {
        $category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Cat Test']);
        $unit = Unit::firstOrCreate(['symbol' => 'UNIT'], ['name' => 'Unit']);

        $product = Product::create([
            'code' => 'TEST-SQM-'.uniqid(),
            'name' => 'Kabinet LED Testing',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'tracking_type' => 'quantity',
            'sqm_per_unit' => 0.50,
            'is_active' => true,
        ]);

        $product->refresh();

        // Must be exactly 2 decimal places: '0.50', not '0.5000' or '0.50000'
        $this->assertSame('0.50', (string) $product->sqm_per_unit);

        $response = $this->actingAs($this->warehouse)->get(route('products.index', ['search' => $product->code]));
        $response->assertStatus(200);
        $response->assertSee('0.50 m²');
        $response->assertDontSee('0.5000 m²');
    }

    public function test_product_show_displays_two_decimal_sqm(): void
    {
        $category = Category::firstOrCreate(['slug' => 'test-cat-2'], ['name' => 'Cat Test 2']);
        $unit = Unit::firstOrCreate(['symbol' => 'UNIT2'], ['name' => 'Unit 2']);

        $product = Product::create([
            'code' => 'TEST-SHOW-'.uniqid(),
            'name' => 'Kabinet LED 50x50',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'tracking_type' => 'quantity',
            'sqm_per_unit' => 0.25,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->warehouse)->get(route('products.show', $product));
        $response->assertStatus(200);
        $response->assertSee('0.25 m² / unit');
    }
}
