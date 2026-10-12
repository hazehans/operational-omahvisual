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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('current_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('asset_code', 100)->unique(); // ID unik internal (auto-gen atau barcode flightcase)
            $table->string('serial_number', 100)->nullable(); // Nomor seri pabrik jika ada
            $table->string('barcode', 100)->nullable()->index();
            $table->string('ownership', 20)->default('company'); // company, external
            $table->string('condition', 20)->default('available'); // available, damaged, in_service
            $table->string('status', 20)->default('in_storage'); // in_storage, deployed, maintenance, lost
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'condition', 'status']);
            $table->index(['ownership', 'current_location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
