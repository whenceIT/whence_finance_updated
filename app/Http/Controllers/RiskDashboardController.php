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
use App\Models\Loan;

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

        $lateDisbursementsThisWeek = Loan::where('created_at', '>=', now()->startOfWeek())
            ->where('created_at', '<=', now()->subHour())
            ->whereNotIn('status', ['disbursed', 'closed'])
            ->count();
            
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
            'lateDisbursementsThisWeek',
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

    /**
     * Return late disbursement loans grouped by province -> office
     * for modal display
     */
    public function lateDisbursementsDetail(Request $request)
    {
        $loans = Loan::with([
            'client' => fn($q) => $q->select('id', 'first_name', 'last_name', 'mobile', 'office_id'),
            'loan_officer' => fn($q) => $q->select('id', 'first_name', 'last_name', 'phone', 'email'),
            'office' => fn($q) => $q->select('id', 'name', 'province_id', 'district_id'),
            'office.province' => fn($q) => $q->select('id', 'name'),
            'office.district' => fn($q) => $q->select('id', 'name'),
        ])
            ->where('created_at', '>=', now()->startOfWeek())
            ->where('created_at', '<=', now()->subHour())
            ->whereNotIn('status', ['disbursed', 'closed'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by province -> office
        $grouped = $loans->groupBy(function ($loan) {
            return $loan->office->province->name ?? 'Unknown Province';
        })->map(function ($provinceLoans) {
            return $provinceLoans->groupBy(function ($loan) {
                return $loan->office->name ?? 'Unknown Office';
            })->map(function ($officeLoans) {
                return $officeLoans->map(function ($loan) {
                    return [
                        'id' => $loan->id,
                        'account_number' => $loan->account_number,
                        'external_id' => $loan->external_id,
                        'principal' => $loan->principal,
                        'status' => $loan->status,
                        'created_at' => $loan->created_at?->format('Y-m-d H:i'),
                        'client' => $loan->client ? [
                            'id' => $loan->client->id,
                            'name' => trim(($loan->client->first_name ?? '') . ' ' . ($loan->client->last_name ?? '')),
                            'phone' => $loan->client->mobile,
                        ] : null,
                        'loan_officer' => $loan->loan_officer ? [
                            'id' => $loan->loan_officer->id,
                            'name' => trim(($loan->loan_officer->first_name ?? '') . ' ' . ($loan->loan_officer->last_name ?? '')),
                            'phone' => $loan->loan_officer->phone,
                            'email' => $loan->loan_officer->email,
                        ] : null,
                    ];
                })->values();
            });
        });

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }
}