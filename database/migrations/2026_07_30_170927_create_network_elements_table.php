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
        Schema::create('network_elements', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('type', 50); // olt, splitter, cto, drop, client, caixa, armario, backbone
            $table->string('model', 100)->nullable();
            $table->string('serial', 100)->nullable();
            $table->foreignId('parent_id')->nullable()->index();
            $table->integer('order')->nullable();
            $table->string('identifier', 100)->nullable(); // identificador físico (porta, posição)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 2)->nullable();
            $table->foreignId('server_id')->nullable()->index();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_elements');
    }
};
