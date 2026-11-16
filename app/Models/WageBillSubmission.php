<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WageBillSubmission extends Model
{
    protected $table = 'wage_bill_submissions';
    public $timestamps = false;

    protected $fillable = [
        'branch_id',
        'cycle_start',
        'submitted_by',
        'submission_number',
        'status',
        'comments',
        'submitted_at',
    ];

    protected $casts = [
        'cycle_start' => 'date:Y-m-d',
        'submitted_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Office::class, 'branch_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvals()
{
    return $this->hasMany(WageBillApproval::class, 'submission_id');
}
}