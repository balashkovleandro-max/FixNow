<?php

use App\Support\CategoryCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('service_categories')) {
            Schema::create('service_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->nullable()->index();
                $table->string('group')->nullable()->index();
                $table->string('type')->default('directory_based')->index();
                $table->boolean('accepts_requests')->default(false)->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('service_categories') && DB::table('service_categories')->count() === 0) {
            $now = now();
            $sortOrder = 0;
            $rows = [];

            foreach (config('bon_categories.groups', []) as $group => $items) {
                foreach ($items as $item) {
                    $rows[] = [
                        'name' => $item['name'],
                        'slug' => CategoryCatalog::slug($item['name']),
                        'group' => $group,
                        'type' => $item['type'],
                        'accepts_requests' => $item['type'] === 'request_based',
                        'sort_order' => $sortOrder++,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            DB::table('service_categories')->insertOrIgnore($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
