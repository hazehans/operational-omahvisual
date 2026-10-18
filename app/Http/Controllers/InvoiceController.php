<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices with summary cards and filters.
     */
    public function index(Request $request): View
    {
        $query = Invoice::with(['customer', 'eventOrder', 'payments'])->latest('invoice_date');

        // Search by invoice number, customer name, or event name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('organization', 'like', "%{$search}%");
                    })
                    ->orWhereHas('eventOrder', function ($eq) use ($search) {
                        $eq->where('event_name', 'like', "%{$search}%")
                            ->orWhere('order_number', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Date range filter
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('invoice_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('invoice_date', '<=', $endDate);
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Metrics for summary cards
        $metrics = [
            'total_invoiced' => (float) Invoice::where('status', '!=', 'cancelled')->sum('total_amount'),
            'total_paid' => (float) Invoice::where('status', '!=', 'cancelled')->sum('paid_amount'),
            'total_balance_due' => (float) Invoice::where('status', '!=', 'cancelled')->sum('balance_due'),
            'overdue_count' => Invoice::whereNotIn('status', ['paid', 'cancelled'])->whereDate('due_date', '<', now()->toDateString())->count(),
        ];

        return view('invoices.index', compact('invoices', 'metrics'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Request $request): View
    {
        $selectedOrderId = $request->input('order_id');
        $selectedOrder = null;
        $suggestedItems = [];

        if ($selectedOrderId) {
            $selectedOrder = EventOrder::with(['customer', 'screens'])->find($selectedOrderId);

            if ($selectedOrder) {
                // Auto-generate suggested items from order screens
                foreach ($selectedOrder->screens as $screen) {
                    $desc = "Sewa LED Screen {$screen->pixel_pitch} {$screen->brand_series} - {$screen->screen_name} ({$screen->width_m}m x {$screen->height_m}m / {$screen->total_sqm} m²)";
                    $suggestedItems[] = [
                        'item_description' => $desc,
                        'quantity' => (float) $screen->total_sqm,
                        'unit_name' => 'm²',
                        'unit_price' => $selectedOrder->total_price > 0 && $selectedOrder->total_sqm > 0
                            ? round((float) $selectedOrder->total_price / (float) $selectedOrder->total_sqm, 2)
                            : 0,
                    ];
                }

                // If no screens or single order package
                if (empty($suggestedItems)) {
                    $suggestedItems[] = [
                        'item_description' => "Paket Sewa LED Screen & Multimedia - {$selectedOrder->event_name}",
                        'quantity' => 1,
                        'unit_name' => 'paket',
                        'unit_price' => (float) $selectedOrder->total_price,
                    ];
                }
            }
        }

        $orders = EventOrder::with('customer')->latest('event_start_date')->get();
        $customers = Customer::orderBy('name')->get();
        $defaultBankInfo = Invoice::DEFAULT_BANK_INFO;
        $defaultTerms = Invoice::DEFAULT_TERMS;

        return view('invoices.create', compact('orders', 'customers', 'selectedOrder', 'suggestedItems', 'defaultBankInfo', 'defaultTerms'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_order_id' => ['required', 'exists:event_orders,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'invoice_type' => ['required', 'in:standard,full,down_payment,settlement,custom'],
            'title' => ['nullable', 'string', 'max:255'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'bank_info' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'terms_conditions' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_name' => ['required', 'string', 'max:50'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        // Generate unique invoice number with collision avoidance (INV-YYYYMM-XXXX)
        $monthYear = now()->format('Ym');
        $count = Invoice::where('invoice_number', 'like', "INV-{$monthYear}-%")->count() + 1;
        do {
            $invoiceNumber = 'INV-'.$monthYear.'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            $count++;
        } while (Invoice::where('invoice_number', $invoiceNumber)->exists());

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'event_order_id' => $validated['event_order_id'],
            'customer_id' => $validated['customer_id'],
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'invoice_type' => $validated['invoice_type'],
            'title' => $validated['title'] ?? null,
            'discount' => $validated['discount'] ?? 0,
            'tax_percent' => $validated['tax_percent'] ?? 0,
            'bank_info' => $validated['bank_info'] ?? Invoice::DEFAULT_BANK_INFO,
            'notes' => $validated['notes'] ?? null,
            'terms_conditions' => $validated['terms_conditions'] ?? Invoice::DEFAULT_TERMS,
            'created_by' => Auth::id(),
        ]);

        // Create line items
        foreach ($validated['items'] as $index => $itemData) {
            $qty = (float) $itemData['quantity'];
            $price = (float) $itemData['unit_price'];
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_description' => $itemData['item_description'],
                'quantity' => $qty,
                'unit_name' => $itemData['unit_name'],
                'unit_price' => $price,
                'total_price' => round($qty * $price, 2),
                'sort_order' => $index,
            ]);
        }

        $invoice->recalculateTotals();

        AuditLog::log(
            action: 'create_invoice',
            entityType: 'Invoice',
            entityId: $invoice->id,
            description: "Invoice {$invoice->invoice_number} dibuat sebesar Rp ".number_format((float) $invoice->total_amount, 0, ',', '.').' oleh '.Auth::user()->name.'.'
        );

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} berhasil dibuat.");
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice): View
    {
        $invoice->load(['customer', 'eventOrder.screens', 'items', 'payments.receiver', 'creator']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice): View
    {
        $invoice->load(['customer', 'eventOrder', 'items']);
        $orders = EventOrder::with('customer')->latest('event_start_date')->get();
        $customers = Customer::orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'orders', 'customers'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'invoice_type' => ['required', 'in:standard,full,down_payment,settlement,custom'],
            'title' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:unpaid,partial,paid,cancelled'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'bank_info' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'terms_conditions' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_name' => ['required', 'string', 'max:50'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $invoice->update([
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'invoice_type' => $validated['invoice_type'],
            'title' => $validated['title'] ?? null,
            'status' => $validated['status'],
            'discount' => $validated['discount'] ?? 0,
            'tax_percent' => $validated['tax_percent'] ?? 0,
            'bank_info' => $validated['bank_info'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'terms_conditions' => $validated['terms_conditions'] ?? null,
        ]);

        // Recreate line items
        $invoice->items()->delete();
        foreach ($validated['items'] as $index => $itemData) {
            $qty = (float) $itemData['quantity'];
            $price = (float) $itemData['unit_price'];
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_description' => $itemData['item_description'],
                'quantity' => $qty,
                'unit_name' => $itemData['unit_name'],
                'unit_price' => $price,
                'total_price' => round($qty * $price, 2),
                'sort_order' => $index,
            ]);
        }

        $invoice->recalculateTotals();

        AuditLog::log(
            action: 'update_invoice',
            entityType: 'Invoice',
            entityId: $invoice->id,
            description: "Invoice {$invoice->invoice_number} diperbarui oleh ".Auth::user()->name.'.'
        );

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} berhasil diperbarui.");
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $num = $invoice->invoice_number;
        $order = $invoice->eventOrder;

        // Delete any uploaded payment proofs
        foreach ($invoice->payments as $payment) {
            if ($payment->proof_image && Storage::disk('public')->exists($payment->proof_image)) {
                Storage::disk('public')->delete($payment->proof_image);
            }
        }

        $invoice->delete();

        // Resync order payment status
        if ($order) {
            $orderTotalPaid = (float) InvoicePayment::where('event_order_id', $order->id)->sum('amount');
            $orderTotal = (float) $order->total_price;

            $status = 'unpaid';
            if ($orderTotalPaid >= $orderTotal && $orderTotal > 0) {
                $status = 'paid';
            } elseif ($orderTotalPaid > 0) {
                $status = 'partial';
            }

            $order->updateQuietly([
                'paid_amount' => $orderTotalPaid,
                'payment_status' => $status,
            ]);
        }

        AuditLog::log(
            action: 'delete_invoice',
            entityType: 'Invoice',
            entityId: $invoice->id,
            description: "Invoice {$num} dihapus oleh ".Auth::user()->name.'.'
        );

        return redirect()->route('invoices.index')
            ->with('success', "Invoice {$num} berhasil dihapus.");
    }

    /**
     * Display printable clean A4 invoice.
     */
    public function print(Invoice $invoice): View
    {
        $invoice->load(['customer', 'eventOrder.screens', 'items', 'payments.receiver']);

        return view('invoices.print', compact('invoice'));
    }

    /**
     * Record a payment against the invoice.
     */
    public function storePayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'proof_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('payments', 'public');
        }

        // Generate payment receipt number (PAY-YYYYMM-XXXX)
        $monthYear = now()->format('Ym');
        $count = InvoicePayment::where('payment_number', 'like', "PAY-{$monthYear}-%")->count() + 1;
        do {
            $paymentNumber = 'PAY-'.$monthYear.'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            $count++;
        } while (InvoicePayment::where('payment_number', $paymentNumber)->exists());

        $payment = InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'event_order_id' => $invoice->event_order_id,
            'payment_number' => $paymentNumber,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'proof_image' => $proofPath,
            'notes' => $validated['notes'] ?? null,
            'received_by' => Auth::id(),
        ]);

        $invoice->recalculateTotals();

        AuditLog::log(
            action: 'record_invoice_payment',
            entityType: 'InvoicePayment',
            entityId: $payment->id,
            description: "Pembayaran {$payment->payment_number} sebesar Rp ".number_format((float) $payment->amount, 0, ',', '.')." dicatat untuk Invoice {$invoice->invoice_number} oleh ".Auth::user()->name.'.'
        );

        return back()->with('success', "Pembayaran {$payment->payment_number} (Rp ".number_format((float) $payment->amount, 0, ',', '.').') berhasil dicatat.');
    }

    /**
     * Delete an invoice payment.
     */
    public function deletePayment(InvoicePayment $payment): RedirectResponse
    {
        $invoice = $payment->invoice;
        $num = $payment->payment_number;
        $amount = $payment->amount;

        if ($payment->proof_image && Storage::disk('public')->exists($payment->proof_image)) {
            Storage::disk('public')->delete($payment->proof_image);
        }

        $payment->delete();
        $invoice->recalculateTotals();

        AuditLog::log(
            action: 'delete_invoice_payment',
            entityType: 'InvoicePayment',
            entityId: $payment->id,
            description: "Pembayaran {$num} sebesar Rp ".number_format((float) $amount, 0, ',', '.').' dihapus oleh '.Auth::user()->name.'.'
        );

        return back()->with('success', "Pembayaran {$num} berhasil dihapus.");
    }

    /**
     * Display printable official payment receipt (Kuitansi).
     */
    public function printReceipt(InvoicePayment $payment): View
    {
        $payment->load(['invoice.customer', 'invoice.eventOrder', 'receiver']);

        return view('invoices.receipt', compact('payment'));
    }
}
