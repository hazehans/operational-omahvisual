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
            $table->string('provinsi')->nullable()->after('venue_name');
            $table->string('kabupaten')->nullable()->after('provinsi');
            $table->string('status_wilayah')->default('dalam_kota')->after('kabupaten'); // dalam_kota, luar_kota
            $table->time('jam_menyala')->nullable()->after('loading_time');
            $table->string('waktu_gladi')->nullable()->after('jam_menyala');
            $table->time('jam_acara')->nullable()->after('waktu_gladi');
            $table->string('kelistrikan')->nullable()->after('electricity_source'); // genset, client, other
            $table->text('kelistrikan_keterangan')->nullable()->after('kelistrikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_orders', function (Blueprint $table) {
            $table->dropColumn([
                'provinsi',
                'kabupaten',
                'status_wilayah',
                'jam_menyala',
                'waktu_gladi',
                'jam_acara',
                'kelistrikan',
                'kelistrikan_keterangan',
            ]);
        });
    }
};
