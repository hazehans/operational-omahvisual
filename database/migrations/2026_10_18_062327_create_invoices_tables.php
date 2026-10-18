<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('event_order_id')->constrained('event_orders')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('invoice_type')->default('standard'); // standard, full, down_payment, settlement, custom
            $table->string('title')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('balance_due', 14, 2)->default(0);
            $table->string('status')->default('unpaid'); // unpaid, partial, paid, cancelled
            $table->text('bank_info')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('invoice_number');
            $table->index('invoice_date');
            $table->index('due_date');
            $table->index('status');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('item_description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit_name')->default('unit');
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('invoice_id');
        });

        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('event_order_id')->constrained('event_orders')->cascadeOnDelete();
            $table->string('payment_number')->unique();
            $table->date('payment_date');
            $table->decimal('amount', 14, 2);
            $table->string('payment_method')->default('transfer_bca');
            $table->string('reference_number')->nullable();
            $table->string('proof_image')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('payment_number');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
