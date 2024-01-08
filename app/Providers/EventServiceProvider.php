<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\RepaymentCreated' => [
            'App\Listeners\RepaymentCreatedNotifications',
            'App\Listeners\RepaymentCreatedUpdateTransactions',
        ],
        'App\Events\LoanApproved' => [
            'App\Listeners\LoanApprovedNotifications',
        ],
        'App\Events\LoanDisbursed' => [
            'App\Listeners\LoanDisbursedNotifications',
        ],
        'App\Events\RepaymentUpdated' => [
            'App\Listeners\RepaymentUpdatedUpdateTransactions',
        ],
        'App\Events\TransactionUpdated' => [
            'App\Listeners\TransactionUpdatedUpdateTransactions',
        ],
        'App\Events\SavingsApproved' => [
            'App\Listeners\SavingsApprovedNotifications',
        ],
        'App\Events\SavingsTransactionCreated' => [
            'App\Listeners\SavingsTransactionNotifications',
            'App\Listeners\SavingsTransactionCreatedUpdateTransaction',
        ],
        'App\Events\SavingsTransactionUpdated' => [
            'App\Listeners\SavingsTransactionUpdatedUpdateTransaction',
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
