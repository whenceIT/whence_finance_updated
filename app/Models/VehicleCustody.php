<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCustody extends Model
{
    protected $table = 'vehicle_custody';

    protected $fillable = [
        'vehicle_id',
        'received_at',
        'received_by',
        'keys_received',
        'key_tag_numbers',
        'garage_name',
        'garage_location',
        'garage_gps',
        'parking_bay',
        'garage_contact_person',
        'garage_contact_phone',
        'remarks',
        'status',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    /**
     * Vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Officer who received the vehicle
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}