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
            $setting = self::where('key', 'branch_deposit_setting')
                ->where('value->office_id', $officeId)
                ->first();
                
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
}
