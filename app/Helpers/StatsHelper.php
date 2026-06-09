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
        $query = Deposit::query()->withoutGlobalScope('approved');
        
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
        return OfficeDebt::where('office_id', '!=', 67)->sum('outstanding_amount');
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
            ->where('id', '!=', 67)
            ->get();
    }
    
    // for exemptions processing
    public static function getRequiredDepositTypes($officeId = null)
    {
        $settings = self::getBranchDepositSettings($officeId);
        
        $typeMapping = [
            'admin' => 1,
            'building' => 3,
            'statutory' => 5,
            'set_up_debt' => 0,
        ];
        
        $requiredTypes = [];
        
        foreach ($typeMapping as $settingKey => $depositTypeId) {
            if ($settings[$settingKey] === true) {
                $requiredTypes[] = $depositTypeId;
            }
        }
        
        $requiredTypes = array_merge($requiredTypes, [4, 6, 2]);
        return $requiredTypes;
    }
    
    // for skiping processing
    public static function getRequiredSkippedTypes($officeId = null)
    {
        $settings = self::getBranchSkipSettings($officeId);
        
        $typeMapping = [
            'admin' => 1,
            'building' => 3,
            'statutory' => 5,
            'set_up_debt' => 0,
        ];
        
        $requiredTypes = [];
        
        foreach ($typeMapping as $settingKey => $depositTypeId) {
            if ($settings[$settingKey] === true) {
                $requiredTypes[] = $depositTypeId;
            }
        }
        
        //difference is i dont want to merge the blocker types here, as this is for skipping deposits, not for checking if they are made or not. so only the types that are enabled for skipping will be returned here.
        return $requiredTypes;
    }
    
    public static function getBranchDepositSettings($officeId = null)
    {
        if ($officeId) {
            $key = 'branch_deposit_setting_' . $officeId;
            $setting = \App\Models\PlatformSetting::where('key', $key)->first();
            
            if ($setting) {
                return [
                    'id' => $setting->id,
                    'office_id' => $officeId,
                    'admin' => $setting->value['admin'] ?? true,
                    'building' => $setting->value['building'] ?? true,
                    'statutory' => $setting->value['statutory'] ?? true,
                    'set_up_debt' => $setting->value['set_up_debt'] ?? true,
                ];
            }
        }
        
        return [
            'id' => null,
            'office_id' => $officeId,
            'admin' => true,
            'building' => true,
            'statutory' => true,
            'set_up_debt' => true,
        ];
    }
    
    public static function getBranchSkipSettings($officeId = null)
    {
        if ($officeId) {
            $key = 'branch_block_skiping_' . $officeId;
            $setting = \App\Models\PlatformSetting::where('key', $key)->first();
            
            if ($setting) {
                return [
                    'id' => $setting->id,
                    'office_id' => $officeId,
                    'admin' => $setting->value['admin'] ?? true,
                    'building' => $setting->value['building'] ?? true,
                    'statutory' => $setting->value['statutory'] ?? true,
                    'set_up_debt' => $setting->value['set_up_debt'] ?? true,
                ];
            }
        }
        
        return [
            'id' => null,
            'office_id' => $officeId,
            'admin' => true,
            'building' => true,
            'statutory' => true,
            'set_up_debt' => true,
        ];
    }
}