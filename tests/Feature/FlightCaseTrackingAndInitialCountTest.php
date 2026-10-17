<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\OrderLedScreen;
use App\Models\Product;
use App\Models\SpkAssignment;
use App\Models\StockBalance;
use App\Models\User;
use App\Services\LedCalculationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FlightCaseTrackingAndInitialCountTest extends TestCase
{
    use DatabaseTransactions;

    protected User $warehouse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->warehouse = User::role('Warehouse')->first() ?? User::factory()->create();
    }

    public function test_only_flight_cases_have_individual_tracking_and_other_items_are_quantity(): void
    {
        // 1. Verify Flight Case products are individual
        $individualCodes = Product::where('tracking_type', 'individual')->pluck('code')->toArray();
        $this->assertContains('FC-CAB-P39-100X50', $individualCodes);
        $this->assertContains('FC-CAB-P39-50X50', $individualCodes);

        // 2. Verify Novastar, TV, and Toolbox are quantity tracking
        $novastar = Product::where('code', 'SYS-NOV-VX-SERIES')->first();
        $this->assertNotNull($novastar);
        $this->assertEquals('quantity', $novastar->tracking_type);

        $tv = Product::where('code', 'TV-DISP-50-SAMS')->first();
        $this->assertNotNull($tv);
        $this->assertEquals('quantity', $tv->tracking_type);

        $toolbox = Product::where('code', 'TLS-BOX-STD')->first();
        $this->assertNotNull($toolbox);
        $this->assertEquals('quantity', $toolbox->tracking_type);

        // 3. Verify Flight Case assets exist
        $fc100 = Product::where('code', 'FC-CAB-P39-100X50')->first();
        $this->assertGreaterThanOrEqual(12, $fc100->assets()->count());

        $fc50 = Product::where('code', 'FC-CAB-P39-50X50')->first();
        $this->assertGreaterThanOrEqual(6, $fc50->assets()->count());
    }

    public function test_warehouse_can_clear_initial_counts_session_and_input_manually(): void
    {
        $session = InventoryCount::firstOrFail();

        // 1. Clear session
        $clearResponse = $this->actingAs($this->warehouse)
            ->post(route('initial-counts.clear-items', $session));
        $clearResponse->assertRedirect(route('initial-counts.index'));
        $this->assertEquals(0, $session->items()->count());

        // 2. Add manual item
        $product = Product::where('code', 'LED-CAB-P39-100X50')->firstOrFail();
        $addResponse = $this->actingAs($this->warehouse)
            ->post(route('initial-counts.add-item', $session), [
                'product_id' => $product->id,
                'condition' => 'available',
                'ownership' => 'company',
                'physical_quantity' => 48,
                'notes' => 'Hitungan fisik mandiri dari nol',
            ]);

        $addResponse->assertRedirect(route('initial-counts.index'));
        $this->assertEquals(1, $session->items()->count());
        $item = $session->items()->first();
        $this->assertEquals(48, (float) $item->physical_quantity);

        // 3. Verify session & sync stock balance
        $verifyResponse = $this->actingAs($this->warehouse)
            ->post(route('initial-counts.verify', $session));
        $verifyResponse->assertRedirect(route('initial-counts.index'));

        $session->refresh();
        $this->assertEquals('verified', $session->status);

        $balance = StockBalance::where('product_id', $product->id)
            ->where('location_id', $session->location_id)
            ->where('condition', 'available')
            ->first();
        $this->assertNotNull($balance);
        $this->assertEquals(48.00, (float) $balance->quantity);
    }

    public function test_warehouse_can_delete_item_from_initial_count(): void
    {
        $session = InventoryCount::firstOrFail();
        $product = Product::firstOrFail();

        $item = InventoryCountItem::create([
            'inventory_count_id' => $session->id,
            'product_id' => $product->id,
            'condition' => 'available',
            'ownership' => 'company',
            'system_quantity' => 0,
            'physical_quantity' => 15,
            'difference' => 15,
            'is_verified' => false,
            'notes' => 'Item percobaan untuk dihapus',
        ]);

        $deleteResponse = $this->actingAs($this->warehouse)
            ->delete(route('initial-counts.delete-item', [$session, $item]));

        $deleteResponse->assertRedirect(route('initial-counts.index'));
        $this->assertDatabaseMissing('inventory_count_items', ['id' => $item->id]);
    }

    public function test_spk_checklist_includes_flight_cases_based_on_cabinet_quantities(): void
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
        ]);

        $assignment = SpkAssignment::factory()->create([
            'event_order_id' => $order->id,
            'spk_number' => 'SPK-2026-'.fake()->unique()->numerify('#####'),
        ]);

        $service->generateStandardChecklist($assignment, $order, 'essential');

        $page2Items = $assignment->checklistItems()->where('page_number', 2)->get();
        $fcItem = $page2Items->firstWhere('item_name', 'Flight Case Kabinet LED 100x50 cm (Isi 6)');
        $this->assertNotNull($fcItem);
        $this->assertEquals(6, $fcItem->quantity); // 36 kabinet / 6 = 6 flight cases
        $this->assertEquals('unit', $fcItem->unit);
    }
}
