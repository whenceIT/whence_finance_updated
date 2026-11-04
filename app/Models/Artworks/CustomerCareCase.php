<?php

namespace App\Models\Artworks;

use Illuminate\Database\Eloquent\Model;

class CustomerCareCase extends Model
{
    protected $table = 'creative_customer_care_cases';

    protected $fillable = [
        'case_reference',
        'branch_id',
        'client_name',
        'nrc_number',
        'contact_number',
        'complaint_type',
        'complaint_description',
        'reported_by_user_id',
        'date_reported',
        'assigned_to_user_id',
        'status',
        'actions_taken',
        'resolution_details',
        'resolution_date',
        'follow_up_actions',
        'escalated_to',
        'escalated_at',
        'notes',
    ];

    protected $dates = [
        'date_reported',
        'resolution_date',
        'escalated_at',
        'created_at',
        'updated_at',
    ];
}
