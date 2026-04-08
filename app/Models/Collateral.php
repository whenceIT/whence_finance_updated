<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{
    protected $table = 'collateral';

    protected $fillable = [
        'name',
        'description',
        'initial_price',
        'current_worth',
        'loan_id',
        'collateral_type_id',
        'created_by_id',
        'status',
        'condition',
        'date_purchased',
        'date_resold',
    ];

    protected $casts = [
        'date_purchased' => 'date',
        'date_resold'    => 'date',
        'initial_price'  => 'decimal:2',
        'current_worth'  => 'decimal:2',
    ];

    public function type()
    {
        return $this->hasOne(CollateralType::class, 'id', 'collateral_type_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id');
    }

    public function statusChanges()
    {
        return $this->hasMany(CollateralStatusChangeRequest::class, 'collateral_id', 'id');
    }

    public function alerts()
    {
        return $this->hasMany(CollateralAlert::class, 'collateral_id', 'id');
    }

    public function auditTrail()
    {
        return $this->hasMany(AuditTrail::class, 'user_id', 'created_by_id')
                    ->where('module', 'collateral');
    }
}
