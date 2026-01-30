<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch',
        'length_of_service',
        'bmos_consistency',
        'bmos_challenges',
        'branch_needs',
        'tools_resources',
        'operational_challenges',
        'supervisor_support',
        'manager_communication',
        'manager_communication_comments',
        'leadership_challenges',
        'manager_effectiveness_rating',
        'workload_rating',
        'unethical_conduct',
        'policy_violation_instructions',
        'policy_violation_description',
        'top_issues',
        'pending_loans_entry',
        'longest_pending_period',
        'missed_target_due_pending',
        'pending_target_explanation',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
