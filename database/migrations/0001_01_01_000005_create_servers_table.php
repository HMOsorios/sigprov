<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('hostname', 200);
            $table->string('ip_address', 45);
            $table->integer('port')->default(22);
            $table->enum('type', ['router', 'switch', 'server', 'firewall', 'nas', 'radius', 'dhcp', 'dns', 'other'])->default('server');
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('firmware_version', 50)->nullable();
            $table->string('location', 200)->nullable();
            $table->string('username', 100)->nullable();
            $table->text('encrypted_password')->nullable();
            $table->text('ssh_key')->nullable();
            $table->enum('status', ['online', 'offline', 'maintenance', 'error'])->default('offline');
            $table->timestamp('last_ping_at')->nullable();
            $table->decimal('cpu_usage', 5, 2)->nullable();
            $table->decimal('memory_usage', 5, 2)->nullable();
            $table->decimal('disk_usage', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('monitoring_config')->nullable();
            $table->boolean('is_monitored')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('server_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['ping', 'cpu', 'memory', 'disk', 'uptime', 'error', 'info']);
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->timestamp('logged_at');
            $table->index(['server_id', 'logged_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_logs');
        Schema::dropIfExists('servers');
    }
};
