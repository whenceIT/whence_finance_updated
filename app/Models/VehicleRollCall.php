<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRollCall extends Model
{
    protected $fillable = [
        'vehicle_id',
        'verification_date',
        'officer_id',
        'location_confirmed',
        'vehicle_present',
        'condition',
        'current_mileage',
        'photos',
        'status',
        'remarks'
    ];

    protected $casts = [
        'verification_date' => 'date',
        'location_confirmed' => 'boolean',
        'vehicle_present' => 'boolean',
        'photos' => 'array',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}
