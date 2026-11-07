<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GAInventoryAndCustomerVerificationTest extends TestCase
{
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Superuser']);
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin-ga-test@omahvisual.com'],
            [
                'name' => 'Admin GA Test',
                'password' => bcrypt('password'),
            ]
        );
        $this->adminUser->syncRoles([$role]);
    }

    public function test_ga_inventory_sync_command_populates_data(): void
    {
        $this->artisan('inventory:sync-ga')
            ->assertSuccessful();

        // 1. Cek Armada Kendaraan
        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'W 8514 DV',
            'name' => 'Isuzu Traga 01',
        ]);
        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'N 8221 BK',
            'name' => 'Isuzu Elf',
        ]);

        // 2. Cek Barang Sewa / Rental Operasional
        $this->assertDatabaseHas('products', [
            'name' => 'Kabinet SAGA 100 x 50',
            'is_rental_item' => true,
        ]);
        $saga = Product::where('name', 'Kabinet SAGA 100 x 50')->first();
        $this->assertNotNull($saga);
        $this->assertEquals(0.50, (float) $saga->sqm_per_unit);
        $this->assertGreaterThan(0, $saga->total_available_stock);

        // 3. Cek Aset Internal Kantor
        $this->assertDatabaseHas('products', [
            'name' => 'Dispenser Arashi',
            'is_rental_item' => false,
        ]);

        // 4. Cek Processors Novastar
        $this->assertDatabaseHas('products', [
            'code' => 'PROC-VX4',
            'is_rental_item' => true,
            'tracking_type' => 'individual',
        ]);
        $vx4 = Product::where('code', 'PROC-VX4')->first();
        $this->assertGreaterThanOrEqual(1, $vx4->assets()->count());
    }

    public function test_product_index_filters_by_item_type(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('products.index', ['item_type' => 'rental', 'search' => 'Kabinet SAGA 100 x 50']))
            ->assertOk()
            ->assertSee('Kabinet SAGA 100 x 50')
            ->assertDontSee('Dispenser Arashi');

        $this->actingAs($this->adminUser)
            ->get(route('products.index', ['item_type' => 'internal', 'search' => 'Dispenser Arashi']))
            ->assertOk()
            ->assertSee('Dispenser Arashi')
            ->assertDontSee('Kabinet SAGA 100 x 50');
    }

    public function test_admin_can_create_customer_with_salutation_and_photo(): void
    {
        Storage::fake('public');

        $uniqueName = 'Edi Evio '.uniqid();
        $photo = UploadedFile::fake()->image('client_edi.jpg', 400, 400);

        $response = $this->actingAs($this->adminUser)
            ->post(route('customers.store'), [
                'salutation' => 'Mas',
                'name' => $uniqueName,
                'full_name' => 'Edi Purnomo',
                'phone' => '081804010484',
                'organization' => 'Evio Multimedia',
                'category' => 'Event Organizer (EO)',
                'address' => 'Jl. Palagan Tentara Pelajar 85A',
                'city' => 'Malang',
                'province' => 'Jawa Timur',
                'pic_secondary_name' => 'Mbak Rina',
                'pic_secondary_phone' => '081987654321',
                'pic_secondary_relation' => 'Asisten Manager',
                'verification_status' => 'verified',
                'photo' => $photo,
            ]);

        $response->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'salutation' => 'Mas',
            'name' => $uniqueName,
            'organization' => 'Evio Multimedia',
            'verification_status' => 'verified',
            'pic_secondary_name' => 'Mbak Rina',
        ]);

        $customer = Customer::where('name', $uniqueName)->first();
        $this->assertNotNull($customer->photo_path);
        Storage::disk('public')->assertExists($customer->photo_path);
    }

    public function test_guest_can_self_register_via_public_form(): void
    {
        Storage::fake('public');

        $uniqueName = 'Budi Santoso '.uniqid();

        // 1. Visit public form
        $this->get(route('customers.public-register'))
            ->assertOk()
            ->assertSee('Registrasi Klien & Vendor Event')
            ->assertSee('Nama Sapaan Akrab');

        // 2. Submit form as guest (unauthenticated)
        $photo = UploadedFile::fake()->image('klien_self.png', 300, 300);

        $response = $this->post(route('customers.public-register.store'), [
            'salutation' => 'Bapak',
            'name' => $uniqueName,
            'full_name' => 'Budi Santoso, M.M.',
            'phone' => '081234567899',
            'organization' => 'Budi Event Organizer',
            'category' => 'Event Organizer (EO)',
            'address' => 'Jl. Ijen No. 12',
            'city' => 'Kota Malang',
            'province' => 'Jawa Timur',
            'pic_secondary_name' => 'Pak Joko',
            'pic_secondary_phone' => '081333444555',
            'pic_secondary_relation' => 'PIC Lapangan',
            'photo' => $photo,
        ]);

        $customer = Customer::where('name', $uniqueName)->first();
        $this->assertNotNull($customer);
        $this->assertEquals('pending_verification', $customer->verification_status);
        $this->assertEquals('Bapak', $customer->salutation);
        $this->assertNotNull($customer->photo_path);
        Storage::disk('public')->assertExists($customer->photo_path);

        $response->assertRedirect(route('customers.public-register.success', ['code' => $customer->customer_code]));

        // 3. Visit success confirmation page
        $this->get(route('customers.public-register.success', ['code' => $customer->customer_code]))
            ->assertOk()
            ->assertSee('Terima Kasih!')
            ->assertSee($customer->customer_code);

        // 4. Admin can quickly verify this pending customer
        $this->actingAs($this->adminUser)
            ->post(route('customers.verify', $customer))
            ->assertRedirect();

        $customer->refresh();
        $this->assertEquals('verified', $customer->verification_status);
        $this->assertEquals($this->adminUser->id, $customer->verified_by);
        $this->assertNotNull($customer->verified_at);
    }
}
