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
            return [
                'success' => false,
                'message' => 'No authenticated user.',
                'data' => [],
            ];
        }

        $loans = Loan::where('status', 'disbursed')
            ->where('loan_officer_id', $user->id)
            ->get();

        $closedLoans = [];
        foreach ($loans as $loan) {
            $balance = $loan->calculateBalance()['balance'];

            if ($balance == 0) {
                $loan->status = 'closed';
                $loan->save();
                $closedLoans[] = [
                    'id' => $loan->id,
                    'balance' => $balance,
                ];
            }
        }

        return [
            'success' => true,
            'data' => $closedLoans,
        ];
    }
}
