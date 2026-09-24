<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\BankDepositLog;
use App\Models\SetupDebtTransaction;
use App\Models\Deadline;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Office;

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
        $debtSetupDeadline = Deadline::where('name', 'Debt Setup Cost')->first();

        // Pending approvals counts
        $pendingDepositApprovals = Deposit::withoutGlobalScope('approved')->whereNull('status')
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
            'statutoryDeadline',
            'debtSetupDeadline'
        ));
    }

    /**
     * Return cash balances for every branch that has a withinhere_wallet_id,
     * fetched live from the WithinHere branch_ledger API.
     */
    public function branchCashBalances(Request $request)
    {
        $offices = Office::select('id', 'name', 'withinhere_wallet_id')
            ->whereNotNull('withinhere_wallet_id')
            ->where('withinhere_wallet_id', '!=', '')
            ->get();

        $startDate = '2025-01-01';
        $endDate   = now()->format('Y-m-d');

        $results = [];

        foreach ($offices as $office) {
            $cashBalance = null;
            $error       = null;

            try {
                $response = Http::timeout(60)
                    ->post(
                        'https://withinheremobileapi.com/api/v1/lmsuser/branch_ledger',
                        [
                            'wallet_id'  => $office->withinhere_wallet_id,
                            'start_date' => $startDate,
                            'end_date'   => $endDate,
                        ]
                    );

                if ($response->successful()) {
                    $data        = $response->json();
                    $cashBalance = $data['user']['cash_balance'] ?? null;
                } else {
                    $error = 'API error (' . $response->status() . ')';
                }
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }

            $results[] = [
                'office_id'   => $office->id,
                'office_name' => $office->name,
                'wallet_id'   => $office->withinhere_wallet_id,
                'balance'     => $cashBalance,
                'error'       => $error,
            ];
        }

        return response()->json([
            'success' => true,
            'offices' => $results,
        ]);
    }
}