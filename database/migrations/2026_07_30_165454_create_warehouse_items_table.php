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
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 200);
            $table->string('category', 100)->nullable();
            $table->string('unit', 20)->default('un'); // un, metro, kg, litro, par, etc
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->nullable();
            $table->integer('current_qty')->default(0);
            $table->string('location', 100)->nullable(); // prateleira/estante
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_items');
    }
};
