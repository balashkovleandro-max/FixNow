<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_suspended')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_suspended')->default(false)->after('role')->index();
            });
        }

        if (!Schema::hasTable('admin_activity_logs')) {
            Schema::create('admin_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action');
                $table->string('subject_type')->nullable();
                $table->unsignedBigInteger('subject_id')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['subject_type', 'subject_id'], 'aal_subject_idx');
                $table->index(['action', 'created_at'], 'aal_action_created_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_activity_logs');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_suspended')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_suspended');
            });
        }
    }
};
