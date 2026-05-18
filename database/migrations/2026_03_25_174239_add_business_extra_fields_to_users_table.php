<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'работно_време')) {
                $table->string('работно_време')->nullable();
            }

            if (!Schema::hasColumn('users', 'адрес')) {
                $table->string('адрес')->nullable();
            }

            if (!Schema::hasColumn('users', 'уебсайт')) {
                $table->string('уебсайт')->nullable();
            }

            if (!Schema::hasColumn('users', 'фейсбук')) {
                $table->string('фейсбук')->nullable();
            }

            if (!Schema::hasColumn('users', 'инстаграм')) {
                $table->string('инстаграм')->nullable();
            }

            if (!Schema::hasColumn('users', 'обслужвани_райони')) {
                $table->string('обслужвани_райони')->nullable();
            }

            if (!Schema::hasColumn('users', 'години_опит')) {
                $table->string('години_опит')->nullable();
            }

            if (!Schema::hasColumn('users', 'спешни_услуги')) {
                $table->boolean('спешни_услуги')->default(false);
            }

            if (!Schema::hasColumn('users', 'методи_на_плащане')) {
                $table->string('методи_на_плащане')->nullable();
            }
        });
    }

    public function down(): void
    {
        // These columns are owned by the earlier guarded business-fields migration.
        // Keeping this rollback empty prevents data loss when this duplicate migration
        // was a no-op on fresh or existing databases.
    }
};
