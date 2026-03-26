<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    protected $table = 'specialists';

    protected $fillable = [
        'user_id',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user associated with this specialist.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    /**
     * Get all recovery cases assigned to this specialist.
     */
    public function recoveryCases()
    {
        return $this->hasMany(\App\Models\RecoveryCase::class, 'assigned_to', 'user_id');
    }
}