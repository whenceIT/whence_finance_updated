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

    public static function dormant_recovery_unit_share($client, $loan)
    {
        $unitShareCount = Loan::where('client_id', $client->id)
            ->where('is_dormant_recovery', 1)
            ->where('status', 'closed')
            ->count();

        $dormantRecovery = $client->is_dormant_recovery ?? false;

        if (!$dormantRecovery) {
            return [
                'unit_share_count' => $unitShareCount,
                'status' => 'not_recovered',
                'message' => 'Client is not marked as recovered.',
                'unit_share_amount' => 0,
            ];
        }

        if ($unitShareCount >= 3) {
            return [
                'unit_share_count' => $unitShareCount,
                'status' => 'max_shares_reached',
                'message' => 'Client has already received maximum 3 unit shares.',
                'unit_share_amount' => 0,
            ];
        }

        $interestRate = GeneralHelper::determine_interest_rate($loan->id);
        $interestAmount = $loan->principal * $interestRate;
        $unitShareAmount = $interestAmount * 0.5;

        UnitShare::create([
            'unit' => $unitShareCount + 1,
            'amount' => $unitShareAmount,
            'loan_id' => $loan->id,
        ]);

        return [
            'unit_share_count' => $unitShareCount + 1,
            'status' => 'unit_share_created',
            'message' => 'Unit share created for loan #' . $loan->id,
            'unit_share_amount' => $unitShareAmount,
        ];
    }
}