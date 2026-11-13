<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WageBillApproval extends Model
{
    protected $table = 'wage_bill_approvals';
    public $timestamps = false;

    protected $fillable = [
        'submission_id',
        'branch_id',
        'cycle_start',
        'approval_type',
        'approver_id',
        'status',
        'comments',
        'actioned_at',
    ];

    protected $casts = [
        'cycle_start' => 'date:Y-m-d',
        'actioned_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Office::class, 'branch_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function submission()
    {
        return $this->belongsTo(WageBillSubmission::class, 'submission_id');
    }
}