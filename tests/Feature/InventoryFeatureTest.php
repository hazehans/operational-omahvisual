<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\InventoryCount;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\User;
use Tests\TestCase;

class InventoryFeatureTest extends TestCase
{
    protected User $owner;

    protected User $warehouse;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::where('email', 'owner@omahvisual.com')->first();
        $this->warehouse = User::where('email', 'warehouse@omahvisual.com')->first();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');

        $responseProducts = $this->get('/products');
        $responseProducts->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('OMAH VISUAL LED');
        $response->assertSee('Selamat Datang');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'owner@omahvisual.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->owner);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'owner@omahvisual.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->owner)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('OMAH VISUAL LED');
        $response->assertSee('Kabinet LED Tersedia');
        $response->assertSee('Unit Aset Individual');
    }

    public function test_products_list_and_details(): void
    {
        $response = $this->actingAs($this->warehouse)->get('/products');
        $response->assertStatus(200);

        $product = Product::first();
        $this->assertNotNull($product);

        $responseShow = $this->actingAs($this->warehouse)->get('/products/'.$product->id);
        $responseShow->assertStatus(200);
        $responseShow->assertSee($product->name);
    }

    public function test_assets_list_and_details(): void
    {
        $response = $this->actingAs($this->owner)->get('/assets');
        $response->assertStatus(200);

        $asset = Asset::first();
        $this->assertNotNull($asset);

        $responseShow = $this->actingAs($this->owner)->get('/assets/'.$asset->id);
        $responseShow->assertStatus(200);
        $responseShow->assertSee($asset->asset_code);
    }

    public function test_locations_list_and_details(): void
    {
        $response = $this->actingAs($this->warehouse)->get('/locations');
        $response->assertStatus(200);

        $location = Location::first();
        $this->assertNotNull($location);

        $responseShow = $this->actingAs($this->warehouse)->get('/locations/'.$location->id);
        $responseShow->assertStatus(200);
        $responseShow->assertSee($location->name);
    }

    public function test_initial_counts_verification_flow(): void
    {
        // Akses halaman sesi stok awal
        $response = $this->actingAs($this->warehouse)->get('/initial-counts');
        $response->assertStatus(200);
        $response->assertSee('Pendataan Fisik Stok Awal');

        $session = InventoryCount::first();
        $this->assertNotNull($session);
        $session->update(['status' => 'pending_verification', 'verified_by' => null]);

        // Warehouse sahkan stok awal
        $verifyResponse = $this->actingAs($this->warehouse)->post('/initial-counts/'.$session->id.'/verify');
        $verifyResponse->assertRedirect('/initial-counts');

        $session->refresh();
        $this->assertEquals('verified', $session->status);
        $this->assertEquals($this->warehouse->id, $session->verified_by);

        // Uji tombol simulasi reset kembali ke pending
        $resetResponse = $this->actingAs($this->warehouse)->post('/initial-counts/'.$session->id.'/reset-simulation');
        $resetResponse->assertRedirect('/initial-counts');

        $session->refresh();
        $this->assertEquals('pending_verification', $session->status);
        $this->assertNull($session->verified_by);
    }

    public function test_stock_movement_execution(): void
    {
        $product = Product::where('tracking_type', 'quantity')->first();
        $staging = Location::where('code', 'GU-STG-LED')->first();
        $almari1 = Location::where('code', 'GU-ALM-1')->first();

        $response = $this->actingAs($this->warehouse)->post('/movements', [
            'type' => 'inbound',
            'destination_location_id' => $staging->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'condition' => 'available',
            'ownership' => 'company',
            'notes' => 'Penerimaan stok tambahan untuk event',
        ]);

        $response->assertRedirect('/movements');
    }

    public function test_stock_adjustment_request_and_owner_approval(): void
    {
        $product = Product::first();
        $location = Location::first();

        $uniqueReason = 'Ditemukan selisih fisik '.uniqid();

        // Warehouse ajukan penyesuaian
        $response = $this->actingAs($this->warehouse)->post('/adjustments', [
            'location_id' => $location->id,
            'product_id' => $product->id,
            'condition' => 'available',
            'ownership' => 'company',
            'adjusted_quantity' => 15,
            'reason' => $uniqueReason,
        ]);

        $response->assertRedirect('/adjustments');

        $adjustment = StockAdjustment::where('reason', $uniqueReason)->first();
        $this->assertNotNull($adjustment);
        $this->assertEquals('pending_approval', $adjustment->status);

        // Owner menyetujui (Approve)
        $approveResponse = $this->actingAs($this->owner)->post('/adjustments/'.$adjustment->id.'/approve');
        $approveResponse->assertRedirect('/adjustments');

        $adjustment->refresh();
        $this->assertEquals('approved', $adjustment->status);
        $this->assertEquals($this->owner->id, $adjustment->approved_by);
    }
}
