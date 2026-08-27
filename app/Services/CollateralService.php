<?php

namespace App\Services;

use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\District;
use App\Models\Office;
use App\Models\Province;
use App\Models\PlatformSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CollateralService
{
    public const REPORTS = [
        'inventory_valuation' => [
            'label'       => 'Inventory Valuation Report',
            'description' => 'Real-time summary of the total number and estimated value of all assets in inventory, broken down by branch, district, province, asset type and age.',
            'icon'        => 'fa-cubes',
        ],
        'sales_liquidation' => [
            'label'       => 'Sales & Liquidation Report',
            'description' => 'Total value of assets sold, actual proceeds collected and the variance (loss/gain) over a given period.',
            'icon'        => 'fa-line-chart',
        ],
        'aging_seized' => [
            'label'       => 'Aging Report for Seized Assets',
            'description' => 'Lists all assets in inventory grouped by the time they have been in Seized status (0-30, 30-60, 60-90, 90+ days).',
            'icon'        => 'fa-hourglass-half',
        ],
        'trend_asset_sell' => [
            'label'       => 'Easiest / Hardest Asset to Sell',
            'description' => 'By asset category showing average time to sell and sell-through rates.',
            'icon'        => 'fa-tags',
        ],
        'trend_branch_performance' => [
            'label'       => 'Branch Performance',
            'description' => 'Compares branches on default rates, seizure volumes and subsequent liquidation performance, including sales source breakdown.',
            'icon'        => 'fa-building',
        ],
        'trend_manager_performance' => [
            'label'       => 'Branch Manager / LC Performance',
            'description' => 'Identifies managers and LCs with the highest seizure rates and where collateral values fall short of the indicated loan valuation.',
            'icon'        => 'fa-user-secret',
        ],
    ];

    public function getReports(): array
    {
        return self::REPORTS;
    }

    public function getFilterOptions(): array
    {
        return [
            'provinces' => Province::orderBy('name')->get(['id', 'name']),
            'districts' => District::orderBy('name')->get(['id', 'name']),
            'offices'   => Office::orderBy('name')->get(['id', 'name']),
            'types'     => CollateralType::orderBy('name')->get(['id', 'name']),
            'categories'=> \App\Models\Collateral::CATEGORIES,
        ];
    }

    public function run(string $type, array $filters): array
    {
        return match ($type) {
            'inventory_valuation'     => $this->inventoryValuation($filters),
            'sales_liquidation'       => $this->salesLiquidation($filters),
            'aging_seized'            => $this->agingSeized($filters),
            'trend_asset_sell'        => $this->trendAssetSell($filters),
            'trend_branch_performance'=> $this->trendBranchPerformance($filters),
            'trend_manager_performance'=> $this->trendManagerPerformance($filters),
            default                   => throw new \InvalidArgumentException('Unknown report type: ' . $type),
        };
    }

    private function dateRange(array $f): array
    {
        $from = !empty($f['date_from'])
            ? Carbon::parse($f['date_from'])->startOfDay()
            : Carbon::now()->subYear()->startOfDay();
        $to = !empty($f['date_to'])
            ? Carbon::parse($f['date_to'])->endOfDay()
            : Carbon::now()->endOfDay();

        return [$from, $to];
    }

    private function applyCommonFilters($query, array $f): void
    {
        if (!empty($f['province_id'])) {
            $query->where('province_id', $f['province_id']);
        }
        if (!empty($f['district_id'])) {
            $query->where('district_id', $f['district_id']);
        }
        if (!empty($f['office_id'])) {
            $query->where('office_id', $f['office_id']);
        }
        if (!empty($f['collateral_type_id'])) {
            $query->where('collateral_type_id', $f['collateral_type_id']);
        }
        if (!empty($f['category'])) {
            $query->where('category', $f['category']);
        }
        if (!empty($f['condition'])) {
            $query->where('condition', $f['condition']);
        }
    }

    private function effectiveValue($row): float
    {
        $vetted = $row->vetted_valuation ?? null;
        if ($vetted !== null && $vetted !== '' && $vetted > 0) {
            return (float) $vetted;
        }
        return (float) ($row->current_worth ?? 0);
    }

    public function inventoryValuation(array $f): array
    {
        [$from, $to] = $this->dateRange($f);

        $query = Collateral::query()->with(['type', 'loan.office']);
        $this->applyCommonFilters($query, $f);

        $dateColumn = $f['age_basis'] === 'seized' ? 'seized_at' : 'created_at';
        $query->whereBetween($dateColumn, [$from, $to]);

        $items = $query->get();

        $summary = [
            'count'            => $items->count(),
            'estimated_value'  => $items->sum(fn ($i) => $this->effectiveValue($i)),
            'current_worth'    => $items->sum('current_worth'),
            'approved_value'   => $items->sum('approved_value'),
        ];

        $byOffice = $items->groupBy('office_id')->map(function ($group) {
            return [
                'label' => optional(optional($group->first()->loan)->office)->name ?? 'Unknown',
                'count' => $group->count(),
                'value' => $group->sum(fn ($i) => $this->effectiveValue($i)),
            ];
        })->values();

        $byProvince = $items->groupBy('province_id')->map(function ($group) {
            return [
                'label' => Province::find($group->first()->province_id)->name ?? 'Unknown',
                'count' => $group->count(),
                'value' => $group->sum(fn ($i) => $this->effectiveValue($i)),
            ];
        })->values();

        $byDistrict = $items->groupBy('district_id')->map(function ($group) {
            return [
                'label' => District::find($group->first()->district_id)->name ?? 'Unknown',
                'count' => $group->count(),
                'value' => $group->sum(fn ($i) => $this->effectiveValue($i)),
            ];
        })->values();

        $byType = $items->groupBy('collateral_type_id')->map(function ($group) {
            return [
                'label' => optional($group->first()->type)->name ?? 'Unknown',
                'count' => $group->count(),
                'value' => $group->sum(fn ($i) => $this->effectiveValue($i)),
            ];
        })->values();

        $byCategory = $items->groupBy('category')->map(function ($group) {
            return [
                'label' => \App\Models\Collateral::CATEGORIES[$group->first()->category] ?? ucfirst($group->first()->category ?? 'Uncategorized'),
                'count' => $group->count(),
                'value' => $group->sum(fn ($i) => $this->effectiveValue($i)),
            ];
        })->values();

        $byAge = $items->groupBy(function ($i) use ($dateColumn) {
            $date = $i->{$dateColumn};
            if (!$date) {
                return 'Unknown';
            }
            $days = Carbon::parse($date)->diffInDays(now());
            if ($days <= 30) return '0-30 days';
            if ($days <= 60) return '31-60 days';
            if ($days <= 90) return '61-90 days';
            return '90+ days';
        })->map(function ($group, $key) {
            return [
                'label' => $key,
                'count' => $group->count(),
                'value' => $group->sum(fn ($i) => $this->effectiveValue($i)),
            ];
        })->values();

        return compact('summary', 'byOffice', 'byProvince', 'byDistrict', 'byType', 'byCategory', 'byAge', 'items');
    }

    public function salesLiquidation(array $f): array
    {
        [$from, $to] = $this->dateRange($f);

        $query = Collateral::query()->with(['type', 'loan.office'])
            ->where('status', 'sold')
            ->whereBetween('sold_at', [$from, $to]);
        $this->applyCommonFilters($query, $f);

        $items = $query->get();

        $totalValue   = $items->sum(fn ($i) => (float) ($i->approved_value ?? $i->current_worth ?? 0));
        $totalProceeds= $items->sum('sold_price');
        $variance     = $totalProceeds - $totalValue;

        $byMonth = $items->groupBy(function ($i) {
            return Carbon::parse($i->sold_at)->format('Y-m');
        })->map(function ($group, $key) {
            $value = $group->sum(fn ($i) => (float) ($i->approved_value ?? $i->current_worth ?? 0));
            $proceeds = $group->sum('sold_price');
            return [
                'label'    => $key,
                'count'    => $group->count(),
                'value'    => $value,
                'proceeds' => $proceeds,
                'variance' => $proceeds - $value,
            ];
        })->sortKeys()->values();

        $byType = $items->groupBy('collateral_type_id')->map(function ($group) {
            $value = $group->sum(fn ($i) => (float) ($i->approved_value ?? $i->current_worth ?? 0));
            $proceeds = $group->sum('sold_price');
            return [
                'label'    => optional($group->first()->type)->name ?? 'Unknown',
                'count'    => $group->count(),
                'value'    => $value,
                'proceeds' => $proceeds,
                'variance' => $proceeds - $value,
            ];
        })->values();

        return compact('totalValue', 'totalProceeds', 'variance', 'byMonth', 'byType', 'items');
    }

    public function agingSeized(array $f): array
    {
        [$from, $to] = $this->dateRange($f);

        $query = Collateral::query()->with(['type', 'loan.office'])
            ->whereIn('status', ['seized_inventory', 'seizure_pending']);
        $this->applyCommonFilters($query, $f);
        $query->whereNotNull('seized_at');
        $query->whereBetween('seized_at', [$from, $to]);

        $items = $query->get();

        $buckets = [
            '0-30 days'  => ['count' => 0, 'value' => 0, 'items' => collect()],
            '31-60 days' => ['count' => 0, 'value' => 0, 'items' => collect()],
            '61-90 days' => ['count' => 0, 'value' => 0, 'items' => collect()],
            '90+ days'   => ['count' => 0, 'value' => 0, 'items' => collect()],
        ];

        foreach ($items as $item) {
            $days = Carbon::parse($item->seized_at)->diffInDays(now());
            $key = $days <= 30 ? '0-30 days' : ($days <= 60 ? '31-60 days' : ($days <= 90 ? '61-90 days' : '90+ days'));
            $buckets[$key]['count']++;
            $buckets[$key]['value'] += $this->effectiveValue($item);
            $buckets[$key]['items']->push($item);
        }

        $summary = [
            'count' => $items->count(),
            'value' => $items->sum(fn ($i) => $this->effectiveValue($i)),
        ];

        return compact('buckets', 'summary', 'items');
    }

    public function trendAssetSell(array $f): array
    {
        [$from, $to] = $this->dateRange($f);

        $soldQuery = Collateral::query()
            ->where('status', 'sold')
            ->whereBetween('sold_at', [$from, $to]);
        $this->applyCommonFilters($soldQuery, $f);
        $sold = $soldQuery->get();

        $allQuery = Collateral::query()->whereBetween('created_at', [$from, $to]);
        $this->applyCommonFilters($allQuery, $f);
        $all = $allQuery->get();

        $categories = $all->groupBy('category');

        $rows = $categories->map(function ($group, $catKey) use ($sold) {
            $catSold = $sold->where('category', $catKey);
            $sellThrough = $group->count() > 0 ? ($catSold->count() / $group->count()) * 100 : 0;
            $avgDays = $catSold->filter(fn ($i) => $i->sold_at && $i->created_at)
                ->avg(fn ($i) => Carbon::parse($i->created_at)->diffInDays(Carbon::parse($i->sold_at)));
            return [
                'category'      => \App\Models\Collateral::CATEGORIES[$catKey] ?? ucfirst($catKey ?? 'Uncategorized'),
                'total'         => $group->count(),
                'sold'          => $catSold->count(),
                'sell_through'  => round($sellThrough, 1),
                'avg_days'      => $avgDays ? round($avgDays, 1) : null,
            ];
        })->values();

        $rows = $rows->sortBy('avg_days')->values();

        return compact('rows');
    }

    public function trendBranchPerformance(array $f): array
    {
        [$from, $to] = $this->dateRange($f);

        $query = Collateral::query()->with(['type', 'loan.office'])
            ->whereBetween('created_at', [$from, $to]);
        $this->applyCommonFilters($query, $f);
        $items = $query->get();

        $supervisorId = optional(PlatformSetting::where('key', 'collateral_supervisor')->first())->value;

        $byOffice = $items->groupBy('office_id')->map(function ($group) use ($supervisorId) {
            $office = optional(optional($group->first()->loan)->office);
            $seized = $group->whereIn('status', ['seized_inventory', 'seizure_pending', 'valuation_completed', 'listed_for_sale', 'sold', 'written_off'])->count();
            $sold = $group->where('status', 'sold');
            $value = $sold->sum(fn ($i) => (float) ($i->approved_value ?? $i->current_worth ?? 0));
            $proceeds = $sold->sum('sold_price');
            $branchGenerated = $group->whereNull('vetted_valuation_by')->count();
            $supervisorGenerated = $group->where('vetted_valuation_by', $supervisorId)->count();
            $withinhere = $group->whereNotNull('vetted_valuation_by')->where('vetted_valuation_by', '<>', $supervisorId)->count();
            return [
                'label'               => $office->name ?? 'Unknown',
                'total'               => $group->count(),
                'seized'              => $seized,
                'sold'                => $sold->count(),
                'liquidation_value'   => $value,
                'proceeds'            => $proceeds,
                'variance'            => $proceeds - $value,
                'branch_generated'    => $branchGenerated,
                'supervisor_generated'=> $supervisorGenerated,
                'withinhere'          => $withinhere,
            ];
        })->values();

        return compact('byOffice');
    }

    public function trendManagerPerformance(array $f): array
    {
        [$from, $to] = $this->dateRange($f);

        $query = Collateral::query()->with(['created_by', 'loan.office'])
            ->whereBetween('created_at', [$from, $to]);
        $this->applyCommonFilters($query, $f);
        $items = $query->get();

        $byUser = $items->groupBy('created_by_id')->map(function ($group) {
            $user = $group->first()->created_by;
            $seized = $group->whereIn('status', ['seized_inventory', 'seizure_pending', 'valuation_completed', 'listed_for_sale', 'sold', 'written_off'])->count();
            $shortfall = $group->sum(function ($i) {
                $indicated = (float) ($i->approved_value ?? $i->current_worth ?? 0);
                $actual = (float) ($i->vetted_valuation ?? $i->current_worth ?? 0);
                return max(0, $indicated - $actual);
            });
            return [
                'name'          => $user ? ($user->first_name . ' ' . $user->last_name) : 'Unknown',
                'position'      => $user->position_name ?? '',
                'office'        => optional(optional($group->first()->loan)->office)->name ?? 'Unknown',
                'total'         => $group->count(),
                'seized'        => $seized,
                'seizure_rate'  => $group->count() > 0 ? round(($seized / $group->count()) * 100, 1) : 0,
                'shortfall'     => $shortfall,
            ];
        })->values()->sortByDesc('seizure_rate')->values();

        return compact('byUser');
    }
}
