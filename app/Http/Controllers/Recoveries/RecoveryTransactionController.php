<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Models\LoanTransaction;
use App\Models\RecoveryCase;
use App\Models\Office;
use App\Models\UserRole;
use App\Models\RecoveryFund;
use App\Services\AuditorService;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Carbon\Carbon;

class RecoveryTransactionController extends Controller
{
    protected $auditorService;

    public function __construct(AuditorService $auditorService)
    {
        $this->middleware('sentinel');
        $this->auditorService = $auditorService;
    }

    /**
     * Display approved recovery transactions
     * Shows all loan transactions where is_recovery = 1
     * Joins with Loan, RecoveryCase, and User (specialist) models
     */
    public function approvedRecoveries()
    {
        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $user = Sentinel::getUser();
        $userId = $user->id;
        $office_id = $user->office_id;
        $province_id = $user->province_id;
        $role = UserRole::where('user_id', $userId)->first();
        $offices = Office::all();

        // Build the base query with all necessary relationships
        $query = LoanTransaction::with([
            'loan.client',
            'loan.loan_officer',
            'loan.office',
            'office',
            'created_by',
            'payment_detail'
        ])
        ->where('is_recovery', 1)
        ->orderBy('created_at', 'desc');

        // Apply role-based filtering
        if ($role && $role->role_id == "6") {
            // Province manager - see all transactions in their province
            $officeIds = Office::where('province_id', $province_id)->pluck('id');
            $query->whereIn('office_id', $officeIds);
        } elseif (!Sentinel::hasAccess('settings')) {
            // Regular user - see only their office transactions
            $query->where('office_id', $office_id);
        }
        // Admin (settings access) sees all transactions

        $transactions = $query->get();

        // Enhance transactions with recovery case information
        $transactions->each(function($transaction) {
            if ($transaction->loan_id) {
                // Find associated recovery case
                $recoveryCase = RecoveryCase::where('loan_id', $transaction->loan_id)
                    ->with(['assignedSpecialist', 'originBranch'])
                    ->first();
                
                $transaction->recovery_case = $recoveryCase;
            }
        });

        // Calculate stats
        $totalAmount = $transactions->sum('credit');
        $uniqueCaseIds = $transactions->filter(function($transaction) {
            return $transaction->recovery_case !== null;
        })->pluck('recovery_case.id')->unique();
        $totalCases = $uniqueCaseIds->count();

        // Group transactions by office
        $transactionsByOffice = $transactions->groupBy(function($transaction) {
            if ($transaction->office) {
                return $transaction->office->name;
            } elseif ($transaction->loan && $transaction->loan->office) {
                return $transaction->loan->office->name;
            }
            return 'Unknown Office';
        });

        // Log audit for accessing approved recoveries
        $this->auditorService->logCustomAudit(
            'App\Models\LoanTransaction',
            $user->id,
            'accessed approved recovery transactions',
            $user->id,
            request(),
            [],
            [
                'action' => 'viewed_approved_recoveries',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'count' => $transactions->count()
            ],
            'recovery_transaction_access'
        );
        $funds = RecoveryFund::sum('amount');
        return view('recoveries.transactions.approved', compact(
            'transactions', 
            'transactionsByOffice', 
            'totalAmount', 
            'totalCases',
            'funds'
        ));
    }

    /**
     * Display detailed recovery ledger with client payment history
     * Filterable by Daily, Weekly, Monthly, Yearly
     */
    public function recoveryLedger(Request $request)
    {
        $user = Sentinel::getUser();
        $userId = $user->id;
        $office_id = $user->office_id;
        $province_id = $user->province_id;
        $role = UserRole::where('user_id', $userId)->first();

        // Get filter period from request (default: monthly)
        $period = $request->get('period', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $officeFilter = $request->get('office_id');
        $caseFilter = $request->get('case_id');

        // Build base query for recovery transactions
        $query = LoanTransaction::with([
            'loan.client',
            'loan.loan_officer',
            'loan.office',
            'office',
            'created_by',
            'payment_detail'
        ])
        ->where('is_recovery', 1)
        ->orderBy('created_at', 'desc');

        // Apply role-based filtering
        if ($role && $role->role_id == "6") {
            $officeIds = Office::where('province_id', $province_id)->pluck('id');
            $query->whereIn('office_id', $officeIds);
        } elseif (!Sentinel::hasAccess('settings')) {
            $query->where('office_id', $office_id);
        }

        // Apply date range filter
        if ($period !== 'custom' && !$startDate && !$endDate) {
            $now = Carbon::now();
            switch ($period) {
                case 'daily':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'weekly':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'yearly':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        } elseif ($period === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Apply office filter
        if ($officeFilter) {
            $query->where('office_id', $officeFilter);
        }

        // Apply case filter
        if ($caseFilter) {
            $caseLoanIds = RecoveryCase::where('id', $caseFilter)->pluck('loan_id');
            $query->whereIn('loan_id', $caseLoanIds);
        }

        $transactions = $query->get();

        // Enhance transactions with recovery case information
        $transactions->each(function($transaction) {
            if ($transaction->loan_id) {
                $recoveryCase = RecoveryCase::where('loan_id', $transaction->loan_id)
                    ->with(['assignedSpecialist', 'originBranch'])
                    ->first();
                $transaction->recovery_case = $recoveryCase;
            }
        });

        // Calculate summary stats
        $totalAmount = $transactions->sum('credit');
        $totalDebit = $transactions->sum('debit');
        $netAmount = $totalAmount - $totalDebit;
        $totalTransactions = $transactions->count();
        $uniqueCaseIds = $transactions->filter(function($transaction) {
            return $transaction->recovery_case !== null;
        })->pluck('recovery_case.id')->unique();
        $totalCases = $uniqueCaseIds->count();
        $uniqueClients = $transactions->filter(function($transaction) {
            return $transaction->loan && $transaction->loan->client;
        })->pluck('loan.client.id')->unique()->count();

        // Get offices for filter dropdown
        if (Sentinel::hasAccess('settings')) {
            $offices = Office::orderBy('name')->get();
        } elseif ($role && $role->role_id == "6") {
            $offices = Office::where('province_id', $province_id)->orderBy('name')->get();
        } else {
            $offices = Office::where('id', $office_id)->get();
        }

        // Get recovery cases for filter dropdown
        $recoveryCases = RecoveryCase::with('loan.client')
            ->when(!$officeFilter && ($role && $role->role_id == "6"), function($q) use ($province_id) {
                $officeIds = Office::where('province_id', $province_id)->pluck('id');
                $q->whereIn('origin_branch_id', $officeIds);
            })
            ->when(!$officeFilter && !Sentinel::hasAccess('settings') && !($role && $role->role_id == "6"), function($q) use ($office_id) {
                $q->where('origin_branch_id', $office_id);
            })
            ->get();

        // Log audit
        $this->auditorService->logCustomAudit(
            'App\Models\LoanTransaction',
            $user->id,
            'accessed recovery ledger',
            $user->id,
            request(),
            [],
            [
                'action' => 'viewed_recovery_ledger',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'period' => $period,
                'count' => $transactions->count()
            ],
            'recovery_ledger_access'
        );

        $funds = RecoveryFund::sum('amount');

        return view('recoveries.transactions.ledger', compact(
            'transactions',
            'totalAmount',
            'totalDebit',
            'netAmount',
            'totalTransactions',
            'totalCases',
            'uniqueClients',
            'period',
            'startDate',
            'endDate',
            'officeFilter',
            'caseFilter',
            'offices',
            'recoveryCases',
            'funds'
        ));
    }
}