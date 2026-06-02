<?php

namespace App\Helpers;

use App\Models\Office;
use App\Models\Deposit;
use App\Models\OfficeDebt;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsHelper
{
    public static function getBranchDepositStats($officeId = null, $period = 'month')
    {
        $query = Deposit::query();
        
        if ($officeId) {
            $query->where('office', $officeId);
        }
        
        $now = Carbon::now();
        switch ($period) {
            case 'year':
                $query->whereYear('date', $now->year);
                break;
            case 'quarter':
                $query->where('date', '>=', $now->startOfQuarter()->toDateString())
                      ->where('date', '<=', $now->endOfQuarter()->toDateString());
                break;
            case 'month':
            default:
                $query->where('date', '>=', $now->startOfMonth()->toDateString())
                      ->where('date', '<=', $now->endOfMonth()->toDateString());
                break;
        }
        
        return $query->sum('amount');
    }
    
    public static function getTotalOffices()
    {
        return Office::where('active', 1)->count();
    }
    
    public static function getTotalDebt()
    {
        return OfficeDebt::sum('outstanding_amount');
    }
    
    public static function getDisbursedLoansCount($officeId = null)
    {
        $query = Loan::where('status', 'disbursed');
        
        if ($officeId) {
            $query->where('office_id', $officeId);
        }
        
        return $query->count();
    }
    
    public static function getDisbursedLoansAmount($officeId = null)
    {
        $query = Loan::where('status', 'disbursed');
        
        if ($officeId) {
            $query->where('office_id', $officeId);
        }
        
        return $query->sum('principal_amount');
    }
    
    public static function getActiveOffices()
    {
        return Office::where('active', 1)
            ->orderBy('name')
            ->select('id', 'name', 'external_id')
            ->get();
    }
}