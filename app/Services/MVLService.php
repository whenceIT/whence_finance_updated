<?php

namespace App\Services;

use App\Models\Loan;
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
        $loans = Loan::where('loan_product_id', 0)
            ->whereNotNull('first_repayment_date')
            ->where('first_repayment_date', '<=', Carbon::now()->subWeek())
            ->where('status', '!=', 'closed')
            ->with('client')
            ->get();

        $message = 'Dear customer, your loan is in default and you have 3 weeks left to repay your motor vehicle loan or else your vehicle will be repossessed by the institution.';

        Log::info('MVL week1 reminder: sending SMS to', ['count' => $loans->count()]);

        if ($loans->isNotEmpty()) {
            return $this->bulkSms->sendToLoans($loans, $message);
        }

        return [];
    }

    public function month1reminder(): array
    {
        $loans = Loan::where('loan_product_id', 0)
            ->whereNotNull('first_repayment_date')
            ->where('first_repayment_date', '<=', Carbon::now()->subMonth())
            ->where('status', '!=', 'closed')
            ->with('client')
            ->get();

        $message = 'Dear customer, your motor vehicle loan vehicle has been repossessed by the institution due to defaulted loan. If a buyer is available please get in touch with the institution.';

        Log::info('MVL month1 reminder: sending SMS to', ['count' => $loans->count()]);

        if ($loans->isNotEmpty()) {
            return $this->bulkSms->sendToLoans($loans, $message);
        }

        return [];
    }
}
