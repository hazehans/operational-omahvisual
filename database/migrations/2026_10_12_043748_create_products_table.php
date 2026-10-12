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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('brand', 100)->nullable();
            $table->string('model_series', 100)->nullable();
            $table->string('specification')->nullable();
            $table->string('tracking_type', 20)->default('quantity'); // 'individual' or 'quantity'
            $table->decimal('sqm_per_unit', 8, 4)->nullable(); // Luas meter persegi per unit jika kabinet
            $table->decimal('min_stock', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['category_id', 'tracking_type', 'is_active']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
