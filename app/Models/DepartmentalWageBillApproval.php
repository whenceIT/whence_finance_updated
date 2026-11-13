<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentalWageBillApproval extends Model
{
    protected $table = 'departmental_wage_bill_approvals';

    protected $fillable = [
        'submission_id',
        'approval_type',
        'approver_id',
        'status',
        'comments',
        'actioned_at',
    ];

    protected $casts = [
        'actioned_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(
            DepartmentalWageBillSubmission::class,
            'submission_id'
        );
    }

    public function approver()
    {
        return $this->belongsTo(\App\User::class, 'approver_id');
    }
}

