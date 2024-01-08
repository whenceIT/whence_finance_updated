<?php

namespace App\Listeners;

use App\Events\SavingsApproved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SavingsApprovedNotifications
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SavingsApproved  $event
     * @return void
     */
    public function handle(SavingsApproved $event)
    {
        //
    }
}
