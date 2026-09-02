<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_code',
        'client_id',
        'make',
        'model',
        'year',
        'color',
        'registration_number',
        'engine_number',
        'chassis_number',
        'mileage',
        'fuel_type',
        'transmission',
        'market_value',
        'forced_sale_value',
        'status',
        'sold_at',
        'buyer_fullname',
        'buyer_phone',
        'buyer_nrc_number',
        'buyer_sex',
        'buyer_location',
        'ownership_type',
        'registered_owner',
        'seller_name',
        'seller_nrc',
        'seller_phone',
        'seller_email',
        'seller_address',
        'company_name',
        'company_registration',
        'company_directors',
        'company_resolution',
        'authorized_representative',
        'letter_of_sale_file',
        'ownership_documents',
        'ownership_verification_status',
        'current_storage_location',
        'current_custodian',
        'custodian_nrc',
        'custodian_phone',
        'custodian_alt_contact',
        'storage_start_date',
        'storage_notes',
        'loan_id'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function valuations()
    {
        return $this->hasMany(VehicleValuation::class);
    }

    public function insurancePolicies()
    {
        return $this->hasMany(VehicleInsurance::class);
    }

    public function documents()
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function inspections()
    {
        return $this->hasMany(VehicleInspection::class);
    }

    public function motorVehicleLoans()
    {
        return $this->hasMany(MotorVehicleLoan::class);
    }

    public function photos()
    {
        return $this->hasMany(VehiclePhoto::class, 'vehicle_id');
    }

    public function custody()
    {
        return $this->hasOne(VehicleCustody::class);
    }

    public function ownershipRecords()
    {
        return $this->hasMany(VehicleOwnershipRecord::class);
    }

    public function movements()
    {
        return $this->hasMany(VehicleMovement::class);
    }

    public function rollCalls()
    {
        return $this->hasMany(VehicleRollCall::class);
    }
}
