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
            if (!Schema::hasColumn('users', 'subscription_status')) {
                $table->string('subscription_status')->nullable()->default('trial');
            }

            if (!Schema::hasColumn('users', 'trial_started_at')) {
                $table->timestamp('trial_started_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'subscription_started_at')) {
                $table->timestamp('subscription_started_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'subscription_ends_at')) {
                $table->timestamp('subscription_ends_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
        });

        $startedAt = now();

        DB::table('users')
            ->where('role', 'business')
            ->whereNull('trial_started_at')
            ->whereNull('trial_ends_at')
            ->where(function ($query) {
                $query
                    ->whereNull('subscription_status')
                    ->orWhere('subscription_status', 'trial');
            })
            ->update([
                'subscription_status' => 'trial',
                'trial_started_at' => $startedAt,
                'trial_ends_at' => $startedAt->copy()->addDays(30),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'subscription_status',
                'trial_started_at',
                'trial_ends_at',
                'subscription_started_at',
                'subscription_ends_at',
                'cancelled_at',
            ] as $column) {
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
