<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 200);
            $table->string('fantasy_name', 200)->nullable();
            $table->string('cpf_cnpj', 18)->unique();
            $table->string('rg_ie', 20)->nullable();
            $table->enum('person_type', ['pf', 'pj'])->default('pf');
            $table->string('email', 150);
            $table->string('phone', 20);
            $table->string('cellphone', 20)->nullable();
            $table->string('zipcode', 10);
            $table->string('address', 200);
            $table->string('address_number', 10);
            $table->string('complement', 100)->nullable();
            $table->string('neighborhood', 100);
            $table->string('city', 100);
            $table->string('state', 2);
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 150)->nullable();
            $table->enum('status', ['active', 'inactive', 'blocked', 'canceled'])->default('active');
            $table->text('observations')->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('client_user', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_main_contact')->default(false);
            $table->primary(['client_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_user');
        Schema::dropIfExists('clients');
    }
};
