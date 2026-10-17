<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\OrderLedScreen;
use App\Models\SpkAssignment;
use App\Services\LedCalculationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LedCalculationServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_calculates_standard_screen_dimensions_accurately(): void
    {
        $service = new LedCalculationService;

        // 6m x 3m P3.9 SAGA screen
        $calc = $service->calculateScreen(6.0, 3.0, 'P3.9', 'SAGA');

        $this->assertEquals(18.0, $calc['total_sqm']);
        $this->assertEquals(12, $calc['columns']); // 6m / 0.5m = 12 columns
        $this->assertEquals(36, $calc['cabinets_1m']); // 12 columns * 3m = 36 cabinets
        $this->assertEquals(0, $calc['cabinets_50cm']);
        $this->assertEquals(36, $calc['total_cabinets']);
        $this->assertEquals(6, $calc['hardcases_1m']); // 36 / 6 = 6 flight cases
        $this->assertEquals(6, $calc['total_hardcases']);
        $this->assertEquals(36, $calc['power_jumpers_qty']);
        $this->assertEquals(36, $calc['lan_jumpers_qty']);
        $this->assertStringContainsString('Novastar VX400 / VX4S', $calc['processor_model']);
        $this->assertGreaterThanOrEqual(2, $calc['spare_modules_qty']);
    }

    public function test_it_calculates_half_meter_screens_with_50cm_cabinets(): void
    {
        $service = new LedCalculationService;

        // 4m x 2.5m P3.9 MAVIC screen
        $calc = $service->calculateScreen(4.0, 2.5, 'P3.9', 'MAVIC');

        $this->assertEquals(10.0, $calc['total_sqm']);
        $this->assertEquals(8, $calc['columns']); // 4m / 0.5m = 8 columns
        $this->assertEquals(16, $calc['cabinets_1m']); // 8 * 2m = 16 1m cabinets
        $this->assertEquals(8, $calc['cabinets_50cm']); // 8 * 0.5m = 8 50cm cabinets
        $this->assertEquals(24, $calc['total_cabinets']);
        $this->assertGreaterThan(0, $calc['hardcases_50cm']);
    }

    public function test_it_generates_standard_spk_checklists_for_page_2_and_page_3(): void
    {
        $service = new LedCalculationService;

        $customer = Customer::factory()->create();
        $order = EventOrder::factory()->create(['customer_id' => $customer->id]);
        OrderLedScreen::factory()->create([
            'event_order_id' => $order->id,
            'width_m' => 6.0,
            'height_m' => 3.0,
            'total_sqm' => 18.0,
            'cabinets_1m' => 36,
            'cabinets_50cm' => 0,
            'total_cabinets' => 36,
            'total_hardcases' => 6,
            'brand_series' => 'SAGA',
            'rigging_method' => 'ground_stake',
        ]);

        $assignment = SpkAssignment::factory()->create([
            'event_order_id' => $order->id,
            'spk_number' => 'SPK-2026-'.fake()->unique()->numerify('#####'),
        ]);

        // 1. Test Essential template (Default)
        $service->generateStandardChecklist($assignment, $order, 'essential');

        $page2Items = $assignment->checklistItems()->where('page_number', 2)->get();
        $this->assertNotEmpty($page2Items);
        $this->assertTrue($page2Items->contains('item_name', 'Besi Ground dan Kabel Grounding 50 Mtr'));
        $this->assertTrue($page2Items->contains('item_name', 'Toolbox Operasional Teknisi (Standar 13 Items)'));

        $page3Items = $assignment->checklistItems()->where('page_number', 3)->get();
        $this->assertNotEmpty($page3Items);
        $this->assertTrue($page3Items->contains('item_name', 'Laptop Operator'));

        // 2. Test Full template
        $service->generateStandardChecklist($assignment, $order, 'full');

        $fullPage2Items = $assignment->checklistItems()->where('page_number', 2)->get();
        $this->assertTrue($fullPage2Items->contains('item_name', 'Tang Kombinasi'));

        $fullPage3Items = $assignment->checklistItems()->where('page_number', 3)->get();
        $this->assertTrue($fullPage3Items->contains('item_name', 'Hammock'));

        // 3. Test Empty template
        $service->generateStandardChecklist($assignment, $order, 'empty');
        $this->assertEquals(0, $assignment->checklistItems()->count());
    }
}
