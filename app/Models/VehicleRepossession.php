<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRepossession extends Model
{
    protected $fillable = [
        'vehicle_id',
        'loan_id',
        'repossession_date',
        'location_found',
        'remarks',
        'status'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}