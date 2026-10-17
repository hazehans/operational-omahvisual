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
        // 1. Tambah tarif acuan pribadi & rekening bank di profil kru
        Schema::table('crews', function (Blueprint $table) {
            $table->decimal('default_fee_loading_in', 12, 2)->nullable()->after('employment_type');
            $table->decimal('default_fee_loading_out', 12, 2)->nullable()->after('default_fee_loading_in');
            $table->decimal('default_fee_meal', 12, 2)->nullable()->after('default_fee_loading_out');
            $table->decimal('default_fee_standby', 12, 2)->nullable()->after('default_fee_meal');
            $table->string('bank_name', 100)->nullable()->after('default_fee_standby');
            $table->string('bank_account_number', 100)->nullable()->after('bank_name');
            $table->string('bank_account_holder', 150)->nullable()->after('bank_account_number');
        });

        // 2. Tambah biaya operasional event (BBM, Tol, Penginapan, Lain-lain)
        Schema::table('event_orders', function (Blueprint $table) {
            $table->decimal('cost_fuel', 12, 2)->default(0)->after('paid_amount');
            $table->decimal('cost_toll', 12, 2)->default(0)->after('cost_fuel');
            $table->decimal('cost_lodging', 12, 2)->default(0)->after('cost_toll');
            $table->decimal('cost_other', 12, 2)->default(0)->after('cost_lodging');
            $table->text('cost_other_notes')->nullable()->after('cost_other');
        });

        // 3. Tabel armada multi-kendaraan per event (bisa lebih dari 1 pickup/truk)
        Schema::create('event_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spk_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('vehicle_name');
            $table->string('plate_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->boolean('has_etoll')->default(false);
            $table->boolean('has_kunci_ban')->default(false);
            $table->boolean('has_stnk')->default(false);
            $table->boolean('has_kir')->default(false);
            $table->boolean('has_barcode_bbm')->default(false);
            $table->string('departure_status')->default('pending');
            $table->string('return_status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_order_id', 'departure_status']);
        });

        // 4. Tabel penugasan kru per event beserta rincian fee & status pembayaran
        Schema::create('event_crew_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('spk_assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('crew_id')->constrained()->cascadeOnDelete();
            $table->string('role_label')->default('Kru Lapangan');
            $table->decimal('fee_loading_in', 12, 2)->default(0);
            $table->decimal('fee_loading_out', 12, 2)->default(0);
            $table->decimal('fee_meal', 12, 2)->default(0);
            $table->decimal('fee_standby', 12, 2)->default(0);
            $table->decimal('fee_transport', 12, 2)->default(0);
            $table->decimal('fee_bonus', 12, 2)->default(0);
            $table->decimal('total_fee', 12, 2)->default(0);
            $table->string('status')->default('offered'); // offered, acc, declined, completed
            $table->string('payment_status')->default('unpaid'); // unpaid, paid
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable(); // transfer, cash
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_order_id', 'status']);
            $table->index(['crew_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_crew_assignments');
        Schema::dropIfExists('event_vehicles');

        Schema::table('event_orders', function (Blueprint $table) {
            $table->dropColumn(['cost_fuel', 'cost_toll', 'cost_lodging', 'cost_other', 'cost_other_notes']);
        });

        Schema::table('crews', function (Blueprint $table) {
            $table->dropColumn([
                'default_fee_loading_in',
                'default_fee_loading_out',
                'default_fee_meal',
                'default_fee_standby',
                'bank_name',
                'bank_account_number',
                'bank_account_holder',
            ]);
        });
    }
};
