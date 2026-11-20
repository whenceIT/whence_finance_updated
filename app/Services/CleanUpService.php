<?php

namespace App\Services;

use App\Models\Loan;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Log;

class CleanUpService
{
    public function hasZeroBalance($loanId): bool
    {
        $loan = Loan::with('transactions')->find($loanId);

        if (!$loan) {
            return false;
        }

        return $loan->calculateBalance()['balance'] == 0;
    }

    public function closeLoansWithZeroBalance(): array
    {
        $user = Sentinel::getUser();
        if (!$user) {
            Log::warning('Loan cleanup skipped because no user is authenticated.');

            return [
                'success' => false,
                'message' => 'No authenticated user.',
                'data' => [],
            ];
        }

        $loans = Loan::where('status', 'disbursed')
            ->where('loan_officer_id', $user->id)
            ->get();

        Log::debug('Disbursed loans selected for zero-balance cleanup.', [
            'user_id' => $user->id,
            'loan_ids' => $loans->pluck('id')->all(),
        ]);

        $closedLoans = [];
        foreach ($loans as $loan) {
            $balance = $loan->calculateBalance()['balance'];
            Log::debug('Loan balance checked during cleanup.', [
                'loan_id' => $loan->id,
                'has_zero_balance' => $balance == 0,
            ]);

            if ($balance == 0) {
                $loan->status = 'closed';
                $loan->save();
                $closedLoans[] = [
                    'id' => $loan->id,
                    'balance' => $balance,
                ];
            }
        }

        Log::info('Loan cleanup completed.', [
            'user_id' => $user->id,
            'checked_count' => $loans->count(),
            'closed_loan_ids' => array_column($closedLoans, 'id'),
        ]);

        return [
            'success' => true,
            'data' => $closedLoans,
        ];
    }
}
