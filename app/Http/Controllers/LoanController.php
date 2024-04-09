<?php

namespace App\Http\Controllers;

use App\Events\LoanApproved;
use App\Events\LoanDisbursed;
use App\Events\RepaymentCreated;
use App\Events\RepaymentUpdated;
use App\Events\TransactionUpdated;
use App\Helpers\GeneralHelper;
use App\Mail\RepaymentScheduleEmail;
use App\Models\Charge;
use App\Models\Client;
use App\Models\Collateral;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Document;
use App\Models\GlJournalEntry;
use App\Models\GroupLoanAllocation;
use App\Models\Guarantor;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanCharge;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionUnapproved;
use App\Models\LoanTransactionsPending;
use App\Models\LoanTopUp;
use App\Models\Note;
use App\Models\PaymentDetail;
use App\Models\Savings;
use App\Models\SavingsTransaction;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use PDF;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class LoanController extends Controller
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
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
       // $data = [];
    //    LoanTransaction::chunk(100,function($loan_transactions){
    //     foreach($loan_transactions as $loan_transaction){
    //         $trans_principal = $loan_transaction->debit;
    //     }

    // });
   // $info = LoanTransaction::where('transaction_type','disbursement')->paginate();
        $data = Loan::where('status', 'disbursed')->with('repayment_schedules')->with('transactions')->get();
      //  LoanTransaction::chunck(2000,function($posts){
            
      //  });
        $loan_transactions = DB::table('loan_transactions')->get();

        return view('loan.data', compact('data'));
    }

    public function my_index()
    {
        if (!Sentinel::hasAccess('loans.my_loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $staff_id = Sentinel::getUser()->id;
        $data = Loan::where('status', 'disbursed')->with('repayment_schedules')->where('loan_officer_id', $staff_id)->get();
        //$loan_transaction = LoanTransaction::get();
        return view('loan.my_loans', compact('data'));
    }


    public function branch_index()
    {
        if (!Sentinel::hasAccess('loans.branch_loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $office_id = Sentinel::getUser()->office_id;
        $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
            if ($office_id != 0) {
                $query->where('office_id', '=', $office_id);
            }
        })->with('loan_officer')->with('office')->with('transactions')->get();

        return view('loan.branch_loans', compact('data'));
    }


    public function reloan_approvals(){

        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $office_id = Sentinel::getUser()->office_id;
        if (Sentinel::hasAccess('settings')){
            $data = LoanTransactionsPending::get();
        } else{
            $data = LoanTransactionsPending::where('office_id',$office_id)->get();
        }

        return view('loan.reloan_approvals',compact('data'));
    }


    public function collections(){
        $userBranch = Sentinel::getUser()->office_id; 
        $BranchLoans = Loan::with('transactions')->where('office_id',$userBranch)->where('status','disbursed')->get();
        $LoanArray = [];
        $LoanArrayTwo = [];
        foreach($BranchLoans as $loan){
            array_push($LoanArray,$loan);
            array_push($LoanArrayTwo,$loan);
        }

        return view('loan.collections',compact('LoanArray','BranchLoans','LoanArrayTwo',));
    }

    public function my_collections(){
        $user = Sentinel::getUser()->id; 
        $BranchLoans = Loan::with('transactions')->where('loan_officer_id',$user)->where('status','disbursed')->get();
        $LoanArray = [];
        $LoanArrayTwo = [];
        foreach($BranchLoans as $loan){
            array_push($LoanArray,$loan);
            array_push($LoanArrayTwo,$loan);
        }

        return view('loan.my_collections',compact('LoanArray','BranchLoans','LoanArrayTwo',));
    }

    
  

//PART PAYMENT AND FULL PAYMENT APPROVALS
    public function transaction_approvals(){

        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $office_id = Sentinel::getUser()->office_id;
        if (Sentinel::hasAccess('settings')){
            $data = LoanTransactionUnapproved::get();
        } else{
            $data = LoanTransactionUnapproved::where('office_id',$office_id)->get();
        }

        return view('loan.transactions',compact('data'));
    }


    public function pending_approval()
    {
        if (!Sentinel::hasAccess('loans.pending_approval')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $staff_id = Sentinel::getUser()->id;
        $data = Loan::where('status', 'pending')->get();

        return view('loan.pending_approval', compact('data'));
    }

    public function managers_pending_approval(){
        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $office_id = Sentinel::getUser()->office_id;
        $data = Loan::where('status', 'pending')->where('office_id',$office_id)->get();

        return view('loan.managers_pending_approval',compact('data'));
    }

    public function awaiting_disbursement()
    {
        if (!Sentinel::hasAccess('loans.awaiting_disbursement')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Loan::where('status', 'approved')->get();

        return view('loan.awaiting_disbursement', compact('data'));
    }

    public function loans_declined()
    {
        if (!Sentinel::hasAccess('loans.declined')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Loan::where('status', 'declined')->get();

        return view('loan.loans_declined', compact('data'));
    }

    public function loans_written_off()
    {
        if (!Sentinel::hasAccess('loans.written_off')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Loan::where('status', 'written_off')->get();

        return view('loan.loans_written_off', compact('data'));
    }

    public function loans_closed()
    {
        if (!Sentinel::hasAccess('loans.closed')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Loan::where('status', 'closed')->get();

        return view('loan.loans_closed', compact('data'));
    }

    public function loans_rescheduled()
    {
        if (!Sentinel::hasAccess('loans.rescheduled')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Loan::where('status', 'rescheduled')->get();

        return view('loan.loans_rescheduled', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $userBranch = Sentinel::getUser()->office_id;
        $clients = Client::where('status', 'active')->where('blacklisted', 0)->get();


        return view('loan.create',compact('userBranch'));
    }
///////////////////////////////////////
    public function create_client_loan($client, $loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        if (empty($client) || empty($loan_product)) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back();
        }
        if ($client->blacklisted) {
            Flash::warning(trans_choice('general.client', 1) . ' ' . trans_choice('general.blacklisted', 1));
            return redirect()->back();
        }

        $client_loan = Loan::where('client_id', '=', $client->id)->where('loan_product_id', '=', $loan_product->id)->where('status', '!=','closed')->where('status', '!=','declined')->first();
        if ($client_loan) {
            Flash::warning($client->first_name . '  ' . $client->last_name . ' ' . 'already has a loan on' . ' ' . $loan_product->name);
            return redirect()->back();
        } else {
            return view('loan.create_client_loan',
                compact('client', 'loan_product'));
        }
    }

    public function create_group_loan($group, $loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($group) || empty($loan_product)) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back();
        }

        return view('loan.create_group_loan',
            compact('group', 'loan_product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store_client_loan(Request $request, $client, $loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $client_loan = Loan::where('client_id', '=', $client->id)->where('loan_product_id', '=', $loan_product->id)->where('status', '!=','closed')->where('status', '!=','declined')->first();
        if ($client_loan) {
            Flash::warning($client->first_name . '  ' . $client->last_name . ' ' . 'already has a loan on' . ' ' . $loan_product->name);
            return redirect('loan/create');
        }


            $rules = array(
                'loan_officer_id' => 'required',
                'principal' => 'required',
                'loan_term' => 'required',
                'loan_term_type' => 'required',
                'repayment_frequency' => 'required',
                'repayment_frequency_type' => 'required',
                'external_id' => 'required',
                'interest_rate' => 'required',
                'expected_disbursement_date' => 'required',
                'expected_first_repayment_date' => 'required|after_or_equal:expected_disbursement_date',
            );
            $messages = [
                'loan_officer_id.required' => 'Loan Officer is required',
                'principal.required' => 'Principal is required',
                'loan_term_type.required' => 'Loan term is required',
                'external_id.required' => 'External ID is required',
                'repayment_frequency.required' => 'repayment frequency is required',
                'repayment_frequency_type.required' => 'repayment frequency type is required',
                'interest_rate.required' => 'interest rate is required',
                'interest_rate_type.required' => 'interest rate type is required',
                'expected_disbursement_date.required' => 'Expected disbursement date is required',
                'expected_first_repayment_date.required' => 'Expected first repayment date is required',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $loan = new Loan();
                $loan->created_by_id = Sentinel::getUser()->id;
                $loan->created_date = $request->created_date;
                $loan->client_type = "client";
                $loan->loan_product_id = $loan_product->id;
                $loan->client_id = $client->id;
                $loan->office_id = $client->office_id;
                $loan->fund_id = $request->fund_id;
                $loan->decimals = $loan_product->decimals;
                $loan->loan_officer_id = $request->loan_officer_id;
                $loan->loan_purpose_id = $request->loan_purpose_id;
                $loan->external_id = $request->external_id;
                $loan->principal = $request->principal;
                $loan->applied_amount = $request->principal;
                $loan->currency_id = $loan_product->currency_id;
                $loan->loan_term = $request->loan_term;
                $loan->loan_term_type = $request->loan_term_type;
                $loan->repayment_frequency = $request->repayment_frequency;
                $loan->repayment_frequency_type = $request->repayment_frequency_type;
                $loan->interest_rate = $request->interest_rate;
                $loan->interest_rate_type = $loan_product->interest_rate_type;
                $loan->override_interest = $request->override_interest;
                $loan->override_interest_rate = $loan_product->override_interest_rate;
                $loan->expected_disbursement_date = $request->expected_disbursement_date;
                if (!empty($request->expected_first_repayment_date)) {
                    $loan->expected_first_repayment_date = $request->expected_first_repayment_date;
                }
                $loan->interest_method = $loan_product->interest_method;
                $loan->armotization_method = $loan_product->armotization_method;
                $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
                $loan->grace_on_principal = $loan_product->grace_on_principal;
                $loan->grace_on_interest_payment = $loan_product->grace_on_interest_payment;
                $date = explode('-', $request->created_date);
                $loan->month = $date[1];
                $loan->year = $date[0];
                $loan->save();
                if (!empty($request->charges)) {
                    //loop through the array
                    foreach ($request->charges as $key) {
                        $charge = Charge::find($key);
                        $loan_charge = new LoanCharge();
                        $loan_charge->loan_id = $loan->id;
                        $loan_charge->charge_id = $key;
                        if ($charge->override == 1) {
                            $loan_charge->amount = $request->charge_amount[$key];
                        } else {
                            $loan_charge->amount = $charge->amount;
                        }
                        if ($charge->charge_type == "specified_due_date") {
                            $loan_charge->due_date = $request->charge_date[$key];
                        } else {
    
                        }
                        $loan_charge->charge_type = $charge->charge_type;
                        $loan_charge->charge_option = $charge->charge_option;
                        $loan_charge->save();
                    }
                }
                //check custom fields
                if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                    $custom_fields = CustomField::where('category', 'loans')->get();
                    foreach ($custom_fields as $key) {
                        $custom_field = new CustomFieldMeta();
                        $id = "custom_field_" . $key->id;
                        if ($key->field_type == "checkbox") {
                            if (!empty($request->$id)) {
                                $custom_field->name = serialize($request->$id);
                            } else {
                                $custom_field->name = serialize([]);
                            }
                        } else {
                            $custom_field->name = $request->$id;
                        }
                        $custom_field->parent_id = $loan->id;
                        $custom_field->custom_field_id = $key->id;
                        $custom_field->category = "loans";
                        $custom_field->save();
                    }
                }
                GeneralHelper::audit_trail("Create", "Loans", $loan->id);
                Flash::success(trans('general.successfully_saved'));
                return redirect('loan/' . $loan->id . '/show');
            }

    }

    public function store_group_loan(Request $request, $group, $loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'loan_officer_id' => 'required',
            'principal' => 'required',
            'loan_term' => 'required',
            'loan_term_type' => 'required',
            'repayment_frequency' => 'required',
            'repayment_frequency_type' => 'required',
            'interest_rate' => 'required',
            'expected_disbursement_date' => 'required',
            'expected_first_repayment_date' => 'required|after_or_equal:expected_disbursement_date',
        );
        $messages = [
            'loan_officer_id.required' => 'Loan Officer is required',
            'principal.required' => 'Principal is required',
            'loan_term_type.required' => 'Loan term is required',
            'repayment_frequency.required' => 'repayment frequency is required',
            'repayment_frequency_type.required' => 'repayment frequency type is required',
            'interest_rate.required' => 'interest rate is required',
            'interest_rate_type.required' => 'interest rate type is required',
            'expected_disbursement_date.required' => 'Expected disbursement date is required',
            'expected_first_repayment_date.required' => 'Expected first repayment date is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if (empty($request->client)) {
            Flash::warning("Group must have clients");
            return redirect()->back()->withInput();
        }
        if (!empty($request->client)) {
            $total = 0;
            foreach ($request->client as $key) {
                $total = $total + $key;
            }
            if ($request->principal != $total) {
                Flash::warning("Group allocation total must be equal to the principal");
                return redirect()->back()->withInput();
            }
        }
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $loan = new Loan();
            $loan->created_by_id = Sentinel::getUser()->id;
            $loan->created_date = $request->created_date;
            $loan->client_type = "group";
            $loan->loan_product_id = $loan_product->id;
            $loan->group_id = $group->id;
            $loan->office_id = $group->office_id;
            $loan->fund_id = $request->fund_id;
            $loan->decimals = $loan_product->decimals;
            $loan->loan_officer_id = $request->loan_officer_id;
            $loan->loan_purpose_id = $request->loan_purpose_id;
            $loan->external_id = $request->external_id;
            $loan->principal = $request->principal;
            $loan->applied_amount = $request->applied_amount;
            $loan->currency_id = $loan_product->currency_id;
            $loan->loan_term = $request->loan_term;
            $loan->loan_term_type = $request->loan_term_type;
            $loan->repayment_frequency = $request->repayment_frequency;
            $loan->repayment_frequency_type = $request->repayment_frequency_type;
            $loan->interest_rate = $request->interest_rate;
            $loan->interest_rate_type = $loan_product->interest_rate_type;
            $loan->override_interest = $request->override_interest;
            $loan->override_interest_rate = $request->override_interest_rate;
            $loan->expected_disbursement_date = $request->expected_disbursement_date;
            if (!empty($request->expected_first_repayment_date)) {
                $loan->expected_first_repayment_date = $request->expected_first_repayment_date;
            }
            $loan->interest_method = $loan_product->interest_method;
            $loan->armotization_method = $loan_product->armotization_method;
            $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
            $loan->grace_on_principal = $loan_product->grace_on_principal;
            $loan->grace_on_interest_payment = $loan_product->grace_on_interest_payment;
            $date = explode('-', $request->created_date);
            $loan->month = $date[1];
            $loan->year = $date[0];
            $loan->save();
            //save loan allocation
            foreach ($request->client as $key => $value) {
                $group_loan_allocation = new GroupLoanAllocation();
                $group_loan_allocation->loan_id = $loan->id;
                $group_loan_allocation->group_id = $group->id;
                $group_loan_allocation->client_id = $key;
                $group_loan_allocation->amount = $value;
                $group_loan_allocation->save();
            }
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $loan_charge = new LoanCharge();
                    $loan_charge->loan_id = $loan->id;
                    $loan_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $loan_charge->amount = $request->charge_amount[$key];
                    } else {
                        $loan_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $loan_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $loan_charge->charge_type = $charge->charge_type;
                    $loan_charge->charge_option = $charge->charge_option;
                    $loan_charge->save();
                }
            }
            //check custom fields
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'loans')->get();
                foreach ($custom_fields as $key) {
                    $custom_field = new CustomFieldMeta();
                    $id = "custom_field_" . $key->id;
                    if ($key->field_type == "checkbox") {
                        if (!empty($request->$id)) {
                            $custom_field->name = serialize($request->$id);
                        } else {
                            $custom_field->name = serialize([]);
                        }
                    } else {
                        $custom_field->name = $request->$id;
                    }
                    $custom_field->parent_id = $loan->id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "loans";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Create", "Loans", $loan->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }
    }


    public function show($loan)
    {
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('loan.show', compact('loan'));
    }


    public function edit($loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($loan->loan_product)) {
            Flash::warning("Loan Product not found");
            return redirect()->back();
        }
        if ($loan->client_type == "client") {
            return view('loan.edit_client_loan',
                compact('loan'));
        }
        if ($loan->client_type == "group") {
            return view('loan.edit_group_loan',
                compact('loan'));
        }

    }


    public function activate($loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan = Loan::find($loan->id);
        $loan->status = "disbursed";
        $loan->save();
        return redirect('loan/' . $loan->id . '/show');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */



    public function set_defaulted(Request $request, $id){

        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $loan = Loan::find($id);
        $loan->defaulted = 'yes';
        $loan->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }


    public function add_top_up(Request $request, $id){
        $loan = Loan::find($id);
        $loanTransDisbursed = LoanTransaction::where('loan_id',$id)->where('transaction_type','disbursement')->first();
        $loanTransInterest = LoanTransaction::where('loan_id',$id)->where('transaction_type','interest_initial')->first();
        $loan_topup = new LoanTopUp();
        if ($request->top_up_date == null ){
            $loan_topup->date = date("Y-m-d");
        }else{
            $loan_topup->date = $request->top_up_date;
        }
        $loan_topup->loan_id = $loan->id;
        $loan_topup->office_id = $loan->office_id;
        $loan_topup->created_by = Sentinel::getUser()->id;
        $loan_topup->amount = $request->amount;
        $loan_topup->balance_bf = $loanTransDisbursed->debit;
        $loan_topup->balance_new = $loanTransDisbursed->debit + $request->amount;
        $am = $loanTransDisbursed->debit + $request->amount;
        $loanTransDisbursed -> debit = $loanTransDisbursed->debit + $request->amount;
        $loanTransInterest-> debit = 0.4 * $am;
        //Create top up database model
        $loan_topup->save();
        $loanTransDisbursed -> save();
        $loanTransInterest -> save();
        return redirect('loan/' . $loan->id . '/show');
    }


    public function update_client_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'loan_officer_id' => 'required',
            'principal' => 'required',
            'loan_term' => 'required',
            'loan_term_type' => 'required',
            'repayment_frequency' => 'required',
            'repayment_frequency_type' => 'required|same:loan_term_type',
            'interest_rate' => 'required',
            'expected_disbursement_date' => 'required',
            'expected_first_repayment_date' => 'required|after_or_equal:expected_disbursement_date',
        );
        $messages = [
            'loan_officer_id.required' => 'Loan Officer is required',
            'principal.required' => 'Principal is required',
            'loan_term_type.required' => 'Loan term is required',
            'repayment_frequency.required' => 'repayment frequency is required',
            'repayment_frequency_type.required' => 'repayment frequency type is required',
            'interest_rate.required' => 'interest rate is required',
            'interest_rate_type.required' => 'interest rate type is required',
            'expected_disbursement_date.required' => 'Expected disbursement date is required',
            'expected_first_repayment_date.required' => 'Expected first repayment date is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $loan = Loan::find($id);
            $loan->created_by_id = Sentinel::getUser()->id;
            $loan->created_date = $request->created_date;
            $loan->fund_id = $request->fund_id;
            $loan->loan_officer_id = $request->loan_officer_id;
            $loan->loan_purpose_id = $request->loan_purpose_id;
            $loan->principal = $request->principal;
            $loan->applied_amount = $request->principal;
            $loan->loan_term = $request->loan_term;
            $loan->external_id = $request->external_id;
            $loan->loan_term_type = $request->loan_term_type;
            $loan->repayment_frequency = $request->repayment_frequency;
            $loan->repayment_frequency_type = $request->repayment_frequency_type;
            $loan->interest_rate = $request->interest_rate;
            $loan->override_interest = $request->override_interest;
            $loan->override_interest_rate = $request->override_interest_rate;
            $loan->expected_disbursement_date = $request->expected_disbursement_date;
            if (!empty($request->expected_first_repayment_date)) {
                $loan->expected_first_repayment_date = $request->expected_first_repayment_date;
            }
            $date = explode('-', $request->created_date);
            $loan->month = $date[1];
            $loan->year = $date[0];
            $loan->save();
            LoanCharge::where('loan_id', $loan->id)->delete();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $loan_charge = new LoanCharge();
                    $loan_charge->loan_id = $loan->id;
                    $loan_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $loan_charge->amount = $request->charge_amount[$key];
                    } else {
                        $loan_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $loan_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $loan_charge->charge_type = $charge->charge_type;
                    $loan_charge->charge_option = $charge->charge_option;
                    $loan_charge->save();
                }
            }
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'loans')->get();
                foreach ($custom_fields as $key) {
                    if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                        'loans')->first())
                    ) {
                        $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                            $id)->where('category', 'loans')->first();
                    } else {
                        $custom_field = new CustomFieldMeta();
                    }
                    $kid = "custom_field_" . $key->id;
                    if ($key->field_type == "checkbox") {
                        if (!empty($request->$kid)) {
                            $custom_field->name = serialize($request->$kid);
                        } else {
                            $custom_field->name = serialize([]);
                        }
                    } else {
                        $custom_field->name = $request->$kid;
                    }
                    $custom_field->parent_id = $id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "loans";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Update", "Loans", $loan->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    public function update_group_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!empty($request->client)) {
            $total = 0;
            foreach ($request->client as $key) {
                $total = $total + $key;
            }
            if ($request->principal != $total) {
                Flash::warning("Group allocation total must be equal to the principal");
                return redirect()->back()->withInput();
            }
        }
        $rules = array(
            'loan_officer_id' => 'required',
            'principal' => 'required',
            'loan_term' => 'required',
            'loan_term_type' => 'required',
            'repayment_frequency' => 'required',
            'repayment_frequency_type' => 'required|same:loan_term_type',
            'interest_rate' => 'required',
            'expected_disbursement_date' => 'required',
            'expected_first_repayment_date' => 'required|after_or_equal:expected_disbursement_date',
        );
        $messages = [
            'loan_officer_id.required' => 'Loan Officer is required',
            'principal.required' => 'Principal is required',
            'loan_term_type.required' => 'Loan term is required',
            'repayment_frequency.required' => 'repayment frequency is required',
            'repayment_frequency_type.required' => 'repayment frequency type is required',
            'interest_rate.required' => 'interest rate is required',
            'interest_rate_type.required' => 'interest rate type is required',
            'expected_disbursement_date.required' => 'Expected disbursement date is required',
            'expected_first_repayment_date.required' => 'Expected first repayment date is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $loan = Loan::find($id);
            $loan->created_by_id = Sentinel::getUser()->id;
            $loan->created_date = $request->created_date;
            $loan->fund_id = $request->fund_id;
            $loan->loan_officer_id = $request->loan_officer_id;
            $loan->loan_purpose_id = $request->loan_purpose_id;
            $loan->principal = $request->principal;
            $loan->applied_amount = $request->principal;
            $loan->external_id = $request->external_id;
            $loan->loan_term = $request->loan_term;
            $loan->loan_term_type = $request->loan_term_type;
            $loan->repayment_frequency = $request->repayment_frequency;
            $loan->repayment_frequency_type = $request->repayment_frequency_type;
            $loan->interest_rate = $request->interest_rate;
            $loan->override_interest = $request->override_interest;
            $loan->override_interest_rate = $request->override_interest_rate;
            $loan->expected_disbursement_date = $request->expected_disbursement_date;
            if (!empty($request->expected_first_repayment_date)) {
                $loan->expected_first_repayment_date = $request->expected_first_repayment_date;
            }
            $date = explode('-', $request->created_date);
            $loan->month = $date[1];
            $loan->year = $date[0];
            $loan->save();
            //save loan allocation
            GroupLoanAllocation::where('loan_id', $loan->id)->delete();
            foreach ($request->client as $key => $value) {
                $group_loan_allocation = new GroupLoanAllocation();
                $group_loan_allocation->loan_id = $loan->id;
                $group_loan_allocation->group_id = $loan->group_id;
                $group_loan_allocation->client_id = $key;
                $group_loan_allocation->amount = $value;
                $group_loan_allocation->save();
            }
            LoanCharge::where('loan_id', $loan->id)->delete();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $loan_charge = new LoanCharge();
                    $loan_charge->loan_id = $loan->id;
                    $loan_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $loan_charge->amount = $request->charge_amount[$key];
                    } else {
                        $loan_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $loan_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $loan_charge->charge_type = $charge->charge_type;
                    $loan_charge->charge_option = $charge->charge_option;
                    $loan_charge->save();
                }
            }
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'loans')->get();
                foreach ($custom_fields as $key) {
                    if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                        'loans')->first())
                    ) {
                        $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                            $id)->where('category', 'loans')->first();
                    } else {
                        $custom_field = new CustomFieldMeta();
                    }
                    $kid = "custom_field_" . $key->id;
                    if ($key->field_type == "checkbox") {
                        if (!empty($request->$kid)) {
                            $custom_field->name = serialize($request->$kid);
                        } else {
                            $custom_field->name = serialize([]);
                        }
                    } else {
                        $custom_field->name = $request->$kid;
                    }
                    $custom_field->parent_id = $id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "loans";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Update", "Loans", $loan->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Loan::destroy($id);
        LoanCharge::where('loan_id', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/product/data');
    }

    //client documents
    public function store_loan_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.documents.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required',
            'attachment' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan_document = new Document();
            $loan_document->record_id = $id;
            $loan_document->type = "loan";
            $loan_document->name = $request->name;
            $loan_document->notes = $request->notes;
            if ($request->hasFile('attachment')) {
                $file = array('attachment' => $request->file('attachment'));
                $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,doc,xlsx,pptx,xls');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = str_slug($request->name, '_') . "" . uniqid() . '.' . $request->file('attachment')->guessExtension();
                    $loan_document->location = $fname;
                    $request->file('attachment')->move(public_path() . '/uploads',
                        $fname);
                }

            }
            $loan_document->save();
            GeneralHelper::audit_trail("Create Document", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_loan_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.documents.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan_document = Document::find($id);
        if (!empty($loan_document->location)) {
            @unlink(public_path() . '/uploads/' . $loan_document->location);
        }
        Document::destroy($id);
        GeneralHelper::audit_trail("Delete Document", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    //client notes
    public function store_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.notes.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $note = new Note();
            $note->reference_id = $id;
            $note->created_by_id = Sentinel::getUser()->id;
            $note->type = "loan";
            $note->notes = $request->notes;
            $note->save();
            GeneralHelper::audit_trail("Create Note", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.notes.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        Note::destroy($id);
        GeneralHelper::audit_trail("Delete Note", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_note($note)
    {
        if (!Sentinel::hasAccess('loans.notes.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.show_note', compact('note'))->render();

    }

    public function edit_note($note)
    {
        if (!Sentinel::hasAccess('loans.notes.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.edit_note', compact('note'))->render();

    }

    public function update_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.notes.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $note = Note::find($id);
            $note->notes = $request->notes;

            $note->save();
            GeneralHelper::audit_trail("Update Note", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    //loan collateral
    public function store_collateral(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.collateral.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'collateral_type_id' => 'required',
            'description' => 'required',
            'serial' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $collateral = new Collateral();
            $collateral->loan_id = $id;
            //$collateral->created_by_id = Sentinel::getUser()->id;
            $collateral->collateral_type_id = $request->collateral_type_id;
            $collateral->description = $request->description;
            $collateral->value = $request->value;
            $collateral->serial = $request->serial;
            $collateral->save();
            GeneralHelper::audit_trail("Create Collateral", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_collateral(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.collateral.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        Collateral::destroy($id);
        GeneralHelper::audit_trail("Delete Collateral", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_collateral($collateral)
    {
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.show_collateral', compact('collateral'))->render();

    }

    public function edit_collateral($collateral)
    {
        if (!Sentinel::hasAccess('loans.collateral.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.edit_collateral', compact('collateral'))->render();

    }

    public function update_collateral(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.collateral.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'collateral_type_id' => 'required',
            'description' => 'required',
            'serial' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $collateral = Collateral::find($id);
            $collateral->collateral_type_id = $request->collateral_type_id;
            $collateral->description = $request->description;
            $collateral->value = $request->value;
            $collateral->serial = $request->serial;
            $collateral->save();
            GeneralHelper::audit_trail("Update Collateral", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    //loan collateral
    public function store_guarantor(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.guarantors.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'is_client' => 'required',
            'amount' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $guarantor = new Guarantor();
            $guarantor->loan_id = $id;
            //$collateral->created_by_id = Sentinel::getUser()->id;
            $guarantor->client_relationship_id = $request->client_relationship_id;
            $guarantor->is_client = $request->is_client;
            if ($request->client_id == 1) {
                $guarantor->client_id = $request->client_id;
                $guarantor->lock_funds = $request->lock_funds;
                $client = Client::find($request->client_id);
                $savings = Savings::where('client_id', $client->id)->first();
                if ($request->lock_funds == 1 && !empty($savings)) {
                    if (GeneralHelper::savings_account_balance($savings->id) < $request->amount) {
                        Flash::warning("Savings balance low");
                        return redirect()->back();
                    }
                    $savings_transaction = new SavingsTransaction();
                    $savings_transaction->created_by_id = Sentinel::getUser()->id;
                    $savings_transaction->office_id = $client->office_id;
                    $savings_transaction->savings_id = $savings->id;
                    $savings_transaction->transaction_type = "guarantee";
                    $savings_transaction->reversible = 1;
                    $savings_transaction->date = date("Y-m-d");
                    $savings_transaction->time = date("H:i");
                    $date = explode('-', date("Y-m-d"));
                    $savings_transaction->year = $date[0];
                    $savings_transaction->month = $date[1];
                    $savings_transaction->debit = $request->amount;
                    $savings_transaction->save();
                    $guarantor->savings_id = $savings->id;
                }
            } else {
                $guarantor->first_name = $request->first_name;
                $guarantor->middle_name = $request->middle_name;
                $guarantor->last_name = $request->last_name;
                $guarantor->mobile = $request->mobile;
            }

            $guarantor->amount = $request->amount;
            $guarantor->save();
            GeneralHelper::audit_trail("Create Guarantor", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_guarantor(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.guarantors.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $guarantor = Guarantor::find($id);
        Guarantor::destroy($id);
        foreach (SavingsTransaction::where('savings_id', $guarantor->savings_id)->where('transaction_type', 'guarantee')->get() as $key) {
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->created_by_id = Sentinel::getUser()->id;
            $savings_transaction->office_id = $key->office_id;
            $savings_transaction->savings_id = $key->savings_id;
            $savings_transaction->transaction_type = "guarantee_restored";
            $savings_transaction->reversible = 1;
            $savings_transaction->date = date("Y-m-d");
            $savings_transaction->time = date("H:i");
            $date = explode('-', date("Y-m-d"));
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->credit = $key->amount;
            $savings_transaction->save();

        }
        GeneralHelper::audit_trail("Delete Guarantor", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_guarantor($guarantor)
    {
        if (!Sentinel::hasAccess('loans.guarantors.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.show_guarantor', compact('guarantor'))->render();

    }

    public function edit_guarantor($guarantor)
    {
        if (!Sentinel::hasAccess('loans.guarantors.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.edit_guarantor', compact('guarantor'))->render();

    }

    public function update_guarantor(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.guarantors.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'amount' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $guarantor = Guarantor::find($id);
            $guarantor->amount = $request->amount;
            $guarantor->save();
            GeneralHelper::audit_trail("Update Guarantor", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function approve_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'approved_amount' => 'required',
            'approved_date' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            if ($loan->status != "pending") {
                Flash::warning("Loan not pending");
                return redirect()->back();
            }
            $loan->status = "approved";
            $loan->approved_by_id = Sentinel::getUser()->id;
            $loan->approved_amount = $request->approved_amount;
            $loan->principal = $request->approved_amount;
            $loan->approved_date = $request->approved_date;
            $loan->approved_notes = $request->approved_notes;
            $loan->save();
            event(new LoanApproved($loan));
            GeneralHelper::audit_trail("Approve", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function decline_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'declined_notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            if ($loan->status != "pending") {
                Flash::warning("Loan not pending");
                return redirect()->back();
            }
            $loan->status = "declined";
            $loan->declined_by_id = Sentinel::getUser()->id;
            $loan->declined_date = date("Y-m-d");
            $loan->declined_notes = $request->declined_notes;
            $loan->save();
            GeneralHelper::audit_trail("Decline", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function change_loan_officer(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'loan_officer_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            $loan->loan_officer_id = $request->loan_officer_id;
            $loan->save();
            GeneralHelper::audit_trail("Update", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function unapprove_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.undo_approval')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $loan = Loan::find($id);
        if ($loan->status != "approved") {
            Flash::warning("Loan not approved");
            return redirect()->back();
        }
        $loan->status = "pending";
        $loan->approved_by_id = null;
        $loan->approved_amount = null;
        $loan->approved_date = null;
        $loan->approved_notes = null;
        $loan->save();
        GeneralHelper::audit_trail("Unapprove", "Loans", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function disburse_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.disburse')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'disbursement_date' => 'required',
            'payment_type_id' => 'required',
            'first_repayment_date' => 'required|after_or_equal:disbursement_date',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            if ($loan->status != "approved") {
                Flash::warning("Loan not approved");
                return redirect()->back();
            }
            $loan->status = "disbursed";
            $loan->disbursed_by_id = Sentinel::getUser()->id;
            $loan->disbursed_notes = $request->disbursed_notes;
            $loan->disbursement_date = $request->disbursement_date;
            $loan->first_repayment_date = $request->first_repayment_date;
            $loan->expected_maturity_date = date_format(date_add(date_create($loan->first_repayment_date),
                date_interval_create_from_date_string($loan->loan_term . ' ' . $loan->loan_term_type)),
                'Y-m-d');
            $loan->save();

            //save repayment schedule
            $interest_rate = GeneralHelper::determine_interest_rate($loan->id);
            $period = $loan->loan_term / $loan->repayment_frequency;
            $next_payment = $loan->first_repayment_date;
            $balance = $loan->principal;
            $decimals = $loan->loan_product->decimals;
            $rounded_interest = 0;
            $loan_product = $loan->loan_product;
            $grace_on_principal = $loan_product->grace_on_principal;
            $grace_on_interest_charged = $loan_product->grace_on_interest_charged;
            $grace_on_interest_payment = $loan_product->grace_on_interest_payment;
            for ($i = 0; $i < $period; $i++) {
                $loan_repayment_schedule = new LoanRepaymentSchedule();
                $loan_repayment_schedule->loan_id = $loan->id;
                $loan_repayment_schedule->due_date = $next_payment;
                $date = explode('-', $next_payment);
                $loan_repayment_schedule->month = $date[1];
                $loan_repayment_schedule->year = $date[0];
                //determine which method to use
                if ($loan->interest_method == "declining_balance") {
                    if ($loan->armotization_method == "equal_installment") {
                        if ($loan_product->grace_on_principal > 0) {
                            $due = round(GeneralHelper::amortized_payment($loan->id, $loan->principal, $period - $loan_product->grace_on_principal), $decimals);
                        } else {
                            $due = round(GeneralHelper::amortized_payment($loan->id, $loan->principal, $period), $decimals);
                        }
                        $interest = ($interest_rate * $balance);


                        //determine next balance
                        if ($i == $period - 1) {
                            //last record, balance rounded figures
                            $principal_due = $balance;
                            $interest = round($interest + $rounded_interest);
                        } else {
                            if ($grace_on_principal > 0) {
                                $grace_on_principal--;
                                $principal_due = 0;
                            } else {
                                $principal_due = $due - round($interest, $decimals);
                            }
                            if ($grace_on_interest_payment > 0) {
                                $interest = 0;
                                $grace_on_interest_payment--;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                            if ($grace_on_interest_charged > 0) {
                                $interest = 0;
                                $grace_on_interest_charged--;
                                $principal_due = $due;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                        }
                        $loan_repayment_schedule->principal = $principal_due;
                        $loan_repayment_schedule->interest = $interest;
                        $balance = ($balance - $principal_due);
                        $rounded_interest = $rounded_interest + ($interest - round($interest, $decimals));
                    }
                    if ($loan->armotization_method == "equal_principal") {
                        $interest = ($interest_rate * $balance);
                        if ($loan_product->grace_on_principal > 0) {
                            $principal_due = round($loan->principal / ($period - $loan_product->grace_on_principal), $decimals);
                        } else {
                            $principal_due = round($loan->principal / $period, $decimals);
                        }
                        //determine next balance
                        if ($i == $period - 1) {
                            //last record, balance rounded figures
                            $principal_due = $balance;
                            $interest = round($interest + $rounded_interest);
                        } else {
                            if ($grace_on_principal > 0) {
                                $grace_on_principal--;
                                $principal_due = 0;
                            } else {

                            }
                            if ($grace_on_interest_payment > 0) {
                                $interest = 0;
                                $grace_on_interest_payment--;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                            if ($grace_on_interest_charged > 0) {
                                $interest = 0;
                                $grace_on_interest_charged--;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                        }
                        $loan_repayment_schedule->principal = $principal_due;
                        $loan_repayment_schedule->interest = $interest;
                        $balance = ($balance - $principal_due);
                        $rounded_interest = $rounded_interest + ($interest - round($interest, $decimals));
                    }
                }
                if ($loan->interest_method == "flat") {
                    $interest = ($interest_rate * $loan->principal);

                    if ($loan_product->grace_on_principal > 0) {
                        $principal_due = round($loan->principal / ($period - $loan_product->grace_on_principal), $decimals);
                    } else {
                        $principal_due = round($loan->principal / $period, $decimals);
                    }
                    //determine next balance

                    if ($i == $period - 1) {
                        //last record, balance rounded figures
                        $principal_due = $balance;
                        $interest = round($interest + $rounded_interest);
                    } else {
                        if ($grace_on_principal > 0) {
                            $grace_on_principal--;
                            $principal_due = 0;
                        } else {

                        }
                        if ($grace_on_interest_payment > 0) {
                            $interest = 0;
                            $grace_on_interest_payment--;
                        } else {
                            $interest = round($interest, $decimals);
                        }
                        if ($grace_on_interest_charged > 0) {
                            $interest = 0;
                            $grace_on_interest_charged--;
                        } else {
                            $interest = round($interest, $decimals);
                        }
                        $loan_repayment_schedule->principal = $principal_due;
                        $loan_repayment_schedule->interest = round($interest, $decimals);
                    }
                    $loan_repayment_schedule->principal = $principal_due;
                    $loan_repayment_schedule->interest = $interest;
                    $rounded_interest = $rounded_interest + ($interest - round($interest, $decimals));
                    $balance = ($balance - $principal_due);
                }

                $loan_repayment_schedule->save();
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string($loan->repayment_frequency . ' ' . $loan->repayment_frequency_type)),
                    'Y-m-d');
            }
            $loan->expected_maturity_date = $next_payment;
            $loan->save();
            $total_interest = LoanRepaymentSchedule::where('loan_id', $loan->id)->sum('interest');
            $payment_detail = new PaymentDetail();
            $payment_detail->payment_type_id = $request->payment_type_id;
            $payment_detail->account_number = $request->account_number;
            $payment_detail->cheque_number = $request->cheque_number;
            $payment_detail->routing_code = $request->routing_code;
            $payment_detail->receipt_number = $request->receipt_number;
            $payment_detail->bank = $request->bank;
            $payment_detail->save();
            //loan disbursement transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->transaction_type = "disbursement";
            $loan_transaction->date = $request->disbursement_date;
            $date = explode('-', $request->disbursement_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $loan->principal;
            $loan_transaction->save();
            //add interest transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->transaction_type = "interest_initial";
            $loan_transaction->date = $request->disbursement_date;
            $date = explode('-', $request->disbursement_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $total_interest;
            $loan_transaction->save();
            //check for  fees
            $fees_disbursement = 0;
            $fees_installment = 0;
            $fees_due_date = [];
            $fees_due_date_amount = 0;
            foreach ($loan->charges as $key) {
                if (!empty($key->charge)) {
                    if ($key->charge->charge_type == "disbursement") {
                        if ($key->charge->charge_option == "flat") {
                            $fees_disbursement = $fees_disbursement + $key->amount;
                        } else {
                            if ($key->charge->charge_option == "installment_principal_due") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * $loan->principal) / 100;
                            }
                            if ($key->charge->charge_option == "installment_principal_interest_due") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            }
                            if ($key->charge->charge_option == "installment_interest_due") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * $total_interest) / 100;
                            }
                            if ($key->charge->charge_option == "original_principal") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * $loan->principal) / 100;
                            }
                            if ($key->charge->charge_option == "total_due") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            }
                        }
                    }
                    if ($key->charge->charge_type == "installment_fee") {
                        if ($key->charge->charge_option == "flat") {
                            $fees_installment = $fees_installment + $key->amount;
                        } else {
                            if ($key->charge->charge_option == "installment_principal_due") {
                                $fees_installment = $fees_installment + ($key->amount * $loan->principal) / 100;
                            }
                            if ($key->charge->charge_option == "installment_principal_interest_due") {
                                $fees_installment = $fees_installment + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            }
                            if ($key->charge->charge_option == "installment_interest_due") {
                                $fees_installment = $fees_installment + ($key->amount * $total_interest) / 100;
                            }
                            if ($key->charge->charge_option == "original_principal") {
                                $fees_installment = $fees_installment + ($key->amount * $loan->principal) / 100;
                            }
                            if ($key->charge->charge_option == "total_due") {
                                $fees_installment = $fees_installment + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            }
                        }
                    }
                    if ($key->charge->charge_type == "specified_due_date") {
                        if ($key->charge->charge_option == "flat") {
                            $fees_due_date_amount = $fees_due_date_amount + $key->amount;
                            $fees_due_date[$key->id] = $key->charge->id;
                        } else {
                            if ($key->charge->charge_option == "installment_principal_due") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $loan->principal) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                            if ($key->charge->charge_option == "installment_principal_interest_due") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * ($loan->principal + $total_interest)) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                            if ($key->charge->charge_option == "installment_interest_due") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $total_interest) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                            if ($key->charge->charge_option == "original_principal") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $loan->principal) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                            if ($key->charge->charge_option == "total_due") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * ($loan->principal + $total_interest)) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                        }
                    }
                }
            }
            if ($fees_disbursement > 0) {
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->transaction_type = "disbursement_fee";
                $loan_transaction->date = $request->disbursement_date;
                $date = explode('-', $request->disbursement_date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->debit = $fees_disbursement;
                $loan_transaction->save();

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->transaction_type = "repayment_disbursement";
                $loan_transaction->date = $request->disbursement_date;
                $date = explode('-', $request->disbursement_date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->credit = $fees_disbursement;
                $loan_transaction->save();
                if ($loan->loan_product->accounting_rule != "none") {
                    //add journal entry for payment and charge
                    if (!empty($loan->loan_product->gl_account_income_fee)) {
                        $journal = new GlJournalEntry();
                        $journal->gl_account_id = $loan->loan_product->gl_account_income_fee->id;
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->date = $request->disbursement_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->loan_transaction_id = $loan_transaction->id;
                        $journal->transaction_type = 'fee';
                        $journal->name = "Fee Income";
                        $journal->loan_id = $loan->id;
                        $journal->credit = $fees_disbursement;
                        $journal->reference = $loan_transaction->name;
                        $journal->save();
                    }
                    if (!empty($loan->loan_product->gl_account_fund_source)) {
                        $journal = new GlJournalEntry();
                        $journal->gl_account_id = $request->fund;
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->date = $request->disbursement_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->loan_transaction_id = $loan_transaction->id;
                        $journal->transaction_type = 'fee';
                        $journal->name = "Fee Income";
                        $journal->loan_id = $loan->id;
                        $journal->debit = $fees_disbursement;
                        $journal->reference = $loan_transaction->id;
                        $journal->save();
                    }
                }
            }
            if ($fees_installment > 0) {
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->transaction_type = "installment_fee";
                $loan_transaction->reversible = 1;
                $loan_transaction->date = $request->disbursement_date;
                $date = explode('-', $request->disbursement_date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->debit = $fees_installment;
                $loan_transaction->save();
                //add installment to schedules
                foreach (LoanRepaymentSchedule::where('loan_id', $loan->id)->get() as $key) {
                    $schedule = LoanRepaymentSchedule::find($key->id);
                    $schedule->fees = $fees_installment;
                    $schedule->save();
                }
            }
            if ($fees_due_date_amount > 0) {
                foreach ($fees_due_date as $key => $value) {
                    $charge = Charge::find($value);
                    $loan_charge = LoanCharge::find($key);
                    $amount = 0;
                    if ($charge->charge_option == "flat") {
                        $amount = $loan_charge->amount;
                    } else {
                        if ($charge->charge_option == "installment_principal_due") {
                            $amount = ($loan_charge->amount * $loan->principal) / 100;
                        }
                        if ($charge->charge_option == "installment_principal_interest_due") {
                            $amount = ($loan_charge->amount * ($loan->principal + $total_interest)) / 100;
                        }
                        if ($charge->charge_option == "installment_interest_due") {
                            $amount = ($loan_charge->amount * $total_interest) / 100;
                        }
                        if ($charge->charge_option == "original_principal") {
                            $amount = ($loan_charge->amount * $loan->principal) / 100;
                        }
                        if ($charge->charge_option == "total_due") {
                            $amount = ($loan_charge->amount * ($loan->principal + $total_interest)) / 100;
                        }
                    }
                    $due_date = GeneralHelper::determine_due_date($loan->id, $loan_charge->due_date);
                    if (!empty($due_date)) {
                        $schedule = LoanRepaymentSchedule::where('loan_id', $loan->id)->where('due_date', $due_date)->first();
                        $schedule->fees = $schedule->fees + $amount;
                        $schedule->save();
                        $loan_transaction = new LoanTransaction();
                        $loan_transaction->created_by_id = Sentinel::getUser()->id;
                        $loan_transaction->office_id = $loan->office_id;
                        $loan_transaction->loan_id = $loan->id;
                        $loan_transaction->loan_repayment_schedule_id = $schedule->id;
                        $loan_transaction->reversible = 1;
                        $loan_transaction->transaction_type = "specified_due_date_fee";
                        $loan_transaction->date = $due_date;
                        $date = explode('-', $due_date);
                        $loan_transaction->year = $date[0];
                        $loan_transaction->month = $date[1];
                        $loan_transaction->debit = $amount;
                        $loan_transaction->save();
                    }
                }

            }
            if ($loan->loan_product->accounting_rule != "none") {
                if (!empty($loan->loan_product->gl_account_fund_source)) {
                    $journal = new GlJournalEntry();
                    $journal->created_by_id = Sentinel::getUser()->id;
                    $journal->office_id = $loan->office_id;
                    $journal->currency_id = $loan->currency_id;
                    $journal->gl_account_id = $loan->fund_id;
                    $journal->date = $request->disbursement_date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->transaction_type = 'disbursement';
                    $journal->name = "Loan Disbursement";
                    $journal->loan_id = $loan->id;
                    $journal->credit = $loan->principal;
                    $journal->reference = $loan->name;
                    $journal->save();
                }
                if (!empty($loan->loan_product->gl_account_loan_portfolio)) {
                    $journal = new GlJournalEntry();
                    $journal->created_by_id = Sentinel::getUser()->id;
                    $journal->office_id = $loan->office_id;
                    $journal->currency_id = $loan->currency_id;
                    $journal->gl_account_id = $loan->loan_product->gl_account_loan_portfolio->id;
                    $journal->date = $request->disbursement_date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->transaction_type = 'disbursement';
                    $journal->name = "Loan Disbursement";
                    $journal->loan_id = $loan->id;
                    $journal->debit = $loan->principal;
                    $journal->reference = $loan->name;
                    $journal->save();
                }
                if ($loan->loan_product->accounting_rule != "accrual_upfront") {
                    if (!empty($loan->loan_product->gl_account_receivable_interest)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_receivable_interest->id;
                        $journal->date = $request->disbursement_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'accrual';
                        $journal->name = "Accrued Interest";
                        $journal->loan_id = $loan->id;
                        $journal->debit = $total_interest;
                        $journal->reference = $loan->name;
                        $journal->save();
                    }
                    if (!empty($loan->loan_product->gl_account_income_interest)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_income_interest->id;
                        $journal->date = $request->disbursement_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'accrual';
                        $journal->name = "Accrued Interest";
                        $journal->loan_id = $loan->id;
                        $journal->credit = $total_interest;
                        $journal->reference = $loan->name;
                        $journal->save();
                    }
                }
            }
            event(new LoanDisbursed($loan));
            GeneralHelper::audit_trail("Disburse", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function undisburse_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.undo_disbursement')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $loan = Loan::find($id);
        if ($loan->status != "disbursed") {
            Flash::warning("Loan not disbursed");
            return redirect()->back();
        }
        $loan->status = "approved";
        $loan->disbursed_by_id = null;
        $loan->disbursed_notes = null;
        $loan->disbursement_date = null;
        $loan->first_repayment_date = null;
        $loan->save();
        LoanRepaymentSchedule::where('loan_id', $loan->id)->delete();
        LoanTransaction::where('loan_id', $loan->id)->delete();
        GlJournalEntry::where('loan_id', $loan->id)->delete();
        GeneralHelper::audit_trail("Undo Disburse", "Loans", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //repayments
    public function create_repayment($loan)
    {
        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.repayment.create', compact('loan'));
    }




    public function transaction_fp_pp(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $rules = array(
            'date' => 'required|before_or_equal:' . date("Y-m-d"),
            'payment_type_id' => 'required',
            'amount' => 'required',
        );
        $messages = [
            'date.required' => 'Date is required',
            'payment_type_id.required' => 'Payment type is required',
            'amount.required' => 'Amount is required',
            'date.before_or_equal' => 'Date must not be a future date',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator);
        }else{
            $loan = Loan::find($id);
            $loan->loan_product->gl_account_fund_source = $request->gl_account_fund_source_id;

            $loan_transaction = new LoanTransactionUnapproved();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->reversible = 1;
            $loan_transaction->payment_apply_to = $request->payment_apply_to;
            $loan_transaction->payment_detail_id = null;
            $loan_transaction->payment_detail_id = null;
            $loan_transaction->transaction_type = "repayment";
            $loan_transaction->date = $request->date;
            $date = explode('-', $request->date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $request->amount;
            $loan_transaction->notes = $request->notes;
            $loan_transaction->payment_type_id = $request->payment_type_id;
            $loan_transaction->account_number = $request->account_number;
            $loan_transaction->cheque_number = $request->cheque_number;
            $loan_transaction->routing_code = $request->routing_code;
            $loan_transaction->receipt_number = $request->receipt_number;
            $loan_transaction->bank = $request->bank;
            $loan_transaction->notes_pd = $request->notes;
           // $loan_transaction->request_id = $request->$id;
            $loan_transaction->save();

            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');

        }
    }



    public function store_repayment(Request $request, $id, $trans_id)
    {
        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
            $loan = Loan::find($id);
            $Trans =  LoanTransactionUnapproved::find($trans_id);
            $loan->loan_product->gl_account_fund_source = $request->gl_account_fund_source_id;

            $payment_detail = new PaymentDetail();
            $payment_detail->payment_type_id = $Trans->payment_type_id_pd;
            $payment_detail->account_number = $Trans->account_number;
            $payment_detail->cheque_number = $Trans->cheque_number;
            $payment_detail->routing_code = $Trans->routing_code;
            $payment_detail->receipt_number = $Trans->receipt_number;
            $payment_detail->bank = $Trans->bank;
            $payment_detail->notes = $Trans->notes_pd;
            $payment_detail->save();


            //repayment  transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->reversible = 1;
            $loan_transaction->payment_apply_to = $Trans->payment_apply_to;
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->transaction_type = "repayment";
            $loan_transaction->date = $Trans->date;
            $date = explode('-', $Trans->date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $Trans->credit;
            $loan_transaction->notes = $Trans->notes;
            $loan_transaction->save();
            //check custom fields
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'repayments')->get();
                foreach ($custom_fields as $key) {
                    $custom_field = new CustomFieldMeta();
                    $id = "custom_field_" . $key->id;
                    if ($key->field_type == "checkbox") {
                        if (!empty($request->$id)) {
                            $custom_field->name = serialize($request->$id);
                        } else {
                            $custom_field->name = serialize([]);
                        }
                    } else {
                        $custom_field->name = $request->$id;
                    }
                    $custom_field->parent_id = $loan_transaction->id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "repayments";
                    $custom_field->save();
                }
            }
            event(new RepaymentCreated($loan_transaction));
            if (GeneralHelper::loan_total_balance($loan->id) <= 0) {
                $loan = Loan::find($loan->id);
                $loan->status = "closed";
                $loan->save();
            }


            GeneralHelper::audit_trail("Create Repayment", "Loans", $id);

            LoanTransactionUnapproved::where('id', $trans_id)->delete();

            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        
    }

    public function edit_repayment($loan_transaction)
    {
        if (!Sentinel::hasAccess('loans.transactions.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.repayment.edit', compact('loan_transaction'));
    }

    public function update_repayment(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'date' => 'required|before_or_equal:' . date("Y-m-d"),
          //  'payment_type_id' => 'required',
            'amount' => 'required',
        );
        $messages = [
            'date.required' => 'Date is required',
          //  'payment_type_id.required' => 'Payment type is required',
            'amount.required' => 'Amount is required',
            'date.before_or_equal' => 'Date must not be a future date',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        $loanT = LoanTransaction::find($id);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
        //   //  reverse transaction
            $loan_transaction = LoanTransaction::find($id);
            $loan_transaction->reversible = 0;
            $loan_transaction->reversed = 1;
            $loan_transaction->reversal_type = "user";
            $loan_transaction->debit = $loan_transaction->credit;
            $loan_transaction->save();
            //reverse journal entries
            foreach (GlJournalEntry::where('loan_transaction_id', $id)->where('loan_id',
                $loan_transaction->loan_id)->where('transaction_type', 'repayment')->get() as $key) {
               $journal = GlJournalEntry::find($key->id);
                if ($key->debit > $key->credit) {
                    $journal->credit = $journal->debit;
                } else {
                    $journal->debit = $journal->credit;
                }
               $journal->reversed = 1;
               $journal->save();
            }
           $loan = $loan_transaction->loan;

            $payment_detail = new PaymentDetail();
            $payment_detail->payment_type_id = $request->payment_type_id;
            $payment_detail->account_number = $request->account_number;
            $payment_detail->cheque_number = $request->cheque_number;
            $payment_detail->routing_code = $request->routing_code;
            $payment_detail->receipt_number = $request->receipt_number;
            $payment_detail->bank = $request->bank;
            $payment_detail->notes = $request->notes;
            $payment_detail->save();

            //repayment  transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->reversible = 1;
            $loan_transaction->payment_apply_to = $loanT->payment_apply_to;
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->transaction_type = $loanT->transaction_type;//"repayment";
            $loan_transaction->date = $request->date;
            $date = explode('-', $request->date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            if($loan_transaction->payment_apply_to == 'full_payment'){
                $loan_transaction->debit = $request->amount;
            }else{
                $loan_transaction->credit = $request->amount;
            }
            $loan_transaction->notes = $request->notes;
            $loan_transaction->save();
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'repayments')->get();
                foreach ($custom_fields as $key) {
                    $custom_field = new CustomFieldMeta();
                    $id = "custom_field_" . $key->id;
                    if ($key->field_type == "checkbox") {
                        if (!empty($request->$id)) {
                            $custom_field->name = serialize($request->$id);
                        } else {
                            $custom_field->name = serialize([]);
                        }
                    } else {
                        $custom_field->name = $request->$id;
                    }
                    $custom_field->parent_id = $loan_transaction->id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "repayments";
                    $custom_field->save();
                }
            }
            event(new RepaymentUpdated($loan_transaction));
            GeneralHelper::audit_trail("Update Repayment", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    public function reverse_repayment(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //reverse transaction
        $loan_transaction = LoanTransaction::find($id);
        $loan_transaction->reversible = 0;
        $loan_transaction->reversed = 1;
        $loan_transaction->reversal_type = "user";
        $loan_transaction->debit = $loan_transaction->credit;
        $loan_transaction->save();
        //reverse journal entries
        foreach (GlJournalEntry::where('loan_transaction_id', $id)->where('loan_id',
            $loan_transaction->loan_id)->where('transaction_type', 'repayment')->get() as $key) {
            $journal = GlJournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        $loan = $loan_transaction->loan;

        event(new RepaymentUpdated($loan_transaction));
        if (GeneralHelper::loan_total_balance($loan->id) <= 0) {
            $loan = Loan::find($loan->id);
            $loan->status = "closed";
            $loan->save();
        }
        GeneralHelper::audit_trail("Reverse Repayment", "Loans", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //transaction
    public function show_transaction($loan_transaction)
    {
        if (!Sentinel::hasAccess('loans.transactions.view')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.transaction.show', compact('loan_transaction'));
    }

    public function print_transaction($loan_transaction)
    {
        if (!Sentinel::hasAccess('loans.transactions.view')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.transaction.print', compact('loan_transaction'));
    }

    public function pdf_transaction($loan_transaction)
    {
        // if (!Sentinel::hasAccess('loans.transactions.view')) {
        //     Flash::warning(trans('general.permission_denied'));
        //     return redirect()->back();
        // }
        $current_balance = 0;
        $out = 0;
        $in = 0;
        $Loan = Loan::with('transactions')->where('id',$loan_transaction->loan_id)->first();
        foreach($Loan->transactions as $transaction){
            $out = $out + $transaction->debit;
            $in = $in + $transaction->credit;
        }
        $current_balance = $out - $in;
        $due_date = $Loan->first_repayment_date;
        ////////////////////////////////////////////
        $pdf = PDF::loadView('loan.transaction.pdf', compact('loan_transaction','due_date','current_balance'));
        return $pdf->download(trans_choice('general.loan', 1) . ' ' . trans_choice('general.transaction', 1) . ' ' . trans_choice('general.receipt', 1) . ".pdf");

    }

    public function withdraw_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'withdrawn_notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            if ($loan->status != "pending") {
                Flash::warning("Loan not pending");
                return redirect()->back();
            }
            $loan->status = "withdrawn";
            $loan->withdrawn_by_id = Sentinel::getUser()->id;
            $loan->withdrawn_date = date("Y-m-d");
            $loan->withdrawn_notes = $request->withdrawn_notes;
            $loan->save();
            GeneralHelper::audit_trail("Withdraw", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function pdf_schedule($loan)
    {
        if (!Sentinel::hasAccess('loans.pdf_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $pdf = PDF::loadView('loan.pdf_schedule', compact('loan'));
        return $pdf->download(trans_choice('general.loan', 1) . ' ' . trans_choice('general.schedule', 1) . ".pdf");

    }

    public function print_schedule($loan)
    {
        if (!Sentinel::hasAccess('loans.pdf_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.print_schedule', compact('loan'));
    }

    public function statement($loan)
    {
        if (!Sentinel::hasAccess('loans.pdf_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.statement', compact('loan'));
    }


    public function email_schedule($loan)
    {
        if (!Sentinel::hasAccess('loans.email_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $email = "";
        if ($loan->client_type == "client") {
            $email = $loan->client->email;
        }
        if ($loan->client_type == "group") {
            $email = $loan->group->email;
        }
        if (!empty($email)) {
            Mail::to($email)->send(new RepaymentScheduleEmail($loan));
        } else {
            Flash::warning("Client has no email");
        }

        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //waive charge
    public function waive_transaction(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //reverse transaction
        $loan_transaction = LoanTransaction::find($id);
        $loan_transaction->reversible = 0;
        $loan_transaction->reversed = 1;
        $loan_transaction->reversal_type = "user";
        $loan_transaction->credit = $loan_transaction->debit;
        $loan_transaction->save();
        //reverse journal entries
        foreach (GlJournalEntry::where('loan_transaction_id', $id)->where('loan_id',
            $loan_transaction->loan_id)->get() as $key) {
            $journal = GlJournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        $loan = $loan_transaction->loan;
        if ($loan_transaction->transaction_type == "installment_fee") {
            $amount = $loan_transaction->debit / LoanRepaymentSchedule::where('loan_id', $loan_transaction->loan_id)->count();
            foreach (LoanRepaymentSchedule::where('loan_id', $loan_transaction->loan_id)->get() as $key) {
                $schedule = LoanRepaymentSchedule::find($key->id);
                $schedule->fees = $schedule->fees - $amount;
                $schedule->save();
            }
            event(new TransactionUpdated($loan_transaction));
        }
        if ($loan_transaction->transaction_type == "specified_due_date_fee") {
            $schedule = LoanRepaymentSchedule::where("due_date", $loan_transaction->date)->where("loan_id", $loan->id)->first();
            $schedule->fees = $schedule->fees - $loan_transaction->debit;
            $schedule->save();
            event(new TransactionUpdated($loan_transaction));
        }

        if (GeneralHelper::loan_total_balance($loan->id) >= 0) {
            $loan = Loan::find($loan->id);
            $loan->status = "disbursed";
            $loan->save();
        }
        GeneralHelper::audit_trail("Waive Transaction", "Loans", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function waive_interest(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.waive_interest')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan = Loan::find($id);
        if ($request->date > date("Y-m-d")) {
            Flash::warning(trans_choice('general.future_date_error', 1));
            return redirect()->back()->withInput();
        }
        if ($request->date < $loan->disbursement_date) {
            Flash::warning(trans_choice('general.early_date_error', 1));
            return redirect()->back()->withInput();
        }
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Sentinel::getUser()->id;
        $loan_transaction->office_id = $loan->office_id;
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->reversible = 0;
        $loan_transaction->transaction_type = "interest_waiver";
        $loan_transaction->date = $request->date;
        $loan_transaction->reversible = 0;
        $date = explode('-', $request->date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->credit = $request->amount;
        $loan_transaction->notes = $request->notes;
        $loan_transaction->save();
        $amount = $request->amount;
        foreach (LoanRepaymentSchedule::select('id', DB::raw("(COALESCE(interest,0)-COALESCE(interest_waived,0)-COALESCE(interest_written_off,0)-COALESCE(interest_paid,0)) as interest_due"))->where('loan_id', $loan->id)->orderBy('due_date', 'asc')->havingRaw("interest_due>0")->get() as $key) {
            if ($amount > 0) {
                if ($amount >= $key->interest_due) {
                    $schedule = LoanRepaymentSchedule::find($key->id);
                    $schedule->interest_waived = $schedule->interest_waived + $key->interest_due;
                    $schedule->save();
                    $amount = $amount - $key->interest_due;

                } else {
                    $schedule = LoanRepaymentSchedule::find($key->id);
                    $schedule->interest_waived = $schedule->interest_waived + $amount;
                    $schedule->save();
                    $amount = 0;
                    break;
                }

            }
            if ($amount <= 0) {
                break;
            }

        }
        event(new TransactionUpdated($loan_transaction));
        if (GeneralHelper::loan_total_balance($loan->id) >= 0) {
            $loan->status = "disbursed";
            $loan->save();
        }
        GeneralHelper::audit_trail("Waive Interest", "Loans", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function store_charge(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.charge.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $rules = array(
            'date' => 'required|after_or_equal:' . $loan->disbursement_date,
            'charge_id' => 'required',
            'amount' => 'required',
        );
        $messages = [
            'date.required' => 'Date is required',
            'payment_type_id.required' => 'Payment type is required',
            'amount.required' => 'Amount is required',
            'date.after_or_equal' => 'Date must not be after disbursement date',
        ];
        $charge = Charge::find($request->charge_id);
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $due_date = GeneralHelper::determine_due_date($loan->id, $request->date);
            if (!empty($due_date)) {
                $amount = $request->amount;
                $schedule = LoanRepaymentSchedule::where("due_date", $due_date)->where("loan_id", $loan->id)->first();
                $schedule->fees = $schedule->fees + $amount;
                $schedule->save();
                //fees  transaction
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->reversible = 1;
                $loan_transaction->loan_repayment_schedule_id = $schedule->id;
                $loan_transaction->transaction_type = "specified_due_date_fee";
                $loan_transaction->date = $due_date;
                $date = explode('-', $due_date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->debit = $amount;
                $loan_transaction->notes = $request->notes;
                $loan_transaction->save();
                event(new TransactionUpdated($loan_transaction));
                if (GeneralHelper::loan_total_balance($loan->id) <= 0) {
                    $loan = Loan::find($loan->id);
                    $loan->status = "closed";
                    $loan->save();
                }
            }


            GeneralHelper::audit_trail("Create Charge", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }


/////////////////////////////////////////////////////////////26376387384949/////////////////
    public function reschedule_loan(Request $request, $id)
    {

        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        
        $balance = \App\Helpers\GeneralHelper::new_loan_total_balance($id);
        $loan = Loan::find($id);

        $loan_transaction = new LoanTransactionsPending();
        $loan_transaction->created_by_id = Sentinel::getUser()->id;
        $loan_transaction->office_id = $loan->office_id;
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->balance_bf = $balance;
        $loan_transaction->transaction_type = "repayment";
        $loan_transaction->payment_apply_to = "reloan_payment";
        $loan_transaction->date = $request->submitte_on_date;
        $date = explode('-', $request->submitte_on_date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->credit = $request->paid;
        $loan_transaction->interest = $request->interest;
        $loan_transaction->save(); 


        GeneralHelper::audit_trail("Update Repayment", "Loans", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $loan->id . '/show');
    }


public function new_reschedule_loan(Request $request, $id , $trans_id){

    if (!Sentinel::hasAccess('loans.transactions.create')) {
        Flash::warning("Permission Denied");
        return redirect()->back();
    }

   $balance = \App\Helpers\GeneralHelper::new_loan_total_balance($id);
    
    $loan = Loan::find($id);
    $Trans =  LoanTransactionsPending::find($trans_id);
    $new_repayment_date =  date('Y-m-d', strtotime($loan->first_repayment_date. ' + 1 months'));
    $decimals = $loan->loan_product->decimals;
    $loan->status = "disbursed";
    $loan->disbursed_by_id = Sentinel::getUser()->id;
    $loan->disbursed_notes = $request->disbursed_notes;
    $loan->disbursement_date = $loan->disbursement_date;
    $loan->first_repayment_date = $new_repayment_date;//$Trans->date;//$request->next_repayment;
    $loan->expected_maturity_date = date_format(date_add(date_create( $Trans->date),//$request->next_repayment),
        date_interval_create_from_date_string($loan->loan_term . ' ' . $loan->loan_term_type)),
        'Y-m-d');
    $loan->save();
  //  $next_payment = $request->next_repayment;
    $next_payment = $Trans->date;
    $period = $loan->loan_term / $loan->repayment_frequency;
        for ($i = 0; $i < $period; $i++) {
        $loan_repayment_schedule = new LoanRepaymentSchedule();
        $loan_repayment_schedule->loan_id = $loan->id;
        $loan_repayment_schedule->due_date = $next_payment;
        $date = explode('-', $next_payment);
        $loan_repayment_schedule->month = $date[1];
        $loan_repayment_schedule->year = $date[0];
        $loan_repayment_schedule->principal = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest;
        $loan_repayment_schedule->interest  = $Trans->interest;
        $loan->expected_maturity_date = $next_payment;
        //determine which method to use        
        $loan_repayment_schedule->save();
        $next_payment = date_format(date_add(date_create($next_payment),
            date_interval_create_from_date_string($loan->repayment_frequency . ' ' . $loan->repayment_frequency_type)),
            'Y-m-d');
    }

    $loan->save();  
    
    $loan_transaction = new LoanTransaction();
    $loan_transaction->created_by_id = Sentinel::getUser()->id;
    $loan_transaction->office_id = $loan->office_id;
    $loan_transaction->loan_id = $loan->id;
    $loan_transaction->balance_bf = $balance;
    $loan_transaction->transaction_type = "repayment";
    $loan_transaction->payment_apply_to = "reloan_payment";
    $loan_transaction->date = $Trans->date;
    $date = explode('-', $Trans->date);
    $loan_transaction->year = $date[0];
    $loan_transaction->month = $date[1];
    $loan_transaction->credit = $Trans->credit;//$request->paid;
    $loan_transaction->save(); 
    event(new RepaymentCreated($loan_transaction));
    if (GeneralHelper::loan_total_balance($loan->id) <= 0) {
        $loan = Loan::find($loan->id);
        $loan->status = "closed";
        $loan->save();
    }
   
    $loan_transaction = new LoanTransaction();
    $loan_transaction->created_by_id = Sentinel::getUser()->id;
    $loan_transaction->office_id = $loan->office_id;
    $loan_transaction->loan_id = $loan->id;
    $loan_transaction->transaction_type = "interest";
    $loan_transaction->date = $Trans->date;
    $date = explode('-', $Trans->date);
    $loan_transaction->year = $date[0];
    $loan_transaction->month = $date[1];
    $loan_transaction->debit = $Trans->interest;
    $loan_transaction->save(); 

    LoanTransactionsPending::where('id', $trans_id)->delete();

 
    GeneralHelper::audit_trail("Update Repayment", "Loans", $id);
    Flash::success(trans('general.successfully_saved'));
    return redirect('loan/' . $loan->id . '/show');

}

public function delete_pending_transactions(Request $request, $trans_id){
    if (!Sentinel::hasAccess('loans.transactions.create')) {
        Flash::warning("Permission Denied");
        return redirect()->back();
    }

    LoanTransactionsPending::where('id', $trans_id)->delete();
    return redirect('loan/reloan_approvals');

}


public function delete_pending_transactions_fp_pp(Request $request, $trans_id){
    if (!Sentinel::hasAccess('loans.transactions.create')) {
        Flash::warning("Permission Denied");
        return redirect()->back();
    }

    LoanTransactionUnapproved::where('id', $trans_id)->delete();
    return redirect('loan/transaction_approvals');

}













    public function print_statement($loan)
    {
        // if (!Sentinel::hasAccess('loans.pdf_schedule')) {
        //     Flash::warning(trans('general.permission_denied'));
        //     return redirect()->back();
        // }

        return view('loan.print_statement', compact('loan'));
    }












    //write off loan
    public function write_off_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.write_off')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'written_off_notes' => 'required',
            'written_off_date' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            if ($loan->status != "disbursed") {
                Flash::warning("Loan not disbursed");
                return redirect()->back();
            }
            $loan->status = "written_off";
            $loan->written_off_by_id = Sentinel::getUser()->id;
            $loan->written_off_date = date("Y-m-d");
            $loan->written_off_notes = $request->withdrawn_notes;
            $loan->save();
            $loan_allocation = GeneralHelper::loan_items($loan->id);
            $principal = $loan_allocation["principal"] - $loan_allocation["principal_paid"] - $loan_allocation["principal_waived"] - $loan_allocation["principal_written_off"];
            $interest = $loan_allocation["interest"] - $loan_allocation["interest_paid"] - $loan_allocation["interest_waived"] - $loan_allocation["interest_written_off"];
            $fees = $loan_allocation["fees"] - $loan_allocation["fees_paid"] - $loan_allocation["fees_waived"] - $loan_allocation["fees_written_off"];
            $penalty = $loan_allocation["penalty"] - $loan_allocation["penalty_paid"] - $loan_allocation["penalty_waived"] - $loan_allocation["penalty_written_off"];
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->reversible = 0;
            $loan_transaction->transaction_type = "write_off";
            $loan_transaction->date = $request->written_off_date;
            $date = explode('-', $request->written_off_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $principal + $interest + $penalty + $fees;
            $loan_transaction->notes = $request->notes;
            $loan_transaction->save();
            //update journals
            $loan_product = $loan->loan_product;
            if ($loan_product->accounting_rule != "none") {
                if ($principal > 0) {
                    if (!empty($loan_product->gl_account_loan_portfolio)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_loan_portfolio->id;
                        $journal->date = $request->written_off_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'write_off';
                        $journal->name = "Principal Written Off";
                        $journal->loan_id = $loan->id;
                        $journal->credit = $principal;
                        $journal->reference = $loan->name;
                        $journal->loan_transaction_id = $loan_transaction->id;
                        $journal->save();
                    }
                    if (!empty($loan_product->gl_account_loans_written_off)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_loans_written_off->id;
                        $journal->date = $request->written_off_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'write_off';
                        $journal->name = "Loan Written Off";
                        $journal->loan_id = $loan->id;
                        $journal->debit = $principal;
                        $journal->reference = $loan->name;
                        $journal->loan_transaction_id = $loan_transaction->id;
                        $journal->save();
                    }
                }
            }
            GeneralHelper::audit_trail("Writeoff Loan", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    //loan calculator
    public function create_calculator()
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('loan_calculator.create');
    }

    public function create_calculator_page($loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('loan_calculator.create_page',
            compact('loan_product'));
    }

    public function create_calculator_show(Request $request, $loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('loan_calculator.show',
            compact('loan_product', 'request'));
    }

    //loan applications
    public function index_application()
    {
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = LoanApplication::get();

        return view('loan.application.data', compact('data'));
    }

//LIVE SYSTEM LINE 87
    public function my_applications(){
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan_officer_id = Sentinel::getUser()->id;
        $data = LoanApplication::where('staff_id',$loan_officer_id)->where('status','pending')->get();
        return view('loan.application.my_data', compact('data'));
    }

    public function show_application($loan_application)
    {
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }


        return view('loan.application.show', compact('loan_application'));
    }

    public function decline_application(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'declined_notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan_application = LoanApplication::find($id);
            if ($loan_application->status != "pending") {
                Flash::warning("Loan application not pending");
                return redirect()->back();
            }
            $loan_application->status = "declined";
            $loan_application->declined_by_id = Sentinel::getUser()->id;
            $loan_application->declined_date = date("Y-m-d");
            $loan_application->declined_notes = $request->declined_notes;
            $loan_application->save();
            GeneralHelper::audit_trail("Decline", "Loan Application", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }



    public function approve_application(Request $request, $id)
    {
        $loan_application = LoanApplication::find($id);
        $client_loan = Loan::where('client_id', '=',  $loan_application->client_id)->where('loan_product_id', '=', $loan_application->loan_product_id)->where('status', '!=','closed')->where('status', '!=','declined')->first();
           
        if($client_loan){
            Flash::warning('This client already has a loan');
            return redirect()->back();
        }else{

        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'approved_date' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            if ($loan_application->status != "pending") {
                Flash::warning("Loan application not pending");
                return redirect()->back();
            }
            $loan_application->status = "approved";
            $loan_application->approved_by_id = Sentinel::getUser()->id;
            $loan_application->approved_date = $request->approved_date;
            $loan_application->approved_notes = $request->approved_notes;
            $loan_application->save();
            $loan_product = $loan_application->loan_product;
            //create loan
            $loan = new Loan();
           // $name = $loan->client->firstname;
         //   $name_array = explode(' ',trim($name));
        
         //   $firstWord = $name_array[0];
           // $lastWord = $name_array[count($name_array)-1];

            $loan->account_number = $loan->id;
            $loan->created_by_id = Sentinel::getUser()->id;
            $loan->created_date = $request->approved_date;
            $loan->client_type = $loan_application->client_type;
            $loan->loan_product_id = $loan_product->id;
            $loan->loan_officer_id = $loan_application->staff_id;
            $loan->group_id = $loan_application->group_id;
            $loan->client_id = $loan_application->client_id;
            $loan->office_id = $loan_application->office_id;
            $loan->decimals = $loan_product->decimals;
            $loan->loan_purpose_id = $loan_application->loan_purpose_id;
            $loan->principal = $loan_application->amount;
            $loan->applied_amount = $loan_application->amount;
            $loan->currency_id = $loan_product->currency_id;
            $loan->loan_term = $loan_product->default_loan_term;
            $loan->loan_term_type = $loan_product->repayment_frequency_type;
            $loan->repayment_frequency = $loan_product->repayment_frequency;
            $loan->repayment_frequency_type = $loan_product->repayment_frequency_type;
            $loan->interest_rate = $loan_product->default_interest_rate;
            $loan->interest_rate_type = $loan_product->interest_rate_type;
            $loan->interest_method = $loan_product->interest_method;
            $loan->armotization_method = $loan_product->armotization_method;
            $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
            $loan->grace_on_principal = $loan_product->grace_on_principal;
            $loan->grace_on_interest_payment = $loan_product->grace_on_interest_payment;
            $date = explode('-', $request->approved_date);
            $loan->month = $date[1];
            $loan->year = $date[0];
          


            $loan->save();
            $loan_application->loan_id = $loan->id;
            $loan_application->save();
            GeneralHelper::audit_trail("Approve", "Loan Application", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/edit');
        }
    }
    
    }
}
