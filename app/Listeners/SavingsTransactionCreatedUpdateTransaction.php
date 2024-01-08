<?php

namespace App\Listeners;

use App\Events\SavingsTransactionCreated;
use App\Models\SavingsTransaction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SavingsTransactionCreatedUpdateTransaction
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
     * @param  SavingsTransactionCreated $event
     * @return void
     */
    public function handle(SavingsTransactionCreated $event)
    {
        $savings_transaction = $event->savings_transaction;

    }
}
