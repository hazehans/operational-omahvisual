<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\Invoice;
use App\Models\SpkAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleAndPermissionAccessTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superuser;

    protected User $owner;

    protected User $managerOps;

    protected User $adminFinance;

    protected User $finance;

    protected User $warehouse;

    protected User $kepalaServis;

    protected User $teknisi;

    protected Customer $customer;

    protected EventOrder $order;

    protected SpkAssignment $assignment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superuser = User::where('email', 'super@omahvisual.com')->firstOrFail();
        $this->owner = User::where('email', 'owner@omahvisual.com')->firstOrFail();
        $this->managerOps = User::where('email', 'manager.ops@omahvisual.com')->firstOrFail();
        $this->adminFinance = User::where('email', 'admin@omahvisual.com')->firstOrFail();
        $this->finance = User::where('email', 'finance@omahvisual.com')->firstOrFail();
        $this->warehouse = User::where('email', 'warehouse@omahvisual.com')->firstOrFail();
        $this->kepalaServis = User::where('email', 'kepala.servis@omahvisual.com')->firstOrFail();
        $this->teknisi = User::where('email', 'teknisi@omahvisual.com')->firstOrFail();

        $this->customer = Customer::firstOrCreate(
            ['phone' => '081299998888'],
            ['name' => 'PT Testing Mandiri Event', 'organization' => 'Test Vendor']
        );

        $this->order = EventOrder::firstOrCreate(
            ['order_number' => 'ORD-RBAC-TEST-001'],
            [
                'customer_id' => $this->customer->id,
                'event_name' => 'Event Pengujian RBAC',
                'venue_name' => 'Grand City Surabaya',
                'event_start_date' => now()->addDays(3)->toDateString(),
                'loading_date' => now()->addDays(2)->toDateString(),
                'loading_time' => '13:00',
                'stage_width_m' => 10,
                'stage_depth_m' => 6,
                'stage_height_m' => 0.6,
                'total_price' => 15000000,
                'status' => 'confirmed',
            ]
        );

        $this->assignment = SpkAssignment::firstOrCreate(
            ['event_order_id' => $this->order->id],
            [
                'spk_number' => 'SPK-RBAC-TEST-001',
                'departure_status' => 'pending',
                'return_status' => 'pending',
            ]
        );
    }

    public function test_superuser_and_owner_have_unrestricted_access(): void
    {
        // Superuser accesses all protected areas
        $this->actingAs($this->superuser)->get(route('orders.index'))->assertStatus(200);
        $this->actingAs($this->superuser)->get(route('invoices.index'))->assertStatus(200);
        $this->actingAs($this->superuser)->get(route('users.index'))->assertStatus(200);
        $this->actingAs($this->superuser)->get(route('audit-logs.index'))->assertStatus(200);

        // Owner accesses protected areas
        $this->actingAs($this->owner)->get(route('orders.index'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('invoices.index'))->assertStatus(200);
        $this->actingAs($this->owner)->get(route('users.index'))->assertStatus(200);
    }

    public function test_manager_operasional_can_manage_orders_and_spk_but_cannot_manage_invoices_or_users(): void
    {
        // Allowed: Orders and SPK
        $this->actingAs($this->managerOps)->get(route('orders.index'))->assertStatus(200);
        $this->actingAs($this->managerOps)->get(route('orders.create'))->assertStatus(200);
        $this->actingAs($this->managerOps)->get(route('spk.show', $this->assignment))->assertStatus(200);
        $this->actingAs($this->managerOps)->get(route('spk.print', $this->assignment))->assertStatus(200);
        $this->actingAs($this->managerOps)->get(route('crews.index'))->assertStatus(200);
        $this->actingAs($this->managerOps)->get(route('vehicles.index'))->assertStatus(200);

        // Forbidden: Invoices and User Management
        $this->actingAs($this->managerOps)->get(route('invoices.index'))->assertStatus(403);
        $this->actingAs($this->managerOps)->get(route('invoices.create'))->assertStatus(403);
        $this->actingAs($this->managerOps)->get(route('users.index'))->assertStatus(403);
    }

    public function test_pure_admin_led_cannot_manage_invoices_or_payroll_payout(): void
    {
        // Create a standalone user with ONLY 'Admin LED' (not Finance)
        $pureAdmin = User::factory()->create();
        $pureAdmin->assignRole('Admin LED');

        // Allowed: Orders, Customer, SPK Print
        $this->actingAs($pureAdmin)->get(route('orders.index'))->assertStatus(200);
        $this->actingAs($pureAdmin)->get(route('orders.create'))->assertStatus(200);
        $this->actingAs($pureAdmin)->get(route('spk.show', $this->assignment))->assertStatus(200);
        $this->actingAs($pureAdmin)->get(route('customers.index'))->assertStatus(200);

        // Allowed per revision: Delete/Cancel Order
        $tempOrder = EventOrder::create([
            'order_number' => 'ORD-TEMP-DEL-'.uniqid(),
            'customer_id' => $this->customer->id,
            'event_name' => 'Temp Event',
            'venue_name' => 'Temp Venue',
            'event_start_date' => now()->addDays(4)->toDateString(),
            'loading_date' => now()->addDays(3)->toDateString(),
            'loading_time' => '10:00',
            'status' => 'draft',
        ]);
        $this->actingAs($pureAdmin)->delete(route('orders.destroy', $tempOrder))->assertRedirect(route('orders.index'));

        // Forbidden: Invoices & Payroll mark-paid
        $this->actingAs($pureAdmin)->get(route('invoices.index'))->assertStatus(403);
        $this->actingAs($pureAdmin)->get(route('invoices.create'))->assertStatus(403);
        $this->actingAs($pureAdmin)->get(route('crew-payroll.index'))->assertStatus(403);
    }

    public function test_multi_role_admin_and_finance_can_manage_both_orders_and_invoices(): void
    {
        // User admin@omahvisual.com has both ['Admin LED', 'Finance']
        $this->actingAs($this->adminFinance)->get(route('orders.index'))->assertStatus(200);
        $this->actingAs($this->adminFinance)->get(route('orders.create'))->assertStatus(200);
        $this->actingAs($this->adminFinance)->get(route('invoices.index'))->assertStatus(200);
        $this->actingAs($this->adminFinance)->get(route('invoices.create'))->assertStatus(200);
        $this->actingAs($this->adminFinance)->get(route('crew-payroll.index'))->assertStatus(200);
    }

    public function test_finance_can_manage_invoices_and_payroll_but_cannot_create_orders_or_inventory(): void
    {
        // Allowed: Invoices & Payroll
        $this->actingAs($this->finance)->get(route('invoices.index'))->assertStatus(200);
        $this->actingAs($this->finance)->get(route('invoices.create'))->assertStatus(200);
        $this->actingAs($this->finance)->get(route('crew-payroll.index'))->assertStatus(200);

        // Forbidden: Create Orders & Inventory movements
        $this->actingAs($this->finance)->get(route('orders.create'))->assertStatus(403);
        $this->actingAs($this->finance)->get(route('movements.create'))->assertStatus(403);
        $this->actingAs($this->finance)->get(route('users.index'))->assertStatus(403);
    }

    public function test_warehouse_can_manage_inventory_and_gate_checklists_but_cannot_create_orders_or_invoices(): void
    {
        // Allowed: Inventory, SPK gate departure/return
        $this->actingAs($this->warehouse)->get(route('products.index'))->assertStatus(200);
        $this->actingAs($this->warehouse)->get(route('movements.index'))->assertStatus(200);
        $this->actingAs($this->warehouse)->get(route('initial-counts.index'))->assertStatus(200);
        $this->actingAs($this->warehouse)->get(route('spk.show', $this->assignment))->assertStatus(200);

        // Forbidden: Create Order & Invoices
        $this->actingAs($this->warehouse)->get(route('orders.create'))->assertStatus(403);
        $this->actingAs($this->warehouse)->get(route('invoices.index'))->assertStatus(403);
        $this->actingAs($this->warehouse)->get(route('crew-payroll.index'))->assertStatus(403);
        $this->actingAs($this->warehouse)->get(route('users.index'))->assertStatus(403);
    }

    public function test_kepala_servis_and_teknisi_access_boundaries(): void
    {
        // Kepala Servis: Service tickets access
        $this->actingAs($this->kepalaServis)->get(route('service-tickets.index'))->assertStatus(200);
        $this->actingAs($this->kepalaServis)->get(route('service-tickets.create'))->assertStatus(200);

        // Forbidden for Kepala Servis: Orders, Invoices, Payroll, Users
        $this->actingAs($this->kepalaServis)->get(route('orders.index'))->assertStatus(403);
        $this->actingAs($this->kepalaServis)->get(route('invoices.index'))->assertStatus(403);
        $this->actingAs($this->kepalaServis)->get(route('crew-payroll.index'))->assertStatus(403);
        $this->actingAs($this->kepalaServis)->get(route('users.index'))->assertStatus(403);

        // Teknisi Servis: View tickets
        $this->actingAs($this->teknisi)->get(route('service-tickets.index'))->assertStatus(200);
        $this->actingAs($this->teknisi)->get(route('orders.index'))->assertStatus(403);
        $this->actingAs($this->teknisi)->get(route('invoices.index'))->assertStatus(403);
    }
}

