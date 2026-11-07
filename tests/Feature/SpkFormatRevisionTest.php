<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\Customer;
use App\Models\EventCrewAssignment;
use App\Models\EventOrder;
use App\Models\OrderLedScreen;
use App\Models\SpkAssignment;
use App\Models\User;
use App\Services\LedCalculationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SpkFormatRevisionTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('email', 'admin@omahvisual.com')->first() ?? User::factory()->create();
    }

    public function test_spk_page_1_strictly_matches_reference_and_hides_unwanted_columns(): void
    {
        $customer = Customer::create([
            'salutation' => 'Mas',
            'name' => 'Edi '.uniqid(),
            'phone' => '081804010484',
            'organization' => 'Evio Multimedia',
            'address' => 'Jl. Palagan Tentara Pelajar Gg. Sedan Asri 85A',
        ]);

        $order = EventOrder::create([
            'order_number' => 'ORD-439-'.uniqid(),
            'customer_id' => $customer->id,
            'event_name' => 'Dinner Gathering',
            'event_type' => 'Dinner',
            'venue_name' => 'Dialoogi Signature Restaurant Malang',
            'venue_address' => 'Malang, Jawa Timur',
            'loading_date' => now()->format('Y-m-d'),
            'loading_time' => '22:00',
            'event_start_date' => now()->addDay()->format('Y-m-d'),
            'event_end_date' => now()->addDay()->format('Y-m-d'),
            'event_time' => '18.00-22.00',
            'jam_acara' => '18:00:00',
            'notes' => 'level 1m',
            'status' => 'confirmed',
            'vehicle_loading_in' => 'TRAGA 1',
        ]);

        // Screen 3 x 1.5m = 4.5 SQM
        OrderLedScreen::create([
            'event_order_id' => $order->id,
            'screen_name' => 'Main Screen',
            'brand_series' => 'SAGA',
            'pixel_pitch' => 'P3.9',
            'width_m' => 3.0,
            'height_m' => 1.5,
            'total_sqm' => 4.5,
            'cabinets_1m' => 6,
            'cabinets_50cm' => 6,
            'total_cabinets' => 12,
            'total_hardcases' => 3,
            'processor_model' => 'Novastar VX600',
            'rigging_method' => 'ground_stake',
        ]);

        $crewPic = Crew::create([
            'crew_code' => 'CRW-'.uniqid(),
            'nickname' => 'Anwar',
            'full_name' => 'Anwar Subekti',
            'phone' => '085755739364',
            'is_active' => true,
        ]);

        $crew2 = Crew::create([
            'crew_code' => 'CRW-'.uniqid(),
            'nickname' => 'Hamdan',
            'full_name' => 'Hamdan Yuwono',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $assignment = SpkAssignment::create([
            'event_order_id' => $order->id,
            'spk_number' => 'SPK-TEST-'.uniqid(),
            'crew_pic_event_name' => 'Anwar',
            'departure_pic_warehouse_name' => 'Byan',
            'return_pic_warehouse_name' => 'Byan',
        ]);

        EventCrewAssignment::create([
            'event_order_id' => $order->id,
            'spk_assignment_id' => $assignment->id,
            'crew_id' => $crewPic->id,
            'role_label' => 'PIC',
        ]);

        EventCrewAssignment::create([
            'event_order_id' => $order->id,
            'spk_assignment_id' => $assignment->id,
            'crew_id' => $crew2->id,
            'role_label' => 'Operator',
        ]);

        $response = $this->actingAs($this->user)->get(route('spk.print', $assignment));
        $response->assertOk();

        // Must see Page 1 official elements
        $response->assertSee('SURAT PERINTAH KERJA & IZIN BARANG KELUAR', false);
        $response->assertSee('Mas '.$customer->name);
        $response->assertSee('Evio Multimedia');
        $response->assertSee('Dialoogi Signature Restaurant Malang');
        $response->assertSee('Dinner');
        $response->assertSee('Anwar');
        $response->assertSee('085755739364');
        $response->assertSee('Share lokasi :');
        $response->assertSee('level 1m');

        // Clean decimal formatting (4,5 and 3x1,5 with comma)
        $response->assertSee('4,5');
        $response->assertSee('3x1,5');

        // Signatures
        $response->assertSee('Penanggung Jawab');
        $response->assertSee('Barang Keluar');
        $response->assertSee('Barang Kembali');
        $response->assertSee('Bagian Oprasional');
        $response->assertSee('Byan');
        $response->assertSee('Dani FT');

        // Page 1 MUST NOT show Hardcase, Processor Video, Total Kabinet, or Vehicle detail in Page 1 table
        // We verify that the Spesifikasi Order table on Page 1 does not contain these columns:
        $page1Content = substr($response->getContent(), 0, strpos($response->getContent(), 'id="page-2"'));
        $this->assertStringNotContainsString('Hardcase', $page1Content);
        $this->assertStringNotContainsString('Processor Video', $page1Content);
        $this->assertStringNotContainsString('Total Kabinet', $page1Content);
        $this->assertStringNotContainsString('TRAGA 1', $page1Content);
        $this->assertStringNotContainsString('E-Toll', $page1Content);
        $this->assertStringNotContainsString('Dengan menandatangani checklist ini, berarti kamu bertanggung jawab penuh', $page1Content);
    }

    public function test_standard_checklist_generator_groups_per_area_and_obfuscates_brand(): void
    {
        $customer = Customer::factory()->create();
        $order = EventOrder::create([
            'order_number' => 'ORD-AREA-'.uniqid(),
            'customer_id' => $customer->id,
            'event_name' => 'Konser Megah',
            'venue_name' => 'Stadion Mandala Krida',
            'loading_date' => now()->format('Y-m-d'),
            'event_start_date' => now()->addDay()->format('Y-m-d'),
            'status' => 'confirmed',
        ]);

        // Area 1: SAGA (should become SG)
        OrderLedScreen::create([
            'event_order_id' => $order->id,
            'screen_name' => 'Layar Tengah',
            'brand_series' => 'SAGA',
            'pixel_pitch' => 'P3.9',
            'width_m' => 6.0,
            'height_m' => 3.0,
            'total_sqm' => 18.0,
            'cabinets_1m' => 36,
            'cabinets_50cm' => 0,
            'total_cabinets' => 36,
            'total_hardcases' => 6,
            'processor_model' => 'Novastar VX600',
            'rigging_method' => 'ground_stake',
        ]);

        // Area 2: MAVIC (should become MV)
        OrderLedScreen::create([
            'event_order_id' => $order->id,
            'screen_name' => 'Sayap Kiri',
            'brand_series' => 'MAVIC',
            'pixel_pitch' => 'P3.9',
            'width_m' => 2.0,
            'height_m' => 3.0,
            'total_sqm' => 6.0,
            'cabinets_1m' => 12,
            'cabinets_50cm' => 0,
            'total_cabinets' => 12,
            'total_hardcases' => 2,
            'processor_model' => 'Novastar VX400',
            'rigging_method' => 'ground_stake',
        ]);

        $assignment = SpkAssignment::create([
            'event_order_id' => $order->id,
            'spk_number' => 'SPK-MULTI-'.uniqid(),
        ]);

        // Generate checklist
        app(LedCalculationService::class)->generateStandardChecklist($assignment, $order, 'essential');

        $items = $assignment->checklistItems()->get();
        $this->assertNotEmpty($items);

        // Verify brand obfuscation: SG and MV
        $ledItems = $items->where('category', 'LED & PERKABELAN UTAMA')->pluck('item_name')->implode(' | ');
        $this->assertStringContainsString('LED SG', $ledItems);
        $this->assertStringContainsString('LED MV', $ledItems);
        $this->assertStringNotContainsString('LED SAGA', $ledItems);
        $this->assertStringNotContainsString('LED MAVIC', $ledItems);

        // Verify Power Corn Jumper is generated per area
        $this->assertTrue($items->contains(fn ($i) => str_contains($i->item_name, 'Power Corn Jumper') && str_contains($i->item_name, 'Layar Tengah')));
        $this->assertTrue($items->contains(fn ($i) => str_contains($i->item_name, 'Power Corn Jumper') && str_contains($i->item_name, 'Sayap Kiri')));

        // Verify 14-item toolbox category is present
        $this->assertTrue($items->contains(fn ($i) => str_contains($i->category, 'TOOLS BOX No : isi 14 ITEM #WAJIB')));
        $this->assertTrue($items->contains(fn ($i) => str_contains($i->item_name, 'Tang Amper')));
        $this->assertTrue($items->contains(fn ($i) => str_contains($i->item_name, 'Terminal Sambung 200A')));

        // Verify spare parts & kits
        $this->assertTrue($items->contains(fn ($i) => str_contains($i->category, 'SPARE PART LED #WAJIB')));
        $this->assertTrue($items->contains(fn ($i) => str_contains($i->item_name, 'KIT SG')));
        $this->assertTrue($items->contains(fn ($i) => str_contains($i->item_name, 'KIT MV')));
    }

    public function test_technical_routing_4a_and_bast_page_render_correctly(): void
    {
        $customer = Customer::factory()->create();
        $order = EventOrder::create([
            'order_number' => 'ORD-ROUTING-'.uniqid(),
            'customer_id' => $customer->id,
            'event_name' => 'Expo Technopreneur 2026',
            'venue_name' => 'Jogja Expo Center',
            'loading_date' => now()->format('Y-m-d'),
            'event_start_date' => now()->addDay()->format('Y-m-d'),
            'status' => 'confirmed',
        ]);

        OrderLedScreen::create([
            'event_order_id' => $order->id,
            'screen_name' => 'Panggung Utama',
            'brand_series' => 'SAGA',
            'pixel_pitch' => 'P3.9',
            'width_m' => 8.0,
            'height_m' => 4.0,
            'total_sqm' => 32.0,
            'cabinets_1m' => 64,
            'cabinets_50cm' => 0,
            'total_cabinets' => 64,
            'total_hardcases' => 11,
            'processor_model' => 'Novastar VX1000',
            'rigging_method' => 'hanging',
        ]);

        $assignment = SpkAssignment::create([
            'event_order_id' => $order->id,
            'spk_number' => 'SPK-ROUTING-'.uniqid(),
        ]);

        app(LedCalculationService::class)->generateStandardChecklist($assignment, $order, 'essential');

        // Test web show view contains 5 tabs
        $showResponse = $this->actingAs($this->user)->get(route('spk.show', $assignment));
        $showResponse->assertOk();
        $showResponse->assertSee('Halaman 4: Routing Teknis 4A');
        $showResponse->assertSee('Halaman 5: Berita Acara (BAST)');

        // Test print view contains Page 4 (4A) and Page 5 (BAST)
        $printResponse = $this->actingAs($this->user)->get(route('spk.print', $assignment));
        $printResponse->assertOk();
        $printResponse->assertSee('4A ROUTING TEKNIS');
        $printResponse->assertSee('DIAGRAM PEMBAGIAN FASA PANEL LISTRIK 16A');
        $printResponse->assertSee('BERITA ACARA SERAH TERIMA (BAST)');
        $printResponse->assertSee('INSTALASI & UJI KELAYAKAN OPERASIONAL LED SCREEN');
        $printResponse->assertSee('Konstruksi & Rigging');
        $printResponse->assertSee('Tampilan Visual & Warna');
        $printResponse->assertSee('Sinyal Video & VJ Mapping');
        $printResponse->assertSee('Kelistrikan & Grounding Safety');
    }
}
