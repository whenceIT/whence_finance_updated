<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorVehicleLoan extends Model
{
    protected $fillable = [
        'loan_id',
        'vehicle_id',
        'client_id',
        'vehicle_value',
        'ltv_percent',
        'requested_amount',
        'approved_amount',
        'status',
        'remarks'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}