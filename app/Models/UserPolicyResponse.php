<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPolicyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'policy_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the user that owns the response.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the policy that the response is for.
     */
    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }
}