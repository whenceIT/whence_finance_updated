<?php

namespace App\Helpers;

use App\Models\Loan;
use App\Models\Client;
use App\Models\Deposit;
use App\Models\LoanTransaction;
use App\Models\UnitShare;
use App\Helpers\GeneralHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialHelper
{
    public static function calculateInterest($principal, $rate, $days, $yearDays = 365)
    {
        return ($principal * $rate * $days) / ($yearDays * 100);
    }

    public static function calculateRepaymentAmount($principal, $interest, $fees = 0)
    {
        return $principal + $interest + $fees;
    }

    public static function calculateOutstandingBalance($clientId, $loanId = null)
    {
        $query = LoanTransaction::where('client_id', $clientId);
        
        if ($loanId) {
            $query->where('loan_id', $loanId);
        }

        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');

        return $totalDebit - $totalCredit;
    }

    public static function getClientTotalBorrowings($clientId)
    {
        return Loan::where('client_id', $clientId)
            ->whereIn('status', ['disbursed', 'closed', 'written_off'])
            ->sum('principal');
    }

    public static function getClientTotalRepayments($clientId)
    {
        return DB::table('loan_repayments')
            ->where('client_id', $clientId)
            ->sum('amount');
    }

    public static function getClientNetPosition($clientId)
    {
        $borrowings = self::getClientTotalBorrowings($clientId);
        $repayments = self::getClientTotalRepayments($clientId);

        return $borrowings - $repayments;
    }

    public static function formatCurrency($amount, $currency = 'K')
    {
        return $currency . number_format($amount, 2);
    }

    public static function calculateDaysInArrears($dueDate)
    {
        $due = Carbon::parse($dueDate);
        $today = Carbon::now();

        if ($today->greaterThan($due)) {
            return $today->diffInDays($due);
        }

        return 0;
    }

    public static function getLoanProfitability($loanId)
    {
        $loan = Loan::find($loanId);
        
        if (!$loan) {
            return null;
        }

        $totalInterest = $loan->repayment_schedules->sum('interest_amount');
        $totalPrincipal = $loan->principal;
        $totalCollected = $loan->payments->sum('amount');

        return [
            'principal' => $totalPrincipal,
            'expected_interest' => $totalInterest,
            'total_expected' => $totalPrincipal + $totalInterest,
            'collected' => $totalCollected,
            'outstanding' => ($totalPrincipal + $totalInterest) - $totalCollected,
            'recovery_rate' => $totalPrincipal + $totalInterest > 0 
                ? ($totalCollected / ($totalPrincipal + $totalInterest)) * 100 
                : 0,
        ];
    }

    public static function getClientFinancialSummary($clientId)
    {
        $client = Client::find($clientId);
        
        if (!$client) {
            return null;
        }

        return [
            'client_name' => $client->first_name . ' ' . $client->last_name,
            'account_number' => $client->account_no,
            'total_borrowings' => self::getClientTotalBorrowings($clientId),
            'total_repayments' => self::getClientTotalRepayments($clientId),
            'net_position' => self::getClientNetPosition($clientId),
            'active_loans' => Loan::where('client_id', $clientId)
                ->whereIn('status', ['disbursed', 'rescheduled'])
                ->count(),
            'closed_loans' => Loan::where('client_id', $clientId)
                ->where('status', 'closed')
                ->count(),
        ];
    }

    /**
     * Handles unit share allocation for dormant recovery loans.
     *
     * Checks whether a client qualifies for a unit share based on their dormant
     * recovery status and how many unit shares they have already received.
     * If eligible, calculates the unit share amount from the loan's interest
     * and creates a new UnitShare record.
     *
     * @param  \App\Models\Client  $client  The client associated with the loan.
     * @param  \App\Models\Loan    $loan    The loan being evaluated for unit share allocation.
     * @return array {
     *     unit_share_count: int,   // Current or updated count of unit shares for this client.
     *     status: string,          // 'not_recovered' | 'max_shares_reached' | 'unit_share_created'
     *     message: string,         // Human-readable description of the outcome.
     *     unit_share_amount: float // Amount allocated to the new unit share (0 if not created).
     * }
     */
    public static function dormant_recovery_unit_share($client, $loan)
    {
        // Count how many dormant recovery unit shares the client has already received.
        $unitShareCount = Loan::where('client_id', $client->id)
            ->where('shared', 1)
            ->count();

        if (!$loan || $loan->shared == 1) {
            return [
                'unit_share_count' => $unitShareCount,
                'status' => 'not_recovered',
                'message' => 'No dormant recovery loan found for client.',
                'unit_share_amount' => 0,
            ];
        }
        
        // Retrieve the client's dormant recovery flag, defaulting to false if not set.
        $dormantRecovery = $client->is_dormant_recovery ?? false;

        // If the client has not been marked as recovered, skip unit share allocation.
        if (!$dormantRecovery) {
            return [
                'unit_share_count' => $unitShareCount,
                'status' => 'not_recovered',
                'message' => 'Client is not marked as recovered.',
                'unit_share_amount' => 0,
            ];
        }

        // Enforce a maximum of 3 unit shares per client.
        if ($unitShareCount >= 3) {
            return [
                'unit_share_count' => $unitShareCount,
                'status' => 'max_shares_reached',
                'message' => 'Client has already received maximum 3 unit shares.',
                'unit_share_amount' => 0,
            ];
        }

        // Calculate the unit share amount as 50% of the loan's interest.
        $interestRate = GeneralHelper::determine_interest_rate($loan->id);
        $interestAmount = $loan->principal * $interestRate;
        $unitShareAmount = $interestAmount * 0.5;

        // Persist the new unit share record linked to the loan and responsible officer.
        UnitShare::create([
            'unit' => $unitShareCount + 1,
            'amount' => $unitShareAmount,
            'loan_id' => $loan->id,
            'office_id' => $loan->office_id,
            'user_id' => $loan->loan_officer_id,
        ]);
        $loan->update(['shared' => 1]);
        return [
            'unit_share_count' => $unitShareCount + 1,
            'status' => 'unit_share_created',
            'message' => 'Unit share created for loan #' . $loan->id,
            'unit_share_amount' => $unitShareAmount,
        ];
    }

    public static function dormant_client_info($client_id)
    {
        $client = Client::find($client_id);
        
        if (!$client) {
            return [
                'eligible' => false,
                'message' => 'Client not found.',
                'unit_share_count' => 0,
            ];
        }

        if ($client->approved_dormant != 1 || $client->is_dormant_recovery != 1) {
            return [
                'eligible' => false,
                'message' => 'Client is not eligible for dormant recovery.',
                'unit_share_count' => 0,
            ];
        }

        $unitShareCount = UnitShare::whereHas('loan', function ($query) use ($client_id) {
            $query->where('client_id', $client_id);
        })->count();

return [
            'eligible' => true,
            'message' => 'Client is eligible for dormant recovery.',
            'unit_share_count' => $unitShareCount,
        ];
    }

    public static function dormant_client_loan_info($loanId)
    {
        $loan = Loan::find($loanId);
        
        if (!$loan) {
            return 0;
        }

        $client = Client::find($loan->client_id);
        
        if (!$client) {
            return 0;
        }

        // $client->approved_dormant != 1 ||
        if ( $client->is_dormant_recovery != 1) {
            return 0;
        }

        $unitShareCount = UnitShare::where('loan_id', $loanId)->first() ?? 0;

        return $unitShareCount->amount ?? 0;
    }
}