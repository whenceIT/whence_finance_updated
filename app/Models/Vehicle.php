<?php

namespace App\Models;

use App\Http\Controllers\VehicleController;
use App\Models\VehicleDocument as ModelsVehicleDocument;
use App\VehicleDocument;
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
        'status'
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
        return $this->hasMany(ModelsVehicleDocument::class);
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
    return $this->hasMany(VehiclePhoto::class);
}

public function custody()
{
    return $this->hasOne(VehicleCustody::class);
}
}