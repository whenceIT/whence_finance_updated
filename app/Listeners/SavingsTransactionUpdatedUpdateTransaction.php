<?php

namespace App\Listeners;

use App\Events\SavingsTransactionUpdated;
use App\Models\SavingsTransaction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SavingsTransactionUpdatedUpdateTransaction
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
     * @param  SavingsTransactionUpdated  $event
     * @return void
     */
    public function handle(SavingsTransactionUpdated $event)
    {
        $savings_transaction = $event->savings_transaction;

    }
}
