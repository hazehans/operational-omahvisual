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
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->string('ownership', 20)->default('company'); // company, external
            $table->string('condition', 20)->default('available'); // available, damaged, in_service
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('allocated_quantity', 12, 2)->default(0); // Fondasi alokasi rental/event masa depan
            $table->timestamps();

            $table->unique(
                ['product_id', 'location_id', 'ownership', 'condition'],
                'uq_stock_balance_dim'
            );
            $table->index(['product_id', 'condition']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};
