<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankDepositLog;
use App\Models\SetupDebtTransaction;
use App\Models\Deadline;
use App\Models\Deposit;
use App\Models\Expense;

class RiskDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function index(Request $request)
    {
        $today = now()->toDateString();
        
        // Get today's collected setup debt
        $collectedSetupDebtToday = SetupDebtTransaction::whereDate('created_at', $today)
            ->sum('amount');
            
        // Get today's collected building (deposit_type = 3)
        $collectedBuildingToday = BankDepositLog::where('deposit_type', 3)
            ->whereDate('created_date', $today)
            ->sum('amount');
            
        // Get today's collected statutory (deposit_type = 5)
        $collectedStatutoryToday = BankDepositLog::where('deposit_type', 5)
            ->whereDate('created_date', $today)
            ->sum('amount');
            
        // Get today's collected administration (deposit_type = 1)
        $collectedAdminToday = BankDepositLog::where('deposit_type', 1)
            ->whereDate('created_date', $today)
            ->sum('amount');
            
        // Get deadlines for countdown
        $buildingDeadline = Deadline::where('name', 'Building & Infrastructure fee deposits')->first();
        $adminDeadline = Deadline::where('name', 'Administration Department fee deposit')->first();
        $statutoryDeadline = Deadline::where('name', 'Statutory payments deposits')->first();

        // Pending approvals counts
        $pendingDepositApprovals = Deposit::whereNull('status')
            ->whereHas('bankDepositLog')
            ->count();

        $pendingExpenseApprovals = Expense::where(function($q) {
                $q->where('status', '!=', 'approved')->orWhereNull('status');
            })->count();
            
        return view('risk.dashboard', compact(
            'collectedSetupDebtToday',
            'collectedBuildingToday',
            'collectedStatutoryToday',
            'collectedAdminToday',
            'pendingDepositApprovals',
            'pendingExpenseApprovals',
            'buildingDeadline',
            'adminDeadline',
            'statutoryDeadline'
        ));
    }
}