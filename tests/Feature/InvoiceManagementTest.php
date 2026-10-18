<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\OrderLedScreen;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected Customer $customer;

    protected EventOrder $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('email', 'admin@omahvisual.com')->first() ?? User::factory()->create();

        $this->customer = Customer::create([
            'name' => 'PT Kreasi Visual Mandiri',
            'full_name' => 'PT Kreasi Visual Mandiri Perkasa',
            'phone' => '+6281234567890',
            'email' => 'finance@kreasivisual.com',
            'organization' => 'Kreasi EO Surabaya',
            'category' => 'Event Organizer (EO)',
            'city' => 'Surabaya',
        ]);

        $this->order = EventOrder::create([
            'order_number' => 'ORD-TEST-INV-'.rand(1000, 9999),
            'customer_id' => $this->customer->id,
            'event_name' => 'Konser Musik Spektakuler',
            'event_type' => 'indoor',
            'venue_name' => 'Grand City Convention Hall Surabaya',
            'loading_date' => now()->addDays(2)->toDateString(),
            'event_start_date' => now()->addDays(3)->toDateString(),
            'total_price' => 15000000,
            'down_payment' => 5000000,
            'down_payment_method' => 'BCA Muhammad Burhanudin',
            'paid_amount' => 0,
            'payment_status' => 'unpaid',
            'status' => 'confirmed',
        ]);

        OrderLedScreen::create([
            'event_order_id' => $this->order->id,
            'screen_name' => 'Main Screen',
            'brand_series' => 'SAGA',
            'pixel_pitch' => 'P3.9',
            'width_m' => 6.00,
            'height_m' => 4.00,
            'total_sqm' => 24.00,
            'cabinets_1m' => 48,
            'total_cabinets' => 48,
            'total_hardcases' => 6,
        ]);
    }

    public function test_can_view_invoices_index_with_metrics(): void
    {
        $response = $this->actingAs($this->user)->get(route('invoices.index'));

        $response->assertOk();
        $response->assertSee('Invoice & Penagihan Client');
        $response->assertSee('Total Ditagihkan');
        $response->assertSee('Total Diterima');
        $response->assertSee('Sisa Piutang');
    }

    public function test_can_view_invoices_create_page_with_prefilled_order(): void
    {
        $response = $this->actingAs($this->user)->get(route('invoices.create', ['order_id' => $this->order->id]));

        $response->assertOk();
        $response->assertSee('Terbitkan Invoice Baru');
        $response->assertSee($this->order->order_number);
        $response->assertSee($this->customer->name);
    }

    public function test_can_create_invoice_with_line_items_and_recalculate(): void
    {
        $response = $this->actingAs($this->user)->post(route('invoices.store'), [
            'event_order_id' => $this->order->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'invoice_type' => 'full',
            'title' => 'Tagihan Sewa LED Screen Konser Musik',
            'discount' => 500000,
            'tax_percent' => 11,
            'bank_info' => Invoice::DEFAULT_BANK_INFO,
            'terms_conditions' => Invoice::DEFAULT_TERMS,
            'items' => [
                [
                    'item_description' => 'Sewa LED Screen P3.9 SAGA (24.00 m²)',
                    'quantity' => 24.00,
                    'unit_name' => 'm²',
                    'unit_price' => 500000, // 24 * 500k = 12,000,000
                ],
                [
                    'item_description' => 'Video Processor Novastar VX600',
                    'quantity' => 1,
                    'unit_name' => 'unit',
                    'unit_price' => 1500000, // 1 * 1.5jt = 1,500,000
                ],
                [
                    'item_description' => 'Transportasi & Rigging Surabaya',
                    'quantity' => 1,
                    'unit_name' => 'paket',
                    'unit_price' => 1500000, // 1 * 1.5jt = 1,500,000
                ],
            ],
        ]);

        $response->assertSessionHas('success');

        // Subtotal = 12jt + 1.5jt + 1.5jt = 15,000,000
        // Discount = 500,000 -> Taxable = 14,500,000
        // PPN 11% = 1,595,000
        // Total = 16,095,000
        $invoice = Invoice::where('event_order_id', $this->order->id)->first();
        $this->assertNotNull($invoice);
        $this->assertStringStartsWith('INV-', $invoice->invoice_number);
        $this->assertEquals(15000000, (float) $invoice->subtotal);
        $this->assertEquals(500000, (float) $invoice->discount);
        $this->assertEquals(1595000, (float) $invoice->tax_amount);
        $this->assertEquals(16095000, (float) $invoice->total_amount);
        $this->assertEquals(16095000, (float) $invoice->balance_due);
        $this->assertEquals('unpaid', $invoice->status);
        $this->assertCount(3, $invoice->items);
    }

    public function test_can_view_invoice_show_page_and_whatsapp_link(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-0001',
            'event_order_id' => $this->order->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'invoice_type' => 'full',
            'subtotal' => 10000000,
            'total_amount' => 10000000,
            'balance_due' => 10000000,
            'status' => 'unpaid',
            'created_by' => $this->user->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_description' => 'Sewa LED Screen 20 m²',
            'quantity' => 20,
            'unit_name' => 'm²',
            'unit_price' => 500000,
            'total_price' => 10000000,
        ]);

        $response = $this->actingAs($this->user)->get(route('invoices.show', $invoice));

        $response->assertOk();
        $response->assertSee('INV-TEST-0001');
        $response->assertSee('PT Kreasi Visual Mandiri');
        $response->assertSee('Sewa LED Screen 20 m²');

        // Check WhatsApp URL helper
        $waUrl = $invoice->whatsapp_url;
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $waUrl);
        $this->assertStringContainsString('INV-TEST-0001', $waUrl);
    }

    public function test_can_record_partial_and_full_payment_with_order_synchronization(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-0002',
            'event_order_id' => $this->order->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'invoice_type' => 'full',
            'subtotal' => 15000000,
            'total_amount' => 15000000,
            'paid_amount' => 0,
            'balance_due' => 15000000,
            'status' => 'unpaid',
            'created_by' => $this->user->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_description' => 'Paket LED Screen P3.9',
            'quantity' => 1,
            'unit_name' => 'paket',
            'unit_price' => 15000000,
            'total_price' => 15000000,
        ]);

        // Record DP (Partial Payment) of 5,000,000
        $payResp = $this->actingAs($this->user)->post(route('invoices.payments.store', $invoice), [
            'payment_date' => now()->toDateString(),
            'amount' => 5000000,
            'payment_method' => 'transfer_bca',
            'reference_number' => 'BCA-TRF-00123',
            'notes' => 'DP 30% dari klien',
        ]);

        $payResp->assertSessionHas('success');

        $invoice->refresh();
        $this->order->refresh();

        $this->assertEquals(5000000, (float) $invoice->paid_amount);
        $this->assertEquals(10000000, (float) $invoice->balance_due);
        $this->assertEquals('partial', $invoice->status);

        // Check sync to EventOrder
        $this->assertEquals(5000000, (float) $this->order->paid_amount);
        $this->assertEquals('partial', $this->order->payment_status);

        // Record Second Payment (Pelunasan) of 10,000,000
        $payResp2 = $this->actingAs($this->user)->post(route('invoices.payments.store', $invoice), [
            'payment_date' => now()->toDateString(),
            'amount' => 10000000,
            'payment_method' => 'transfer_bca',
            'reference_number' => 'BCA-TRF-00456',
            'notes' => 'Pelunasan sisa tagihan',
        ]);

        $payResp2->assertSessionHas('success');

        $invoice->refresh();
        $this->order->refresh();

        $this->assertEquals(15000000, (float) $invoice->paid_amount);
        $this->assertEquals(0, (float) $invoice->balance_due);
        $this->assertEquals('paid', $invoice->status);

        // Check sync to EventOrder -> fully paid!
        $this->assertEquals(15000000, (float) $this->order->paid_amount);
        $this->assertEquals('paid', $this->order->payment_status);
    }

    public function test_can_delete_invoice_payment(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-0003',
            'event_order_id' => $this->order->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'invoice_type' => 'full',
            'subtotal' => 10000000,
            'total_amount' => 10000000,
            'paid_amount' => 5000000,
            'balance_due' => 5000000,
            'status' => 'partial',
            'created_by' => $this->user->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_description' => 'Sewa LED Screen 20 m²',
            'quantity' => 20,
            'unit_name' => 'm²',
            'unit_price' => 500000,
            'total_price' => 10000000,
        ]);

        $payment = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'event_order_id' => $this->order->id,
            'payment_number' => 'PAY-TEST-0001',
            'payment_date' => now()->toDateString(),
            'amount' => 5000000,
            'payment_method' => 'transfer_bca',
            'received_by' => $this->user->id,
        ]);

        $delResp = $this->actingAs($this->user)->delete(route('invoices.payments.destroy', $payment));
        $delResp->assertSessionHas('success');

        $invoice->refresh();
        $this->assertEquals(0, (float) $invoice->paid_amount);
        $this->assertEquals(10000000, (float) $invoice->balance_due);
        $this->assertEquals('unpaid', $invoice->status);
        $this->assertDatabaseMissing('invoice_payments', ['id' => $payment->id]);
    }

    public function test_can_view_printable_a4_invoice_and_receipt(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-PRINT',
            'event_order_id' => $this->order->id,
            'customer_id' => $this->customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'invoice_type' => 'full',
            'subtotal' => 10000000,
            'total_amount' => 10000000,
            'paid_amount' => 10000000,
            'balance_due' => 0,
            'status' => 'paid',
            'created_by' => $this->user->id,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_description' => 'Sewa LED Screen 20 m²',
            'quantity' => 20,
            'unit_name' => 'm²',
            'unit_price' => 500000,
            'total_price' => 10000000,
        ]);

        $payment = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'event_order_id' => $this->order->id,
            'payment_number' => 'PAY-TEST-RECEIPT',
            'payment_date' => now()->toDateString(),
            'amount' => 10000000,
            'payment_method' => 'transfer_bca',
            'received_by' => $this->user->id,
        ]);

        // Test Print A4 Invoice
        $printResp = $this->actingAs($this->user)->get(route('invoices.print', $invoice));
        $printResp->assertOk();
        $printResp->assertSee('FAKTUR TAGIHAN');
        $printResp->assertSee('INV-TEST-PRINT');
        $printResp->assertSee('OMAH VISUAL LED');
        $printResp->assertSee('LUNAS');

        // Test Print Kuitansi
        $receiptResp = $this->actingAs($this->user)->get(route('invoices.payments.receipt', $payment));
        $receiptResp->assertOk();
        $receiptResp->assertSee('KUITANSI PEMBAYARAN');
        $receiptResp->assertSee('PAY-TEST-RECEIPT');
        $receiptResp->assertSee($this->customer->name);
    }

    public function test_invoice_terbilang_helper_generates_correct_indonesian_words(): void
    {
        $this->assertEquals('Dua Puluh Lima Ribu', Invoice::terbilang(25000));
        $this->assertEquals('Satu Juta Lima Ratus Ribu', Invoice::terbilang(1500000));
        $this->assertEquals('Lima Belas Juta', Invoice::terbilang(15000000));
        $this->assertEquals('Dua Puluh Empat Juta Lima Ratus Ribu', Invoice::terbilang(24500000));
    }
}
