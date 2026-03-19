<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Services\RecoveryDashboardService;
use Illuminate\Http\Request;

class RecoveryDashboardController extends Controller
{
    public function __construct(public RecoveryDashboardService $dashboard)
    {
        $this->middleware('sentinel');
    }

    public function overview(Request $request)
    {
        
        // Increase PHP upload limits to handle large files
        ini_set('max_execution_time', 600); // 10 minutes
        ini_set('max_input_time', 600); // 10 minutes
        ini_set('memory_limit', '256M');

        $period   = $request->get('period', 'month');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        // If custom selected but no dates given, fall back to month
        if ($period === 'custom' && (!$dateFrom || !$dateTo)) {
            $period   = 'month';
            $dateFrom = null;
            $dateTo   = null;
        }
        $kpis            = $this->dashboard->getExecutiveKpis($period, $dateFrom, $dateTo);

        $pipeline        = $this->dashboard->getPipelineData($dateFrom, $dateTo);
        $specialists     = $this->dashboard->getSpecialistPerformance($period, $dateFrom, $dateTo);
        $branchBreakdown = $this->dashboard->getBranchBreakdown($period, $dateFrom, $dateTo);
        $recentActivity  = $this->dashboard->getRecentActivity(10);
        $monthlyTrend    = $this->dashboard->getMonthlyTrend();
        $recoveryMix     = $this->dashboard->getRecoveryMix($period, $dateFrom, $dateTo);

        $categories = \App\Models\RecoveryCase::CATEGORIES;
        
        return view('recoveries.dashboard.index', compact(
            'period', 'dateFrom', 'dateTo', 'kpis', 'pipeline', 'specialists', 'categories',
            'branchBreakdown', 'recentActivity', 'monthlyTrend', 'recoveryMix'
        ));
    }
}