<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\SentMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MVLService
{
    protected $bulkSms;

    public function __construct(BulkSMS $bulkSms)
    {
        $this->bulkSms = $bulkSms;
    }

    public function week1reminder(): array
    {
        $sentLoanIds = SentMessage::where('message_type', 'week1_reminder')->pluck('loan_id');

        $loans = Loan::where('loan_product_id', 0)
            ->whereNotNull('first_repayment_date')
            ->where('first_repayment_date', '<=', Carbon::now()->subWeek())
            ->where('status', '!=', 'closed')
            ->whereNotIn('id', $sentLoanIds)
            ->with('client')
            ->get();
            
        $message = 'Dear customer, your loan is in default and you have 3 weeks left to repay your motor vehicle loan or else your vehicle will be repossessed by the institution.';

        Log::info('MVL week1 reminder: sending SMS to', ['count' => $loans->count()]);

        if ($loans->isNotEmpty()) {
            $result = $this->bulkSms->sendToLoans($loans, $message);

            if (!empty($result)) {
                $now = now();
                $sentMessages = [];
                foreach ($loans as $loan) {
                    $sentMessages[] = [
                        'loan_id' => $loan->id,
                        'message_type' => 'week1_reminder',
                        'message' => $message,
                        'sent_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                SentMessage::insert($sentMessages);
            }

            return $result;
        }

        return [];
    }

    public function month1reminder(): array
    {
        $sentLoanIds = SentMessage::where('message_type', 'month1_reminder')->pluck('loan_id');

        $loans = Loan::where('loan_product_id', 0)
            ->whereNotNull('first_repayment_date')
            ->where('first_repayment_date', '<=', Carbon::now()->subMonth())
            ->where('status', '!=', 'closed')
            ->whereNotIn('id', $sentLoanIds)
            ->with('client')
            ->get();

        $message = 'Dear customer, your motor vehicle loan vehicle has been repossessed by the institution due to defaulted loan. If a buyer is available please get in touch with the institution.';

        Log::info('MVL month1 reminder: sending SMS to', ['count' => $loans->count()]);

        if ($loans->isNotEmpty()) {
            $result = $this->bulkSms->sendToLoans($loans, $message);

            if (!empty($result)) {
                $now = now();
                $sentMessages = [];
                foreach ($loans as $loan) {
                    $sentMessages[] = [
                        'loan_id' => $loan->id,
                        'message_type' => 'month1_reminder',
                        'message' => $message,
                        'sent_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                SentMessage::insert($sentMessages);
            }

            return $result;
        }

        return [];
    }
}
