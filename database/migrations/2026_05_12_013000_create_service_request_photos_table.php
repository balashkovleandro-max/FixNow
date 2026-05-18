<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_request_photos')) {
            Schema::create('service_request_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_request_id')->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->string('original_name')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['service_request_id', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_request_photos');
    }
};
