<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->integer('minimum_duration_months')->default(12)->after('notes');
            $table->string('cancellation_fine_formula', 50)->nullable()->after('minimum_duration_months');
            $table->string('signature_status', 30)->default('pending')->after('cancellation_fine_formula');
            $table->string('signature_id', 100)->nullable()->after('signature_status');
            $table->string('signed_pdf_path')->nullable()->after('signature_id');
            $table->timestamp('signed_at')->nullable()->after('signed_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'minimum_duration_months', 'cancellation_fine_formula',
                'signature_status', 'signature_id', 'signed_pdf_path', 'signed_at',
            ]);
        });
    }
};
