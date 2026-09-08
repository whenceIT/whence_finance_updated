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
        'report_file',
        'valuator_id',
        'valuation_company',
        'valuation_cost',
        'expiry_date',
        'report_file_path',
        'photos',
        'supporting_documents'
    ];

    protected $casts = [
        'photos' => 'array',
        'supporting_documents' => 'array',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function valuator()
    {
        return $this->belongsTo(User::class, 'valuator_id');
    }
}
