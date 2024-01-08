<?php

namespace App\Mail;

use App\Helpers\GeneralHelper;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;
class SavingsTransactionCreatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $savings_transaction;

    public function __construct($savings_transaction)
    {
        $this->savings_transaction = $savings_transaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $clientName = "";
        $savings_transaction = $this->savings_transaction;
        $savings = $savings_transaction->savings;
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
        $body = Setting::where('setting_key', 'savings_transaction_email_template')->first()->setting_value;
        $body = str_replace('{clientName}', $clientName, $body);
        $body = str_replace('{savingsNumber}', $savings->id, $body);
        $body = str_replace('{transactionAmount}', $transactionAmount, $body);
        $body = str_replace('{transactionType}', $transactionType, $body);
        $body = str_replace('{transactionId}', $savings_transaction->id, $body);
        $body = str_replace('{transactionDate}', $savings_transaction->date, $body);
        $body = str_replace('{savingsBalance}', GeneralHelper::savings_account_balance($savings->id), $body);
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $pdf = PDF::loadView('savings.transaction.pdf', compact('savings_transaction'));
        return $this->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value)->subject(Setting::where('setting_key',
            'savings_transaction_email_subject')->first()->setting_value)
            ->view('emails.basic_base', compact('body'))->attachData($pdf->output(),
                trans_choice('general.savings', 1) . ' ' . trans_choice('general.transaction', 1) . ' ' . trans_choice('general.receipt', 1) . ".pdf",
                ['mime' => 'application/pdf'
                ]);
    }
}
