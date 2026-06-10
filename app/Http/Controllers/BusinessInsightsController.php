<?php

namespace App\Http\Controllers;

use App\Models\BusinessFinancialReport;
use App\Models\User;
use App\Support\BusinessFinancialAnalyzer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessInsightsController extends Controller
{
    public function __construct(private readonly BusinessFinancialAnalyzer $analyzer)
    {
    }

    public function index(Request $request): View
    {
        $business = $this->business($request);
        $reports = $business->financialReports()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->take(18)
            ->get();

        return view('business.insights.index', [
            'business' => $business,
            'reports' => $reports,
            'currentReport' => $reports->first(),
            'scoreLabeler' => fn (int $score): string => $this->analyzer->scoreLabel($score),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->business($request);
        $validated = $this->validated($request);
        $staffRoles = $this->staffRoles($validated['staff_roles'] ?? []);

        foreach ($this->moneyFields() as $field) {
            $validated[$field] = (float) ($validated[$field] ?? 0);
        }

        $validated['orders_count'] = filled($validated['orders_count'] ?? null)
            ? (int) $validated['orders_count']
            : null;
        $validated['employees_count'] = (int) ($validated['employees_count'] ?? 0);
        $validated['staff_roles'] = $staffRoles;

        $analysis = $this->analyzer->analyze($validated);

        $payload = array_merge($validated, [
            'average_order_value' => $analysis['average_order_value'],
            'total_costs' => $analysis['total_costs'],
            'gross_profit' => $analysis['gross_profit'],
            'net_profit' => $analysis['net_profit'],
            'profit_margin' => $analysis['profit_margin'],
            'health_score' => $analysis['health_score'],
            'recommendations' => $analysis['recommendations'],
        ]);

        BusinessFinancialReport::updateOrCreate(
            [
                'business_id' => $business->id,
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            $payload
        );

        return redirect()
            ->route('business.insights.index')
            ->with('success', 'Финансовият анализ е запазен. BON обнови ключовите метрики и препоръките.');
    }

    private function business(Request $request): User
    {
        $business = $request->user();

        abort_unless($business && $business->isBusiness(), 403);

        return $business;
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2020', 'max:'.((int) now()->year + 1)],
            'revenue' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'orders_count' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'average_order_value' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'rent_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'payroll_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'payroll_taxes_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'inventory_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'utilities_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'marketing_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'software_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'transport_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'other_fixed_costs' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'other_variable_costs' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'employees_count' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'staff_roles' => ['nullable', 'array', 'max:20'],
            'staff_roles.*.title' => ['nullable', 'string', 'max:80'],
            'staff_roles.*.monthly_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'staff_roles.*.hours' => ['nullable', 'numeric', 'min:0', 'max:744'],
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function staffRoles(array $roles): array
    {
        return collect($roles)
            ->map(function (array $role): ?array {
                $title = trim((string) ($role['title'] ?? ''));
                $monthlyCost = (float) ($role['monthly_cost'] ?? 0);
                $hours = (float) ($role['hours'] ?? 0);

                if ($title === '' && $monthlyCost <= 0 && $hours <= 0) {
                    return null;
                }

                return [
                    'title' => $title,
                    'monthly_cost' => round(max(0, $monthlyCost), 2),
                    'hours' => round(max(0, $hours), 2),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function moneyFields(): array
    {
        return [
            'revenue',
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
        ];
    }
}
