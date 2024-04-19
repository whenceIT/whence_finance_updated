<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Mail\UpcomingRepaymentReminderEmail;
use App\Models\Client;
use App\Models\Group;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProductCharge;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\PaymentType;
use App\Models\Savings;
use App\Models\SavingsTransaction;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class PortalController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }



    public function loan_index()
    {
            $thisClient = Client::where('user_id',Sentinel::getUser()->id)->first();
        $client_ids = [$thisClient->id];
        foreach (Sentinel::getUser()->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::getUser()->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        $data = Loan::where(function ($query) use ($client_ids, $group_ids) {
            $query->whereIn('client_id', $client_ids)
                ->orWhereIn('group_id', $group_ids);
        })->with('repayment_schedules')->cursor();

        return view('portal.loan.data', compact('data'));

    }

    public function loan_show($loan)
    {
            $thisClient = Client::where('user_id',Sentinel::getUser()->id)->first();
        $client_ids = [$thisClient->id];
        foreach (Sentinel::getUser()->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::getUser()->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        if ($loan->client_type == "client") {
            if (!in_array($loan->client_id, $client_ids)) {
                Flash::warning("Permission Denied");
                return redirect()->back();
            }
        }
        if ($loan->client_type == "group") {
            if (!in_array($loan->group_id, $group_ids)) {
                Flash::warning("Permission Denied");
                return redirect()->back();
            }
        }

        return view('portal.loan.show', compact('loan'));

    }

    public function savings_index()
    {
            $thisClient = Client::where('user_id',Sentinel::getUser()->id)->first();
        $client_ids = [$thisClient->id];
        foreach (Sentinel::getUser()->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::getUser()->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        $data = Savings::where(function ($query) use ($client_ids, $group_ids) {
            $query->whereIn('client_id', $client_ids)
                ->orWhereIn('group_id', $group_ids);
        })->get();

        return view('portal.savings.data', compact('data'));

    }

    public function savings_show($savings)
    {
            $thisClient = Client::where('user_id',Sentinel::getUser()->id)->first();
        $client_ids = [$thisClient->id];
        foreach (Sentinel::getUser()->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::getUser()->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        if ($savings->client_type == "client") {
            if (!in_array($savings->client_id, $client_ids)) {
                Flash::warning("Permission Denied");
                return redirect()->back();
            }
        }
        if ($savings->client_type == "group") {
            if (!in_array($savings->group_id, $group_ids)) {
                Flash::warning("Permission Denied");
                return redirect()->back();
            }
        }

        return view('portal.savings.show', compact('savings'));

    }

    public function loan_application_index()
    {
           $thisClient = Client::where('user_id',Sentinel::getUser()->id)->first();
        $client_ids = [$thisClient->id];
        foreach (Sentinel::getUser()->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::getUser()->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        $data = LoanApplication::where(function ($query) use ($client_ids, $group_ids) {
            $query->whereIn('client_id', $client_ids)
                ->orWhereIn('group_id', $group_ids);
        })->get();

        return view('portal.loan_application.data', compact('data'));

    }

    public function loan_application_show($loan_application)
    {
            $thisClient = Client::where('user_id',Sentinel::getUser()->id)->first();
        $client_ids = [$thisClient->id];
        foreach (Sentinel::getUser()->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::getUser()->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        if ($loan_application->client_type == "client") {
            if (!in_array($loan_application->client_id, $client_ids)) {
                Flash::warning("Permission Denied");
                return redirect()->back();
            }
        }
        if ($loan_application->client_type == "group") {
            if (!in_array($loan_application->group_id, $group_ids)) {
                Flash::warning("Permission Denied");
                return redirect()->back();
            }
        }

        return view('portal.loan_application.show', compact('loan_application'));

    }

    public function loan_application_create()
    {
        $thisClient = Client::where('user_id',Sentinel::getUser()->id)->first();
        $client_ids = [$thisClient->id];


        foreach (Sentinel::getUser()->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }

        $group_ids = [];
        foreach (Sentinel::getUser()->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }

        // dd($client_ids);


        return view('portal.loan_application.create', compact('client_ids', 'group_ids'));

    }

    public function loan_application_store(Request $request)
    {
           $thisClient = Client::where('user_id',Sentinel::getUser()->id)->first();
        $client_ids = [$thisClient->id];

        foreach (Sentinel::getUser()->client_users as $key) {
            array_push($client_ids, $key->client_id);
        }
        $group_ids = [];
        foreach (Sentinel::getUser()->group_users as $key) {
            array_push($group_ids, $key->group_id);
        }
        $loan_application = new LoanApplication();

        if ($request->type == "client") {
            if (!in_array($request->client_id, $client_ids)) {
                Flash::warning("Permission Denied");
                return redirect()->back();
            }
            $client = Client::find($request->client_id);
            $loan_application->office_id = $client->office_id;
            $loan_application->client_id = $client->id;
            $loan_application->staff_id = $client->staff_id;
        }
        if ($request->type == "group") {
            if (!in_array($request->group_id, $group_ids)) {
                Flash::warning("Permission Denied");
                return redirect()->back();
            }
            $group = Group::find($request->group_id);
            $loan_application->office_id = $group->office_id;
            $loan_application->group_id = $group->id;
        }
        $loan_application->amount = $request->amount;
        $loan_application->loan_product_id = $request->loan_product_id;
        $loan_application->client_type = $request->type;
        $loan_application->loan_purpose_id = $request->loan_purpose_id;
        $loan_application->notes = $request->notes;
        $loan_application->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('portal/loan_application/data');

    }


        /**
     * create profile by client
     *
     * @return \Illuminate\Http\Response
     */
    public function create_profile()
    {


        if (!Sentinel::inRole('client')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //get custom fields
        //$custom_fields = CustomField::where('category', 'clients')->get();
        return view('portal/profile.create');
    }


}
