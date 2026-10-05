<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashAuditSubmission extends Model
{
    protected $table = 'cash_audit_submissions';

    protected $fillable = [
        'user_id',
        'office_id',
        'branch_name',
        'district_manager_name',
        // Cash
        'cash_5', 'cash_10', 'cash_20', 'cash_50', 'cash_100', 'cash_200', 'cash_500',
        'cash_total',
        'cash_count_datetime',
        // Petty
        'petty_5', 'petty_10', 'petty_20', 'petty_50', 'petty_100', 'petty_200', 'petty_500',
        'petty_total',
        'petty_via_mobile_wallet',
        // Mobile money
        'mobile_ussd_reference',
        'mobile_number',
        'sim_registered_name',
        'dm_using_sim',
    ];

    protected $casts = [
        'petty_via_mobile_wallet' => 'boolean',
        'cash_count_datetime'     => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
