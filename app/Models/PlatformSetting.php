<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'json',
    ];

public static function getBranchDepositSettings($officeId = null)
    {
        if ($officeId) {
            $key = 'branch_deposit_setting_' . $officeId;
            $setting = self::where('key', $key)->first();
            
            if ($setting) {
                return [
                    'id' => $setting->id,
                    'office_id' => $officeId,
                    'admin' => $setting->value['admin'] ?? false,
                    'building' => $setting->value['building'] ?? false,
                    'statutory' => $setting->value['statutory'] ?? false,
                    'set_up_debt' => $setting->value['set_up_debt'] ?? false,
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

    public static function getBranchBlockSkipSettings($officeId = null)
    {
        if ($officeId) {
            $key = 'branch_block_skiping_' . $officeId;
            $setting = self::where('key', $key)->first();
            
            if ($setting) {
                return [
                    'id' => $setting->id,
                    'office_id' => $officeId,
                    'admin' => $setting->value['admin'] ?? false,
                    'building' => $setting->value['building'] ?? false,
                    'statutory' => $setting->value['statutory'] ?? false,
                    'set_up_debt' => $setting->value['set_up_debt'] ?? false,
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

    public static function getAllOfficesWithSettings()
    {
        $offices = \App\Models\Office::orderBy('name')->get();
        
        return $offices->map(function ($office) {
            $settings = self::getBranchDepositSettings($office->id);
            return [
                'id' => $office->id,
                'name' => $office->name,
                'code' => $office->external_id ?? '#'.$office->id,
                'admin' => $settings['admin'],
                'building' => $settings['building'],
                'statutory' => $settings['statutory'],
                'set_up_debt' => $settings['set_up_debt'],
            ];
        })->all();
    }

    public static function getAllOfficesWithBlockSkipSettings()
    {
        $offices = \App\Models\Office::orderBy('name')->get();
        
        return $offices->map(function ($office) {
            $settings = self::getBranchBlockSkipSettings($office->id);
            return [
                'id' => $office->id,
                'name' => $office->name,
                'code' => $office->external_id ?? '#'.$office->id,
                'admin' => $settings['admin'],
                'building' => $settings['building'],
                'statutory' => $settings['statutory'],
                'set_up_debt' => $settings['set_up_debt'],
            ];
        })->all();
    }
}
