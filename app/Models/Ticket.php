<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'name',
        'description',
        'datetime_open',
        'datetime_close',
        'date_raised',
        'date_closed',
        'opened_by',
        'assigned_to',
        'closed_by',
        'status',
        'priority',
        'department',
        'issue_category_id',
        'sla_days',
        'due_date',
        'sla_met',
        'rating',
        'remarks',
    ];

    protected $casts = [
        'date_raised' => 'datetime',
        'due_date' => 'datetime',
        'date_closed' => 'datetime',
        'sla_met' => 'boolean',
    ];

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by', 'id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by', 'id');
    }

    public function issueCategory()
    {
        return $this->belongsTo(\App\Models\TicketCategory::class, 'issue_category_id', 'id');
    }

    public function getTicketNumberAttribute()
    {
        return 'TICKT-' . $this->created_at->year . '' . $this->id;
    }
}
