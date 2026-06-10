<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_diagnostics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('business_name')->nullable();
            $table->string('business_type')->nullable();
            $table->string('city')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('problem_type');
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->string('urgency')->nullable();
            $table->string('customer_source')->nullable();
            $table->string('budget')->nullable();
            $table->boolean('active_ads')->default(false);
            $table->boolean('website')->default(false);
            $table->boolean('google_business')->default(false);
            $table->boolean('social_profiles')->default(false);
            $table->string('website_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('google_business_url')->nullable();
            $table->text('likely_reason')->nullable();
            $table->json('recommended_specialists')->nullable();
            $table->json('next_steps')->nullable();
            $table->json('warnings')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at'], 'bd_user_created_idx');
            $table->index(['problem_type', 'created_at'], 'bd_problem_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_diagnostics');
    }
};
