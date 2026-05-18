<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'service')) {
                $table->string('service')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'urgency')) {
                $table->string('urgency')->default('normal');
            }

            if (!Schema::hasColumn('service_requests', 'budget')) {
                $table->string('budget')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'assigned_business_id')) {
                $table->unsignedBigInteger('assigned_business_id')->nullable();
                $table->index('assigned_business_id');
            }
        });

        DB::table('service_requests')
            ->whereIn('status', ['нова', 'novа', 'new_request'])
            ->update(['status' => 'new']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'assigned_business_id')) {
                $table->dropIndex(['assigned_business_id']);
                $table->dropColumn('assigned_business_id');
            }

            foreach (['email', 'service', 'urgency', 'budget'] as $column) {
                if (Schema::hasColumn('service_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
