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
        // 1. Detailed fields for customers
        Schema::table('customers', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('name');
            $table->date('birth_date')->nullable()->after('full_name');
            $table->string('email')->nullable()->after('phone');
            $table->string('province')->nullable()->after('address');
            $table->string('category')->nullable()->default('Event Organizer')->after('organization');
            $table->string('pic_secondary_name')->nullable()->after('category');
            $table->string('pic_secondary_phone')->nullable()->after('pic_secondary_name');
            $table->string('pic_secondary_relation')->nullable()->after('pic_secondary_phone');
            $table->date('registration_date')->nullable()->after('pic_secondary_relation');
        });

        // 2. Detailed fields for crews
        Schema::table('crews', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('full_name');
            $table->string('email')->nullable()->after('phone');
            $table->json('positions')->nullable()->after('position'); // Multi-role: PIC, Crew, Supir, Operator
            $table->string('employment_type')->default('freelance_outsourcing')->after('positions'); // freelance_outsourcing, karyawan_tetap, magang
            $table->string('hris_employee_id')->nullable()->after('employment_type'); // Bridge with HRIS app
            $table->date('join_date')->nullable()->after('hris_employee_id');
            $table->string('photo_path')->nullable()->after('join_date');
            $table->string('ktp_photo_path')->nullable()->after('photo_path');
            $table->string('rate_card_photo_path')->nullable()->after('ktp_photo_path');
        });

        // 3. Detailed fields for vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('vehicle_code')->unique()->nullable()->after('id');
            $table->string('status')->default('tersedia')->after('type'); // tersedia, dipakai, maintenance, standby
            $table->string('special_condition')->nullable()->after('status'); // Normal, Di Bengkel, Dipakai Non-Order
            $table->string('photo_path')->nullable()->after('has_barcode_bbm');
            $table->string('stnk_photo_path')->nullable()->after('photo_path');
            $table->string('etoll_photo_path')->nullable()->after('stnk_photo_path');
            $table->string('barcode_bbm_photo_path')->nullable()->after('etoll_photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'birth_date',
                'email',
                'province',
                'category',
                'pic_secondary_name',
                'pic_secondary_phone',
                'pic_secondary_relation',
                'registration_date',
            ]);
        });

        Schema::table('crews', function (Blueprint $table) {
            $table->dropColumn([
                'birth_date',
                'email',
                'positions',
                'employment_type',
                'hris_employee_id',
                'join_date',
                'photo_path',
                'ktp_photo_path',
                'rate_card_photo_path',
            ]);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_code',
                'status',
                'special_condition',
                'photo_path',
                'stnk_photo_path',
                'etoll_photo_path',
                'barcode_bbm_photo_path',
            ]);
        });
    }
};
