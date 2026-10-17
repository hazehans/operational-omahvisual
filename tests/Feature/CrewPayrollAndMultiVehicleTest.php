<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\Customer;
use App\Models\EventCrewAssignment;
use App\Models\EventOrder;
use App\Models\EventVehicle;
use App\Models\OrderLedScreen;
use App\Models\SpkAssignment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CrewPayrollAndMultiVehicleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected EventOrder $order;
    protected SpkAssignment $assignment;
    protected Crew $crew;
    protected Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('email', 'admin@omahvisual.com')->first() ?? User::factory()->create();

        $this->customer = Customer::create([
            'name' => 'PT Gemilang Kreasi Event',
            'phone' => '081234567890',
            'organization' => 'Gemilang EO',
        ]);

        $this->order = EventOrder::create([
            'order_number' => 'ORD-2026-TEST-999',
            'customer_id' => $this->customer->id,
            'event_name' => 'Festival Musik Bromo 2026',
            'venue_name' => 'Amphitheater Bromo',
            'event_start_date' => now()->addDays(5)->toDateString(),
            'event_end_date' => now()->addDays(6)->toDateString(),
            'loading_date' => now()->addDays(4)->toDateString(),
            'loading_time' => '13:00',
            'total_sqm' => 24.0,
            'total_price' => 15000000,
            'status' => 'confirmed',
        ]);

        OrderLedScreen::create([
            'event_order_id' => $this->order->id,
            'screen_name' => 'Main Center Screen',
            'width_m' => 8.0,
            'height_m' => 3.0,
            'pixel_pitch' => 'P3.9',
            'brand_series' => 'SAGA',
            'sqm' => 24.0,
            'total_cabinets' => 48,
            'total_hardcases' => 8,
            'position_order' => 1,
        ]);

        $this->assignment = SpkAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_number' => 'SPK-TEST-PAYROLL-01',
            'departure_status' => 'pending',
            'return_status' => 'pending',
        ]);

        $this->crew = Crew::create([
            'nickname' => 'Boby',
            'full_name' => 'Boby Wijaya Pratama',
            'phone' => '081298765432',
            'positions' => ['Operator', 'Crew'],
            'employment_type' => 'freelance_outsourcing',
            'default_fee_loading_in' => 125000,
            'default_fee_loading_out' => 125000,
            'default_fee_meal' => 30000,
            'default_fee_standby' => 75000,
            'bank_name' => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Boby Wijaya',
            'is_active' => true,
        ]);

        $this->vehicle = Vehicle::create([
            'name' => 'TRAGA 1',
            'plate_number' => 'N 8888 OV',
            'type' => 'pickup',
            'is_active' => true,
        ]);
    }

    public function test_crew_can_be_created_with_default_rate_card_and_bank_account(): void
    {
        $response = $this->actingAs($this->user)->post(route('crews.store'), [
            'nickname' => 'Dimas',
            'full_name' => 'Dimas Arya Pamungkas',
            'phone' => '081333444555',
            'positions' => ['Teknisi', 'Supir'],
            'employment_type' => 'freelance_outsourcing',
            'default_fee_loading_in' => 150000,
            'default_fee_loading_out' => 150000,
            'default_fee_meal' => 35000,
            'default_fee_standby' => 100000,
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '9876543210123',
            'bank_account_holder' => 'Dimas Arya',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('crews.index'));
        $this->assertDatabaseHas('crews', [
            'nickname' => 'Dimas',
            'default_fee_loading_in' => 150000,
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '9876543210123',
        ]);
    }

    public function test_crew_can_be_updated_with_new_rate_card_and_bank_details(): void
    {
        $response = $this->actingAs($this->user)->put(route('crews.update', $this->crew), [
            'nickname' => 'Boby',
            'full_name' => 'Boby Wijaya Pratama Updated',
            'phone' => '081298765432',
            'positions' => ['Operator'],
            'employment_type' => 'freelance_outsourcing',
            'default_fee_loading_in' => 175000,
            'default_fee_loading_out' => 175000,
            'default_fee_meal' => 40000,
            'default_fee_standby' => 80000,
            'bank_name' => 'Seabank',
            'bank_account_number' => '9012345678',
            'bank_account_holder' => 'Boby Wijaya Pratama',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('crews.index'));
        $this->assertDatabaseHas('crews', [
            'id' => $this->crew->id,
            'default_fee_loading_in' => 175000,
            'bank_name' => 'Seabank',
            'bank_account_number' => '9012345678',
        ]);
    }

    public function test_can_assign_crew_to_event_with_modular_fees_and_calculate_total(): void
    {
        $response = $this->actingAs($this->user)->post(route('spk.crews.store', $this->assignment), [
            'crew_id' => $this->crew->id,
            'role_label' => 'Operator LED Utama',
            'fee_loading_in' => 125000,
            'fee_loading_out' => 125000,
            'fee_meal' => 30000,
            'fee_standby' => 75000,
            'fee_transport' => 50000,
            'fee_bonus' => 20000,
            'status' => 'offered',
            'notes' => 'Pegang Novastar VX1000',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('event_crew_assignments', [
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Operator LED Utama',
            'total_fee' => 425000, // 125k + 125k + 30k + 75k + 50k + 20k
            'status' => 'offered',
            'payment_status' => 'unpaid',
        ]);
    }

    public function test_can_update_crew_assignment_fee_and_status(): void
    {
        $assignment = EventCrewAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Teknisi Rigging',
            'fee_loading_in' => 100000,
            'fee_loading_out' => 100000,
            'fee_meal' => 25000,
            'fee_standby' => 50000,
            'status' => 'offered',
        ]);

        $response = $this->actingAs($this->user)->put(route('spk.crews.update', $assignment), [
            'role_label' => 'PIC & Operator',
            'fee_loading_in' => 150000,
            'fee_loading_out' => 150000,
            'fee_meal' => 30000,
            'fee_standby' => 70000,
            'fee_transport' => 0,
            'fee_bonus' => 0,
            'status' => 'acc',
            'payment_status' => 'unpaid',
            'notes' => 'Telah di-ACC oleh kru',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('event_crew_assignments', [
            'id' => $assignment->id,
            'role_label' => 'PIC & Operator',
            'total_fee' => 400000,
            'status' => 'acc',
        ]);
    }

    public function test_can_delete_crew_assignment(): void
    {
        $ca = EventCrewAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Helper',
            'fee_loading_in' => 50000,
            'status' => 'offered',
        ]);

        $response = $this->actingAs($this->user)->delete(route('spk.crews.destroy', $ca));
        $response->assertSessionHas('status');
        $this->assertDatabaseMissing('event_crew_assignments', ['id' => $ca->id]);
    }

    public function test_can_add_multiple_vehicles_to_event_with_checklists(): void
    {
        // Add Vehicle 1 (TRAGA 1)
        $resp1 = $this->actingAs($this->user)->post(route('spk.vehicles.store', $this->assignment), [
            'vehicle_id' => $this->vehicle->id,
            'vehicle_name' => 'TRAGA 1',
            'plate_number' => 'N 8888 OV',
            'driver_name' => 'Pak Agus',
            'has_etoll' => 1,
            'has_kunci_ban' => 1,
            'has_stnk' => 1,
            'has_kir' => 1,
            'has_barcode_bbm' => 1,
            'notes' => 'Muat kabinet utama',
        ]);
        $resp1->assertSessionHas('status');

        // Add Vehicle 2 (GRAN MAX)
        $resp2 = $this->actingAs($this->user)->post(route('spk.vehicles.store', $this->assignment), [
            'vehicle_name' => 'GRAN MAX 2',
            'plate_number' => 'N 1234 CD',
            'driver_name' => 'Dimas',
            'has_etoll' => 1,
            'has_kunci_ban' => 1,
            'has_stnk' => 1,
            'has_kir' => 0,
            'has_barcode_bbm' => 1,
            'notes' => 'Muat kabel & aksesoris',
        ]);
        $resp2->assertSessionHas('status');

        $this->assertEquals(2, $this->assignment->assignedVehicles()->count());
        $this->assertDatabaseHas('event_vehicles', [
            'spk_assignment_id' => $this->assignment->id,
            'vehicle_name' => 'TRAGA 1',
            'driver_name' => 'Pak Agus',
        ]);
        $this->assertDatabaseHas('event_vehicles', [
            'spk_assignment_id' => $this->assignment->id,
            'vehicle_name' => 'GRAN MAX 2',
            'driver_name' => 'Dimas',
        ]);
    }

    public function test_can_update_and_delete_vehicle_assignment(): void
    {
        $ev = EventVehicle::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'vehicle_name' => 'L300 Sewaan',
            'driver_name' => 'Rudi',
            'has_etoll' => false,
        ]);

        $updateResp = $this->actingAs($this->user)->put(route('spk.vehicles.update', $ev), [
            'vehicle_name' => 'L300 Sewaan (Update)',
            'driver_name' => 'Rudi Santoso',
            'has_etoll' => 1,
            'has_kunci_ban' => 1,
            'has_stnk' => 1,
            'notes' => 'Saldo E-Toll sudah diisi 200k',
        ]);
        $updateResp->assertSessionHas('status');
        $this->assertDatabaseHas('event_vehicles', [
            'id' => $ev->id,
            'driver_name' => 'Rudi Santoso',
            'has_etoll' => true,
        ]);

        $delResp = $this->actingAs($this->user)->delete(route('spk.vehicles.destroy', $ev));
        $delResp->assertSessionHas('status');
        $this->assertDatabaseMissing('event_vehicles', ['id' => $ev->id]);
    }

    public function test_can_update_operational_costs_and_event_calculates_profit_margin(): void
    {
        // Create crew assignment with fee
        EventCrewAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Crew',
            'fee_loading_in' => 200000,
            'fee_loading_out' => 200000,
            'fee_meal' => 50000,
            'status' => 'acc',
        ]); // Total fee: 450,000

        // Update Operational Costs (BBM, Tol, Penginapan, Lain-lain)
        $response = $this->actingAs($this->user)->put(route('spk.costs.update', $this->assignment), [
            'cost_fuel' => 350000,
            'cost_toll' => 120000,
            'cost_lodging' => 500000,
            'cost_other' => 80000,
            'cost_other_notes' => 'Parkir Bromo & Lakban kabel',
        ]);

        $response->assertSessionHas('status');
        $this->order->refresh();

        $this->assertEquals(350000, $this->order->cost_fuel);
        $this->assertEquals(120000, $this->order->cost_toll);
        $this->assertEquals(500000, $this->order->cost_lodging);
        $this->assertEquals(80000, $this->order->cost_other);
        $this->assertEquals('Parkir Bromo & Lakban kabel', $this->order->cost_other_notes);

        // Verification of computed properties:
        // Total crew fees: 450,000
        // Total other operational costs: 350,000 + 120,000 + 500,000 + 80,000 = 1,050,000
        // Total Operational Cost: 450,000 + 1,050,000 = 1,500,000
        // Total Price: 15,000,000
        // Net Profit: 15,000,000 - 1,500,000 = 13,500,000
        $this->assertEquals(450000, $this->order->total_crew_fees);
        $this->assertEquals(1500000, $this->order->total_operational_cost);
        $this->assertEquals(13500000, $this->order->net_profit);
    }

    public function test_payroll_index_displays_metrics_and_filters(): void
    {
        EventCrewAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Operator',
            'fee_loading_in' => 150000,
            'fee_loading_out' => 150000,
            'status' => 'acc',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)->get(route('crew-payroll.index', [
            'month' => now()->format('Y-m'),
        ]));

        $response->assertOk();
        $response->assertSee('Rekap Honor & Payroll Kru Freelance');
        $response->assertSee($this->crew->nickname);
        $response->assertSee('Rp 300.000');
        $response->assertSee('Belum Lunas');
    }

    public function test_can_mark_crew_fee_as_paid_and_batch_mark_paid(): void
    {
        $ca1 = EventCrewAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Crew 1',
            'fee_loading_in' => 100000,
            'status' => 'acc',
            'payment_status' => 'unpaid',
        ]);

        $ca2 = EventCrewAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Crew 2',
            'fee_loading_in' => 200000,
            'status' => 'acc',
            'payment_status' => 'unpaid',
        ]);

        // Single Mark Paid
        $singleResp = $this->actingAs($this->user)->post(route('crew-payroll.mark-paid', $ca1), [
            'payment_method' => 'transfer',
            'payment_date' => now()->toDateString(),
            'payment_reference' => 'TRF-BCA-98124',
            'notes' => 'Lunas via m-BCA',
        ]);
        $singleResp->assertSessionHas('success');
        $this->assertDatabaseHas('event_crew_assignments', [
            'id' => $ca1->id,
            'payment_status' => 'paid',
            'payment_reference' => 'TRF-BCA-98124',
        ]);

        // Batch Mark Paid
        $batchResp = $this->actingAs($this->user)->post(route('crew-payroll.batch-mark-paid'), [
            'assignment_ids' => [$ca2->id],
            'payment_method' => 'transfer',
            'payment_date' => now()->toDateString(),
            'payment_reference' => 'BATCH-PAYROLL-01',
        ]);
        $batchResp->assertSessionHas('success');
        $this->assertDatabaseHas('event_crew_assignments', [
            'id' => $ca2->id,
            'payment_status' => 'paid',
            'payment_reference' => 'BATCH-PAYROLL-01',
        ]);
    }

    public function test_can_view_printable_individual_slip_and_monthly_slip(): void
    {
        $ca = EventCrewAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Operator LED',
            'fee_loading_in' => 125000,
            'fee_loading_out' => 125000,
            'fee_meal' => 30000,
            'fee_standby' => 75000,
            'status' => 'acc',
            'payment_status' => 'paid',
            'payment_method' => 'transfer',
            'payment_date' => now()->toDateString(),
        ]);

        // Individual Slip
        $slipResp = $this->actingAs($this->user)->get(route('crew-payroll.slip', $ca));
        $slipResp->assertOk();
        $slipResp->assertSee('SLIP HONOR KRU');
        $slipResp->assertSee($this->crew->nickname);
        $slipResp->assertSee($this->crew->bank_account_number);
        $slipResp->assertSee('Rp 355.000');

        // Monthly Slip
        $monthlyResp = $this->actingAs($this->user)->get(route('crew-payroll.crew-monthly-slip', [
            'crew' => $this->crew->id,
            'month' => now()->format('Y-m'),
        ]));
        $monthlyResp->assertOk();
        $monthlyResp->assertSee('REKAP HONOR BULANAN');
        $monthlyResp->assertSee($this->crew->full_name);
        $monthlyResp->assertSee('Festival Musik Bromo 2026');
    }

    public function test_spk_show_and_print_render_with_multi_vehicles_and_crews(): void
    {
        EventVehicle::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'vehicle_name' => 'TRAGA 1',
            'plate_number' => 'N 8888 OV',
            'driver_name' => 'Pak Agus',
            'has_etoll' => true,
        ]);

        EventCrewAssignment::create([
            'event_order_id' => $this->order->id,
            'spk_assignment_id' => $this->assignment->id,
            'crew_id' => $this->crew->id,
            'role_label' => 'Operator',
            'fee_loading_in' => 100000,
            'status' => 'acc',
        ]);

        $showResp = $this->actingAs($this->user)->get(route('spk.show', $this->assignment));
        $showResp->assertOk();
        $showResp->assertSee('Armada Kendaraan Logistik');
        $showResp->assertSee('TRAGA 1');
        $showResp->assertSee($this->crew->nickname);
        $showResp->assertSee('Kalkulasi Margin & Laba Event');

        $printResp = $this->actingAs($this->user)->get(route('spk.print', $this->assignment));
        $printResp->assertOk();
        $printResp->assertSee('TRAGA 1');
        $printResp->assertSee($this->crew->nickname);
    }
}

