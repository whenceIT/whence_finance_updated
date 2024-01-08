<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use PDF;

class SavingsStatementEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $savings;
    public $start_date;
    public $end_date;

    public function __construct($savings, $start_date, $end_date)
    {
        $this->savings = $savings;
        $this->start_date = $start_date;
        $this->end_date = $end_date;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $clientName = "";
        $savings=$this->savings;
        $start_date=$this->start_date;
        $end_date=$this->end_date;
        if ($savings->client_type == "client") {
            if (!empty($savings->client)) {
                if ($savings->client->client_type == "individual") {
                    $clientName = $savings->client->first_name . ' ' . $savings->client->middle_name . ' ' . $savings->client->last_name;
                }
                if ($savings->client->client_type == "business") {
                    $clientName = $savings->client->full_name;
                }
            }
        }
        if ($savings->client_type == "group") {
            if (!empty($savings->group)) {
                $clientName = $savings->group->name;
            }
        }
        $body = Setting::where('setting_key', 'savings_statement_email_template')->first()->setting_value;
        $body = str_replace('{clientName}', $clientName, $body);
        $body = str_replace('{savingsNumber}', $savings->id, $body);
        $body = str_replace('{savingsBalance}',0, $body);
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        $pdf = PDF::loadView('savings.pdf_statement', compact('savings','start_date','end_date'));
        return $this->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value)->subject(Setting::where('setting_key',
            'savings_statement_email_subject')->first()->setting_value)
            ->view('emails.savings_statement', compact('body'))->attachData($pdf->output(),
                trans_choice('general.savings', 1) . ' ' . trans_choice('general.statement', 1) . ".pdf",
                ['mime' => 'application/pdf'
                ]);
    }
}
