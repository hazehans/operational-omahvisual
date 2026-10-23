<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\Customer;
use App\Models\EventCrewAssignment;
use App\Models\EventOrder;
use App\Models\SpkAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EventCalendarAndCrewReminderTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected Customer $customer;

    protected EventOrder $order;

    protected Crew $crew;

    protected EventCrewAssignment $crewAssignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('email', 'owner@omahvisual.com')->firstOrFail();

        $this->customer = Customer::firstOrCreate(
            ['phone' => '081299990001'],
            ['name' => 'PT Testing Calendar Client', 'email' => 'client@test.com']
        );

        $this->order = EventOrder::create([
            'order_number' => 'ORD-CAL-2026-001',
            'customer_id' => $this->customer->id,
            'event_name' => 'Konser Musik Harmoni 2026',
            'event_type' => 'Konser Musik',
            'venue_name' => 'Grand City Convention Hall',
            'venue_address' => 'Jl. Gubeng Pojok No.1 Surabaya',
            'loading_date' => now()->startOfMonth()->addDays(5)->toDateString(),
            'loading_time' => '10:00',
            'event_start_date' => now()->startOfMonth()->addDays(6)->toDateString(),
            'event_end_date' => now()->startOfMonth()->addDays(6)->toDateString(),
            'status' => 'confirmed',
            'total_price' => 15000000,
            'public_token' => 'token-calendar-test-1234567890',
            'created_by' => $this->user->id,
        ]);

        $this->crew = Crew::firstOrCreate(
            ['phone' => '081234567899'],
            [
                'crew_code' => 'CRW-TEST-99',
                'nickname' => 'Budi',
                'full_name' => 'Budi Santoso',
                'position' => 'Operator VJ',
                'default_fee_loading_in' => 100000,
                'default_fee_loading_out' => 100000,
            ]
        );

        $spk = SpkAssignment::firstOrCreate(
            ['event_order_id' => $this->order->id],
            ['spk_number' => 'SPK-CAL-001', 'vehicle_name' => 'Pick Up Gran Max L 9001 XX']
        );

        $this->crewAssignment = EventCrewAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $spk->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Operator VJ Novastar',
            'fee_loading_in' => 100000,
            'fee_loading_out' => 100000,
            'fee_meal' => 25000,
            'fee_standby' => 50000,
            'fee_transport' => 0,
            'fee_bonus' => 0,
            'status' => 'acc',
        ]);
    }

    public function test_guests_are_redirected_when_accessing_calendar(): void
    {
        $response = $this->get(route('calendar.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_calendar(): void
    {
        $response = $this->actingAs($this->user)->get(route('calendar.index'));

        $response->assertStatus(200);
        $response->assertViewIs('calendar.index');
        $response->assertSee('Kalender Jadwal');
        $response->assertSee('Konser Musik Harmoni 2026');
        $response->assertSee('Grand City Convention Hall');
    }

    public function test_user_can_navigate_to_different_month_and_year(): void
    {
        $response = $this->actingAs($this->user)->get(route('calendar.index', [
            'month' => 12,
            'year' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Desember 2026');
    }

    public function test_crew_assignment_generates_valid_whatsapp_offer_and_reminder_urls(): void
    {
        $offerUrl = $this->crewAssignment->whatsapp_offer_url;
        $reminderUrl = $this->crewAssignment->whatsapp_reminder_url;

        $this->assertNotEmpty($offerUrl);
        $this->assertStringContainsString('wa.me', $offerUrl);
        $this->assertStringContainsString('6281234567899', $offerUrl);

        $this->assertNotEmpty($reminderUrl);
        $this->assertStringContainsString('wa.me', $reminderUrl);
        $this->assertStringContainsString('6281234567899', $reminderUrl);
        $this->assertStringContainsString('REMINDER+JADWAL+TUGAS', $reminderUrl);
    }

    public function test_spk_page_displays_reminder_button_for_crew(): void
    {
        $spk = $this->order->spkAssignment;

        $response = $this->actingAs($this->user)->get(route('spk.show', $spk));

        $response->assertStatus(200);
        $response->assertSee('⏰ Reminder');
    }

    public function test_dashboard_and_sidebar_contain_links_to_calendar(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('calendar.index'));
        $response->assertSee('Kalender Event');
    }
}
