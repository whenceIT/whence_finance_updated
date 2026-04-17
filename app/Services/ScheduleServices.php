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
     * Run training links notifications every 7 hours
     */
    public function runTrainingLinksNotifications()
    {
        // run every after 7 hours
        Artisan::call('notifications:send-training-links');
    }

    /**
     * Run overdue clients notifications
     */
    public function runOverdueClientsNotifications()
    {
        // run to send overdue clients notifications
        Artisan::call('notifications:send-overdue-clients');
    }

    /**
     * Run expense creation notifications after 19:00
     */
    public function runExpenseNotifications()
    {
        // run to send expense notifications for creations after 19:00
        Artisan::call('notifications:send-expenses');
    }


}