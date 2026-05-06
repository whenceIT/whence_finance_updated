<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FleetMaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'fleet_id',
        'maintenance_type',
        'technician',
        'due_date',
        'notes',
        'status',
        'completed_at',
        'amount',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }
}
