<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('business_analytics_events')) {
            return;
        }

        Schema::table('business_analytics_events', function (Blueprint $table) {
            if (!Schema::hasColumn('business_analytics_events', 'ip_hash')) {
                $table->string('ip_hash', 64)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('business_analytics_events')) {
            return;
        }

        Schema::table('business_analytics_events', function (Blueprint $table) {
            if (Schema::hasColumn('business_analytics_events', 'ip_hash')) {
                $table->dropColumn('ip_hash');
            }
        });
    }
};
