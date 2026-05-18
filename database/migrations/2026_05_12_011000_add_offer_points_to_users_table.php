<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'offer_points_balance')) {
                $table->unsignedInteger('offer_points_balance')->nullable()->after('extra_city_addon_count');
            }

            if (!Schema::hasColumn('users', 'offer_points_initialized_at')) {
                $table->timestamp('offer_points_initialized_at')->nullable()->after('offer_points_balance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['offer_points_initialized_at', 'offer_points_balance'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
