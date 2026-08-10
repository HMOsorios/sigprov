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
        Schema::create('nf_telecom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->string('numero', 20);
            $table->string('serie', 3)->default('1');
            $table->string('competencia', 7); // YYYY-MM
            $table->enum('modelo', ['21', '22'])->default('21');
            $table->decimal('valor', 10, 2);
            $table->text('xml')->nullable();
            $table->string('protocolo', 50)->nullable();
            $table->string('chave_acesso', 44)->nullable();
            $table->string('link_danfe')->nullable();
            $table->enum('status', ['pendente', 'autorizada', 'cancelada', 'rejeitada', 'denegada'])
                ->default('pendente');
            $table->json('motivos_rejeicao')->nullable();
            $table->timestamp('emitida_em')->nullable();
            $table->timestamp('cancelada_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['numero', 'serie', 'competencia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nf_telecom');
    }
};
