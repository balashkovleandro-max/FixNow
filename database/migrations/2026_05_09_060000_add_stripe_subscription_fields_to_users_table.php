<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable();
            }

            if (!Schema::hasColumn('users', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('users', 'stripe_subscription_id')) {
                $columns[] = 'stripe_subscription_id';
            }

            if (Schema::hasColumn('users', 'stripe_customer_id')) {
                $columns[] = 'stripe_customer_id';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
