<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentalWageBillSubmission extends Model
{
    protected $table = 'departmental_wage_bill_submissions';

    protected $fillable = [
        'cycle_start',
        'submitted_by',
        'submission_number',
        'status',
        'comments',
        'submitted_at',
    ];

    protected $casts = [
        'cycle_start' => 'date',
        'submitted_at' => 'datetime',
    ];

    public function submitter()
    {
        return $this->belongsTo(\App\User::class, 'submitted_by');
    }

    public function approvals()
    {
        return $this->hasMany(
            DepartmentalWageBillApproval::class,
            'submission_id'
        );
    }
}
