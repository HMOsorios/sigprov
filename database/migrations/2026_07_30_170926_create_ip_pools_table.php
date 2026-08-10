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
        Schema::create('ip_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('subnet', 45); // CIDR ex: 192.168.1.0/24
            $table->string('gateway', 45)->nullable();
            $table->string('dns1', 45)->nullable();
            $table->string('dns2', 45)->nullable();
            $table->string('range_start', 45);
            $table->string('range_end', 45);
            $table->enum('type', ['ipv4', 'ipv6'])->default('ipv4');
            $table->boolean('is_cgnat')->default(false);
            $table->foreignId('server_id')->nullable()->index();
            $table->integer('used')->default(0);
            $table->integer('total')->default(0);
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
        Schema::dropIfExists('ip_pools');
    }
};
