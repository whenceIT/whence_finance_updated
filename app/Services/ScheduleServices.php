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

    /**
     * Run performance-linked training triggers daily at 07:00
     *
     * Analyses performance indicators and pushes relevant training content:
     *  - High loan defaults → vetting & due-diligence materials
     *  - High staff turnover → leadership & management materials
     *  - Declining client base → client management materials
     */
    public function runPerformanceLinkedTraining()
    {
        Artisan::call('training:push-performance-triggers');
    }


}