<?php

namespace App\Support;

class BusinessFinancialAnalyzer
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function analyze(array $data): array
    {
        $revenue = $this->money($data, 'revenue');
        $ordersCount = (int) ($data['orders_count'] ?? 0);
        $employeesCount = (int) ($data['employees_count'] ?? 0);

        $costs = [
            'rent_cost' => $this->money($data, 'rent_cost'),
            'payroll_cost' => $this->money($data, 'payroll_cost'),
            'payroll_taxes_cost' => $this->money($data, 'payroll_taxes_cost'),
            'inventory_cost' => $this->money($data, 'inventory_cost'),
            'utilities_cost' => $this->money($data, 'utilities_cost'),
            'marketing_cost' => $this->money($data, 'marketing_cost'),
            'software_cost' => $this->money($data, 'software_cost'),
            'transport_cost' => $this->money($data, 'transport_cost'),
            'other_fixed_costs' => $this->money($data, 'other_fixed_costs'),
            'other_variable_costs' => $this->money($data, 'other_variable_costs'),
        ];

        $totalCosts = array_sum($costs);
        $personnelCost = $costs['payroll_cost'] + $costs['payroll_taxes_cost'];
        $variableCosts = $costs['inventory_cost'] + $costs['marketing_cost'] + $costs['transport_cost'] + $costs['other_variable_costs'];
        $fixedCosts = $costs['rent_cost'] + $personnelCost + $costs['utilities_cost'] + $costs['software_cost'] + $costs['other_fixed_costs'];
        $grossProfit = $revenue - $costs['inventory_cost'] - $costs['other_variable_costs'];
        $netProfit = $revenue - $totalCosts;
        $profitMargin = $this->percentage($netProfit, $revenue);
        $costRatio = $this->percentage($totalCosts, $revenue);
        $personnelRatio = $this->percentage($personnelCost, $revenue);
        $fixedCostRatio = $this->percentage($fixedCosts, $revenue);
        $rentRatio = $this->percentage($costs['rent_cost'], $revenue);
        $marketingRatio = $this->percentage($costs['marketing_cost'], $revenue);
        $averageRevenuePerEmployee = $employeesCount > 0 ? $revenue / $employeesCount : null;
        $averageCostPerEmployee = $employeesCount > 0 ? $personnelCost / $employeesCount : null;
        $averageOrderValue = $this->money($data, 'average_order_value');

        if ($averageOrderValue <= 0 && $ordersCount > 0) {
            $averageOrderValue = $revenue / $ordersCount;
        }

        $contributionMargin = $revenue - $variableCosts;
        $contributionMarginRatio = $revenue > 0 ? $contributionMargin / $revenue : null;
        $breakEvenRevenue = $contributionMarginRatio !== null && $contributionMarginRatio > 0
            ? $fixedCosts / $contributionMarginRatio
            : null;

        $score = $this->score(
            revenue: $revenue,
            netProfit: $netProfit,
            profitMargin: $profitMargin,
            personnelRatio: $personnelRatio,
            fixedCostRatio: $fixedCostRatio,
            marketingRatio: $marketingRatio,
            breakEvenRevenue: $breakEvenRevenue
        );

        $recommendations = $this->recommendations(
            revenue: $revenue,
            netProfit: $netProfit,
            profitMargin: $profitMargin,
            costRatio: $costRatio,
            personnelRatio: $personnelRatio,
            rentRatio: $rentRatio,
            marketingRatio: $marketingRatio,
            breakEvenRevenue: $breakEvenRevenue,
            averageRevenuePerEmployee: $averageRevenuePerEmployee,
            averageCostPerEmployee: $averageCostPerEmployee
        );

        return [
            'total_costs' => round($totalCosts, 2),
            'gross_profit' => round($grossProfit, 2),
            'net_profit' => round($netProfit, 2),
            'profit_margin' => round($profitMargin, 2),
            'cost_ratio' => round($costRatio, 2),
            'personnel_ratio' => round($personnelRatio, 2),
            'fixed_cost_ratio' => round($fixedCostRatio, 2),
            'rent_ratio' => round($rentRatio, 2),
            'marketing_ratio' => round($marketingRatio, 2),
            'average_revenue_per_employee' => $averageRevenuePerEmployee !== null ? round($averageRevenuePerEmployee, 2) : null,
            'average_cost_per_employee' => $averageCostPerEmployee !== null ? round($averageCostPerEmployee, 2) : null,
            'average_order_value' => $averageOrderValue > 0 ? round($averageOrderValue, 2) : null,
            'break_even_revenue' => $breakEvenRevenue !== null ? round($breakEvenRevenue, 2) : null,
            'health_score' => $score,
            'recommendations' => $recommendations,
        ];
    }

    public function scoreLabel(int $score): string
    {
        return match (true) {
            $score >= 80 => 'Стабилен бизнес',
            $score >= 60 => 'Добър, но има места за оптимизация',
            $score >= 40 => 'Рискова зона',
            default => 'Сериозен финансов риск',
        };
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function money(array $data, string $key): float
    {
        return max(0, (float) ($data[$key] ?? 0));
    }

    private function percentage(float $value, float $base): float
    {
        return $base > 0 ? ($value / $base) * 100 : 0;
    }

    private function score(
        float $revenue,
        float $netProfit,
        float $profitMargin,
        float $personnelRatio,
        float $fixedCostRatio,
        float $marketingRatio,
        ?float $breakEvenRevenue
    ): int {
        if ($revenue <= 0) {
            return 20;
        }

        $score = 50;

        $score += $netProfit > 0 ? 20 : -25;

        $score += match (true) {
            $profitMargin >= 20 => 15,
            $profitMargin >= 10 => 10,
            $profitMargin >= 5 => 5,
            $profitMargin < 0 => -15,
            default => -5,
        };

        $score += match (true) {
            $personnelRatio <= 35 => 10,
            $personnelRatio <= 50 => 5,
            $personnelRatio > 65 => -15,
            $personnelRatio > 50 => -8,
            default => 0,
        };

        $score += match (true) {
            $fixedCostRatio <= 45 => 10,
            $fixedCostRatio <= 65 => 3,
            $fixedCostRatio > 75 => -12,
            $fixedCostRatio > 65 => -6,
            default => 0,
        };

        if ($breakEvenRevenue !== null) {
            $score += match (true) {
                $revenue >= $breakEvenRevenue * 1.2 => 10,
                $revenue >= $breakEvenRevenue => 3,
                default => -10,
            };
        }

        if ($marketingRatio >= 2 && $marketingRatio <= 10) {
            $score += 5;
        } elseif ($marketingRatio < 1) {
            $score -= 3;
        }

        return max(0, min(100, $score));
    }

    /**
     * @return array<int, string>
     */
    private function recommendations(
        float $revenue,
        float $netProfit,
        float $profitMargin,
        float $costRatio,
        float $personnelRatio,
        float $rentRatio,
        float $marketingRatio,
        ?float $breakEvenRevenue,
        ?float $averageRevenuePerEmployee,
        ?float $averageCostPerEmployee
    ): array {
        $recommendations = [];

        if ($revenue <= 0) {
            return [
                'Добави реален месечен оборот, за да може анализът да покаже маржове, структура на разходите и break-even точка.',
            ];
        }

        if ($netProfit < 0) {
            $recommendations[] = 'Бизнесът е на загуба за този период. Прегледай най-големите разходни групи и потърси кои от тях растат по-бързо от оборота.';
        }

        if ($profitMargin < 5) {
            $recommendations[] = 'Маржът е нисък. Провери ценообразуването, себестойността и дали средната стойност на клиент може да се повиши.';
        }

        if ($costRatio > 85) {
            $recommendations[] = 'Разходите заемат много голям дял от оборота. Раздели ги на фиксирани и променливи и приоритизирай най-големите позиции за преглед.';
        }

        if ($personnelRatio > 45) {
            $recommendations[] = 'Разходът за персонал е висок спрямо оборота. Прегледай графиците, натоварването по роли и продуктивността преди да взимаш крайни решения.';
        }

        if ($rentRatio > 18) {
            $recommendations[] = 'Наемът заема значителен процент от оборота. Провери дали локацията, часовете и използването на пространството носят достатъчна възвръщаемост.';
        }

        if ($marketingRatio < 2 && $profitMargin > 0) {
            $recommendations[] = 'Маркетинг бюджетът е нисък спрямо оборота. Ако целта е растеж, тествай малки кампании с ясно измерване на заявки и продажби.';
        }

        if ($breakEvenRevenue !== null && $revenue > 0 && $revenue < $breakEvenRevenue * 1.1) {
            $recommendations[] = 'Бизнесът е близо до break-even и няма голям буфер. Следи фиксираните разходи и търси начини да увеличиш средната стойност на клиент.';
        }

        if ($averageRevenuePerEmployee !== null && $averageCostPerEmployee !== null && $averageRevenuePerEmployee < $averageCostPerEmployee * 2) {
            $recommendations[] = 'Средният оборот на служител изглежда нисък спрямо разхода. Анализирай натовареността по роли, процесите и часовете с най-силен клиентски интерес.';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Финансовата картина изглежда стабилна за този период. Продължи да следиш маржа, фиксираните разходи и средната стойност на клиент всеки месец.';
        }

        return array_slice($recommendations, 0, 6);
    }
}
