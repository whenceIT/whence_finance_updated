<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecoveriesDeptExcalatedShare extends Model
{
    protected $fillable = [
        'recovery_case_id',
        'recovery_payment_id',
        'office_id',
        'dept_share_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'dept_share_amount' => 'decimal:2',
    ];

    public function recoveryCase()
    {
        return $this->belongsTo(RecoveryCase::class);
    }

    public function recoveryPayment()
    {
        return $this->belongsTo(RecoveryPayment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}