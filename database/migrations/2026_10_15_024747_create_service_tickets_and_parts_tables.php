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
        Schema::create('service_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->foreignId('reported_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('pending_inspection');
            $table->string('priority', 20)->default('normal');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->text('issue_description');
            $table->text('diagnosis')->nullable();
            $table->text('action_taken')->nullable();
            $table->foreignId('source_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('workshop_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('return_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->timestamp('intake_date')->useCurrent();
            $table->timestamp('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['product_id', 'status']);
            $table->index(['asset_id', 'status']);
            $table->index('intake_date');
        });

        Schema::create('service_spareparts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_ticket_id')->constrained('service_tickets')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('source_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index(['service_ticket_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_spareparts');
        Schema::dropIfExists('service_tickets');
    }
};
