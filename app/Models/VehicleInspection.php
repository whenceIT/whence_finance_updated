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
        'result',
        'mileage',
        'mechanical_condition',
        'interior_condition',
        'exterior_condition',
        'tyres_condition',
        'battery_condition',
        'accessories_condition',
        'report_file_path',
        'inspection_photos',
        'condition_score'
    ];

    protected $casts = [
        'inspection_photos' => 'array',
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
