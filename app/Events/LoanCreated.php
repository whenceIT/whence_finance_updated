<?php

namespace App\Events;

use App\Models\Loan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class LoanCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
        Log::info('LoanCreated event constructed for loan ID: ' . $loan->id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        Log::info('LoanCreated broadcastOn called - loans channel');
        return new Channel('loans');
    }

    public function broadcastAs()
    {
        return 'loan.created';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->loan->id,
            'client_id' => $this->loan->client_id,
            'principal' => $this->loan->principal,
            'office_id' => $this->loan->office_id,
            'status' => $this->loan->status,
            'created_by' => $this->loan->created_by_id,
            'loan_product_id' => $this->loan->loan_product_id,
        ];
    }
}
