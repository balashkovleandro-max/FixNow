<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('favorite_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('favorite_type', 32);
            $table->timestamps();

            $table->unique(['user_id', 'favorite_user_id'], 'uf_user_target_unique');
            $table->index(['favorite_user_id', 'favorite_type'], 'uf_target_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_favorites');
    }
};
