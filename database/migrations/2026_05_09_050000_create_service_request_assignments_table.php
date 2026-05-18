<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_request_assignments')) {
            return;
        }

        Schema::create('service_request_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('sent');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['service_request_id', 'business_id'], 'service_request_assignment_unique');
            $table->index(['business_id', 'status'], 'service_request_assignment_business_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_assignments');
    }
};
