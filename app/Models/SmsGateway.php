<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\BulkSMS;

class SmsGateway extends Model
{
    protected $table = "sms_gateways";

    public $timestamps = false;

    /**
     * Send SMS notification for repayment received and pending approval.
     *
     * @param \App\Models\Client $client
     * @param float $amount
     * @return array
     */
    public static function sendRepaymentSms($client, $request, $loan = null, $trans = null, $balance = null, $nextDueDate = null)
    {
        if ($request && $request->payment_apply_to == 'reloan_payment' || $trans) {
            $amount = $trans ? $trans->credit : ($request ? $request->amount : 0);
            if (!$nextDueDate && $loan) {
                $nextDueDate = \App\Models\LoanRepaymentSchedule::where('loan_id', $loan->id)
                    ->where('due_date', '>', now())
                    ->orderBy('due_date', 'asc')
                    ->value('due_date');
            }

            $message = 'Thank you for your reloan payment of ' . number_format($amount, 2) . '. ';
            if ($balance !== null) {
                $message .= 'Your outstanding balance is ' . number_format($balance, 2) . '. ';
            }
            if ($nextDueDate) {
                $message .= 'Your next due date is ' . date('d/m/Y', strtotime($nextDueDate)) . '.';
            } else {
                $message .= 'Your loan is fully paid.';
            }
            $bulkSms = new BulkSMS();
            return $bulkSms->sendToClients([$client], $message);
        } elseif ($request) {
            $message = 'Your repayment of ' . number_format($request->amount, 2) . ' has been received and is pending approval.';
            $bulkSms = new BulkSMS();
            return $bulkSms->sendToClients([$client], $message);
        }
        return false;
    }


    public static function sendOverdueSms($office_id)
    {
        $loanModel = new \App\Models\Loan();
        $overdueLoans = $loanModel->overdue_loans($office_id);

        $results = [];
        foreach ($overdueLoans as $loan) {
            $client = (object) [
                'first_name' => explode(' ', $loan->client_name)[0] ?? '',
                'last_name' => explode(' ', $loan->client_name)[1] ?? '',
                'phone' => $loan->Client_Phone,
                'mobile' => $loan->Client_Phone,
            ];
            $amount = $loan->principal; // or approved_amount?
            $balance = null;
            try {
                $balance = \App\Helpers\GeneralHelper::loan_total_balance($loan->id);
                if ($balance === null || $balance === false || !is_numeric($balance)) {
                    $balance = null;
                }
            } catch (\Exception $e) {
                $balance = null;
            }

            $message = 'Dear Customer, this is a reminder that your loan of ZMW ' . number_format($amount, 2);
            if ($balance !== null) {
                $message .= ' with outstanding balance of ZMW ' . number_format($balance, 2);
            }
            $message .= ' is overdue. Kindly make your payment to avoid penalties or further legal action. For assistance, contact 0972654596.';

            $bulkSms = new BulkSMS();
            $result = $bulkSms->sendToClients([$client], $message);
            $results[] = $result;
        }

        return $results;
    }

    //add sms function to send this message to clients remainder
        public static function sendRepaymentClientRemindSms()
        {

            $message = 'Hello ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . ', your loan of ' . number_format($topup->amount, 2) . ' has been overdue. Please ensure that your loan is paid on time to avoid any legal actions. Thank you for choosing Merger Finance.';
            $bulkSms = new BulkSMS();
            return $bulkSms->sendToClients([$client], $message);
        }



}
