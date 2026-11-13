<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentalWageBill extends Model
{
    protected $table = 'departmental_wage_bills';

    protected $fillable = [
        'user_id',
        'cycle_start',
        'basic_salary',
        'allowances',
        'gross_pay',
        'paye',
        'napsa',
        'nhima',
        'advance_deductions',
        'net_pay',
        'status',
    ];

    protected $casts = [
        'cycle_start' => 'date',
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'paye' => 'decimal:2',
        'napsa' => 'decimal:2',
        'nhima' => 'decimal:2',
        'advance_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(
            DepartmentalWageBillAuditLog::class,
            'departmental_wage_bill_id'
        );
    }
}

