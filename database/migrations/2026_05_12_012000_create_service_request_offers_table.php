<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_request_offers')) {
            Schema::create('service_request_offers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_request_id')->constrained()->cascadeOnDelete();
                $table->foreignId('business_id')->constrained('users')->cascadeOnDelete();
                $table->string('price_estimate');
                $table->string('timeframe');
                $table->text('message');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('status')->default('sent')->index();
                $table->unsignedInteger('points_spent')->default(3);
                $table->timestamps();

                $table->unique(['service_request_id', 'business_id'], 'service_request_offer_unique');
                $table->index(['business_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_offers');
    }
};
