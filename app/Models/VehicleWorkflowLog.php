<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleWorkflowLog extends Model
{
    protected $table = 'vehicle_workflow_logs';

    protected $fillable = [
        'vehicle_id',
        'from_status',
        'to_status',
        'reason',
        'user_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
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
