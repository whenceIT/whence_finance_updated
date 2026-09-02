<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleOwnershipRecord extends Model
{
    protected $fillable = [
        'vehicle_id',
        'ownership_type',
        'registered_owner_name',
        'seller_name',
        'seller_nrc',
        'seller_phone',
        'witness_1_name',
        'witness_1_nrc',
        'witness_2_name',
        'witness_2_nrc',
        'company_name',
        'company_registration_number',
        'directors',
        'authorized_representative_name',
        'authorized_representative_nrc',
        'ownership_documents_path',
        'verified',
        'verified_by_id',
        'verified_at'
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'directors' => 'array',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }
}
