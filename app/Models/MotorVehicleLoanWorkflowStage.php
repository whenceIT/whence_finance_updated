<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorVehicleLoanWorkflowStage extends Model
{
    protected $fillable = [
        'motor_vehicle_loan_id',
        'stage',
        'previous_stage',
        'comments',
        'officer_id',
        'branch_id',
        'district_id',
        'province_id',
        'approval_notes',
        'transition_date'
    ];

    protected $casts = [
        'transition_date' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'motor_vehicle_loan_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
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
