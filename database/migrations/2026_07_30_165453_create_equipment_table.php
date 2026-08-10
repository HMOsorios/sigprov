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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('serial', 100)->unique();
            $table->string('patrimony', 100)->nullable()->unique();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('type', 50); // router, onu, modem, cto, splitter, ont, ups, etc
            $table->string('mac', 17)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('firmware_version', 50)->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->foreignId('supplier_id')->nullable()->index();
            $table->string('status', 30)->default('disponivel'); // disponivel, emprestado, manutencao, descartado, perdido
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
        Schema::dropIfExists('equipment');
    }
};
