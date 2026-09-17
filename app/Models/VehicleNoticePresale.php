<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleNoticePresale extends Model
{
    protected $table = 'vehicle_notice_presales';

    protected $fillable = [
        'vehicle_id',
        'notice_generated_date',
        'notice_served_date',
        'service_method',
        'officer_issuing',
        'deadline_to_client',
        'client_settled',
        'client_presented_buyer',
        'buyer_details',
        'outcome_after_expiry',
        'created_by',
    ];

    protected $casts = [
        'notice_generated_date' => 'date',
        'notice_served_date' => 'date',
        'deadline_to_client' => 'date',
        'client_settled' => 'boolean',
        'client_presented_buyer' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
