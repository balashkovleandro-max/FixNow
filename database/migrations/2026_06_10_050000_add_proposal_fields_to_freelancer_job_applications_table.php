<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancer_job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('freelancer_job_applications', 'proposed_price')) {
                $table->string('proposed_price')->nullable()->after('cover_message');
            }

            if (!Schema::hasColumn('freelancer_job_applications', 'proposed_timeframe')) {
                $table->string('proposed_timeframe')->nullable()->after('proposed_price');
            }

            if (!Schema::hasColumn('freelancer_job_applications', 'selected_at')) {
                $table->timestamp('selected_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('freelancer_job_applications', function (Blueprint $table) {
            foreach (['selected_at', 'proposed_timeframe', 'proposed_price'] as $column) {
                if (Schema::hasColumn('freelancer_job_applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
