<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Services\RecoveryDashboardService;
use App\Models\RecoveryFund;
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
        
        $funds = RecoveryFund::sum('amount');


        //Non filtered data
        $overal_tt_recovered = \App\Models\LoanTransaction::where('is_recovery', 1)->sum('credit');
        $overal_tt_attribution = \App\Models\Expense::where('is_attribution', 1)->sum('amount');

        return view('recoveries.dashboard.index', compact(
            'period', 'dateFrom', 'dateTo', 'kpis', 'pipeline', 'specialists', 'categories',
            'branchBreakdown', 'recentActivity', 'monthlyTrend', 'recoveryMix', 'funds',
            // unfiltered data
            'overal_tt_recovered', 'overal_tt_attribution'
        ));
    }

    public function getFunds()
    {
        $funds = RecoveryFund::get();
        $totalAmount = RecoveryFund::sum('amount');

        return response()->json([
            'funds' => $funds,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function storeFund(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $fund = RecoveryFund::create($validated);

        return response()->json([
            'success' => true,
            'fund' => $fund,
            'message' => 'Recovery fund entry created successfully',
        ]);
    }

    public function updateFund(Request $request, $id)
    {
        $fund = RecoveryFund::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $fund->update($validated);

        return response()->json([
            'success' => true,
            'fund' => $fund,
            'message' => 'Recovery fund entry updated successfully',
        ]);
    }

    public function destroyFund($id)
    {
        $fund = RecoveryFund::findOrFail($id);
        $fund->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recovery fund entry deleted successfully',
        ]);
    }
}