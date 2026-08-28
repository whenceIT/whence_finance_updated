<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollateralWorkflowLog extends Model
{
    protected $table = 'collateral_workflow_logs';

    protected $fillable = [
        'collateral_id',
        'from_status',
        'to_status',
        'reason',
        'user_id',
    ];

    public function collateral()
    {
        return $this->belongsTo(Collateral::class, 'collateral_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
