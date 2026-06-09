<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleInsurance extends Model
{
    protected $table = 'vehicle_insurance';

    protected $fillable = [
        'vehicle_id',
        'insurer_name',
        'policy_number',
        'start_date',
        'expiry_date',
        'insured_value'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}