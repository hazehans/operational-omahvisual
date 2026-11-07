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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_rental_item')->default(true)->after('tracking_type');
            $table->decimal('purchase_price', 15, 2)->nullable()->after('min_stock');
            $table->decimal('rental_rate_per_day', 15, 2)->nullable()->after('purchase_price');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('salutation', 20)->nullable()->after('customer_code');
            $table->string('photo_path')->nullable()->after('address');
            $table->string('verification_status', 30)->default('verified')->after('notes');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'salutation',
                'photo_path',
                'verification_status',
                'verified_at',
                'verified_by',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_rental_item',
                'purchase_price',
                'rental_rate_per_day',
            ]);
        });
    }
};
