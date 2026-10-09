<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FleetAccident extends Model
{
    protected $fillable = [
        'fleet_id',
        'accident_date',
        'driver',
        'location',
        'description',
        'police_report_number',
        'insurance_claim_number',
        'repair_details',
        'cost',
        'document_path',
        'recorded_by',
    ];

    protected $casts = [
        'accident_date' => 'date',
        'cost'          => 'decimal:2',
    ];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }
}
