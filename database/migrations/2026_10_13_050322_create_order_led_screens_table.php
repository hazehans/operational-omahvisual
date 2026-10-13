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
        Schema::create('order_led_screens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_order_id')->constrained('event_orders')->cascadeOnDelete();
            $table->string('screen_name')->default('Main Screen');
            $table->string('brand_series')->default('SAGA'); // SAGA, LITE, LAMPRO, MAVIC
            $table->string('pixel_pitch')->default('P3.9'); // P3.9, P2.6, P2.9
            $table->decimal('width_m', 5, 2);
            $table->decimal('height_m', 5, 2);
            $table->decimal('total_sqm', 6, 2);
            $table->unsignedInteger('cabinets_1m')->default(0);
            $table->unsignedInteger('cabinets_50cm')->default(0);
            $table->unsignedInteger('total_cabinets')->default(0);
            $table->unsignedInteger('total_hardcases')->default(0);

            // Staging & Rigging
            $table->string('rigging_method')->default('ground_stake'); // ground_stake, hanging, stage_frame
            $table->text('stage_specification')->nullable();

            // Electronics & Calculations
            $table->string('processor_model')->nullable(); // e.g. Novastar VX4S, VX600, VX1000
            $table->unsignedInteger('power_jumpers_qty')->default(0);
            $table->unsignedInteger('lan_jumpers_qty')->default(0);
            $table->unsignedInteger('spare_modules_qty')->default(2);
            $table->unsignedInteger('spare_receiving_cards_qty')->default(1);
            $table->unsignedInteger('spare_psu_qty')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('event_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_led_screens');
    }
};
