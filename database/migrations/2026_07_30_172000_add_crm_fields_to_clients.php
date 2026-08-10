<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->json('notification_preferences')->nullable()->after('observations');
            $table->timestamp('last_contacted_at')->nullable()->after('notification_preferences');
            $table->string('lead_source', 100)->nullable()->after('last_contacted_at');
            $table->decimal('nps_score', 3, 1)->nullable()->after('lead_source');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['notification_preferences', 'last_contacted_at', 'lead_source', 'nps_score']);
        });
    }
};
