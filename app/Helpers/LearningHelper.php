<?php

namespace App\Helpers;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LearningHelper
{
    /**
     * Generate a unique key combining today's date and current user ID
     *
     * @return string
     */
    public static function generateDailyLearningKey()
    {
        $user = Sentinel::getUser();
        $userId = $user ? $user->id : 0;
        $date = date('Y-m-d');

        return $date . '_' . $userId;
    }

    /**
     * Update the current user's daily_learning field with the generated key
     *
     * @return bool
     */
    public static function updateDailyLearning()
    {
        $user = Sentinel::getUser();
        if (!$user) {
            return false;
        }

        $key = self::generateDailyLearningKey();

        if(self::hasCompletedDailyLearning()) {
            return true; // Already completed for today
        }

        $user->daily_learning = $key;
        $user->save();

        return true;
    }

    /**
     * Check if the current user's daily_learning matches today's generated key
     *
     * @return bool
     */
    public static function hasCompletedDailyLearning()
    {
        $user = Sentinel::getUser();
        if (!$user) {
            return false;
        }

        $currentKey = self::generateDailyLearningKey();
        return $user->daily_learning === $currentKey;
    }
}