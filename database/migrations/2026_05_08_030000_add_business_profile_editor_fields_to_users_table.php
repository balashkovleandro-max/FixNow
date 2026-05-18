<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'short_description')) {
                $table->text('short_description')->nullable();
            }

            if (!Schema::hasColumn('users', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('users', 'facebook')) {
                $table->string('facebook')->nullable();
            }

            if (!Schema::hasColumn('users', 'instagram')) {
                $table->string('instagram')->nullable();
            }

            if (!Schema::hasColumn('users', 'whatsapp')) {
                $table->string('whatsapp')->nullable();
            }

            if (!Schema::hasColumn('users', 'viber')) {
                $table->string('viber')->nullable();
            }

            if (!Schema::hasColumn('users', 'payment_methods')) {
                $table->string('payment_methods')->nullable();
            }

            if (!Schema::hasColumn('users', 'years_experience')) {
                $table->string('years_experience')->nullable();
            }

            if (!Schema::hasColumn('users', 'emergency_services')) {
                $table->boolean('emergency_services')->default(false);
            }

            if (!Schema::hasColumn('users', 'service_areas')) {
                $table->text('service_areas')->nullable();
            }

            if (!Schema::hasColumn('users', 'service_categories')) {
                $table->text('service_categories')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'short_description',
                'description',
                'facebook',
                'instagram',
                'whatsapp',
                'viber',
                'payment_methods',
                'years_experience',
                'emergency_services',
                'service_areas',
                'service_categories',
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
