<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\OrderLedScreen;
use App\Models\User;
use App\Support\IndonesianHolidays;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OperationalDashboardAndCalendarTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->user = User::where('email', 'admin@omahvisual.com')->first() ?? User::factory()->create();
        Permission::findOrCreate('view orders');
        Permission::findOrCreate('create orders');
        Permission::findOrCreate('edit orders');
        $this->user->givePermissionTo(['view orders', 'create orders', 'edit orders']);
    }

    public function test_orders_dashboard_renders_operational_metrics_and_hides_revenue(): void
    {
        $customer = Customer::factory()->create([
            'salutation' => 'Bapak',
            'name' => 'Budi Santoso',
            'phone' => '081234567899',
            'organization' => 'PT Kreasi Event',
        ]);

        $order = EventOrder::factory()->create([
            'customer_id' => $customer->id,
            'order_number' => 'ORD-2026-TEST',
            'event_name' => 'Konser Musik Surabaya',
            'venue_name' => 'Grand City Hall',
            'status' => 'confirmed',
            'loading_date' => now()->toDateString(),
            'loading_time' => '13:00 WIB',
            'total_price' => 15000000,
            'payment_status' => 'paid',
        ]);

        OrderLedScreen::factory()->create([
            'event_order_id' => $order->id,
            'screen_name' => 'Main Stage LED',
            'brand_series' => 'SAGA',
            'pixel_pitch' => 'P3.9',
            'rigging_method' => 'Hanging Rigging 5m',
            'total_sqm' => 24,
        ]);

        $response = $this->actingAs($this->user)->get(route('orders.index'));

        $response->assertOk();
        // Operational metrics must be present
        $response->assertSee('Event Bulan Ini');
        $response->assertSee('Terkonfirmasi');
        $response->assertSee('Loading / Live Hari Ini');
        $response->assertSee('Event Selesai');

        // Revenue metric must be strictly removed
        $response->assertDontSee('Total Pendapatan Lunas');

        // Customer details
        $response->assertSee('Bapak Budi Santoso');
        $response->assertSee('PT Kreasi Event');
        $response->assertSee('Grand City Hall');
        $response->assertSee('🎯 Gantung / Rigging');

        // Phone number should NOT be printed in the table cell body text
        $response->assertDontSee('<div class="text-xs text-slate-500 dark:text-slate-400">'.$customer->phone.'</div>', false);
    }

    public function test_orders_can_be_filtered_by_rigging_and_status(): void
    {
        $customer = Customer::factory()->create();

        $hangingOrder = EventOrder::factory()->create([
            'customer_id' => $customer->id,
            'order_number' => 'ORD-HANGING',
            'status' => 'confirmed',
        ]);
        OrderLedScreen::factory()->create([
            'event_order_id' => $hangingOrder->id,
            'rigging_method' => 'Rigging Gantung Takel',
        ]);

        $groundOrder = EventOrder::factory()->create([
            'customer_id' => $customer->id,
            'order_number' => 'ORD-GROUND',
            'status' => 'completed',
        ]);
        OrderLedScreen::factory()->create([
            'event_order_id' => $groundOrder->id,
            'rigging_method' => 'Ground Support Besi H',
        ]);

        // Filter Gantung
        $responseHanging = $this->actingAs($this->user)->get(route('orders.index', ['rigging_filter' => 'gantung']));
        $responseHanging->assertOk();
        $responseHanging->assertSee('ORD-HANGING');
        $responseHanging->assertDontSee('ORD-GROUND');

        // Filter Status Completed
        $responseCompleted = $this->actingAs($this->user)->get(route('orders.index', ['status' => 'completed']));
        $responseCompleted->assertOk();
        $responseCompleted->assertSee('ORD-GROUND');
        $responseCompleted->assertDontSee('ORD-HANGING');
    }

    public function test_calendar_renders_indonesian_holidays_and_highlight_tabs(): void
    {
        $customer = Customer::factory()->create([
            'salutation' => 'Ibu',
            'name' => 'Dewi Lestari',
            'organization' => 'EO Bintang Lima',
        ]);

        // Create an event in August 2026 (includes Independence Day Aug 17)
        $order = EventOrder::factory()->create([
            'customer_id' => $customer->id,
            'order_number' => 'ORD-AGUSTUS',
            'event_name' => 'Festival Kemerdekaan',
            'venue_name' => 'Lapangan Kodam V',
            'loading_date' => '2026-08-16',
            'loading_time' => '08:00 WIB',
            'event_start_date' => '2026-08-17',
            'event_end_date' => '2026-08-17',
            'status' => 'confirmed',
        ]);

        OrderLedScreen::factory()->create([
            'event_order_id' => $order->id,
            'screen_name' => 'Layar Panggung Utama',
            'brand_series' => 'MAVIC',
            'rigging_method' => 'Hanging Rigging',
            'total_sqm' => 36,
        ]);

        $response = $this->actingAs($this->user)->get(route('calendar.index', ['month' => 8, 'year' => 2026]));

        $response->assertOk();

        // Check Highlight Monitoring Tabs (Kasir LED tab style)
        $response->assertSee('Kalender Bulanan');
        $response->assertSee('Highlight: Project Berjalan / Live');
        $response->assertSee('Highlight: Project Kedepannya');
        $response->assertSee('Project Selesai & Termin');

        // Check Indonesian Holiday render on Aug 17
        $response->assertSee('Proklamasi Kemerdekaan RI Ke-81');

        // Check event pill in calendar grid
        $response->assertSee('Ibu Dewi Lestari');
        $response->assertSee('Lapangan Kodam V');
        $response->assertSee('36m²');
        $response->assertSee('In: 08:00 WIB');
    }

    public function test_indonesian_holidays_helper_accurately_detects_holidays(): void
    {
        $this->assertTrue(IndonesianHolidays::isHoliday('2026-01-01'));
        $this->assertSame('Tahun Baru 2026 Masehi', IndonesianHolidays::getForDate('2026-01-01'));

        $this->assertTrue(IndonesianHolidays::isHoliday('2026-08-17'));
        $this->assertSame('Proklamasi Kemerdekaan RI Ke-81', IndonesianHolidays::getForDate('2026-08-17'));

        $this->assertTrue(IndonesianHolidays::isHoliday('2026-03-21'));
        $this->assertSame('Hari Raya Idul Fitri 1447 H (Hari 1)', IndonesianHolidays::getForDate('2026-03-21'));

        $this->assertFalse(IndonesianHolidays::isHoliday('2026-08-18'));
        $this->assertNull(IndonesianHolidays::getForDate('2026-08-18'));
    }
}
