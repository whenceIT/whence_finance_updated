<?php

namespace App\Helpers;

use App\Models\Blockage;
use App\Models\Loan;
use App\Models\DebtBalances;
use App\Models\Deposit;
use App\Models\SetupDebtCost;
use App\Models\SetupDebtTransaction;
use App\Models\Deadline;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class BlockerHelper
{
    /**
     * Debt Blocker for Loan Operations
     *
     * @param object $user
     * @return array{status: bool, balance: int}
     *         status  = true  → no blocker (allowed)
     *         status  = false → blocker active (needs deposit)
     *         balance = 0
     */
    

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
        if (true) {
            return [
                'status'=>false,
                'amount' => 0,
                'deposit_type'=> '',
                'message'=> 'Not a BM or LC'
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

    public static function debt_blocker($user) 
    {
        
        $officeId = $user->office_id ?? null;
        
        // Super admin or non-employee roles bypass debt check
        // if (isset($user->role->role_id) &&  && !in_array($user->role->role_id, [4, 3])) {
        //     return false;
        // }
        
        // No office ID means no debt to check
        if (!$officeId) {
            return false;
        }
        
        $now = Carbon::now();
        
        // Check if office is listed in debt cost table
        $listed = SetupDebtCost::where('office_id', $officeId)->exists();
        
       
        // If not listed, no debt check needed → allow
        if (!$listed) {
            return false;
        }
        
        // Office IS listed → check for 5000 transaction this month
        $hasTransaction = SetupDebtTransaction::where('office_id', $officeId)
            ->whereBetween('transaction_date', [
                $now->copy()->startOfMonth()->toDateString(), 
                $now->copy()->endOfMonth()->toDateString()
            ])
            ->havingRaw('SUM(amount) >= 5000')
            ->exists();

            
        return !$hasTransaction; 
     }

     public static function cleanupExpiredDeadlines()
     {
         $expired = Deadline::where('countdown_date', '<', Carbon::now())->get();

         if ($expired->isEmpty()) {
             return [
                 'deleted' => 0,
                 'names' => [],
                 'message' => 'No expired deadlines found'
             ];
         }

         $names = $expired->pluck('name')->toArray();
         $count = $expired->count();

         $expired->each(function ($deadline) {
             $deadline->delete();
         });

         return [
             'deleted' => $count,
             'names' => $names,
             'message' => 'Deleted ' . $count . ' expired deadline(s): ' . implode(', ', $names)
         ];
    }


    public static function autolock(){

        //Get the latest expired deadline
        $expired = Deadline::where('countdown_date', '<', Carbon::now())->first();

        if (!$expired) {
            return [
                'status' => false,
                'message' => 'No expired deadlines found',
                'deposit_type_id' => null,
                'exempted_offices' => [],
                'paid_offices' => [],
                'locked_offices' => [],
            ];
        }

        // Get the deposit name = $name
        $name = $expired->name;

        // a. Get the deposit type id where name matches $name
        $depositType = \App\Models\DepositType::where('name', 'like', '%' . $name . '%')->first();

        if (!$depositType) {
            return [
                'status' => false,
                'message' => 'No deposit type found matching deadline: ' . $name,
                'deposit_type_id' => null,
                'exempted_offices' => [],
                'paid_offices' => [],
                'locked_offices' => [],
            ];
        }

        $depositTypeId = $depositType->id;

        // b. Get offices that are exempted in the current month for this deposit type
        $exemptedOffices = \App\Models\DepositMonthExemption::getExemptedOfficesCurrentMonth($depositTypeId);
        $exemptedIds = $exemptedOffices->pluck('id')->toArray();

        // c. Get offices that have paid for this deposit type for the current month
        $paidOffices = \App\Models\Deposit::getOfficesWithDepositCurrentMonth($depositTypeId);
        $paidIds = $paidOffices->pluck('id')->toArray();

        //d. lock offices that are not paid and not exempted
        // by creating records for them in the Blockages model
        $allOfficeIds = \App\Models\Office::pluck('id')->toArray();
        $lockedIds = array_diff($allOfficeIds, $paidIds, $exemptedIds);

        foreach ($lockedIds as $officeId) {
            if($officeId != 67){
                Blockage::firstOrCreate([
                    'office_id' => $officeId,
                ], [
                    'reason' => 'Auto-locked: expired deadline "' . $name . '" - office has not deposited for current month.',
                ]);
            }
        }

        self::cleanupExpiredDeadlines();
        return [
            'status' => !empty($lockedIds),
            'message' => !empty($lockedIds)
                ? 'Deadline "' . $name . '" expired. ' . count($lockedIds) . ' office(s) locked (not paid, not exempted).'
                : 'Deadline "' . $name . '" expired. No offices need to be locked.',
            'deposit_type_id' => $depositTypeId,
            'exempted_offices' => $exemptedIds,
            'paid_offices' => $paidIds,
            'locked_offices' => array_values($lockedIds),
        ];
    }

}
