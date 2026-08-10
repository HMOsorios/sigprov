<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number', 30)->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('restrict');
            $table->enum('status', ['active', 'suspended', 'canceled', 'expired'])->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('due_day')->comment('dia de vencimento');
            $table->decimal('signed_price', 10, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->string('installation_address', 200);
            $table->string('installation_zipcode', 10);
            $table->string('installation_neighborhood', 100);
            $table->string('installation_city', 100);
            $table->string('installation_state', 2);
            $table->string('installation_complement', 100)->nullable();
            $table->string('installation_latitude', 20)->nullable();
            $table->string('installation_longitude', 20)->nullable();
            $table->text('notes')->nullable();
            $table->string('contract_file', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
