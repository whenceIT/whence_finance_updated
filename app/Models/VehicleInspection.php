<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleInspection extends Model
{
    protected $fillable = [
        'vehicle_id',
        'inspection_date',
        'inspector',
        'condition_notes',
        'result'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function photos()
{
    return $this->hasMany(VehicleInspectionPhoto::class);
}
}