<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRecovery extends Model
{
    protected $table = 'vehicle_recoveries';

    protected $fillable = [
        'loan_id',
        'vehicle_id',
        'recovery_actions',
        'communications',
        'promises_arrangements',
        'repossession_costs',
        'legal_costs',
        'other_expenses',
        'penalties',
        'current_recovery_stage',
        'valuation_costs',
        'created_by',
    ];

    protected $casts = [
        'repossession_costs' => 'decimal:2',
        'legal_costs' => 'decimal:2',
        'other_expenses' => 'decimal:2',
        'penalties' => 'decimal:2',
        'valuation_costs' => 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
