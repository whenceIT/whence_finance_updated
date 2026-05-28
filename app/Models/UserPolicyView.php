<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPolicyView extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'policy_id',
        'policy_of_the_day_id',
        'engagement_time',
    ];

    protected $casts = [
        'engagement_time' => 'integer',
        'timestamp' => 'datetime',
    ];

    /**
     * Get the user that viewed the policy.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the policy that was viewed.
     */
    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }

    /**
     * Get the policy of the day that was viewed.
     */
    public function policyOfTheDay()
    {
        return $this->belongsTo(PolicyOfTheDay::class);
    }
}
