<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorVehicleLoanStatusHistory extends Model
{
    protected $fillable = [
        'motor_vehicle_loan_id',
        'previous_status',
        'new_status',
        'user_id',
        'branch_id',
        'action_reason',
        'transition_date'
    ];

    protected $casts = [
        'transition_date' => 'datetime',
    ];

    public function motorVehicleLoan()
    {
        return $this->belongsTo(MotorVehicleLoan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Office::class, 'branch_id');
    }
}
