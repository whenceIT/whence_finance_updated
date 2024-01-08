<?php

namespace App\Mail;

use App\Helpers\GeneralHelper;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;

class RepaymentCreatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $loan_transaction;

    public function __construct($loan_transaction)
    {
        $this->loan_transaction = $loan_transaction;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $clientName = "";
        $loan = $this->loan_transaction->loan;
        $loan_transaction = $this->loan_transaction;
        if ($loan->client_type == "client") {
            if (!empty($loan->client)) {
                if ($loan->client->client_type == "individual") {
                    $clientName = $loan->client->first_name . ' ' . $loan->client->middle_name . ' ' . $loan->client->last_name;
                }
                if ($loan->client->client_type == "business") {
                    $clientName = $loan->client->full_name;
                }
            }
        }
        if ($loan->client_type == "group") {
            if (!empty($loan->group)) {
                $clientName = $loan->group->name;
            }
        }
        $body = Setting::where('setting_key', 'payment_received_email_template')->first()->setting_value;
        $body = str_replace('{clientName}', $clientName, $body);
        $body = str_replace('{loanNumber}', $loan->id, $body);
        $body = str_replace('{paymentAmount}', $this->loan_transaction->credit, $body);
        $body = str_replace('{transactionId}', $this->loan_transaction->id, $body);
        $body = str_replace('{transactionDate}', $this->loan_transaction->date, $body);
        $body = str_replace('{loanBalance}', GeneralHelper::loan_total_balance($loan->id), $body);
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $pdf = PDF::loadView('loan.transaction.pdf', compact('loan_transaction'));
        return $this->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value)->subject(Setting::where('setting_key',
            'payment_received_email_subject')->first()->setting_value)
            ->view('emails.repayment_created', compact('body'))->attachData($pdf->output(),
                trans_choice('general.loan', 1) . ' ' . trans_choice('general.transaction', 1) . ' ' . trans_choice('general.receipt', 1) . ".pdf",
                ['mime' => 'application/pdf'
                ]);
    }
}
