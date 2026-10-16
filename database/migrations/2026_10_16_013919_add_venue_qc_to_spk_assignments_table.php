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
        Schema::table('spk_assignments', function (Blueprint $table) {
            $table->string('qc_status')->default('pending')->after('return_notes');
            $table->boolean('qc_test_rgb')->default(false)->after('qc_status');
            $table->boolean('qc_grounding_safe')->default(false)->after('qc_test_rgb');
            $table->boolean('qc_video_signal_ok')->default(false)->after('qc_grounding_safe');
            $table->string('qc_photo_path')->nullable()->after('qc_video_signal_ok');
            $table->string('qc_technician_name')->nullable()->after('qc_photo_path');
            $table->text('qc_notes')->nullable()->after('qc_technician_name');
            $table->timestamp('qc_verified_at')->nullable()->after('qc_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spk_assignments', function (Blueprint $table) {
            $table->dropColumn([
                'qc_status',
                'qc_test_rgb',
                'qc_grounding_safe',
                'qc_video_signal_ok',
                'qc_photo_path',
                'qc_technician_name',
                'qc_notes',
                'qc_verified_at',
            ]);
        });
    }
};
