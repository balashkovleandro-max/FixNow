<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessFinancialReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'month',
        'year',
        'revenue',
        'orders_count',
        'average_order_value',
        'rent_cost',
        'payroll_cost',
        'payroll_taxes_cost',
        'inventory_cost',
        'utilities_cost',
        'marketing_cost',
        'software_cost',
        'transport_cost',
        'other_fixed_costs',
        'other_variable_costs',
        'employees_count',
        'staff_roles',
        'total_costs',
        'gross_profit',
        'net_profit',
        'profit_margin',
        'health_score',
        'recommendations',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'revenue' => 'decimal:2',
            'orders_count' => 'integer',
            'average_order_value' => 'decimal:2',
            'rent_cost' => 'decimal:2',
            'payroll_cost' => 'decimal:2',
            'payroll_taxes_cost' => 'decimal:2',
            'inventory_cost' => 'decimal:2',
            'utilities_cost' => 'decimal:2',
            'marketing_cost' => 'decimal:2',
            'software_cost' => 'decimal:2',
            'transport_cost' => 'decimal:2',
            'other_fixed_costs' => 'decimal:2',
            'other_variable_costs' => 'decimal:2',
            'employees_count' => 'integer',
            'staff_roles' => 'array',
            'total_costs' => 'decimal:2',
            'gross_profit' => 'decimal:2',
            'net_profit' => 'decimal:2',
            'profit_margin' => 'decimal:2',
            'health_score' => 'integer',
            'recommendations' => 'array',
        ];
    }

    public function business()
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function isProfitable(): bool
    {
        return (float) $this->net_profit >= 0;
    }

    public function healthLabel(): string
    {
        return match (true) {
            $this->health_score >= 80 => 'Стабилен бизнес',
            $this->health_score >= 60 => 'Добра основа',
            $this->health_score >= 40 => 'Рискова зона',
            default => 'Сериозен финансов риск',
        };
    }
}
