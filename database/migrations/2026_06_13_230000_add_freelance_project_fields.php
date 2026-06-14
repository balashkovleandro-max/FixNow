<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('freelancer_jobs')) {
            Schema::table('freelancer_jobs', function (Blueprint $table) {
                if (!Schema::hasColumn('freelancer_jobs', 'work_mode')) {
                    $table->string('work_mode')->nullable()->after('location');
                }

                if (!Schema::hasColumn('freelancer_jobs', 'client_name')) {
                    $table->string('client_name')->nullable()->after('work_mode');
                }

                if (!Schema::hasColumn('freelancer_jobs', 'client_phone')) {
                    $table->string('client_phone')->nullable()->after('client_name');
                }

                if (!Schema::hasColumn('freelancer_jobs', 'client_email')) {
                    $table->string('client_email')->nullable()->after('client_phone');
                }

                if (!Schema::hasColumn('freelancer_jobs', 'attachment_path')) {
                    $table->string('attachment_path')->nullable()->after('client_email');
                }
            });
        }

        if (Schema::hasTable('freelancer_job_applications')) {
            Schema::table('freelancer_job_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('freelancer_job_applications', 'contact_phone')) {
                    $table->string('contact_phone')->nullable()->after('proposed_timeframe');
                }

                if (!Schema::hasColumn('freelancer_job_applications', 'contact_email')) {
                    $table->string('contact_email')->nullable()->after('contact_phone');
                }

                if (!Schema::hasColumn('freelancer_job_applications', 'portfolio_url')) {
                    $table->string('portfolio_url')->nullable()->after('contact_email');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('freelancer_job_applications')) {
            Schema::table('freelancer_job_applications', function (Blueprint $table) {
                foreach (['portfolio_url', 'contact_email', 'contact_phone'] as $column) {
                    if (Schema::hasColumn('freelancer_job_applications', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('freelancer_jobs')) {
            Schema::table('freelancer_jobs', function (Blueprint $table) {
                foreach (['attachment_path', 'client_email', 'client_phone', 'client_name', 'work_mode'] as $column) {
                    if (Schema::hasColumn('freelancer_jobs', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
