<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleAuditTrail extends Model
{
    protected $table = 'vehicle_audit_trails';

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'reason',
        'ip_address',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
