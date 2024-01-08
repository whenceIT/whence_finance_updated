<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Mail\SendSingleEmail;
use App\Models\Client;
use App\Models\CommunicationCampaign;

use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class CommunicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Sentinel::hasAccess('communication.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = CommunicationCampaign::all();
        return view('communication.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('communication.create' );
    }

    public function create_client_sms($client)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($client->mobile)) {
            Flash::warning("Client has no mobile");
            return redirect()->back();
        }
        return view('communication.single_sms', compact('client'));
    }

    public function create_group_sms($group)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($group->mobile)) {
            Flash::warning("Group has no mobile");
            return redirect()->back();
        }
        return view('communication.single_sms', compact('group'));
    }


    public function store_client_sms(Request $request, $client)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($client->mobile)) {
            Flash::warning("Client has no mobile");
            return redirect()->back();
        }
        $communication_campaign = new CommunicationCampaign();
        $communication_campaign->type = "sms";
        $communication_campaign->name = $request->name;
        $communication_campaign->message = $request->message;
        $communication_campaign->created_by_id = Sentinel::getUser()->id;
        $communication_campaign->number_of_recipients = 1;
        $communication_campaign->sent = 1;
        $communication_campaign->status = "active";
        $communication_campaign->recurrence_type = "none";
        $communication_campaign->save();
        GeneralHelper::send_sms($client->mobile, $request->message);
        GeneralHelper::audit_trail("Create", "Communication Campaign", $communication_campaign->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('communication/data');
    }

    public function store_group_sms(Request $request, $group)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($group->mobile)) {
            Flash::warning("group has no mobile");
            return redirect()->back();
        }
        $communication_campaign = new CommunicationCampaign();
        $communication_campaign->type = "sms";
        $communication_campaign->name = $request->name;
        $communication_campaign->message = $request->message;
        $communication_campaign->created_by_id = Sentinel::getUser()->id;
        $communication_campaign->number_of_recipients = 1;
        $communication_campaign->sent = 1;
        $communication_campaign->status = "active";
        $communication_campaign->recurrence_type = "none";
        $communication_campaign->save();
        GeneralHelper::send_sms($group->mobile, $request->message);
        GeneralHelper::audit_trail("Create", "Communication Campaign", $communication_campaign->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('communication/data');
    }

    public function create_client_email($client)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($client->email)) {
            Flash::warning("Client has no email");
            return redirect()->back();
        }
        return view('communication.single_email', compact('client'));
    }

    public function create_group_email($group)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($group->email)) {
            Flash::warning("Group has no email");
            return redirect()->back();
        }
        return view('communication.single_email', compact('group'));
    }


    public function store_client_email(Request $request, $client)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($client->email)) {
            Flash::warning("Client has no email");
            return redirect()->back();
        }
        $communication_campaign = new CommunicationCampaign();
        $communication_campaign->type = "email";
        $communication_campaign->name = $request->name;
        $communication_campaign->message = $request->message;
        $communication_campaign->created_by_id = Sentinel::getUser()->id;
        $communication_campaign->number_of_recipients = 1;
        $communication_campaign->sent = 1;
        $communication_campaign->status = "active";
        $communication_campaign->recurrence_type = "none";
        $communication_campaign->save();
        Mail::to($client->email)->send(new SendSingleEmail($request->subject, $request->message));
        GeneralHelper::audit_trail("Create", "Communication Campaign", $communication_campaign->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('communication/data');
    }

    public function store_group_email(Request $request, $group)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($group->email)) {
            Flash::warning("group has no email");
            return redirect()->back();
        }
        $communication_campaign = new CommunicationCampaign();
        $communication_campaign->type = "email";
        $communication_campaign->name = $request->name;
        $communication_campaign->message = $request->message;
        $communication_campaign->created_by_id = Sentinel::getUser()->id;
        $communication_campaign->number_of_recipients = 1;
        $communication_campaign->sent = 1;
        $communication_campaign->status = "active";
        $communication_campaign->recurrence_type = "none";
        $communication_campaign->save();
        Mail::to($group->email)->send(new SendSingleEmail($request->subject, $request->message));
        GeneralHelper::audit_trail("Create", "Communication Campaign", $communication_campaign->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('communication/data');
    }

    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('communication.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $communication_campaign = new CommunicationCampaign();
        $communication_campaign->created_by_id = Sentinel::getUser()->id;
        $communication_campaign->type = $request->type;
        $communication_campaign->recurrence_type = $request->recurrence_type;
        if ($request->recurrence_type == "schedule") {
            $communication_campaign->recur_frequency = $request->recur_frequency;
            $communication_campaign->report_start_date = $request->report_start_date;
            $communication_campaign->report_start_time = $request->report_start_time;
            $communication_campaign->recur_interval = $request->recur_interval;
        } else {

        }
        $communication_campaign->name = $request->name;
        $communication_campaign->recipients_category = $request->recipients_category;
        $communication_campaign->report_attachment = $request->report_attachment;
        $communication_campaign->from_day = $request->from_day;
        $communication_campaign->to_day = $request->to_day;
        $communication_campaign->office_id = $request->office_id;
        $communication_campaign->loan_officer_id = $request->loan_officer_id;
        $communication_campaign->email_subject = $request->email_subject;
        $communication_campaign->message = $request->message;
        $communication_campaign->status = "pending";
        $communication_campaign->save();
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $from_day = $request->from_day;
        $to_day = $request->to_day;
        //lets try to send the campaign here
        if ($communication_campaign->recurrence_type == "none") {
            //send message immediately
            if ($communication_campaign->recipients_category == "active_clients") {
                $recipients = 0;
                $active_clients = Client::where('status', 'active')->get();
                foreach ($active_clients as $key) {
                    $body = $request->message;
                    $clientName = "";
                    $clientEmail = "";
                    $clientEmail = $key->email;
                    if ($key->client_type == "individual") {
                        $clientName = $key->first_name . ' ' . $key->middle_name . ' ' . $key->last_name;
                    }
                    if ($key->client_type == "business") {
                        $clientName = $key->full_name;
                    }
                    $body = str_replace('{clientName}', $clientName, $body);
                    $body = str_replace('{clientEmail}', $key->email, $body);
                    $body = str_replace('{clientMobile}', $key->mobile, $body);
                    if ($communication_campaign->type == "sms") {
                        if (!empty($key->mobile)) {
                            GeneralHelper::send_sms($key->mobile, strip_tags($body));
                            $recipients++;
                        }

                    }
                    if ($communication_campaign->type == "email") {
                        if (!empty($key->email)) {
                            Mail::to($key->email)->send(new SendSingleEmail($request->email_subject, $body));
                            $recipients++;
                        }

                    }
                }
                $communication_campaign->sent = 1;
                $communication_campaign->active = 1;
                $communication_campaign->status = "active";
                $communication_campaign->save();
            }
            if ($communication_campaign->recipients_category == "prospective_clients") {
                $prospective_clients = Client::where('status', 'active')->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->whereNOTIn('id', function ($query) use ($loan_officer_id) {
                    $query->select('client_id')->from('loans')->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                        if ($loan_officer_id != 0) {
                            $query->where('loan_officer_id', '=', $loan_officer_id);
                        }
                    });
                })->get();
                $recipients = 0;
                foreach ($prospective_clients as $key) {
                    $body = $request->message;
                    $clientName = "";
                    $clientEmail = "";
                    $clientEmail = $key->email;
                    if ($key->client_type == "individual") {
                        $clientName = $key->first_name . ' ' . $key->middle_name . ' ' . $key->last_name;
                    }
                    if ($key->client_type == "business") {
                        $clientName = $key->full_name;
                    }
                    $body = str_replace('{clientName}', $clientName, $body);
                    $body = str_replace('{clientEmail}', $key->email, $body);
                    $body = str_replace('{clientMobile}', $key->mobile, $body);
                    if ($communication_campaign->type == "sms") {
                        if (!empty($key->mobile)) {
                            GeneralHelper::send_sms($key->mobile, strip_tags($body));
                            $recipients++;
                        }

                    }
                    if ($communication_campaign->type == "email") {
                        if (!empty($key->email)) {
                            Mail::to($key->email)->send(new SendSingleEmail($request->email_subject, $body));
                            $recipients++;
                        }

                    }
                }
                $communication_campaign->sent = 1;
                $communication_campaign->active = 1;
                $communication_campaign->status = "active";
                $communication_campaign->save();
            }
            if ($communication_campaign->recipients_category == "active_loans") {
                $active_loans = \App\Models\Client::where('status', 'active')->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->whereIn('id', function ($query) use ($from_day, $to_day) {
                    $query->select('l.client_id')->from('loans as l')->join("loan_repayment_schedules as lr", "l.id", '=', "lr.loan_id")->groupBy("l.id")->havingRaw('(COALESCE(SUM(lr.principal),0)+COALESCE(SUM(lr.interest),0)+COALESCE(SUM(lr.fees),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) >0')->distinct();
                })->get();
                $recipients = 0;
                foreach ($active_loans as $key) {
                    $body = $request->message;
                    $clientName = "";
                    $clientEmail = "";
                    $clientEmail = $key->email;
                    if ($key->client_type == "individual") {
                        $clientName = $key->first_name . ' ' . $key->middle_name . ' ' . $key->last_name;
                    }
                    if ($key->client_type == "business") {
                        $clientName = $key->full_name;
                    }
                    $body = str_replace('{clientName}', $clientName, $body);
                    $body = str_replace('{clientEmail}', $key->email, $body);
                    $body = str_replace('{clientMobile}', $key->mobile, $body);
                    if ($communication_campaign->type == "sms") {
                        if (!empty($key->mobile)) {
                            GeneralHelper::send_sms($key->mobile, strip_tags($body));
                            $recipients++;
                        }

                    }
                    if ($communication_campaign->type == "email") {
                        if (!empty($key->email)) {
                            Mail::to($key->email)->send(new SendSingleEmail($request->email_subject, $body));
                            $recipients++;
                        }

                    }
                }
                $communication_campaign->sent = 1;
                $communication_campaign->active = 1;
                $communication_campaign->status = "active";
                $communication_campaign->save();
            }
            if ($communication_campaign->recipients_category == "overdue_loans") {
                $overdue_loans = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
                $recipients=0;
                foreach ($overdue_loans as $key) {
                    $due = 0;
                    $amount = 0;
                    $outstanding = 0;
                    $principal = 0;
                    $interest = 0;
                    $fees = 0;
                    $penalty = 0;
                    $amount_in_arrears = 0;
                    $overdue_date = "";
                    $days_in_arrears = 0;
                    if (strtotime($key->expected_maturity_date) < strtotime(date("Y-m-d"))) {
                        $remaining_days = 0;
                    } else {
                        $date1 = new \DateTime($key->expected_maturity_date);
                        $date2 = new \DateTime(date("Y-m-d"));
                        $remaining_days = $date2->diff($date1)->format("%a");
                    }

                    $days_since_last_payment = 0;
                    $balance = 0;
                    $percentage = 0;
                    $late_count = 0;
                    foreach ($key->repayment_schedules as $schedule) {
                        if (strtotime($schedule->due_date) < strtotime(date("Y-m-d"))) {
                            $amount_in_arrears = $amount_in_arrears + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
                        }
                        $principal = $principal + $schedule->principal - $schedule->principal_waived - $schedule->principal_written_off;
                        $interest = $interest + $schedule->interest - $schedule->interest_waived - $schedule->interest_written_off;
                        $fees = $fees + $schedule->fees - $schedule->fees_waived - $schedule->fees_written_off;
                        $penalty = $penalty + $schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off;
                        $balance = $balance + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
                        if ($amount_in_arrears > 0) {
                            $late_count++;
                            if ($late_count == 1) {
                                $overdue_date = $schedule->due_date;
                            }
                        }
                    }

                    if ($amount_in_arrears > 0) {
                        $date1 = new \DateTime($overdue_date);
                        $date2 = new \DateTime(date("Y-m-d"));
                        $days_in_arrears = $date2->diff($date1)->format("%a");
                        $transaction = \App\Models\LoanTransaction::where('loan_id',
                            $key->id)->where('transaction_type',
                            'repayment')->where('reversed', 0)->orderBy('date', 'desc')->first();
                        if (!empty($transaction)) {
                            $date2 = new \DateTime($transaction->date);
                            $date1 = new \DateTime(date("Y-m-d"));
                            $days_since_last_payment = $date2->diff($date1)->format("%r%a");
                        } else {
                            $days_since_last_payment = 0;
                        }
                        $body = $request->message;
                        $clientName = "";
                        $clientEmail = "";
                        $clientMobile = "";

                        if ($key->client_type == "client") {
                            if (!empty($key->client)) {
                                $clientEmail = $key->client->email;
                                $clientMobile = $key->client->mobile;
                                if ($key->client->client_type == "individual") {
                                    $clientName = $key->client->first_name . ' ' . $key->client->middle_name . ' ' . $key->client->last_name;
                                }
                                if ($key->client->client_type == "business") {
                                    $clientName = $key->client->full_name;
                                }
                            }
                        }
                        if ($key->client_type == "group") {
                            if (!empty($key->group)) {
                                $clientMobile = $key->group->mobile;
                                $clientName = $key->group->name;
                                $clientEmail = $key->group->email;
                            }
                        }
                        $body = str_replace('{clientName}', $clientName, $body);
                        $body = str_replace('{clientEmail}', $clientEmail, $body);
                        $body = str_replace('{clientMobile}', $clientMobile, $body);
                        $body = str_replace('{arrearsAmount}', $amount_in_arrears, $body);
                        $body = str_replace('{daysInArrears}', $days_in_arrears, $body);
                        $body = str_replace('{daysSinceLastPayment}', $days_since_last_payment, $body);
                        $body = str_replace('{loanBalance}', $balance, $body);
                        if ($communication_campaign->type == "sms") {
                            if (!empty($clientMobile)) {
                                GeneralHelper::send_sms($clientMobile, strip_tags($body));
                                $recipients++;
                            }

                        }
                        if ($communication_campaign->type == "email") {
                            if (!empty($clientEmail)) {
                                Mail::to($clientEmail)->send(new SendSingleEmail($request->email_subject, $body));
                                $recipients++;
                            }

                        }
                    }
                }
                $communication_campaign->sent = 1;
                $communication_campaign->active = 1;
                $communication_campaign->status = "active";
                $communication_campaign->save();
            }
        }
        GeneralHelper::audit_trail("Create", "Communication Campaign", $communication_campaign->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('communication/data');
    }

    public function show($communication_campaign)
    {
        if (!Sentinel::hasAccess('communication.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('fund.show', compact('fund'));
    }


    public function edit($communication_campaign)
    {
        if (!Sentinel::hasAccess('communication.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('fund.edit', compact('fund'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('communication.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $communication_campaign = CommunicationCampaign::find($id);
        $communication_campaign->name = $request->name;
        $communication_campaign->save();
        GeneralHelper::audit_trail("Update", "Communication Campaign", $communication_campaign->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('fund/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('communication.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        CommunicationCampaign::destroy($id);
        GeneralHelper::audit_trail("Delete", "Communication Campaign", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();
    }

}
