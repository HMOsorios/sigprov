<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('server_id')->nullable()->constrained()->onDelete('set null');
            $table->string('pppoe_user', 100)->nullable()->unique();
            $table->text('pppoe_password')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('mac_address', 17)->nullable();
            $table->string('vlan', 10)->nullable();
            $table->string('ont_serial', 50)->nullable()->comment('Número serial da ONT');
            $table->string('ont_brand', 50)->nullable();
            $table->string('ont_model', 50)->nullable();
            $table->string('cable_origin', 100)->nullable();
            $table->string('cable_drop', 100)->nullable();
            $table->string('splitter_location', 200)->nullable();
            $table->integer('signal_rx')->nullable()->comment('dBm');
            $table->integer('signal_tx')->nullable()->comment('dBm');
            $table->enum('status', ['active', 'inactive', 'blocked', 'maintenance'])->default('active');
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('link_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['activated', 'blocked', 'unblocked', 'speed_changed', 'ip_changed', 'maintenance', 'signal_alert']);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_logs');
        Schema::dropIfExists('links');
    }
};
