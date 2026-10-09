<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FleetServiceRecord extends Model
{
    protected $fillable = [
        'fleet_id',
        'service_date',
        'service_type',
        'description',
        'parts_replaced',
        'workshop',
        'odometer_reading',
        'cost',
        'document_path',
        'recorded_by',
    ];

    protected $casts = [
        'service_date'    => 'date',
        'cost'            => 'decimal:2',
        'odometer_reading' => 'integer',
    ];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }
}
