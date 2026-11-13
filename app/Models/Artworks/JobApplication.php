<?php

namespace App\Models\Artworks;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'creative_job_applications';

    protected $fillable = [
        'nrc_number',
        'full_name',
        'contact_number',
        'email',
        'position_applied_for',
        'recommending_department',
        'recommended_by_user_id',
        'date_recommended',
        'status',
        'admin_notified_at',
        'employment_date',
        'is_currently_employed',
        'qualifying_completed_at',
        'reward_notified_at',
        'notes',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $dates = [
        'date_recommended',
        'admin_notified_at',
        'employment_date',
        'qualifying_completed_at',
        'reward_notified_at',
        'approved_at',
        'rejected_at',
        'created_at',
        'updated_at',
    ];
}
