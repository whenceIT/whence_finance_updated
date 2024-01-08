<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoanApprovedEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $loan;

    public function __construct($loan)
    {
        $this->loan = $loan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $clientName = "";
        if ($this->loan->client_type == "client") {
            if (!empty($this->loan->client)) {
                if ($this->loan->client->client_type == "individual") {
                    $clientName = $this->loan->client->first_name . ' ' . $this->loan->client->middle_name . ' ' . $this->loan->client->last_name;
                }
                if ($this->loan->client->client_type == "business") {
                    $clientName = $this->loan->client->full_name;
                }
            }
        }
        if ($this->loan->client_type == "group") {
            if (!empty($this->loan->group)) {
                $clientName = $this->loan->group->name;
            }
        }
        $body = Setting::where('setting_key', 'loan_approved_email_template')->first()->setting_value;
        $body = str_replace('{clientName}', $clientName, $body);
        $body = str_replace('{loanNumber}', $this->loan->id, $body);
        $body = str_replace('{approvedAmount}', $this->loan->approved_amount, $body);
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        return $this->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value)->subject(Setting::where('setting_key',
            'loan_approved_email_subject')->first()->setting_value)
            ->view('emails.loan_approved', compact('body'));
    }
}
