<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class ScheduleServices
{
    /**
     * Run anniversary notifications only on the 24th of each month
     */
    public function runMonthlyAnniversaryNotifications()
    {
        // Run anniversary notifications only on the 24th of each month
        if (now()->day === 24) {
            Artisan::call('notifications:send-anniversaries');
        }
    }
    /**
     * Run training links notifications only on the 24th of each month
     */
    public function runTrainingLinksNotifications()
    {
        // run every after 5hour
         Artisan::call('notifications:send-training-links');
      
    }

    
}