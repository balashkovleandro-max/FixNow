<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'business_name')) {
                $table->string('business_name')->nullable();
            }

            if (!Schema::hasColumn('users', 'business_category')) {
                $table->string('business_category')->nullable();
            }

            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
            }

            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable();
            }

            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable();
            }

            if (!Schema::hasColumn('users', 'working_hours')) {
                $table->text('working_hours')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'business_name',
                'business_category',
                'city',
                'address',
                'website',
                'working_hours',
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