<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\Customer;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MasterDataManagementTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('email', 'admin@omahvisual.com')->first() ?? User::factory()->create();
    }

    public function test_user_can_create_customer_with_detailed_fields(): void
    {
        $uniqueName = 'Hans Dimas '.uniqid();

        $response = $this->actingAs($this->user)->post(route('customers.store'), [
            'name' => $uniqueName,
            'full_name' => 'Hans Dimas Pratama, S.Kom',
            'birth_date' => '1995-08-17',
            'phone' => '081234567890',
            'email' => 'hans@bless-eo.com',
            'organization' => 'Bless Event Organizer',
            'category' => 'Event Organizer (EO)',
            'province' => 'Jawa Timur',
            'city' => 'Kota Malang',
            'address' => 'Jl. Ijen No. 45, Klojen',
            'pic_secondary_name' => 'Rina Keuangan',
            'pic_secondary_phone' => '081987654321',
            'pic_secondary_relation' => 'Bendahara & Operasional',
            'registration_date' => '2026-10-16',
            'notes' => 'Klien prioritas LED videotron P3.9',
        ]);

        $response->assertRedirect(route('customers.index'));

        $customer = Customer::where('name', $uniqueName)->first();
        $this->assertNotNull($customer);
        $this->assertEquals('Hans Dimas Pratama, S.Kom', $customer->full_name);
        $this->assertEquals('Jawa Timur', $customer->province);
        $this->assertEquals('Event Organizer (EO)', $customer->category);
        $this->assertEquals('Rina Keuangan', $customer->pic_secondary_name);
        $this->assertStringStartsWith('CUST-', $customer->customer_code);
    }

    public function test_user_can_create_crew_with_multiple_positions_and_uploads(): void
    {
        Storage::fake('public');

        $uniqueNickname = 'Boby Tech '.uniqid();
        $photo = UploadedFile::fake()->image('profile.jpg');
        $ktp = UploadedFile::fake()->image('ktp.jpg');
        $rateCard = UploadedFile::fake()->image('rate_card.png');

        $response = $this->actingAs($this->user)->post(route('crews.store'), [
            'nickname' => $uniqueNickname,
            'full_name' => 'Boby Pratama Wijaya',
            'birth_date' => '1998-04-12',
            'phone' => '081299988877',
            'emergency_phone' => '081344455566',
            'email' => 'boby@gmail.com',
            'positions' => ['PIC', 'Operator', 'Supir'],
            'employment_type' => 'freelance_outsourcing',
            'hris_employee_id' => 'EXT-BOBY-01',
            'join_date' => '2026-01-10',
            'city' => 'Malang',
            'address' => 'Jl. Soekarno Hatta',
            'is_active' => '1',
            'photo' => $photo,
            'ktp_photo' => $ktp,
            'rate_card_photo' => $rateCard,
        ]);

        $response->assertRedirect(route('crews.index'));

        $crew = Crew::where('nickname', $uniqueNickname)->first();
        $this->assertNotNull($crew);
        $this->assertEquals(['PIC', 'Operator', 'Supir'], $crew->positions);
        $this->assertStringContainsString('PIC, Operator, Supir', $crew->position);
        $this->assertEquals('freelance_outsourcing', $crew->employment_type);
        $this->assertNotNull($crew->photo_path);
        $this->assertNotNull($crew->ktp_photo_path);
        $this->assertNotNull($crew->rate_card_photo_path);
    }

    public function test_user_can_create_vehicle_with_status_and_documents(): void
    {
        Storage::fake('public');

        $uniqueVehName = 'TRAGA DIESEL '.uniqid();
        $photo = UploadedFile::fake()->image('traga.jpg');
        $stnk = UploadedFile::fake()->image('stnk.jpg');
        $etoll = UploadedFile::fake()->image('etoll.jpg');
        $bbm = UploadedFile::fake()->image('barcode_bbm.png');

        $response = $this->actingAs($this->user)->post(route('vehicles.store'), [
            'name' => $uniqueVehName,
            'plate_number' => 'N 8844 CD',
            'type' => 'Pick Up Box',
            'status' => 'tersedia',
            'special_condition' => 'Normal (Oli Baru Diganti)',
            'has_etoll' => '1',
            'has_kunci_ban' => '1',
            'has_stnk' => '1',
            'has_kir' => '1',
            'has_barcode_bbm' => '1',
            'is_active' => '1',
            'photo' => $photo,
            'stnk_photo' => $stnk,
            'etoll_photo' => $etoll,
            'barcode_bbm_photo' => $bbm,
        ]);

        $response->assertRedirect(route('vehicles.index'));

        $vehicle = Vehicle::where('name', $uniqueVehName)->first();
        $this->assertNotNull($vehicle);
        $this->assertEquals('tersedia', $vehicle->status);
        $this->assertEquals('Normal (Oli Baru Diganti)', $vehicle->special_condition);
        $this->assertNotNull($vehicle->photo_path);
        $this->assertNotNull($vehicle->stnk_photo_path);
        $this->assertNotNull($vehicle->etoll_photo_path);
        $this->assertNotNull($vehicle->barcode_bbm_photo_path);
        $this->assertStringStartsWith('VH-', $vehicle->vehicle_code);
    }
}

