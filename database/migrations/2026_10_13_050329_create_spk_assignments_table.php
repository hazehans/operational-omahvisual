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
        Schema::create('spk_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_order_id')->constrained('event_orders')->cascadeOnDelete();
            $table->string('spk_number')->unique();

            // Crew Team Roster
            $table->foreignId('crew_pic_event_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('crew_pic_event_name')->nullable();
            $table->string('crew_1_name')->nullable();
            $table->string('crew_2_name')->nullable();
            $table->string('crew_3_name')->nullable();
            $table->string('crew_4_name')->nullable();
            $table->string('crew_5_name')->nullable();
            $table->string('crew_6_name')->nullable();
            $table->string('crew_7_name')->nullable();

            // Vehicle Equipment Check
            $table->string('vehicle_name')->nullable();
            $table->boolean('has_etoll')->default(false);
            $table->boolean('has_kunci_ban')->default(false);
            $table->boolean('has_stnk')->default(false);
            $table->boolean('has_kir')->default(false);
            $table->boolean('has_barcode_bbm')->default(false);

            // Phase 1: Keberangkatan (Departure Gate)
            $table->string('departure_pic_event_name')->nullable();
            $table->string('departure_pic_warehouse_name')->nullable();
            $table->timestamp('departure_datetime')->nullable();
            $table->string('departure_status')->default('pending'); // pending, verified
            $table->text('departure_notes')->nullable();

            // Phase 2: Kepulangan (Return Gate - PIC Kepulangan Wajib Orang yg Berbeda)
            $table->string('return_pic_event_name')->nullable();
            $table->string('return_pic_warehouse_name')->nullable();
            $table->timestamp('return_datetime')->nullable();
            $table->string('return_status')->default('pending'); // pending, verified
            $table->text('return_notes')->nullable();

            $table->timestamps();

            $table->index('spk_number');
            $table->index('departure_status');
            $table->index('return_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_assignments');
    }
};
