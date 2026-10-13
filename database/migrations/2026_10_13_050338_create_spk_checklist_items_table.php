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
        Schema::create('spk_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_assignment_id')->constrained('spk_assignments')->cascadeOnDelete();
            $table->unsignedTinyInteger('page_number'); // 2: Hardware/Rigging, 3: PIC Operator/Spares
            $table->string('category');
            $table->string('item_name');
            $table->string('item_detail')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('unit')->default('pcs');
            $table->boolean('checked_out')->default(false); // Keluar
            $table->boolean('checked_in')->default(false);  // Kembali
            $table->text('condition_notes')->nullable();
            $table->timestamps();

            $table->index(['spk_assignment_id', 'page_number']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_checklist_items');
    }
};
