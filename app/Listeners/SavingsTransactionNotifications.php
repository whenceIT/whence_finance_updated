<?php

namespace App\Listeners;

use App\Events\SavingsTransactionCreated;
use App\Helpers\GeneralHelper;
use App\Mail\SavingsTransactionCreatedEmail;
use App\Models\Setting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SavingsTransactionNotifications
{

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
        $savings = $savings_transaction->savings;
        if (Setting::where('setting_key',
                'auto_payment_receipt_sms')->first()->setting_value == 1
        ) {
            $mobile = "";
            $clientName = "";
            if ($savings->client_type == "client") {
                if (!empty($savings->client)) {
                    if ($savings->client->client_type == "individual") {
                        $clientName = $savings->client->first_name . ' ' . $savings->client->middle_name . ' ' . $savings->client->last_name;
                    }
                    if ($savings->client->client_type == "business") {
                        $clientName = $savings->client->full_name;
                    }
                    $mobile = $savings->client->mobile;
                }
            }
            if ($savings->client_type == "group") {
                if (!empty($savings->group)) {
                    $mobile = $savings->group->mobile;
                    $clientName = $savings->group->name;
                }
            }
            if (!empty($mobile) && Setting::where('setting_key',
                    'sms_enabled')->first()->setting_value == 1
            ) {
                $transactionType = '';
                $transactionAmount = '';
                if ($savings_transaction->transaction_type == "withdrawal") {
                    $transactionType = trans_choice('general.withdrawal', 1);
                    $transactionAmount = $savings_transaction->debit;
                }
                if ($savings_transaction->transaction_type == "deposit") {
                    $transactionType = trans_choice('general.deposit', 1);
                    $transactionAmount = $savings_transaction->credit;
                }
                if ($savings_transaction->transaction_type == "interest") {
                    $transactionType = trans_choice('general.interest', 1);
                    $transactionAmount = $savings_transaction->credit;
                }
                if ($savings_transaction->transaction_type == "bank_fees") {
                    $transactionType = trans_choice('general.fee', 2);
                    $transactionAmount = $savings_transaction->debit;
                }
                $body = Setting::where('setting_key', 'savings_transaction_sms_template')->first()->setting_value;
                $body = str_replace('{clientName}', $clientName, $body);
                $body = str_replace('{savingsNumber}', $savings->id, $body);
                $body = str_replace('{transactionAmount}', $transactionAmount, $body);
                $body = str_replace('{transactionType}', $transactionType, $body);
                $body = str_replace('{transactionId}', $savings_transaction->id, $body);
                $body = str_replace('{transactionDate}', $savings_transaction->date, $body);
                $body = str_replace('{savingsBalance}', GeneralHelper::savings_account_balance($savings->id), $body);
                $body = strip_tags($body);
                GeneralHelper::send_sms($mobile, $body);
            }

        }
        if (Setting::where('setting_key',
                'auto_payment_receipt_email')->first()->setting_value == 1
        ) {
            $email = "";
            if ($savings->client_type == "client") {
                if (!empty($savings->client)) {
                    $email = $savings->client->email;
                }
            }
            if ($savings->client_type == "group") {
                if (!empty($savings->group)) {
                    $email = $savings->group->email;
                }
            }
            if (!empty($email)) {
                Mail::to($email)->send(new SavingsTransactionCreatedEmail($savings_transaction));
            }

        }
    }
}
