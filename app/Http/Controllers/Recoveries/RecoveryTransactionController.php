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
}