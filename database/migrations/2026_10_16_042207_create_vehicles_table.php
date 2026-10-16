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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('plate_number')->nullable();
            $table->string('type')->nullable(); // Pickup, Blindvan, Truk Engkel, Pribadi
            $table->boolean('has_etoll')->default(true);
            $table->boolean('has_kunci_ban')->default(true);
            $table->boolean('has_stnk')->default(true);
            $table->boolean('has_kir')->default(true);
            $table->boolean('has_barcode_bbm')->default(true);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
