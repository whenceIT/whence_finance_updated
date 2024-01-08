<?php

namespace App\Mail;

use App\Models\Loan;
use App\Models\LoanRepaymentSchedule;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpcomingRepaymentReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $loan_id;
    protected $schedule_id;

    public function __construct($loan_id, $schedule_id)
    {
        $this->loan_id = $loan_id;
        $this->schedule_id = $schedule_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $loan = Loan::find($this->loan_id);
        $schedule = LoanRepaymentSchedule::find($this->schedule_id);
        $clientName = "";
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
        $body = Setting::where('setting_key',
            'loan_payment_reminder_email_template')->first()->setting_value;
        $body = str_replace('{clientName}', $clientName, $body);
        $body = str_replace('{paymentDate}', $schedule->due_date, $body);
        $body = str_replace('{loanNumber}', $schedule->loan_id, $body);
        $body = str_replace('{paymentAmount}', ($schedule->principal - $schedule->principal_paid - $schedule->principal_waived - $schedule->principal_written_off + $schedule->interest - $schedule->interest_paid - $schedule->interest_waived - $schedule->interest_written_off + $schedule->fees - $schedule->fees_paid - $schedule->fees_waived - $schedule->fees_written_off + $schedule->penalty - $schedule->penalty_paid - $schedule->penalty_waived - $schedule->penalty_written_off), $body);
        $companyLogo = '<a href="' . Setting::where('setting_key',
                'company_website')->first()->setting_value . '"><img src="' . asset('uploads/' . Setting::where('setting_key',
                    'company_logo')->first()->setting_value) . '" height="150"/></a>';
        $body = str_replace('{companyLogo}', $companyLogo, $body);
        return $this->from(Setting::where('setting_key', 'company_email')->first()->setting_value, Setting::where('setting_key', 'company_name')->first()->setting_value)->subject(Setting::where('setting_key',
            'loan_payment_reminder_subject')->first()->setting_value)
            ->view('emails.upcoming_payment_reminder', compact('body'));
    }
}
