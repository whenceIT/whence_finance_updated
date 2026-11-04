<?php

namespace App\Models\Artworks;

use Illuminate\Database\Eloquent\Model;

class ConflictResolutionCase extends Model
{
    protected $table = 'creative_conflict_resolution_cases';

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
        'resolution_details',
        'resolution_date',
        'charge_amount',
        'amount_paid',
        'notes',
    ];

    protected $dates = [
        'date_reported',
        'resolution_date',
        'created_at',
        'updated_at',
    ];
}
