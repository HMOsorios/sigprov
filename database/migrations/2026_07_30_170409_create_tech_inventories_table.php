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
        Schema::create('tech_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('checked_out_at')->useCurrent();
            $table->timestamp('checked_in_at')->nullable();
            $table->string('condition_out', 50)->nullable();
            $table->string('condition_in', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tech_inventories');
    }
};
