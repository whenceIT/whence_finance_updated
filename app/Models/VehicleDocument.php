<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    protected $fillable = [
        'vehicle_id',
        'document_type',
        'document_file',
        'uploaded_by'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}