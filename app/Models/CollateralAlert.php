<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollateralAlert extends Model
{
    protected $table = 'collateral_alerts';

    protected $fillable = [
        'collateral_id',
        'user_id',
        'alert_type',
        'is_resolved',
        'resolved_at',
    ];

    public function collateral()
    {
        return $this->hasOne(Collateral::class, 'id', 'collateral_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
