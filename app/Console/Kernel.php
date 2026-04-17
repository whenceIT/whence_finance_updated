<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ResetTrainerFlagCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Send anniversary notifications daily at 9 AM
        $schedule->command('notifications:send-anniversaries')
                 ->dailyAt('09:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Send training links notifications every 7 hours
        $schedule->command('notifications:send-training-links')
                 ->cron('0 */7 * * *')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Send expense notifications daily at 19:01 (after 19:00)
        $schedule->command('notifications:send-expenses')
                 ->dailyAt('19:01')
                 ->withoutOverlapping()
                 ->runInBackground();

        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
