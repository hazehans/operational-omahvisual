<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\ServiceTicket;
use App\Models\StockBalance;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServiceTicketAndWorkshopTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected Location $mainGudang;

    protected Location $workshop;

    protected Location $lemariSparepart;

    protected Product $ledModule;

    protected Product $processorProd;

    protected Product $icDriver;

    protected Asset $processorAsset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'Hans Superuser']);
        $uid = uniqid();

        $this->mainGudang = Location::create([
            'name' => 'Gedung Utama '.$uid,
            'code' => 'LOC-GDG-'.$uid,
            'type' => 'building',
            'is_active' => true,
        ]);

        $this->workshop = Location::create([
            'name' => 'Area Workshop Meja Servis '.$uid,
            'code' => 'LOC-WS-'.$uid,
            'type' => 'locker',
            'parent_id' => $this->mainGudang->id,
            'is_active' => true,
        ]);

        $this->lemariSparepart = Location::create([
            'name' => 'Lemari 2 (Sparepart) '.$uid,
            'code' => 'LOC-LEM2-'.$uid,
            'type' => 'cabinet',
            'parent_id' => $this->mainGudang->id,
            'is_active' => true,
        ]);

        $catLed = Category::firstOrCreate(['slug' => 'led-screen'], ['name' => 'LED Screen', 'is_active' => true]);
        $catProc = Category::firstOrCreate(['slug' => 'video-processor'], ['name' => 'Video Processor', 'is_active' => true]);
        $catPart = Category::firstOrCreate(['slug' => 'komponen-elektronik'], ['name' => 'Komponen Elektronik', 'is_active' => true]);

        $unitPcs = Unit::firstOrCreate(['name' => 'Pieces'], ['symbol' => 'pcs']);
        $unitUnit = Unit::firstOrCreate(['name' => 'Unit'], ['symbol' => 'unit']);

        // Bulk LED product
        $this->ledModule = Product::create([
            'name' => 'Modul LED P3.9 Outdoor '.$uid,
            'code' => 'MOD-'.$uid,
            'category_id' => $catLed->id,
            'unit_id' => $unitPcs->id,
            'tracking_type' => 'bulk',
            'is_active' => true,
        ]);

        // Individual Processor product
        $this->processorProd = Product::create([
            'name' => 'Novastar VX600 '.$uid,
            'code' => 'PROC-'.$uid,
            'category_id' => $catProc->id,
            'unit_id' => $unitUnit->id,
            'tracking_type' => 'individual',
            'is_active' => true,
        ]);

        // Sparepart product
        $this->icDriver = Product::create([
            'name' => 'IC Driver ICN2053 '.$uid,
            'code' => 'IC-'.$uid,
            'category_id' => $catPart->id,
            'unit_id' => $unitPcs->id,
            'tracking_type' => 'bulk',
            'is_active' => true,
        ]);

        // Initial Stocks
        StockBalance::create([
            'product_id' => $this->ledModule->id,
            'location_id' => $this->mainGudang->id,
            'ownership' => 'company',
            'condition' => 'available',
            'quantity' => 100,
        ]);

        StockBalance::create([
            'product_id' => $this->icDriver->id,
            'location_id' => $this->lemariSparepart->id,
            'ownership' => 'company',
            'condition' => 'available',
            'quantity' => 50,
        ]);

        $this->processorAsset = Asset::create([
            'product_id' => $this->processorProd->id,
            'current_location_id' => $this->mainGudang->id,
            'asset_code' => 'OVL-AST-'.$uid,
            'serial_number' => 'SN-VX600-'.$uid,
            'ownership' => 'company',
            'condition' => 'available',
            'status' => 'in_storage',
        ]);
    }

    public function test_user_can_view_service_tickets_index_and_metrics(): void
    {
        $response = $this->actingAs($this->user)->get(route('service-tickets.index'));

        $response->assertStatus(200);
        $response->assertSee('Pemeliharaan Unit');
        $response->assertSee('Antrean Masuk');
        $response->assertSee('Uji Coba (QC)');
    }

    public function test_user_can_create_service_ticket_for_individual_asset(): void
    {
        $response = $this->actingAs($this->user)->post(route('service-tickets.store'), [
            'product_id' => $this->processorProd->id,
            'asset_id' => $this->processorAsset->id,
            'priority' => 'urgent',
            'quantity' => 1,
            'issue_description' => 'Port HDMI 2 tidak mengeluarkan gambar saat tes sinyal.',
            'source_location_id' => $this->mainGudang->id,
            'workshop_location_id' => $this->workshop->id,
        ]);

        $this->assertDatabaseHas('service_tickets', [
            'product_id' => $this->processorProd->id,
            'asset_id' => $this->processorAsset->id,
            'status' => 'pending_inspection',
            'priority' => 'urgent',
        ]);

        // Verifikasi aset otomatis masuk karantina maintenance di workshop
        $this->processorAsset->refresh();
        $this->assertEquals('in_service', $this->processorAsset->condition);
        $this->assertEquals('maintenance', $this->processorAsset->status);
        $this->assertEquals($this->workshop->id, $this->processorAsset->current_location_id);

        // Verifikasi mutasi transfer tercatat
        $this->assertDatabaseHas('stock_movements', [
            'type' => 'transfer',
            'source_location_id' => $this->mainGudang->id,
            'destination_location_id' => $this->workshop->id,
            'reference_type' => ServiceTicket::class,
        ]);

        $response->assertRedirect();
    }

    public function test_user_can_create_service_ticket_for_bulk_product(): void
    {
        $response = $this->actingAs($this->user)->post(route('service-tickets.store'), [
            'product_id' => $this->ledModule->id,
            'priority' => 'normal',
            'quantity' => 5,
            'issue_description' => '5 modul mati lampu baris merah pasca event.',
            'source_location_id' => $this->mainGudang->id,
            'workshop_location_id' => $this->workshop->id,
        ]);

        $this->assertDatabaseHas('service_tickets', [
            'product_id' => $this->ledModule->id,
            'quantity' => 5,
            'status' => 'pending_inspection',
        ]);

        // Saldo available di gudang utama berkurang dari 100 menjadi 95
        $srcBal = StockBalance::where('product_id', $this->ledModule->id)
            ->where('location_id', $this->mainGudang->id)
            ->where('condition', 'available')
            ->first();
        $this->assertEquals(95, (float) $srcBal->quantity);

        // Saldo in_service di workshop bertambah menjadi 5
        $wsBal = StockBalance::where('product_id', $this->ledModule->id)
            ->where('location_id', $this->workshop->id)
            ->where('condition', 'in_service')
            ->first();
        $this->assertEquals(5, (float) $wsBal->quantity);

        $response->assertRedirect();
    }

    public function test_technician_can_update_service_diagnosis_and_action(): void
    {
        $ticket = ServiceTicket::create([
            'ticket_number' => 'SRV-TEST-0001',
            'product_id' => $this->processorProd->id,
            'asset_id' => $this->processorAsset->id,
            'reported_by' => $this->user->id,
            'status' => 'in_progress',
            'priority' => 'normal',
            'quantity' => 1,
            'issue_description' => 'Tombol menu macet',
            'source_location_id' => $this->mainGudang->id,
            'workshop_location_id' => $this->workshop->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('service-tickets.update', $ticket), [
            'diagnosis' => 'Switch tact button aus akibat pemakaian rutin',
            'action_taken' => 'Ganti tact switch baru dan semprot contact cleaner',
            'priority' => 'high',
        ]);

        $ticket->refresh();
        $this->assertEquals('Switch tact button aus akibat pemakaian rutin', $ticket->diagnosis);
        $this->assertEquals('high', $ticket->priority);
        $response->assertRedirect();
    }

    public function test_technician_can_use_spareparts_which_deducts_from_inventory(): void
    {
        $ticket = ServiceTicket::create([
            'ticket_number' => 'SRV-TEST-0002',
            'product_id' => $this->ledModule->id,
            'reported_by' => $this->user->id,
            'status' => 'in_progress',
            'priority' => 'normal',
            'quantity' => 1,
            'issue_description' => 'IC Driver jebol',
            'source_location_id' => $this->mainGudang->id,
            'workshop_location_id' => $this->workshop->id,
        ]);

        // Ambil 3 buah IC driver dari Lemari 2
        $response = $this->actingAs($this->user)->post(route('service-tickets.spareparts.store', $ticket), [
            'sparepart_product_id' => $this->icDriver->id,
            'source_location_id' => $this->lemariSparepart->id,
            'quantity' => 3,
            'notes' => 'Pasang di baris 1, 2, 3',
        ]);

        $this->assertDatabaseHas('service_spareparts', [
            'service_ticket_id' => $ticket->id,
            'product_id' => $this->icDriver->id,
            'quantity' => 3,
        ]);

        // Saldo IC Driver di lemari berkurang dari 50 menjadi 47
        $icBal = StockBalance::where('product_id', $this->icDriver->id)
            ->where('location_id', $this->lemariSparepart->id)
            ->first();
        $this->assertEquals(47, (float) $icBal->quantity);

        // Verifikasi mutasi outbound tercatat
        $this->assertDatabaseHas('stock_movements', [
            'type' => 'outbound',
            'source_location_id' => $this->lemariSparepart->id,
            'reference_type' => ServiceTicket::class,
        ]);

        $response->assertRedirect();
    }

    public function test_completing_service_ticket_returns_asset_to_available_inventory(): void
    {
        $this->processorAsset->update([
            'condition' => 'in_service',
            'status' => 'maintenance',
            'current_location_id' => $this->workshop->id,
        ]);

        $ticket = ServiceTicket::create([
            'ticket_number' => 'SRV-TEST-0003',
            'product_id' => $this->processorProd->id,
            'asset_id' => $this->processorAsset->id,
            'reported_by' => $this->user->id,
            'status' => 'quality_control',
            'priority' => 'normal',
            'quantity' => 1,
            'issue_description' => 'Port kendur',
            'source_location_id' => $this->mainGudang->id,
            'workshop_location_id' => $this->workshop->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('service-tickets.update-status', $ticket), [
            'status' => 'completed',
            'return_location_id' => $this->mainGudang->id,
        ]);

        $ticket->refresh();
        $this->assertEquals('completed', $ticket->status);
        $this->assertNotNull($ticket->completion_date);
        $this->assertEquals($this->mainGudang->id, $ticket->return_location_id);

        // Aset kembali Bagus / Tersedia di Gedung Utama
        $this->processorAsset->refresh();
        $this->assertEquals('available', $this->processorAsset->condition);
        $this->assertEquals('in_storage', $this->processorAsset->status);
        $this->assertEquals($this->mainGudang->id, $this->processorAsset->current_location_id);

        // Mutasi pengembalian tercatat
        $this->assertDatabaseHas('stock_movements', [
            'type' => 'transfer',
            'source_location_id' => $this->workshop->id,
            'destination_location_id' => $this->mainGudang->id,
            'reference_type' => ServiceTicket::class,
        ]);

        $response->assertRedirect();
    }

    public function test_scrapping_service_ticket_marks_asset_as_damaged(): void
    {
        $ticket = ServiceTicket::create([
            'ticket_number' => 'SRV-TEST-0004',
            'product_id' => $this->processorProd->id,
            'asset_id' => $this->processorAsset->id,
            'reported_by' => $this->user->id,
            'status' => 'in_progress',
            'priority' => 'normal',
            'quantity' => 1,
            'issue_description' => 'Terbakar petir saat event',
            'source_location_id' => $this->mainGudang->id,
            'workshop_location_id' => $this->workshop->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('service-tickets.update-status', $ticket), [
            'status' => 'scrapped',
        ]);

        $ticket->refresh();
        $this->assertEquals('scrapped', $ticket->status);

        $this->processorAsset->refresh();
        $this->assertEquals('damaged', $this->processorAsset->condition);

        $response->assertRedirect();
    }
}
