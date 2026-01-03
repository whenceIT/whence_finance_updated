<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InductionChecklist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'item', 'completed'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function hasCompletedPolicies($userId)
    {
        $totalPolicies = \App\Models\Policy::count();
        $userResponses = \App\Models\UserPolicyResponse::where('user_id', $userId)->count();
        return $userResponses >= $totalPolicies;
    }
}
