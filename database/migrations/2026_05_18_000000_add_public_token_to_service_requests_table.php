<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_requests')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'public_token')) {
                $table->string('public_token', 80)->nullable()->index();
            }
        });

        if (!Schema::hasColumn('service_requests', 'public_token')) {
            return;
        }

        DB::table('service_requests')
            ->where(function ($query) {
                $query->whereNull('public_token')->orWhere('public_token', '');
            })
            ->orderBy('id')
            ->select('id')
            ->chunkById(100, function ($requests) {
                foreach ($requests as $request) {
                    do {
                        $token = Str::random(40);
                    } while (DB::table('service_requests')->where('public_token', $token)->exists());

                    DB::table('service_requests')
                        ->where('id', $request->id)
                        ->update(['public_token' => $token]);
                }
            });
    }

    public function down(): void
    {
        if (!Schema::hasTable('service_requests') || !Schema::hasColumn('service_requests', 'public_token')) {
            return;
        }

        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('public_token');
        });
    }
};
