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
        'custody_approved',
        'house_owner_name',
        'house_owner_nrc',
        'house_owner_phone',
        'alternative_contact_name',
        'alternative_contact_phone',
        'storage_start_date',
        'storage_end_date',
        'gps_location',
        'location_description',
        'intake_date',
        'documents_received',
        'accessories_received',
        'fuel_level',
        'intake_photos',
        'signed_intake_form_path'
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'intake_date' => 'datetime',
        'storage_start_date' => 'date',
        'storage_end_date' => 'date',
        'keys_received' => 'boolean',
        'documents_received' => 'boolean',
        'accessories_received' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
