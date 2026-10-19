<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\Invoice;
use App\Models\OrderLedScreen;
use App\Models\SpkAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CustomerOrderTrackingTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('email', 'admin@omahvisual.com')->first() ?? User::factory()->create();
    }

    /**
     * Helper to create a complete event order with screens and SPK assignment.
     */
    protected function createCompleteOrder(array $attributes = []): EventOrder
    {
        $customer = Customer::create([
            'name' => 'Aditya Pratama',
            'organization' => 'Semesta Wedding Organizer',
            'phone' => '+6281234567890',
            'email' => 'aditya@semestawo.com',
            'address' => 'Jl. Kaliurang KM 9, Sleman',
        ]);

        $order = EventOrder::create(array_merge([
            'order_number' => 'OV-TEST-'.uniqid(),
            'customer_id' => $customer->id,
            'event_name' => 'Grand Wedding Rizky & Alya',
            'event_type' => 'indoor',
            'venue_name' => 'Grand Pacific Hall Yogyakarta',
            'venue_address' => 'Jl. Magelang KM 4.5, Sleman',
            'provinsi' => 'DI Yogyakarta',
            'kabupaten' => 'Kabupaten Sleman',
            'status_wilayah' => 'dalam_kota',
            'google_maps_url' => 'https://maps.google.com/?q=-7.765,110.362',
            'loading_date' => now()->addDays(2)->format('Y-m-d'),
            'loading_time' => '22:00',
            'ready_time' => '07:00',
            'jam_acara' => '09:00 - 13:00',
            'event_start_date' => now()->addDays(3)->format('Y-m-d'),
            'event_end_date' => now()->addDays(3)->format('Y-m-d'),
            'total_price' => 12500000,
            'down_payment' => 5000000,
            'paid_amount' => 5000000,
            'payment_status' => 'partial',
            'status' => 'confirmed',
            'created_by' => $this->user->id,
        ], $attributes));

        // Create main screen
        OrderLedScreen::create([
            'event_order_id' => $order->id,
            'screen_name' => 'Layar Utama Panggung (Center Stage)',
            'brand_series' => 'SAGA',
            'pixel_pitch' => 'P3.9',
            'width_m' => 6.0,
            'height_m' => 3.0,
            'total_sqm' => 18.0,
            'rigging_method' => 'ground_stake',
            'processor_model' => 'Novastar VX600',
        ]);

        // Create wing screen
        OrderLedScreen::create([
            'event_order_id' => $order->id,
            'screen_name' => 'Sayap Kiri (Left Wing)',
            'brand_series' => 'SAGA',
            'pixel_pitch' => 'P3.9',
            'width_m' => 2.0,
            'height_m' => 3.0,
            'total_sqm' => 6.0,
            'rigging_method' => 'ground_stake',
            'processor_model' => 'Novastar VX400',
        ]);

        // Create SPK Assignment
        SpkAssignment::create([
            'event_order_id' => $order->id,
            'spk_number' => 'SPK-TEST-'.uniqid(),
            'crew_pic_event_name' => 'Bambang Sudiro',
            'vehicle_name' => 'Isuzu Giga Box (AB 1234 CD)',
            'departure_status' => 'pending',
            'qc_status' => 'pending',
            'return_status' => 'pending',
        ]);

        return $order->fresh(['customer', 'screens', 'spkAssignment']);
    }

    public function test_customer_can_access_tracking_page_via_valid_public_token_without_authentication(): void
    {
        $order = $this->createCompleteOrder();

        $response = $this->get(route('orders.track', $order->public_token));

        $response->assertOk();
        $response->assertSee('Portal Pelacakan Pesanan Resmi');
        $response->assertSee($order->event_name);
        $response->assertSee($order->order_number);
        $response->assertSee('Grand Pacific Hall Yogyakarta');
        $response->assertSee('Semesta Wedding Organizer');
    }

    public function test_invalid_public_token_returns_404_not_found(): void
    {
        $response = $this->get(route('orders.track', 'non-existent-token-12345'));

        $response->assertNotFound();
    }

    public function test_tracking_page_displays_screen_specifications_aspect_ratio_and_pixel_resolution(): void
    {
        $order = $this->createCompleteOrder();

        $response = $this->get(route('orders.track', $order->public_token));

        $response->assertOk();
        $response->assertSee('Spesifikasi Teknis Layar LED');
        $response->assertSee('Layar Utama Panggung (Center Stage)');
        $response->assertSee('18.00 m²');
        $response->assertSee('6.00 m × 3.00 m');
        // Native resolution for 6m x 3m P3.9 (256 px/m) is 1536 × 768 px
        $response->assertSee('1536 × 768 px');
        // Aspect ratio 6:3 = 2:1
        $response->assertSee('2:1');
        $response->assertSee('Novastar VX600');
    }

    public function test_tracking_page_displays_5_stage_timeline_and_progress_percentage(): void
    {
        $order = $this->createCompleteOrder();

        $response = $this->get(route('orders.track', $order->public_token));

        $response->assertOk();
        $response->assertSee('Pesanan Terkonfirmasi');
        $response->assertSee('Pemuatan Barang di Gudang (Loading Out)');
        $response->assertSee('Armada Logistik Menuju Venue Acara');
        $response->assertSee('Instalasi &amp; Uji Kelayakan QC Panggung', false);
        $response->assertSee('Acara Berlangsung &amp; Selesai', false);
        $response->assertSee('Progress Kesiapan Operasional');
    }

    public function test_tracking_page_displays_qc_status_checks_and_bast_photo_when_completed(): void
    {
        $order = $this->createCompleteOrder();
        $spk = $order->spkAssignment;

        // Simulate stage completed: departure verified & QC passed
        $spk->update([
            'departure_status' => 'verified',
            'departure_datetime' => now(),
            'qc_status' => 'passed',
            'qc_grounding_safe' => true,
            'qc_test_rgb' => true,
            'qc_video_signal_ok' => true,
            'qc_photo_path' => 'qc_documentation/sample_stage_photo.jpg',
            'qc_technician_name' => 'Eko Prasetyo',
            'qc_verified_at' => now(),
            'qc_notes' => 'Layar aman 100%, grounding 0.8V, RGB test perfect.',
        ]);

        $order->update(['status' => 'running']);

        $response = $this->get(route('orders.track', $order->public_token));

        $response->assertOk();
        $response->assertSee('QC Telah Lolos Standar Omah Visual');
        $response->assertSee('Grounding');
        $response->assertSee('Keamanan Listrik');
        $response->assertSee('Uji Warna Layar');
        $response->assertSee('Sinyal Video');
        $response->assertSee('NovaStar');
        $response->assertSee('Eko Prasetyo');
        $response->assertSee('qc_documentation/sample_stage_photo.jpg');
        $response->assertSee('Layar aman 100%, grounding 0.8V, RGB test perfect.');
    }

    public function test_tracking_page_displays_billing_summary_and_invoice_details(): void
    {
        $order = $this->createCompleteOrder([
            'total_price' => 10000000,
            'paid_amount' => 4000000,
            'payment_status' => 'partial',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-202610-TEST01',
            'event_order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'title' => 'Tagihan Sewa LED Screen '.$order->event_name,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(3)->format('Y-m-d'),
            'subtotal' => 10000000,
            'tax_amount' => 0,
            'total_amount' => 10000000,
            'paid_amount' => 4000000,
            'balance_due' => 6000000,
            'status' => 'partial',
            'created_by' => $this->user->id,
        ]);

        $response = $this->get(route('orders.track', $order->public_token));

        $response->assertOk();
        $response->assertSee('Rincian Tagihan');
        $response->assertSee('10.000.000');
        $response->assertSee('4.000.000');
        $response->assertSee('6.000.000');
        $response->assertSee('Unduh Invoice Resmi (#INV-202610-TEST01)');
        $response->assertSee('4390865177'); // BCA Rekening 1
        $response->assertSee('3660399282'); // BCA Rekening 2
    }

    public function test_event_order_model_provides_tracking_url_and_whatsapp_click_to_chat_url(): void
    {
        $order = $this->createCompleteOrder();

        $this->assertEquals(route('orders.track', $order->public_token), $order->tracking_url);
        $this->assertStringContainsString('https://wa.me/6281234567890', $order->tracking_whatsapp_url);
        $this->assertStringContainsString(urlencode($order->tracking_url), $order->tracking_whatsapp_url);
    }

    public function test_orders_show_page_displays_customer_tracking_portal_links_for_authenticated_users(): void
    {
        $order = $this->createCompleteOrder();

        $response = $this->actingAs($this->user)->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertSee('Portal Pelacakan Customer');
        $response->assertSee(route('orders.track', $order->public_token));
        $response->assertSee('Kirim ke WA Customer');
        $response->assertSee('Salin Link');
    }
}
