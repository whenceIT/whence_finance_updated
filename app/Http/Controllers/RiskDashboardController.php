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
        $collectedBuildingToday = Deposit::join('bank_deposit_log', 'deposits.id', '=', 'bank_deposit_log.deposit_id')
            ->where('deposits.deposit_type', 3)
            ->whereDate('bank_deposit_log.created_date', $today)
            ->sum('deposits.amount');
            
        // Get today's collected statutory (deposit_type = 5)
        $collectedStatutoryToday = Deposit::join('bank_deposit_log', 'deposits.id', '=', 'bank_deposit_log.deposit_id')
            ->where('deposits.deposit_type', 5)
            ->whereDate('bank_deposit_log.created_date', $today)
            ->sum('deposits.amount');
            
        // Get today's collected administration (deposit_type = 1)
        $collectedAdminToday = Deposit::join('bank_deposit_log', 'deposits.id', '=', 'bank_deposit_log.deposit_id')
            ->where('deposits.deposit_type', 1)
            ->whereDate('bank_deposit_log.created_date', $today)
            ->sum('deposits.amount');
            
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