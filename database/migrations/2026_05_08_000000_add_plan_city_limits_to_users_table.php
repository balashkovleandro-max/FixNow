<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'subscription_plan')) {
                $table->string('subscription_plan')->nullable()->default('standard');
            }

            if (!Schema::hasColumn('users', 'service_cities')) {
                $table->text('service_cities')->nullable();
            }

            if (!Schema::hasColumn('users', 'extra_city_addon_count')) {
                $table->unsignedInteger('extra_city_addon_count')->default(0);
            }
        });

        DB::table('users')
            ->where('role', 'business')
            ->where('subscription_status', 'active')
            ->where(function ($query) {
                $query
                    ->whereNull('subscription_plan')
                    ->orWhere('subscription_plan', 'standard');
            })
            ->update(['subscription_plan' => 'premium']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            foreach (['subscription_plan', 'service_cities', 'extra_city_addon_count'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columns[] = $column;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
