<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;

use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\GlAccount;
use App\Models\GlJournalEntry;
use App\Models\Payroll;
use App\Models\PayrollMeta;
use App\Models\PayrollTemplate;
use App\Models\PayrollTemplateMeta;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\View;
use PDF;
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

    public function pdfPayslip($payroll)
    {

        $top_left = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'top_left')->get();
        $top_right = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'top_right')->get();
        $bottom_left = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'bottom_left')->get();
        $bottom_right = PayrollMeta::where('payroll_id', $payroll->id)->where('position',
            'bottom_right')->get();
        $pdf = PDF::loadView('payroll.pdf_payslip',
            compact('payroll', 'top_left', 'top_right', 'bottom_left', 'bottom_right'));
        return $pdf->download($payroll->employee_name . " - Payslip.pdf",
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
        $user = Sentinel::getUser();
        return view('payroll.myPayslips',compact('user'));
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
