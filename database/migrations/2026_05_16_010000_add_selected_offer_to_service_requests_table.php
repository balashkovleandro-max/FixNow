<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasTable('service_request_offers') && !Schema::hasColumn('service_requests', 'selected_offer_id')) {
                $table->foreignId('selected_offer_id')
                    ->nullable()
                    ->constrained('service_request_offers')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('service_requests', 'accepted_offer_at')) {
                $table->timestamp('accepted_offer_at')->nullable();
            }

            if (!Schema::hasColumn('service_requests', 'closed_at')) {
                $table->timestamp('closed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'selected_offer_id')) {
                $table->dropConstrainedForeignId('selected_offer_id');
            }

            $columns = [];

            foreach (['accepted_offer_at', 'closed_at'] as $column) {
                if (Schema::hasColumn('service_requests', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
