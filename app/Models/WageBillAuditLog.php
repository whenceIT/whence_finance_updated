<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WageBillAuditLog extends Model
{
    protected $table = 'wage_bill_audit_logs';
    

    public $timestamps = false;

    protected $fillable = [
        'wage_bill_id',
        'user_id',
        'field_name',
        'old_value',
        'new_value',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'old_value' => 'decimal:2',
        'new_value' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function wageBill()
    {
        return $this->belongsTo(WageBill::class, 'wage_bill_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}