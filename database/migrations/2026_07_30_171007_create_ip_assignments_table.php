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
        Schema::create('ip_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_id')->constrained('ip_pools')->cascadeOnDelete();
            $table->foreignId('link_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->string('mac_address', 17)->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->unique(['pool_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ip_assignments');
    }
};
