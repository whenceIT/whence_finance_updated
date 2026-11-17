<?php

namespace App\Services;

use App\Models\Loan;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class CleanUpService
{
    public function closeLoansWithZeroBalance()
    {
        // $user = Sentinel::getUser();
        // if (!$user) {
        //     return;
        // }

        // $loans = Loan::where('status', 'disbursed')
        //     ->where('loan_officer_id', $user->id)
        //     ->with('transactions')
        //     ->get();

        // foreach ($loans as $loan) {
        //     $balance = $loan->calculateBalance()['balance'];
        //     if ($balance && $balance < 1) {
        //         $loan->status = 'closed';
        //         $loan->save();
        //     }
        // }
    }
}
