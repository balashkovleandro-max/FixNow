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
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'hourly_rate')) {
                $table->decimal('hourly_rate', 10, 2)->nullable()->after('years_experience');
            }

            if (!Schema::hasColumn('users', 'project_rate')) {
                $table->decimal('project_rate', 10, 2)->nullable()->after('hourly_rate');
            }

            if (!Schema::hasColumn('users', 'availability')) {
                $table->string('availability')->nullable()->after('project_rate');
            }

            if (!Schema::hasColumn('users', 'work_mode')) {
                $table->string('work_mode')->nullable()->after('availability');
            }

            if (!Schema::hasColumn('users', 'languages')) {
                $table->json('languages')->nullable()->after('work_mode');
            }

            if (!Schema::hasColumn('users', 'preferred_categories')) {
                $table->json('preferred_categories')->nullable()->after('languages');
            }

            if (!Schema::hasColumn('users', 'linkedin')) {
                $table->string('linkedin')->nullable()->after('preferred_categories');
            }

            if (!Schema::hasColumn('users', 'github')) {
                $table->string('github')->nullable()->after('linkedin');
            }

            if (!Schema::hasColumn('users', 'behance')) {
                $table->string('behance')->nullable()->after('github');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            foreach (['behance', 'github', 'linkedin', 'preferred_categories', 'languages', 'work_mode', 'availability', 'project_rate', 'hourly_rate', 'avatar'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
