<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\Setting;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class SettingController extends Controller
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
    public function updateSystem()
    {
        Artisan::call('migrate');
        Flash::success("Successfully Updated");
        return redirect('setting/data');
    }

    public function index()
    {

        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }

        return view('setting.data');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function general()
    {
        return view('setting.general');
    }

    public function organisation()
    {
        return view('setting.organisation');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    public function update_general(Request $request)
    {
        if (!Sentinel::hasAccess('settings.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Setting::where('setting_key', 'company_name')->update(['setting_value' => $request->company_name]);
        Setting::where('setting_key', 'company_phone')->update(['setting_value' => $request->company_phone]);
        Setting::where('setting_key', 'company_email')->update(['setting_value' => $request->company_email]);
        Setting::where('setting_key', 'company_website')->update(['setting_value' => $request->company_website]);
        Setting::where('setting_key', 'company_currency')->update(['setting_value' => $request->company_currency]);
        Setting::where('setting_key', 'company_country')->update(['setting_value' => $request->company_country]);
        Setting::where('setting_key', 'sms_enabled')->update(['setting_value' => $request->sms_enabled]);
        Setting::where('setting_key', 'active_sms')->update(['setting_value' => $request->active_sms]);
        Setting::where('setting_key', 'password_reset_subject')->update(['setting_value' => $request->password_reset_subject]);
        Setting::where('setting_key', 'password_reset_template')->update(['setting_value' => $request->password_reset_template]);
        Setting::where('setting_key', 'payment_received_email_subject')->update(['setting_value' => $request->payment_received_email_subject]);
        Setting::where('setting_key', 'payment_received_email_template')->update(['setting_value' => $request->payment_received_email_template]);
        Setting::where('setting_key', 'payment_email_subject')->update(['setting_value' => $request->payment_email_subject]);
        Setting::where('setting_key', 'payment_email_template')->update(['setting_value' => $request->payment_email_template]);
        Setting::where('setting_key', 'client_statement_email_subject')->update(['setting_value' => $request->client_statement_email_subject]);
        Setting::where('setting_key', 'client_statement_email_template')->update(['setting_value' => $request->client_statement_email_template]);
        Setting::where('setting_key', 'loan_statement_email_subject')->update(['setting_value' => $request->loan_statement_email_subject]);
        Setting::where('setting_key', 'loan_statement_email_template')->update(['setting_value' => $request->loan_statement_email_template]);
        Setting::where('setting_key', 'loan_schedule_email_subject')->update(['setting_value' => $request->loan_schedule_email_subject]);
        Setting::where('setting_key', 'auto_apply_penalty')->update(['setting_value' => $request->auto_apply_penalty]);
        Setting::where('setting_key', 'loan_schedule_email_template')->update(['setting_value' => $request->loan_schedule_email_template]);
        Setting::where('setting_key', 'loan_overdue_email_subject')->update(['setting_value' => $request->loan_overdue_email_subject]);
        Setting::where('setting_key', 'loan_overdue_email_template')->update(['setting_value' => $request->loan_overdue_email_template]);
        Setting::where('setting_key', 'missed_payment_email_subject')->update(['setting_value' => $request->missed_payment_email_subject]);
        Setting::where('setting_key', 'missed_payment_email_template')->update(['setting_value' => $request->missed_payment_email_template]);
        Setting::where('setting_key', 'loan_payment_reminder_subject')->update(['setting_value' => $request->loan_payment_reminder_subject]);
        Setting::where('setting_key', 'loan_payment_reminder_email_template')->update(['setting_value' => $request->loan_payment_reminder_email_template]);
        Setting::where('setting_key', 'loan_approved_email_subject')->update(['setting_value' => $request->loan_approved_email_subject]);
        Setting::where('setting_key', 'loan_approved_email_template')->update(['setting_value' => $request->loan_approved_email_template]);
        Setting::where('setting_key', 'loan_disbursed_email_subject')->update(['setting_value' => $request->loan_disbursed_email_subject]);
        Setting::where('setting_key', 'loan_disbursed_email_template')->update(['setting_value' => $request->loan_disbursed_email_template]);
        Setting::where('setting_key', 'savings_statement_email_template')->update(['setting_value' => $request->savings_statement_email_template]);
        Setting::where('setting_key', 'payment_received_sms_template')->update(['setting_value' => $request->payment_received_sms_template]);
        Setting::where('setting_key', 'loan_overdue_sms_template')->update(['setting_value' => $request->loan_overdue_sms_template]);
        Setting::where('setting_key', 'loan_payment_reminder_sms_template')->update(['setting_value' => $request->loan_payment_reminder_sms_template]);
        Setting::where('setting_key', 'missed_payment_sms_template')->update(['setting_value' => $request->missed_payment_sms_template]);
        Setting::where('setting_key', 'loan_approved_sms_template')->update(['setting_value' => $request->loan_approved_sms_template]);
        Setting::where('setting_key', 'loan_disbursed_sms_template')->update(['setting_value' => $request->loan_disbursed_sms_template]);
        Setting::where('setting_key', 'enable_cron')->update(['setting_value' => $request->enable_cron]);
        Setting::where('setting_key', 'auto_payment_receipt_sms')->update(['setting_value' => $request->auto_payment_receipt_sms]);
        Setting::where('setting_key', 'auto_payment_receipt_email')->update(['setting_value' => $request->auto_payment_receipt_email]);
        Setting::where('setting_key', 'loan_approved_auto_email')->update(['setting_value' => $request->loan_approved_auto_email]);
        Setting::where('setting_key', 'loan_approved_auto_sms')->update(['setting_value' => $request->loan_approved_auto_sms]);
        Setting::where('setting_key', 'loan_disbursed_auto_email')->update(['setting_value' => $request->loan_disbursed_auto_email]);
        Setting::where('setting_key', 'loan_disbursed_auto_sms')->update(['setting_value' => $request->loan_disbursed_auto_sms]);
        Setting::where('setting_key', 'auto_repayment_sms_reminder')->update(['setting_value' => $request->auto_repayment_sms_reminder]);
        Setting::where('setting_key', 'auto_repayment_email_reminder')->update(['setting_value' => $request->auto_repayment_email_reminder]);
        Setting::where('setting_key', 'auto_repayment_days')->update(['setting_value' => $request->auto_repayment_days]);
        Setting::where('setting_key', 'auto_overdue_repayment_sms_reminder')->update(['setting_value' => $request->auto_overdue_repayment_sms_reminder]);
        Setting::where('setting_key', 'auto_overdue_repayment_email_reminder')->update(['setting_value' => $request->auto_overdue_repayment_email_reminder]);
        Setting::where('setting_key', 'auto_overdue_repayment_days')->update(['setting_value' => $request->auto_overdue_repayment_days]);
        Setting::where('setting_key', 'auto_overdue_loan_sms_reminder')->update(['setting_value' => $request->auto_overdue_loan_sms_reminder]);
        Setting::where('setting_key', 'auto_overdue_loan_email_reminder')->update(['setting_value' => $request->auto_overdue_loan_email_reminder]);
        Setting::where('setting_key', 'auto_overdue_loan_days')->update(['setting_value' => $request->auto_overdue_loan_days]);
        Setting::where('setting_key', 'allow_self_registration')->update(['setting_value' => $request->allow_self_registration]);
        Setting::where('setting_key', 'allow_client_apply')->update(['setting_value' => $request->allow_client_apply]);
        Setting::where('setting_key', 'enable_google_recaptcha')->update(['setting_value' => $request->enable_google_recaptcha]);
        Setting::where('setting_key', 'google_recaptcha_site_key')->update(['setting_value' => $request->google_recaptcha_site_key]);
        Setting::where('setting_key', 'google_recaptcha_secret_key')->update(['setting_value' => $request->google_recaptcha_secret_key]);
        Setting::where('setting_key', 'enable_custom_fields')->update(['setting_value' => $request->enable_custom_fields]);
        Setting::where('setting_key', 'savings_transaction_sms_template')->update(['setting_value' => $request->savings_transaction_sms_template]);
        Setting::where('setting_key', 'savings_transaction_email_template')->update(['setting_value' => $request->savings_transaction_email_template]);
        Setting::where('setting_key', 'savings_transaction_email_subject')->update(['setting_value' => $request->savings_transaction_email_subject]);
        Setting::where('setting_key', 'payroll_gl_account_asset_id')->update(['setting_value' => $request->payroll_gl_account_asset_id]);
        Setting::where('setting_key', 'payroll_gl_account_expense_id')->update(['setting_value' => $request->payroll_gl_account_expense_id]);
        if ($request->hasFile('company_logo')) {
            $file = array('company_logo' => $request->file('company_logo'));
            $rules = array('company_logo' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                Setting::where('setting_key',
                    'company_logo')->update(['setting_value' => $request->file('company_logo')->getClientOriginalName()]);
                $request->file('company_logo')->move(public_path() . '/uploads',
                    $request->file('company_logo')->getClientOriginalName());
            }
        }

        //GeneralHelper::audit_trail("Updated Settings");
        Flash::success(trans_choice("general.successfully_saved", 1));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
