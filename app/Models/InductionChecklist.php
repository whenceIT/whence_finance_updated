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
        // Get the user instance
        $user = \Cartalyst\Sentinel\Laravel\Facades\Sentinel::findUserById($userId);
        if (!$user) {
            return false;
        }

        // Determine if user has managerial role based on role IDs (from key.md)
        $managerialRoles = [1, 4, 6]; // Admin (1), Branch Manager (4), Provincial Manager (6)
        $isManagerial = false;
        foreach ($managerialRoles as $roleId) {
            if ($user->inRole($roleId)) {
                $isManagerial = true;
                break;
            }
        }

        // Get total accessible policies for this user
        $totalPolicies = \App\Models\Policy::where(function ($query) use ($isManagerial) {
            $query->where('access_level', \App\Models\Policy::ACCESS_ALL)
                  ->orWhere(function ($q) use ($isManagerial) {
                      if ($isManagerial) {
                          $q->where('access_level', \App\Models\Policy::ACCESS_MANAGERIAL);
                      }
                  });
        })->count();

        // Get user's policy responses
        $userResponses = \App\Models\UserPolicyResponse::where('user_id', $userId)
            ->whereHas('policy', function ($query) use ($isManagerial) {
                $query->where(function ($q) use ($isManagerial) {
                    $q->where('access_level', \App\Models\Policy::ACCESS_ALL)
                      ->orWhere(function ($subQ) use ($isManagerial) {
                          if ($isManagerial) {
                              $subQ->where('access_level', \App\Models\Policy::ACCESS_MANAGERIAL);
                          }
                      });
                });
            })
            ->count();

        return $userResponses >= $totalPolicies;
    }
}
