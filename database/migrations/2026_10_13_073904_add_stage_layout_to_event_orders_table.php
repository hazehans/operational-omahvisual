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
        Schema::table('event_orders', function (Blueprint $table) {
            $table->decimal('stage_width_m', 5, 2)->default(10.00)->after('dress_code');
            $table->decimal('stage_depth_m', 5, 2)->default(6.00)->after('stage_width_m');
            $table->decimal('stage_height_m', 5, 2)->default(0.60)->after('stage_depth_m');
            $table->json('stage_layout_json')->nullable()->after('stage_height_m');
            $table->text('stage_layout_image')->nullable()->after('stage_layout_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_orders', function (Blueprint $table) {
            $table->dropColumn([
                'stage_width_m',
                'stage_depth_m',
                'stage_height_m',
                'stage_layout_json',
                'stage_layout_image',
            ]);
        });
    }
};
