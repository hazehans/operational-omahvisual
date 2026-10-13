<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\OrderLedScreen;
use App\Models\SpkAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventOrderAndSpkManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('email', 'admin@omahvisual.com')->first() ?? User::factory()->create();
    }

    public function test_user_can_view_orders_index(): void
    {
        $response = $this->actingAs($this->user)->get(route('orders.index'));
        $response->assertOk();
        $response->assertSee('Pesanan Event');
        $response->assertSee('SPK LED');
    }

    public function test_user_can_view_orders_create(): void
    {
        $response = $this->actingAs($this->user)->get(route('orders.create'));
        $response->assertOk();
        $response->assertSee('Input Pesanan');
        $response->assertSee('Kalkulator Otomatis SPK');
    }

    public function test_screen_calculator_api_returns_accurate_specifications(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('orders.calculate'), [
            'width_m' => 6.0,
            'height_m' => 3.0,
            'pixel_pitch' => 'P3.9',
            'brand_series' => 'SAGA',
        ]);

        $response->assertOk();
        $response->assertJson([
            'total_sqm' => 18.0,
            'cabinets_1m' => 36,
            'cabinets_50cm' => 0,
            'total_cabinets' => 36,
            'total_hardcases' => 6,
        ]);
    }

    public function test_user_can_store_order_and_system_auto_generates_spk_and_checklists(): void
    {
        $orderData = [
            'customer_name' => 'Mas Dani Bless',
            'customer_phone' => '081234567890',
            'customer_organization' => 'Bless Visual Production',
            'event_name' => 'Konser Musik Kampus',
            'event_type' => 'indoor',
            'venue_name' => 'Graha Cakrawala Malang',
            'loading_date' => now()->addDays(2)->format('Y-m-d'),
            'loading_time' => '22:00',
            'event_start_date' => now()->addDays(3)->format('Y-m-d'),
            'ready_time' => 'Ready 07:00',
            'screens' => [
                [
                    'screen_name' => 'Main Screen',
                    'brand_series' => 'SAGA',
                    'pixel_pitch' => 'P3.9',
                    'width_m' => 6.0,
                    'height_m' => 3.0,
                    'rigging_method' => 'ground_stake',
                ],
            ],
            'crew_pic_event_name' => 'Boby',
            'crew_1_name' => 'Yoga',
            'crew_2_name' => 'Kemal',
            'vehicle_name' => 'TRAGA 1',
            'total_price' => 4500000,
            'down_payment' => 2000000,
            'down_payment_method' => 'BCA',
        ];

        $response = $this->actingAs($this->user)->post(route('orders.store'), $orderData);

        $order = EventOrder::where('event_name', 'Konser Musik Kampus')->first();
        $this->assertNotNull($order);
        $this->assertEquals('Konser Musik Kampus', $order->event_name);
        $this->assertStringStartsWith('ORD-2026-', $order->order_number);

        // Verify customer created
        $customer = Customer::where('name', 'Mas Dani Bless')->first();
        $this->assertNotNull($customer);
        $this->assertEquals($customer->id, $order->customer_id);

        // Verify screen created with calculated specs
        $screen = OrderLedScreen::where('event_order_id', $order->id)->first();
        $this->assertNotNull($screen);
        $this->assertEquals(18.0, (float) $screen->total_sqm);
        $this->assertEquals(36, $screen->total_cabinets);
        $this->assertEquals(6, $screen->total_hardcases);

        // Verify SPK Assignment created
        $spk = SpkAssignment::where('event_order_id', $order->id)->first();
        $this->assertNotNull($spk);
        $this->assertEquals('Boby', $spk->crew_pic_event_name);
        $this->assertEquals('TRAGA 1', $spk->vehicle_name);

        // Verify checklists populated (page 2 and page 3)
        $this->assertGreaterThan(20, $spk->checklistItems()->where('page_number', 2)->count());
        $this->assertGreaterThan(15, $spk->checklistItems()->where('page_number', 3)->count());

        $response->assertRedirect(route('orders.show', $order));
    }

    public function test_user_can_view_order_show_and_spk_show(): void
    {
        $customer = Customer::factory()->create();
        $order = EventOrder::factory()->create(['customer_id' => $customer->id]);
        $screen = OrderLedScreen::factory()->create(['event_order_id' => $order->id]);
        $assignment = SpkAssignment::factory()->create(['event_order_id' => $order->id]);

        $response = $this->actingAs($this->user)->get(route('orders.show', $order));
        $response->assertOk();
        $response->assertSee($order->event_name);

        $spkResponse = $this->actingAs($this->user)->get(route('spk.show', $assignment));
        $spkResponse->assertOk();
        $spkResponse->assertSee($assignment->spk_number);

        $printResponse = $this->actingAs($this->user)->get(route('spk.print', $assignment));
        $printResponse->assertOk();
        $printResponse->assertSee('1 SERAH TERIMA');
    }

    public function test_departure_gate_verification_works(): void
    {
        $assignment = SpkAssignment::factory()->create([
            'departure_status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->post(route('spk.verify-departure', $assignment), [
            'departure_pic_event_name' => 'Boby PIC',
            'departure_pic_warehouse_name' => 'Pak Joko Gudang',
        ]);

        $response->assertRedirect();
        $assignment->refresh();
        $this->assertEquals('verified', $assignment->departure_status);
        $this->assertEquals('Boby PIC', $assignment->departure_pic_event_name);
        $this->assertEquals('Pak Joko Gudang', $assignment->departure_pic_warehouse_name);
        $this->assertEquals('loading', $assignment->eventOrder->status);
    }

    public function test_return_gate_enforces_different_pic_rule(): void
    {
        $assignment = SpkAssignment::factory()->create([
            'departure_status' => 'verified',
            'departure_pic_event_name' => 'Boby PIC',
            'departure_pic_warehouse_name' => 'Pak Joko Gudang',
            'return_status' => 'pending',
        ]);

        // Attempt return verification with IDENTICAL PIC name
        $response = $this->actingAs($this->user)->post(route('spk.verify-return', $assignment), [
            'return_pic_event_name' => 'Boby PIC',
            'return_pic_warehouse_name' => 'Pak Joko Gudang',
        ]);

        $response->assertSessionHasErrors('return_pic_event_name');
        $assignment->refresh();
        $this->assertEquals('pending', $assignment->return_status);

        // Now attempt with a DIFFERENT PIC name
        $successResponse = $this->actingAs($this->user)->post(route('spk.verify-return', $assignment), [
            'return_pic_event_name' => 'Yoga PIC Kepulangan',
            'return_pic_warehouse_name' => 'Pak Joko Gudang',
        ]);

        $successResponse->assertSessionHasNoErrors();
        $assignment->refresh();
        $this->assertEquals('verified', $assignment->return_status);
        $this->assertEquals('completed', $assignment->eventOrder->status);
    }

    public function test_field_crew_can_access_live_view_without_login_via_token(): void
    {
        $customer = Customer::factory()->create();
        $order = EventOrder::factory()->create([
            'customer_id' => $customer->id,
            'event_name' => 'Wedding Senayan Super Mega',
            'public_token' => 'secret-crew-token-12345',
        ]);
        OrderLedScreen::factory()->create(['event_order_id' => $order->id]);
        SpkAssignment::factory()->create(['event_order_id' => $order->id]);

        // Access as GUEST without authentication
        $response = $this->get(route('spk.live', 'secret-crew-token-12345'));

        $response->assertOk();
        $response->assertSee('Wedding Senayan Super Mega');
        $response->assertSee('Portal Crew Lapangan');
    }

    public function test_user_can_store_order_with_3d_stage_layout_and_print_2d_blueprint(): void
    {
        $sampleLayoutJson = json_encode([
            [
                'id' => 1,
                'name' => 'Main Screen',
                'width' => 8.0,
                'height' => 4.0,
                'x' => 0.0,
                'z' => -2.0,
                'rotY' => 0,
                'rigging' => 'ground_stake',
            ],
            [
                'id' => 2,
                'name' => 'Wing Kiri',
                'width' => 3.0,
                'height' => 4.0,
                'x' => -5.5,
                'z' => -1.0,
                'rotY' => 25,
                'rigging' => 'ground_stake',
            ],
        ]);

        $sampleBlueprintImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';
        $sampleBlueprintFrontImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $orderData = [
            'customer_name' => 'EO Diamond Wedding',
            'customer_phone' => '089988776655',
            'event_name' => 'Grand Wedding Kevin & Jessica',
            'event_type' => 'indoor',
            'venue_name' => 'Grand Ballroom Ijen Suites Malang',
            'loading_date' => now()->addDays(2)->format('Y-m-d'),
            'loading_time' => '23:00',
            'event_start_date' => now()->addDays(3)->format('Y-m-d'),
            'stage_width_m' => 12.0,
            'stage_depth_m' => 8.0,
            'stage_height_m' => 0.8,
            'stage_layout_json' => $sampleLayoutJson,
            'stage_layout_image' => $sampleBlueprintImage,
            'stage_layout_front_image' => $sampleBlueprintFrontImage,
            'screens' => [
                [
                    'screen_name' => 'Main Screen',
                    'brand_series' => 'SAGA',
                    'pixel_pitch' => 'P3.9',
                    'width_m' => 8.0,
                    'height_m' => 4.0,
                    'rigging_method' => 'ground_stake',
                ],
            ],
            'vehicle_name' => 'TRAGA 2',
        ];

        $response = $this->actingAs($this->user)->post(route('orders.store'), $orderData);
        $response->assertSessionHasNoErrors();

        $order = EventOrder::where('event_name', 'Grand Wedding Kevin & Jessica')->first();
        $this->assertNotNull($order);
        $this->assertEquals(12.0, (float) $order->stage_width_m);
        $this->assertEquals(8.0, (float) $order->stage_depth_m);
        $this->assertEquals(0.8, (float) $order->stage_height_m);
        $this->assertCount(2, $order->stage_layout_json);
        $this->assertEquals($sampleBlueprintImage, $order->stage_layout_image);
        $this->assertEquals($sampleBlueprintFrontImage, $order->stage_layout_front_image);

        // Verify printed SPK contains Halaman 4 with both 2D Blueprints (Top Plan & Front Elevation)
        $printResponse = $this->actingAs($this->user)->get(route('spk.print', $order->spkAssignment));
        $printResponse->assertOk();
        $printResponse->assertSee('BLUEPRINT LAYOUT PANGGUNG 2D');
        $printResponse->assertSee('4 BLUEPRINT TEKNIS: TATA LETAK PANGGUNG & FORMASI LAYAR LED');
        $printResponse->assertSee('TAMPAK ATAS (TOP-DOWN FLOOR PLAN)');
        $printResponse->assertSee('TAMPAK DEPAN (FRONT ELEVATION)');
        $printResponse->assertSee($sampleBlueprintImage);
        $printResponse->assertSee($sampleBlueprintFrontImage);

        // Verify live crew token view also displays both blueprints
        $crewResponse = $this->get(route('spk.live', $order->public_token));
        $crewResponse->assertOk();
        $crewResponse->assertSee('Tampak Atas (Top Plan)');
        $crewResponse->assertSee('Tampak Depan (Front Elevation)');
        $crewResponse->assertSee($sampleBlueprintImage);
        $crewResponse->assertSee($sampleBlueprintFrontImage);
    }

    public function test_user_can_update_stage_layout_via_ajax_endpoint(): void
    {
        $customer = Customer::factory()->create();
        $order = EventOrder::factory()->create([
            'customer_id' => $customer->id,
            'stage_width_m' => 10.0,
            'stage_depth_m' => 6.0,
        ]);

        $newLayout = [
            ['id' => 1, 'name' => 'Main Screen', 'width' => 10.0, 'height' => 4.0, 'x' => 0.0, 'z' => -1.5, 'rotY' => 0],
        ];
        $newTopImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $newFrontImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->user)->putJson(route('orders.update-stage-layout', $order), [
            'stage_width_m' => 14.0,
            'stage_depth_m' => 8.0,
            'stage_height_m' => 1.0,
            'stage_layout_json' => $newLayout,
            'stage_layout_image' => $newTopImage,
            'stage_layout_front_image' => $newFrontImage,
        ]);

        $response->assertOk();
        $response->assertJson([
            'message' => 'Layout panggung dan blueprint 2D berhasil diperbarui!',
        ]);

        $order->refresh();
        $this->assertEquals(14.0, (float) $order->stage_width_m);
        $this->assertEquals(8.0, (float) $order->stage_depth_m);
        $this->assertEquals(1.0, (float) $order->stage_height_m);
        $this->assertEquals($newTopImage, $order->stage_layout_image);
        $this->assertEquals($newFrontImage, $order->stage_layout_front_image);
        $this->assertEquals('Main Screen', $order->stage_layout_json[0]['name']);
    }
}
