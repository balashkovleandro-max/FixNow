<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('business_financial_reports')) {
            return;
        }

        Schema::create('business_financial_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('revenue', 12, 2)->default(0);
            $table->unsignedInteger('orders_count')->nullable();
            $table->decimal('average_order_value', 12, 2)->nullable();
            $table->decimal('rent_cost', 12, 2)->default(0);
            $table->decimal('payroll_cost', 12, 2)->default(0);
            $table->decimal('payroll_taxes_cost', 12, 2)->default(0);
            $table->decimal('inventory_cost', 12, 2)->default(0);
            $table->decimal('utilities_cost', 12, 2)->default(0);
            $table->decimal('marketing_cost', 12, 2)->default(0);
            $table->decimal('software_cost', 12, 2)->default(0);
            $table->decimal('transport_cost', 12, 2)->default(0);
            $table->decimal('other_fixed_costs', 12, 2)->default(0);
            $table->decimal('other_variable_costs', 12, 2)->default(0);
            $table->unsignedSmallInteger('employees_count')->default(0);
            $table->json('staff_roles')->nullable();
            $table->decimal('total_costs', 12, 2)->default(0);
            $table->decimal('gross_profit', 12, 2)->default(0);
            $table->decimal('net_profit', 12, 2)->default(0);
            $table->decimal('profit_margin', 8, 2)->default(0);
            $table->unsignedTinyInteger('health_score')->default(0);
            $table->json('recommendations')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'month', 'year'], 'bfr_business_month_year_unique');
            $table->index(['business_id', 'year', 'month'], 'bfr_business_period_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_financial_reports');
    }
};
