<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdministrativeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'record_type',
        'disciplinary_type',
        'warning_type',
        'warning_level',
        'health_type',
        'incident_type',
        'career_type',
        'name',
        'description',
        'recording_date',
        'comments',
        'number_of_days',
        'absence_dates',
        'status',
        'approved_by',
        'approved_at',
        'decline_reason',
        'created_by',
    ];

    protected $casts = [
        'recording_date' => 'date',
        'absence_dates' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function scopeDisciplinary($query)
    {
        return $query->where('record_type', 'disciplinary');
    }

    public function scopeHealth($query)
    {
        return $query->where('record_type', 'health');
    }

    public function scopeCareer($query)
    {
        return $query->where('record_type', 'career');
    }
}
