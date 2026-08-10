<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->decimal('download_speed', 10, 2)->comment('Mbps');
            $table->decimal('upload_speed', 10, 2)->comment('Mbps');
            $table->enum('speed_unit', ['mbps', 'gbps'])->default('mbps');
            $table->bigInteger('monthly_traffic')->nullable()->comment('GB');
            $table->enum('traffic_type', ['unlimited', 'limited', 'fup'])->default('unlimited');
            $table->decimal('price', 10, 2);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->integer('contract_duration')->default(12)->comment('months');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semiannual', 'annual'])->default('monthly');
            $table->integer('max_connections')->default(1);
            $table->string('technology', 50)->nullable()->comment('fibra, rádio, adsl, etc');
            $table->boolean('has_static_ip')->default(false);
            $table->integer('static_ip_qty')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('order')->default(0);
            $table->json('features')->nullable();
            $table->json('fine_print')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
