<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollateralStatusChangeRequest extends Model
{
    protected $table = 'collateral_status_change_requests';

    protected $fillable = [
        'collateral_id',
        'requested_by_id',
        'approved_by_id',
        'old_status',
        'new_status',
        'reason',
        'sold_price',
        'penalty',
        'disposal_costs',
        'stage',
        'approval_status',
        'request_date',
        'approval_date',
    ];

    protected $casts = [
        'disposal_costs' => 'array',
    ];

    public function collateral()
    {
        return $this->hasOne(Collateral::class, 'id', 'collateral_id');
    }

    public function requested_by()
    {
        return $this->hasOne(User::class, 'id', 'requested_by_id');
    }

    public function approved_by()
    {
        return $this->hasOne(User::class, 'id', 'approved_by_id');
    }
}
