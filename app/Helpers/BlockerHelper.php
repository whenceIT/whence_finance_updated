<?php

namespace App\Helpers;

use App\Models\Loan;
use App\Models\DebtBalances;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class BlockerHelper
{
    /**
     * Debt Blocker for Loan Operations
     *
     * Checks whether the office has made its required monthly setup cost deposit (deposit_type 0)
     * and returns both the payment status and the current outstanding balance from office_debts.
     *
     * @param object $user
     * @return array{status: bool, balance: float|int|null}
     *         status  = true  → deposit recorded this month (no blocker)
     *         status  = false → no deposit found this month (show blocker)
     *         balance = outstanding_amount from office_debts table for this office/month
     */
    public static function debt_blocker($user)
    {
        $officeId = $user->office_id ?? null;

        $settings = \App\Models\PlatformSetting::getBranchDepositSettings($officeId);
        

        if (isset($user->role->role_id) && $user->role->role_id != 4) {
            return [
                'status'  => true,
                'balance' => 0,
            ];
        }
        if (!$officeId) {
            return ['status' => true, 'balance' => 0]; // Can't determine office → do not block
        }

        $now = Carbon::now();

        $outstandingDebtPaidThisMonth = DB::table('deposits')
            ->where('deposit_type', 0) //Set up debt has deposit_type_id = 0 in office_debts table
            ->where('office', $officeId)
            ->where('date', '>=', $now->format('Y-m') . '-01')
            ->where('date', '<=', $now->format('Y-m') . '-31')
            ->exists();

        $outstandingBalance = DB::table('office_debts')
            ->where('office_id', $officeId)
            ->where('deposit_type_id', 0) // Set up debt has deposit_type_id = 0 in office_debts table
            ->value('outstanding_amount') ?? 0;

        if (isset($settings['set_up_debt']) && $settings['set_up_debt'] == true) {
            return [
                'status'  => false,
                'balance' => 0,
            ];
        }
        
        return [
            'status'  => $outstandingDebtPaidThisMonth,
            'balance' => $outstandingBalance,
        ];

    }

    public static function ledger_blocker()
    {
        $user = Sentinel::getUser();
        $officeId = $user->office_id ?? null;
        // overall: full months from Jan 1 this year through 28th of current month
        $overallPeriodMonths = 6;   

        if(request()->path() == 'user/branch_deposits' || request()->path() == 'accounting/money_movements' || request()->path() == 'accounting/expenses' || request()->path() == 'expense/data'){
            return [
                'status'=>false,
                'amount' => 0,
                'deposit_type'=> '',
                'message'=> ''
            ];
        }
        // if (true) {
        if (isset($user->role->role_id) && !in_array($user->role->role_id, [4, 3])) {
            return [
                'status'=>false,
                'amount' => 0,
                'deposit_type'=> '',
                'message'=> 'Not a BM or LC'
            ];
        }
        
        // Except Choma and Mongu for June only
        if (($user->office_id == 11 || $user->office_id == 18) && date('n') == 6) {
            return [
                'status'=>false,
                'amount' => 0,
                'deposit_type'=> '',
                'message'=> 'Except this office'
            ];
        }

        if (!$officeId) {
            return null;
        }

        $ledgers = DebtBalances::where('office_id', $officeId)->get();

        if ($ledgers->isEmpty()) {
            return [
                'status'=>false,
                'amount' => 0,
                'deposit_type'=> '',
                'message'=> 'No ledgers found for this office'
            ];
        }

        foreach ($ledgers as $key => $ledger) {
            if (!$ledger) {
                return null;
            }
            $depositType = \App\Models\DepositType::where('id', $ledger->deposit_type_id)
                        ->first();

            if (!$depositType) {
                continue; // Skip if deposit type not found
            }

            $deposits =  Deposit::with('bankDepositLog')
                ->where('office', $officeId)
                ->where('deposit_type', $ledger->deposit_type_id)
                ->get();

            $months = $overallPeriodMonths - \App\Models\DepositMonthExemption::get_months_exempted($officeId, $depositType);
        
            $system_balance = (((int)$depositType->monthly_amount * $months) - $deposits->sum('amount'));
            
            if($system_balance > $ledger->balance){
                $whatsNotRecorded = $system_balance - $ledger->balance;
                return [
                    'status'=>true,
                    'amount' => $whatsNotRecorded,
                    'deposit_type'=> $depositType->name,
                    'message'=> 'Blocked',
                    'system_balance'=> $system_balance,
                    'ledger_balance'=> $ledger->balance
                ];
            }
        }

        return [
            'status'=>false,
            'amount' => 0,
            'deposit_type'=> '',
            'message'=> 'All ledgers cleared and unblocked',
            'system'=>  $system_balance,
            'ledger'=>  $ledger->balance
        ];
    }

    public static function deposit_blocker(): string
    {
        return "Your office has not made the required monthly deposit of K5,000. Please contact your branch manager to resolve this issue and regain access to loan operations.";   
    }

    public static function monthlyDepositExists($user)
    {
        $officeId = $user->office_id ?? null;

        if (isset($user->role->role_id) && !in_array($user->role->role_id, [4, 3])) {
            return true;
        }


        if (!$officeId) {
            return false;
        }

        $now = Carbon::now();

        $enabledTypes = \App\Helpers\StatsHelper::getRequiredSkippedTypes($officeId);
  
        
        if (empty($enabledTypes)) {
            return true;
        }

        $types = DB::table('deposit_types as dt')
            ->leftJoin('deposits as d', function ($join) use ($officeId, $now) {
                $join->on('dt.id', '=', 'd.deposit_type')
                    ->where('d.office', $officeId)
                    ->whereBetween('d.date', [
                        $now->copy()->startOfMonth()->toDateString(),
                        $now->copy()->endOfMonth()->toDateString(),
                    ]);
            })
            ->whereIn('dt.id', $enabledTypes)
            ->whereNotNull('dt.monthly_amount')
            ->groupBy('dt.id', 'dt.monthly_amount')
            ->havingRaw('COALESCE(SUM(d.amount), 0) < dt.monthly_amount')
            ->pluck('dt.id')
            ->toArray();

        return empty($types) ? false : true;
        
    }
    
}
