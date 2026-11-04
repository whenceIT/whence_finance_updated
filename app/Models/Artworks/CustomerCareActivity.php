<?php

namespace App\Models\Artworks;

use Illuminate\Database\Eloquent\Model;

class CustomerCareActivity extends Model
{
    protected $table = 'creative_institutional_activities';

    protected $fillable = [
        'title',
        'description',
        'activity_type',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'venue',
        'responsible_department',
        'responsible_user_id',
        'status',
        'reminder_sent_at',
        'notes',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'reminder_sent_at',
        'created_at',
        'updated_at',
    ];
}
