<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorVehicleAuditLog extends Model
{
    protected $fillable = [
        'motor_vehicle_loan_id',
        'vehicle_id',
        'action',
        'old_value',
        'new_value',
        'user_id',
        'branch_id',
        'district_id',
        'province_id',
        'ip_address',
        'user_agent',
        'actioned_at'
    ];

    protected $casts = [
        'actioned_at' => 'datetime',
    ];

    public function motorVehicleLoan()
    {
        return $this->belongsTo(MotorVehicleLoan::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Office::class, 'branch_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
