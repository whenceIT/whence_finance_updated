<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleValuation extends Model
{
    protected $fillable = [
        'vehicle_id',
        'valuation_date',
        'market_value',
        'forced_sale_value',
        'valuator_name',
        'report_file'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}