<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'source')) {
                $table->string('source')->default('offer_form');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
