<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'account_type')) {
                $table->string('account_type')->nullable()->after('role')->index();
            }

            if (!Schema::hasColumn('users', 'profile_type')) {
                $table->string('profile_type')->nullable()->after('account_type')->index();
            }
        });

        DB::table('users')
            ->whereNull('account_type')
            ->update([
                'account_type' => DB::raw("CASE WHEN role = 'customer' THEN 'client' ELSE role END"),
            ]);

        DB::table('users')
            ->whereNull('profile_type')
            ->update([
                'profile_type' => DB::raw("CASE WHEN role IN ('business', 'freelancer') THEN role ELSE NULL END"),
            ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'profile_type')) {
                $table->dropColumn('profile_type');
            }

            if (Schema::hasColumn('users', 'account_type')) {
                $table->dropColumn('account_type');
            }
        });
    }
};
