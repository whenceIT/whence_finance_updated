<?php

namespace App\Listeners;

use App\Events\LoanDisbursed;
use App\Helpers\GeneralHelper;
use App\Mail\LoanDisbursedEmail;
use App\Models\LoanRepaymentSchedule;
use App\Models\Setting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class LoanDisbursedNotifications
{

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LoanDisbursed $event
     * @return void
     */
    public function handle(LoanDisbursed $event)
    {

        $loan = $event->loan;
        if (Setting::where('setting_key',
                'loan_disbursed_auto_sms')->first()->setting_value == 1
        ) {
            $mobile = "";
            $clientName = "";
            if ($loan->client_type == "client") {
                if (!empty($loan->client)) {
                    if ($loan->client->client_type == "individual") {
                        $clientName = $loan->client->first_name . ' ' . $loan->client->middle_name . ' ' . $loan->client->last_name;
                    }
                    if ($loan->client->client_type == "business") {
                        $clientName = $loan->client->full_name;
                    }
                    $mobile = $loan->client->mobile;
                }
            }
            if ($loan->client_type == "group") {
                if (!empty($loan->group)) {
                    $mobile = $loan->group->mobile;
                    $clientName = $loan->group->name;
                }
            }
            if (!empty($mobile) && Setting::where('setting_key',
                    'sms_enabled')->first()->setting_value == 1
            ) {
                $schedule = LoanRepaymentSchedule::where('loan_id', $loan->id)->orderBy('due_date', 'asc')->first();
                $body = Setting::where('setting_key', 'loan_disbursed_sms_template')->first()->setting_value;
                $body = str_replace('{clientName}', $clientName, $body);
                $body = str_replace('{loanNumber}', $loan->id, $body);
                $body = str_replace('{firstPaymentDate}', $schedule->due_date, $body);
                $body = str_replace('{firstPaymentAmount}', $schedule->principal + $schedule->interest + $schedule->penalty + $schedule->fees, $body);
                $body = strip_tags($body);
                GeneralHelper::send_sms($mobile, $body);
            }

        }
        if (Setting::where('setting_key',
                'loan_disbursed_auto_email')->first()->setting_value == 1
        ) {
            $email = "";
            if ($loan->client_type == "client") {
                if (!empty($loan->client)) {
                    $email = $loan->client->email;
                }
            }
            if ($loan->client_type == "group") {
                if (!empty($loan->group)) {
                    $email = $loan->group->email;
                }
            }
            if (!empty($email)) {
                Mail::to($email)->send(new LoanDisbursedEmail($loan));
            }

        }
    }
}
