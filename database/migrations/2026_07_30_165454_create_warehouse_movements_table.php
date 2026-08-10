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
        Schema::create('warehouse_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('warehouse_items')->cascadeOnDelete();
            $table->enum('type', ['in', 'out']);
            $table->integer('qty');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->string('reference_type', 50)->nullable(); // purchase, sale, transfer, adjustment, return
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('movement_at')->useCurrent();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_movements');
    }
};
