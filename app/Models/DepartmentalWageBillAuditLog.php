<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentalWageBillAuditLog extends Model
{
    protected $table = 'departmental_wage_bill_audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'departmental_wage_bill_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'reason',
    ];

    public function wageBill()
    {
        return $this->belongsTo(
            DepartmentalWageBill::class,
            'departmental_wage_bill_id'
        );
    }

    public function editor()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }
}
