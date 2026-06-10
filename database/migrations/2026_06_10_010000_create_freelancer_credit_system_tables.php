<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'freelancer_credits_balance')) {
                $table->integer('freelancer_credits_balance')->default(0)->after('offer_points_initialized_at');
            }

            if (!Schema::hasColumn('users', 'freelancer_monthly_credits_granted_at')) {
                $table->timestamp('freelancer_monthly_credits_granted_at')->nullable()->after('freelancer_credits_balance');
            }
        });

        Schema::create('freelancer_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('title');
            $table->text('description');
            $table->decimal('budget', 12, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->string('category')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();

            $table->index(['business_id', 'status'], 'fj_business_status_idx');
            $table->index(['status', 'category'], 'fj_status_category_idx');
            $table->index(['status', 'deadline'], 'fj_status_deadline_idx');
            $table->foreign('business_id', 'fj_business_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::create('freelancer_job_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('freelancer_job_id');
            $table->unsignedBigInteger('freelancer_id');
            $table->text('cover_message')->nullable();
            $table->unsignedInteger('credits_spent')->default(3);
            $table->string('status')->default('submitted');
            $table->timestamps();

            $table->unique(['freelancer_job_id', 'freelancer_id'], 'fja_job_freelancer_unique');
            $table->index(['freelancer_id', 'created_at'], 'fja_freelancer_created_idx');
            $table->index(['freelancer_job_id', 'status'], 'fja_job_status_idx');
            $table->foreign('freelancer_job_id', 'fja_job_fk')
                ->references('id')
                ->on('freelancer_jobs')
                ->cascadeOnDelete();
            $table->foreign('freelancer_id', 'fja_freelancer_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        Schema::create('freelancer_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('freelancer_job_id')->nullable();
            $table->unsignedBigInteger('freelancer_job_application_id')->nullable();
            $table->string('type');
            $table->integer('amount');
            $table->integer('balance_after');
            $table->string('credit_package')->nullable();
            $table->decimal('price_amount', 8, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at'], 'fct_user_created_idx');
            $table->index(['type', 'created_at'], 'fct_type_created_idx');
            $table->index(['credit_package', 'created_at'], 'fct_package_created_idx');
            $table->foreign('user_id', 'fct_user_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->foreign('admin_id', 'fct_admin_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('freelancer_job_id', 'fct_job_fk')
                ->references('id')
                ->on('freelancer_jobs')
                ->nullOnDelete();
            $table->foreign('freelancer_job_application_id', 'fct_app_fk')
                ->references('id')
                ->on('freelancer_job_applications')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freelancer_credit_transactions');
        Schema::dropIfExists('freelancer_job_applications');
        Schema::dropIfExists('freelancer_jobs');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'freelancer_monthly_credits_granted_at')) {
                $table->dropColumn('freelancer_monthly_credits_granted_at');
            }

            if (Schema::hasColumn('users', 'freelancer_credits_balance')) {
                $table->dropColumn('freelancer_credits_balance');
            }
        });
    }
};
