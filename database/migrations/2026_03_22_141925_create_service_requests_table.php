<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_requests')) {
            return;
        }

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('city');
            $table->string('category')->nullable();
            $table->string('service')->nullable();
            $table->text('description');
            $table->string('urgency')->default('normal');
            $table->string('budget')->nullable();
            $table->string('status')->default('new');
            $table->foreignId('assigned_business_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
