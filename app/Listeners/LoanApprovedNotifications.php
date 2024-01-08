<?php

namespace App\Listeners;

use App\Events\LoanApproved;
use App\Helpers\GeneralHelper;
use App\Mail\LoanApprovedEmail;
use App\Models\LoanRepaymentSchedule;
use App\Models\Setting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class LoanApprovedNotifications
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LoanApproved  $event
     * @return void
     */
    public function handle(LoanApproved $event)
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
                $body = Setting::where('setting_key', 'loan_approved_sms_template')->first()->setting_value;
                $body = str_replace('{clientName}', $clientName, $body);
                $body = str_replace('{loanNumber}', $loan->id, $body);
                $body = str_replace('{approvedAmount}', $loan->approved_amount, $body);
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
                Mail::to($email)->send(new LoanApprovedEmail($loan));
            }

        }
    }
}
