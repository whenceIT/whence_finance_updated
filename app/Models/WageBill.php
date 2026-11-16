<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WageBill extends Model
{
    protected $table = 'wage_bill';
    public $timestamps = false;

    protected $fillable = [
        'branch_id',
        'user_id',
        'cycle_start',

        'predicted_salary',

        'system_gross_pay',
        'system_paye',
        'system_napsa',
        'system_nhima',
        'system_net_pay',

        'gross_pay',
        'paye',
        'napsa',
        'nhima',
        'allowances',
        'advance_deductions',
        'net_pay',

        'manual_overrides',
        'status',
    ];

    protected $casts = [
        'cycle_start' => 'date:Y-m-d',
        'manual_overrides' => 'array',

        'predicted_salary' => 'decimal:2',

        'system_gross_pay' => 'decimal:2',
        'system_paye' => 'decimal:2',
        'system_napsa' => 'decimal:2',
        'system_nhima' => 'decimal:2',
        'system_net_pay' => 'decimal:2',

        'gross_pay' => 'decimal:2',
        'paye' => 'decimal:2',
        'napsa' => 'decimal:2',
        'nhima' => 'decimal:2',
        'allowances' => 'decimal:2',
        'advance_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Office::class, 'branch_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(WageBillAuditLog::class, 'wage_bill_id');
    }
}