<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class VehicleInspectionPhoto extends Model
{
    protected $fillable = [
        'vehicle_inspection_id',
        'photo_url',
    ];

    public function inspection()
    {
 
 
    return $this->belongsTo(VehicleInspection::class, 'vehicle_inspection_id');
    }
}