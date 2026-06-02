<?php

namespace App\Helpers;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BlockerHelper
{
    /**
     * Debt Blocker for Loan Operations
     *
     * Checks whether the office has made its required monthly setup deposit (deposit_type 0)
     * and returns both the payment status and the current outstanding balance from office_debts.
     *
     * @param object $user
     * @return array{status: bool, balance: float|int|null}
     *         status  = true  → deposit recorded this month (no blocker)
     *         status  = false → no deposit found this month (show blocker)
     *         balance = outstanding_amount from office_debts table for this office/month
     */
    public static function debt_blocker($user): array
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

        if (isset($settings['set_up_debt']) && $settings['set_up_debt'] == false) {
            return [
                'status'  => true,
                'balance' => 0,
            ];
        }
        
        return [
            'status'  => $outstandingDebtPaidThisMonth,
            'balance' => $outstandingBalance,
        ];

    }

    public static function deposit_blocker(): string
    {
        return "Your office has not made the required monthly deposit of K5,000. Please contact your branch manager to resolve this issue and regain access to loan operations.";   

    }

    public static function monthlyDepositExists($user): bool
    {
        $officeId = $user->office_id ?? null;

        $settings = \App\Models\PlatformSetting::getBranchDepositSettings($officeId);

          
        if (isset($user->role->role_id) && $user->role->role_id != 4) {
            return true;
        }

        if (!$officeId) {
            return false;
        }

        $now = Carbon::now();

        $enabledTypes = [];
        if (isset($settings['admin']) && $settings['admin'] == true) $enabledTypes[] = 1;
        if (isset($settings['building']) && $settings['building'] == true) $enabledTypes[] = 3;
        if (isset($settings['statutory']) && $settings['statutory'] == true) $enabledTypes[] = 5;
        if (isset($settings['set_up_debt']) && $settings['set_up_debt'] == true) $enabledTypes[] = 0;

        if (empty($enabledTypes)) {
            return true;
        }

        $types = DB::table('deposits')
            ->whereIn('deposit_type', $enabledTypes)
            ->where('office', $officeId)
            ->where('date', '>=', $now->format('Y-m') . '-01')
            ->where('date', '<=', $now->format('Y-m') . '-' . $now->format('t'))
            ->pluck('deposit_type')
            ->unique()
            ->values()
            ->toArray();
        
        return count(array_intersect($enabledTypes, $types)) === count($enabledTypes);
        
    }
    
}
