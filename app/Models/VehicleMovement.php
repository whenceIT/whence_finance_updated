<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleMovement extends Model
{
    protected $fillable = [
        'vehicle_id',
        'previous_location',
        'new_location',
        'movement_date',
        'authorized_by',
        'moved_by',
        'reason',
        'condition',
        'photos'
    ];

    protected $casts = [
        'movement_date' => 'date',
        'photos' => 'array',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function authorizedBy()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function movedBy()
    {
        return $this->belongsTo(User::class, 'moved_by');
    }
}
