<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'overdue', 'paid', 'canceled', 'refunded'])->default('pending');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('interest', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('boleto_barcode', 100)->nullable();
            $table->string('boleto_url', 255)->nullable();
            $table->string('pix_code', 255)->nullable();
            $table->string('pix_qrcode', 255)->nullable();
            $table->text('notes')->nullable();
            $table->json('items')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('payment_code', 50)->unique();
            $table->enum('method', ['boleto', 'pix', 'credit_card', 'debit_card', 'transfer', 'cash', 'other'])->default('pix');
            $table->enum('status', ['pending', 'confirmed', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->string('gateway', 50)->nullable();
            $table->string('gateway_id', 100)->nullable();
            $table->string('gateway_response', 255)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
