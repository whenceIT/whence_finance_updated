<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Exports\ExportReport;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\GlAccount;
use App\Models\GlJournalEntry;
use App\Models\Payroll;
use App\Models\PayrollMeta;
use App\Models\PayrollTemplate;
use App\Models\PayrollTemplateMeta;
use App\Models\PayrollInfo;
use App\Models\Setting;
use App\Models\User;
use App\Models\NewPayroll;
use App\Models\UserRole;
use Illuminate\Support\Facades\View;
use PDF;
use Excel;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class PayrollController extends Controller
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
        if (!Sentinel::hasAccess('payroll.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = User::all();
        return view('payroll.data', compact('data'));
    }


    public function myPayslipsOld(){
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $user = Sentinel::getUser();
        return view('payroll.myPayslipsOld',compact('user'));
    }


    public function create_payroll(){
        $users = User::all();
        $todaysDate = date('m');
        foreach($users as $user){
            $payroll = NewPayroll::where('user_id',$user->id)->orderBy('created_at','desc')->first();
            if(!empty($payroll)){
                if(date('m',strtotime($payroll->created_at)) != $todaysDate){
                    $new_payroll = new NewPayroll();
                    $new_payroll -> user_id =  $payroll->user_id;
                    $new_payroll -> basic_pay = $payroll->basic_pay;
                    $new_payroll -> charges = $payroll->charges;
                    $new_payroll -> allowances = $payroll->allowances;
                    $new_payroll -> salary_deductions = $payroll->advance_deductions;
                    $new_payroll -> save();
                    Flash::success(trans('general.successfully_saved'));
                    return redirect('payroll/payroll_list');
                }else{
                    Flash::success(trans('Payroll for this month already exists'));
                    return redirect('payroll/payroll_list');
                }
            }
        }
    }



    
    public function pdfPayslipOld($payroll)
    {

        $top_left = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'top_left')->get();
        $top_right = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'top_right')->get();
        $bottom_left = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'bottom_left')->get();
        $bottom_right = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'bottom_right')->get();
        $pdf = PDF::loadView('payroll.pdf_payslip_old',
            compact('payroll', 'top_left', 'top_right', 'bottom_left', 'bottom_right'));
        return $pdf->download($payroll->employee_name . " - Payslip.pdf",
            'D');

    }

    public function payroll_list(){

        $payroll_list = [];
        $user = Sentinel::findById(Sentinel::getUser()->id);
     //  $payroll = NewPayroll::get();
        $users = User::all();
        foreach($users as $user){
            $payroll = NewPayroll::where('user_id',$user->id)->orderBy('created_at','desc')->first();
            if($payroll != null){
                array_push($payroll_list,$payroll);
            }
        }
        return view('payroll.payroll_list',compact('payroll_list',));
    }

    public function payroll_query(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $payroll_list = [];
        $users = User::all();
        foreach($users as $user){
            if($start_date && $end_date != null){
                $payroll = NewPayroll::where('user_id',$user->id)->whereBetween('created_at',[$start_date,$end_date])->get();
                if(count($payroll) != 0){
                    array_push($payroll_list,$payroll);
                }
            }
        }
        return view('payroll.payroll_query',compact('payroll_list','start_date','end_date'));
    }


    public function payroll_report_excel(){
        $payroll_list = [];
        $user = Sentinel::findById(Sentinel::getUser()->id);
     //  $payroll = NewPayroll::get();
        $users = User::all();
        foreach($users as $user){
            $payroll = NewPayroll::where('user_id',$user->id)->orderBy('created_at','desc')->first();
            if($payroll != null){
                array_push($payroll_list,$payroll);
                $date = $payroll->created_at;
            }
        }
        $payroll_list = [
            "payroll_list" => $payroll_list,
            "date" => $date
        ];
        return Excel::download(new ExportReport("payroll.payroll_report",$payroll_list),trans_choice('Payroll', 2) . ' ' . trans_choice('Report',
        1) . ' ' . trans_choice(date("M Y",strtotime($date)), 2) . '.xlsx');
    }


    public function edit_payroll($id){
        $payroll_user = NewPayroll::where('id',$id)->orderBy('created_at','desc')->first();
        $user =  User::where('id',$payroll_user->user_id)->first();
        
        $template = PayrollTemplate::first();
        $top_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_left')->get();
        $top_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_right')->get();

        return view('payroll.edit_payroll',compact('payroll_user','template','top_left','top_right','id','user',));
    }

    public function save_payroll(Request $request,$id){
        $payroll = NewPayroll::where('id',$id)->first();
        $user =  User::where('id',$payroll->user_id)->first();
        $payroll -> user_id = $user->id;
        $payroll -> basic_pay = $request->basic_pay;
        $payroll -> charges = $request->charges;
        $payroll -> allowances = $request->allowances;
        $payroll -> salary_deductions = $request->advance_deductions;
        $payroll -> save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('payroll/payroll_list');
    }



public function user_payslip($user){
    if (!Sentinel::hasAccess('payroll.create')) {
        Flash::warning("Permission Denied");
        return redirect()->back();
    }
    $PayrollInformation = PayrollInfo::where('employee_id',$user->id)->first();
    $template = PayrollTemplate::first();
    $top_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
        'top_left')->get();
    $top_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
        'top_right')->get();
    $bottom_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
        'bottom_left')->get();
    $bottom_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
        'bottom_right')->get();

    return view('payroll.user_payslip', compact( 'bottom_right', 'bottom_left', 'top_right', 'top_left', 'template','PayrollInformation'));
}

    public function my_payroll_information(){
        $information = [];
        $user = Sentinel::findById(Sentinel::getUser()->id);
        $information = PayrollInfo::where('employee_id',$user->id)->first();
      //  $information = count($information);
        //$new = $information->employee_name;
       //$information = 2;
      //  $information ['1','2',3,4,5];
       
    return view('payroll.my_payroll_information',compact('information'));
     
    }

    public function add_payroll_information(Request $request){
        $user = Sentinel::findById(Sentinel::getUser()->id);
        $role = UserRole::where('user_id',$user->id)->first();
        $user_name = $user->first_name.' '.$user->last_name;
        $PayrollInformation = new PayrollInfo();
        $PayrollInformation -> employee_name = $user_name;
        $PayrollInformation -> employee_id = $user->id;
        $PayrollInformation -> branch_id = $user->office_id;
        $PayrollInformation -> position = $role->role_id;
        $PayrollInformation -> SSN = $request->SSN;
        $PayrollInformation -> TPIN = $request->TPIN;
        $PayrollInformation -> payment_type = $request->payment_type;
        $PayrollInformation -> account_number = $request->account_number;
        $PayrollInformation -> save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('/dashboard');
    }

    // public function edit_payroll(Request $request,$id){
    //     $user = Sentinel::findById(Sentinel::getUser()->id);
    //     $role = UserRole::where('user_id',$user->id)->first();
    //     $user_name = $user->first_name.' '.$user->last_name;
    //     $PayrollInformation = PayrollInfo::where('id',$id)->first();
    //     $PayrollInformation -> employee_name = $user_name;
    //     $PayrollInformation -> branch_id = $user->office_id;
    //     $PayrollInformation -> position = $role->role_id;
    //     $PayrollInformation -> SSN = $request->SSN;
    //     $PayrollInformation -> TPIN = $request->TPIN;
    //     $PayrollInformation -> payment_type = $request->payment_type;
    //     $PayrollInformation -> account_number = $request->account_number;
    //     $PayrollInformation -> save();
    //     Flash::success(trans('general.successfully_saved'));
    //     return redirect('/dashboard');
    // }

    //
    public function create_new_payroll(Request $request){
        $new_payroll = new NewPayroll();
        $new_payroll -> user_id =  $request->user_id;
        $new_payroll -> basic_pay = $request->basic_pay;
        $new_payroll -> charges = $request->charges;
        $new_payroll -> allowances = $request->allowances;
        $new_payroll -> salary_deductions = $request->advance_deductions;
        $new_payroll -> save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('payroll/payroll_list');
      //  $PayrollInformation -> employee_id = $request->user_id;
    }

    public function edit_payroll_information_manager(Request $request,$id){
        $basic_pay = 'Basic Pay';
        $performance_allowance = 'Performance Allowance';
        $extra_responsibitly_allowance = 'Extra Responsibility Allowance';
        $salary_advance_deductions = 'Salary Advance Deductions';
        $penalty_deductions = 'Penalty Deductions';
        $PayrollInformation = PayrollInfo::where('id',$id)->first();
        $PayrollInformation -> gross_amount = $request->total_pay;
        $PayrollInformation -> charges = $request->total_deductions;
        $PayrollInformation -> allowances = $request->allowances;
        $PayrollInformation -> net_pay = $request->net_pay;
        $PayrollInformation -> save();
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('payroll.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $template = PayrollTemplate::first();
        $top_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_left')->get();
        $top_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_right')->get();
        $bottom_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'bottom_left')->get();
        $bottom_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'bottom_right')->get();
        return view('payroll.create',
            compact( 'bottom_right', 'bottom_left', 'top_right', 'top_left', 'template'));
    }

    public function store(Request $request)
    {

        $user = Sentinel::findById(Sentinel::getUser()->id);
        $role = UserRole::where('user_id',$user->id)->first();
        $user_name = $user->first_name.' '.$user->last_name;
        $PayrollInformation = new PayrollInfo();
        $PayrollInformation -> employee_name = $request->employee_name;
        $PayrollInformation -> employee_id = $request->user_id;
        $PayrollInformation -> branch_id = Sentinel::findUserById($request->user_id)->office_id;
        //$PayrollInformation -> position =  $role->role_id;
        $PayrollInformation -> SSN = $request->SSN;
        $PayrollInformation -> TPIN = $request->TPIN;
        $PayrollInformation -> payment_type = $request->payment_type;
        $PayrollInformation -> account_number = $request->account_number;
        $PayrollInformation -> save();



        if (!Sentinel::hasAccess('payroll.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $payroll = new Payroll();
        $payroll->payroll_template_id = $request->payroll_template_id;
        $payroll->user_id = $request->user_id;
        $payroll->employee_name = $request->employee_name;
        $payroll->business_name = $request->business_name;
        $payroll->payment_method = $request->payment_method;
        $payroll->office_id = Sentinel::findUserById($request->user_id)->office_id;
        $payroll->bank_name = $request->bank_name;
        $payroll->account_number = $request->account_number;
        $payroll->description = $request->description;
        $payroll->comments = $request->comments;
        $payroll->paid_amount = $request->paid_amount;
        $payroll->date = $request->date;
        $date = explode('-', $request->date);
        $payroll->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $payroll->recur_frequency = $request->recur_frequency;
            $payroll->recur_start_date = $request->recur_start_date;
            if (!empty($request->recur_end_date)) {
                $payroll->recur_end_date = $request->recur_end_date;
            }
            $payroll->recur_next_date =$request->recur_start_date;
            $payroll->recur_type = $request->recur_type;
        }
        $payroll->year = $date[0];
        $payroll->month = $date[1];
        $payroll->save();
        //save payroll meta
        $metas = PayrollTemplateMeta::where('payroll_template_id', $request->template_id)->get();;
        foreach ($metas as $key) {
            $meta = new PayrollMeta();
            $kid = $key->id;
            $meta->value = $request->$kid;
            $meta->payroll_id = $payroll->id;
            $meta->payroll_template_meta_id = $key->id;
            $meta->position = $key->position;
            $meta->save();
        }
        //debit and credit the necessary accounts
        if (!empty(GlAccount::find(Setting::where('setting_key', 'payroll_gl_account_expense_id')->first()->setting_value))) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->gl_account_id = Setting::where('setting_key', 'payroll_gl_account_expense_id')->first()->setting_value;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'payroll';
            $journal->name = "Payroll";
            $journal->payroll_transaction_id = $payroll->id;
            $journal->debit = GeneralHelper::single_payroll_pay($payroll->id);
            $journal->reference = $payroll->id;
            $journal->transaction_id = $payroll->id;
            $journal->save();
        }
        if (!empty(GlAccount::find(Setting::where('setting_key', 'payroll_gl_account_asset_id')->first()->setting_value))) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->gl_account_id = Setting::where('setting_key', 'payroll_gl_account_asset_id')->first()->setting_value;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'payroll';
            $journal->name = "Payroll";
            $journal->payroll_transaction_id = $payroll->id;
            $journal->credit = GeneralHelper::single_payroll_pay($payroll->id);
            $journal->reference = $payroll->id;
            $journal->transaction_id = $payroll->id;
            $journal->save();
        }
        GeneralHelper::audit_trail("Create", "Payroll", $payroll->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('payroll/data');
    }

    public function pdfPayslip($id)
    {
        $user = Sentinel::findById(Sentinel::getUser()->id);
        $template = PayrollTemplate::first();
        $payslip = NewPayroll::where('id',$id)->first();
        $top_left = PayrollMeta::where('payroll_id', $template->id)->where('position',
            'top_left')->get();
        $top_right = PayrollMeta::where('payroll_id', $template->id)->where('position',
            'top_right')->get();
        $bottom_left = PayrollMeta::where('payroll_id', $template->id)->where('position',
            'bottom_left')->get();
        $bottom_right = PayrollMeta::where('payroll_id', $template->id)->where('position',
            'bottom_right')->get();
        $pdf = PDF::loadView('payroll.pdf_payslip',
            compact('top_left', 'top_right', 'bottom_left', 'bottom_right','payslip','user'));
        return $pdf->download( $user->first_name." ".$user->last_name." - Payslip.pdf",
            'D');


    }

    public function staffPayroll($user)
    {
        if (!Sentinel::hasAccess('payroll.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('payroll.staff_payroll', compact('user'));
    }


    public function myPayslips(){
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $user = Sentinel::findById(Sentinel::getUser()->id);
        $payslips = NewPayroll::where('user_id',$user->id)->get();
        $user = Sentinel::getUser();
        return view('payroll.myPayslips',compact('user','payslips'));
    }




    public function getUser($id)
    {
        $user = User::find($id);
        return $user->first_name . ' ' . $user->last_name;
    }

    public function show($borrower)
    {
        if (!Sentinel::hasAccess('payroll.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $users = User::all();
        $user = array();
        foreach ($users as $key) {
            $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        }
        //get custom fields
        $custom_fields = CustomField::where('category', 'borrowers')->get();
        return view('borrower.show', compact('borrower', 'user', 'custom_fields'));
    }


    public function edit($payroll)
    {
        if (!Sentinel::hasAccess('payroll.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        

        // $users = User::all();
        // $user = array();
        // foreach ($users as $key) {
        //     $user[$key->id] = $key->first_name . ' ' . $key->last_name;
        // }
        $user = '';
        $custom_fields = '';
        $chart = '';

        $template = PayrollTemplate::first();
        $top_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_left')->get();
        $top_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'top_right')->get();
        $bottom_left = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'bottom_left')->get();
        $bottom_right = PayrollTemplateMeta::where('payroll_template_id', $template->id)->where('position',
            'bottom_right')->get();
        return view('payroll.edit',
            compact('user','custom_fields', 'bottom_right', 'bottom_left', 'top_right', 'top_left', 'template',
                'payroll', 'chart'));
    }

    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('payroll.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $payroll = Payroll::find($id);
        $payroll->payroll_template_id = $request->payroll_template_id;
        $payroll->employee_name = $request->employee_name;
        $payroll->business_name = $request->business_name;
        $payroll->payment_method = $request->payment_method;
        $payroll->bank_name = $request->bank_name;
        $payroll->account_number = $request->account_number;
        $payroll->description = $request->description;
        $payroll->comments = $request->comments;
        $payroll->paid_amount = $request->paid_amount;
        $payroll->date = $request->date;
        $date = explode('-', $request->date);
        $payroll->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $payroll->recur_frequency = $request->recur_frequency;
            $payroll->recur_start_date = $request->recur_start_date;
            if (!empty($request->recur_end_date)) {
                $payroll->recur_end_date = $request->recur_end_date;
            }
            if (empty($payroll->recur_next_date)) {
                $payroll->recur_next_date =$request->recur_start_date;
            }
            $payroll->recur_type = $request->recur_type;
        }
        $payroll->year = $date[0];
        $payroll->month = $date[1];
        $payroll->save();
        //save payroll meta
        $metas = PayrollTemplateMeta::where('payroll_template_id', $request->template_id)->get();;
        foreach ($metas as $key) {
            if (!empty(PayrollMeta::where('payroll_template_meta_id', $key->id)->where('payroll_id',
                $id)->first())
            ) {
                $meta = PayrollMeta::where('payroll_template_meta_id', $key->id)->where('payroll_id',
                    $id)->first();
            } else {
                $meta = new PayrollMeta();
            }
            $kid = $key->id;
            $meta->value = $request->$kid;
            $meta->payroll_id = $payroll->id;
            $meta->payroll_template_meta_id = $key->id;
            $meta->position = $key->position;
            $meta->save();
        }
        //debit and credit the necessary accounts
        GlJournalEntry::where('reference', $id)->where('transaction_type', "payroll")->delete();
        //debit and credit the necessary accounts
        if (!empty(GlAccount::find(Setting::where('setting_key', 'payroll_gl_account_expense_id')->first()->setting_value))) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->gl_account_id = Setting::where('setting_key', 'payroll_gl_account_expense_id')->first()->setting_value;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'payroll';
            $journal->name = "Payroll";
            $journal->payroll_transaction_id = $payroll->id;
            $journal->debit = GeneralHelper::single_payroll_pay($payroll->id);
            $journal->reference = $payroll->id;
            $journal->transaction_id = $payroll->id;
            $journal->save();
        }
        if (!empty(GlAccount::find(Setting::where('setting_key', 'payroll_gl_account_asset_id')->first()->setting_value))) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->gl_account_id = Setting::where('setting_key', 'payroll_gl_account_asset_id')->first()->setting_value;
            $journal->date = $request->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'payroll';
            $journal->name = "Payroll";
            $journal->payroll_transaction_id = $payroll->id;
            $journal->credit = GeneralHelper::single_payroll_pay($payroll->id);
            $journal->reference = $payroll->id;
            $journal->transaction_id = $payroll->id;
            $journal->save();
        }
        GeneralHelper::audit_trail("Update","Payroll", $payroll->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('payroll/data');
    }




    public function delete($id)
    {
        if (!Sentinel::hasAccess('payroll.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Payroll::destroy($id);
        PayrollMeta::where('payroll_id', $id)->delete();
        GeneralHelper::audit_trail("Delete","Payroll", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('payroll/data');
    }

}
