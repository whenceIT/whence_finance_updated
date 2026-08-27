<?php

namespace App\Http\Controllers;

use App\Services\CollateralService;
use Illuminate\Http\Request;

class CollateralReportController extends Controller
{
    protected CollateralService $service;

    public function __construct(CollateralService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $reports = $this->service->getReports();

        return view('collateral.reports.index', compact('reports'));
    }

    public function form(Request $request, string $type)
    {
        $reports = $this->service->getReports();
        if (!isset($reports[$type])) {
            abort(404);
        }

        $report = $reports[$type];
        $filters = $this->service->getFilterOptions();

        return view('collateral.reports.form', array_merge(compact('type', 'report'), $filters));
    }

    public function generate(Request $request, string $type)
    {
        $reports = $this->service->getReports();
        if (!isset($reports[$type])) {
            abort(404);
        }

        $filters = $request->only([
            'date_from',
            'date_to',
            'province_id',
            'district_id',
            'office_id',
            'collateral_type_id',
            'category',
            'condition',
            'age_basis',
        ]);

        $data = $this->service->run($type, $filters);
        $report = $reports[$type];

        $viewMap = [
            'inventory_valuation'       => 'collateral.reports.inventory_valuation',
            'sales_liquidation'         => 'collateral.reports.sales_liquidation',
            'aging_seized'              => 'collateral.reports.aging_seized',
            'trend_asset_sell'          => 'collateral.reports.trend_asset_sell',
            'trend_branch_performance'  => 'collateral.reports.trend_branch_performance',
            'trend_manager_performance' => 'collateral.reports.trend_manager_performance',
        ];

        return view($viewMap[$type], array_merge(compact('type', 'report', 'filters'), $data));
    }
}
