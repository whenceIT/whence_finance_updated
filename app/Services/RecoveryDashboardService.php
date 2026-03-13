<?php

namespace App\Services;

use App\Models\{RecoveryCase, RecoveryPayment, RecoverySpecialistTarget, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RecoveryDashboardService
{
    /**
     * All KPI data for the Head of Recoveries executive view.
     */
    public function getExecutiveKpis(string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $cases    = RecoveryCase::forPeriod($period, $dateFrom, $dateTo);
        $payments = RecoveryPayment::whereHas('recoveryCase', fn($q) => $q->forPeriod($period, $dateFrom, $dateTo));

        // Use amount_recovered on cases as source of truth; payments for attribution split
        $totalRecovered   = RecoveryCase::forPeriod($period, $dateFrom, $dateTo)->sum('amount_recovered');
        $deptRecovered    = (clone $payments)->sum('recoveries_dept_amount');
        $activeCases      = RecoveryCase::active()->count();
        $resolvedCases    = RecoveryCase::forPeriod($period)->resolved()->count();
        $totalCases       = (clone $cases)->count();
        $resolutionRate   = $totalCases > 0 ? round(($resolvedCases / $totalCases) * 100, 1) : 0;
        $portfolioAtRisk  = RecoveryCase::active()->sum('loan_outstanding_amount');
        $totalCosts       = RecoveryCase::forPeriod($period)->sum(
            DB::raw('recovery_costs + legal_costs_incurred + skip_trace_costs')
        );
        $netRecovered     = $totalRecovered - $totalCosts;

        // Compare to previous period
        $prevRecovered = RecoveryPayment::whereHas('recoveryCase', function($q) use ($period) {
            match($period) {
                'month'   => $q->whereMonth('created_at', now()->subMonth()->month)
                              ->whereYear('created_at', now()->subMonth()->year),
                'quarter' => $q->whereBetween('created_at', [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()]),
                'year'    => $q->whereYear('created_at', now()->subYear()->year),
                default   => $q,
            };
        })->sum('amount');

        $recoveredChange = $prevRecovered > 0
            ? round((($totalRecovered - $prevRecovered) / $prevRecovered) * 100, 1)
            : null;

        return compact(
            'totalRecovered', 'deptRecovered', 'activeCases', 'resolvedCases',
            'resolutionRate', 'portfolioAtRisk', 'totalCosts', 'netRecovered',
            'recoveredChange'
        );
    }

    /**
     * Pipeline data: counts and values per category.
     */
    public function getPipelineData(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $categories = array_keys(RecoveryCase::CATEGORIES);
        $pipeline   = [];

        foreach ($categories as $cat) {
            // All non-resolved cases in this category
            $active   = RecoveryCase::byCategory($cat)->active();
            $allCases = RecoveryCase::byCategory($cat);

            $pipeline[$cat] = [
                'label'           => RecoveryCase::CATEGORIES[$cat],
                'count'           => (clone $active)->count(),
                'total_cases'     => (clone $allCases)->count(),
                'total_value'     => (clone $active)->sum('loan_outstanding_amount'),
                'amount_recovered'=> (clone $allCases)->sum('amount_recovered'),
                'recent_cases'    => (clone $active)
                    ->with(['client', 'originBranch'])
                    ->latest()
                    ->limit(3)
                    ->get(),
            ];
        }

        return $pipeline;
    }

    /**
     * Specialist performance table data with MTD actuals vs targets.
     */
    public function getSpecialistPerformance(string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): Collection
    {
        return User::orderBy('first_name')->orderBy('last_name')->get()
            ->map(function (User $specialist) use ($period, $dateFrom, $dateTo) {

                // All cases ever assigned — for active/resolved counts (not period-filtered)
                $allCases = RecoveryCase::assignedTo($specialist->id);

                // Period-filtered cases — for recovered amount this period
                $periodCases = RecoveryCase::assignedTo($specialist->id)->forPeriod($period, $dateFrom, $dateTo);

                $activeCases   = (clone $allCases)->active()->count();
                $resolvedCases = (clone $allCases)->resolved()->count();

                // Amount recovered — sum directly from period cases (source of truth)
                $totalRecovered = RecoveryCase::assignedTo($specialist->id)->forPeriod($period, $dateFrom, $dateTo)->sum('amount_recovered');

                // Most common category across ALL assigned cases
                $category = (clone $allCases)
                    ->selectRaw('category, count(*) as cnt')
                    ->groupBy('category')
                    ->orderByDesc('cnt')
                    ->value('category');

                // Sum all targets set for this specialist this month (any category)
                $targetAmount  = RecoverySpecialistTarget::where('specialist_id', $specialist->id)
                    ->where('year', now()->year)
                    ->where('month', now()->month)
                    ->sum('target_amount');
                $hasTarget     = $targetAmount > 0;
                $targetPct     = $hasTarget
                    ? round(($totalRecovered / $targetAmount) * 100, 1)
                    : null;

                return [
                    'specialist'      => $specialist,
                    'category'        => $category,
                    'total_recovered' => $totalRecovered,
                    'active_cases'    => $activeCases,
                    'resolved_cases'  => $resolvedCases,
                    'target_amount'   => $targetAmount,
                    'has_target'      => $hasTarget,
                    'target_pct'      => $targetPct !== null ? min($targetPct, 999) : null,
                    'status'          => match(true) {
                        !$hasTarget          => 'no_target',
                        $targetPct >= 100    => 'exceeding',
                        $targetPct >= 75     => 'on_track',
                        $targetPct >= 50     => 'at_risk',
                        default              => 'behind',
                    },
                ];
            })
            ->filter(fn($row) => RecoveryCase::assignedTo($row['specialist']->id)->exists());
    }

    /**
     * Recovery breakdown by branch for the branch performance bars.
     */
    public function getBranchBreakdown(string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): Collection
    {
        return DB::table('recovery_payments')
            ->join('recovery_cases', 'recovery_payments.recovery_case_id', '=', 'recovery_cases.id')
            ->join('offices', 'recovery_cases.origin_branch_id', '=', 'offices.id')
            ->select(
                'offices.id',
                'offices.name',
                DB::raw('SUM(recovery_payments.amount) as total_recovered'),
                DB::raw('COUNT(DISTINCT recovery_cases.id) as case_count')
            )
            ->when($period === 'custom' && $dateFrom && $dateTo, fn($q) =>
                $q->whereBetween('recovery_payments.payment_date', [$dateFrom, $dateTo])
            )
            ->when($period === 'month' && !($period === 'custom'), fn($q) =>
                $q->whereMonth('recovery_payments.payment_date', now()->month)
                  ->whereYear('recovery_payments.payment_date', now()->year)
            )
            ->groupBy('offices.id', 'offices.name')
            ->orderByDesc('total_recovered')
            ->limit(8)
            ->get();
    }

    /**
     * Recent activity feed for the dashboard.
     */
    public function getRecentActivity(int $limit = 10): Collection
    {
        return \App\Models\RecoveryActivity::with(['recoveryCase.client', 'performedBy'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Monthly trend data for sparkline chart (last 6 months).
     */
    public function getMonthlyTrend(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $total = RecoveryPayment::whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');
            $months[] = [
                'label'  => $date->format('M'),
                'amount' => (float) $total,
            ];
        }
        return $months;
    }

    /**
     * Recovery mix percentages for the donut chart.
     */
    public function getRecoveryMix(string $period = 'month', ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $total = RecoveryCase::active()->count(); // active is always all-time for mix
        if ($total === 0) return [];

        $mix = [];
        foreach (array_keys(RecoveryCase::CATEGORIES) as $cat) {
            $count = RecoveryCase::byCategory($cat)->active()->count();
            $mix[$cat] = [
                'label'      => RecoveryCase::CATEGORIES[$cat],
                'count'      => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        }
        return $mix;
    }
}
