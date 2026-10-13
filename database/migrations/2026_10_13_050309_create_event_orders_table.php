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
        Schema::create('event_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('event_name');
            $table->string('event_type')->default('indoor');
            $table->string('venue_name');
            $table->text('venue_address')->nullable();
            $table->text('google_maps_url')->nullable();

            // Scheduling
            $table->date('loading_date');
            $table->string('loading_time')->default('22:00');
            $table->string('ready_time')->nullable();
            $table->date('event_start_date');
            $table->date('event_end_date')->nullable();
            $table->string('event_time')->nullable();
            $table->string('standby_time')->nullable();

            // Technical & Staging Requirements
            $table->string('electricity_source')->nullable();
            $table->string('dress_code')->default('Seragam Omahvisual');
            $table->string('status')->default('confirmed'); // draft, confirmed, loading, running, completed, cancelled
            $table->text('notes')->nullable();

            // Financials
            $table->decimal('total_price', 14, 2)->default(0);
            $table->decimal('down_payment', 14, 2)->default(0);
            $table->string('down_payment_method')->nullable();
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->string('payment_status')->default('unpaid'); // unpaid, partial, paid

            // Logistics / Vehicle
            $table->string('vehicle_loading_in')->nullable();
            $table->string('vehicle_loading_out')->nullable();
            $table->string('vehicle_notes')->nullable();

            // Multimedia Add-ons
            $table->string('jimmy_jib')->nullable();
            $table->unsignedInteger('tv_43_qty')->default(0);
            $table->unsignedInteger('tv_50_qty')->default(0);
            $table->unsignedInteger('tv_55_qty')->default(0);
            $table->unsignedInteger('tv_60_qty')->default(0);
            $table->unsignedInteger('tv_65_qty')->default(0);
            $table->unsignedInteger('projector_qty')->default(0);
            $table->unsignedInteger('projector_screen_qty')->default(0);

            // Public / Crew Token Access
            $table->string('public_token', 64)->unique();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('order_number');
            $table->index('loading_date');
            $table->index('event_start_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_orders');
    }
};
