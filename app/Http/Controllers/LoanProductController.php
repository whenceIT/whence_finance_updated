<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\Charge;
use App\Models\CustomField;
use App\Models\LoanProduct;
use App\Models\LoanProductCharge;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class LoanProductController extends Controller
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
        if (!Sentinel::hasAccess('products.loan_products.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = LoanProduct::all();

        return view('loan_product.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('products.loan_products.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $charges = Charge::where('product', 'loan')->where('active', 1)->get();

        return view('loan_product.create',
            compact('charges'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('products.loan_products.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required',
            'short_name' => 'required',
            'description' => 'required',
            'decimals' => 'required',
            'minimum_principal' => 'required',
            'maximum_principal' => 'required',
            'minimum_loan_term' => 'required',
            'default_loan_term' => 'required',
            'maximum_loan_term' => 'required',
            'repayment_frequency' => 'required',
            'repayment_frequency_type' => 'required',
            'minimum_interest_rate' => 'required',
            'default_interest_rate' => 'required',
            'maximum_interest_rate' => 'required',
            'interest_rate_type' => 'required',
            'grace_on_interest_charged' => 'required',
            'grace_on_principal' => 'required',
            'grace_on_interest_payment' => 'required',
            'interest_method' => 'required',
            'armotization_method' => 'required',
            'year_days' => 'required',
            'month_days' => 'required',
            'loan_transaction_strategy' => 'required',
        );
        $messages = [
            'name.required' => 'Name is required',
            'gl_code.required' => 'GL Code is required',
            'gl_code.unique' => 'The GL Code already exists',
            'account_type.required' => 'Account type is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $loan_product = new LoanProduct();
            $loan_product->created_by_id = Sentinel::getUser()->id;
            $loan_product->name = $request->name;
            $loan_product->short_name = $request->short_name;
            $loan_product->description = $request->description;
            $loan_product->fund_id = $request->fund_id;
            $loan_product->currency_id = $request->currency_id;
            $loan_product->decimals = $request->decimals;
            $loan_product->minimum_principal = $request->minimum_principal;
            $loan_product->default_principal = $request->default_principal;
            $loan_product->maximum_principal = $request->maximum_principal;
            $loan_product->minimum_loan_term = $request->minimum_loan_term;
            $loan_product->default_loan_term = $request->default_loan_term;
            $loan_product->maximum_loan_term = $request->maximum_loan_term;
            $loan_product->repayment_frequency = $request->repayment_frequency;
            $loan_product->repayment_frequency_type = $request->repayment_frequency_type;
            $loan_product->minimum_interest_rate = $request->minimum_interest_rate;
            $loan_product->default_interest_rate = $request->default_interest_rate;
            $loan_product->maximum_interest_rate = $request->maximum_interest_rate;
            $loan_product->interest_rate_type = $request->interest_rate_type;
            $loan_product->interest_method = $request->interest_method;
            $loan_product->grace_on_interest_charged = $request->grace_on_interest_charged;
            $loan_product->grace_on_principal = $request->grace_on_principal;
            $loan_product->grace_on_interest_payment = $request->grace_on_interest_payment;
            $loan_product->interest_method = $request->interest_method;
            $loan_product->armotization_method = $request->armotization_method;
            //$loan_product->interest_calculation_period_type = $request->interest_calculation_period_type;
            $loan_product->year_days = $request->year_days;
            $loan_product->month_days = $request->month_days;
            $loan_product->loan_transaction_strategy = $request->loan_transaction_strategy;
            $loan_product->allow_additional_charges = $request->allow_additional_charges;
            $loan_product->lock_guarantee = $request->lock_guarantee;
            $loan_product->accounting_rule = $request->accounting_rule;
            $loan_product->gl_account_fund_source_id = $request->gl_account_fund_source_id;
            $loan_product->gl_account_loan_portfolio_id = $request->gl_account_loan_portfolio_id;
            $loan_product->gl_account_receivable_interest_id = $request->gl_account_receivable_interest_id;
            $loan_product->gl_account_receivable_fee_id = $request->gl_account_receivable_fee_id;
            $loan_product->gl_account_receivable_penalty_id = $request->gl_account_receivable_penalty_id;
            $loan_product->gl_account_loan_over_payments_id = $request->gl_account_loan_over_payments_id;
            $loan_product->gl_account_suspended_income_id = $request->gl_account_suspended_income_id;
            $loan_product->gl_account_income_interest_id = $request->gl_account_income_interest_id;
            $loan_product->gl_account_income_fee_id = $request->gl_account_income_fee_id;
            $loan_product->gl_account_income_penalty_id = $request->gl_account_income_penalty_id;
            $loan_product->gl_account_income_recovery_id = $request->gl_account_income_recovery_id;
            $loan_product->gl_account_loans_written_off_id = $request->gl_account_loans_written_off_id;
            $loan_product->arrears_grace_days = $request->arrears_grace_days;
            $loan_product->save();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $loan_product_charge = new LoanProductCharge();
                    $loan_product_charge->loan_product_id = $loan_product->id;
                    $loan_product_charge->charge_id = $key;
                    $loan_product_charge->save();
                }
            }
            GeneralHelper::audit_trail("Create", "Loan Product", $loan_product->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/product/data');
        }
    }


    public function show($loan_product)
    {
        if (!Sentinel::hasAccess('products.loan_products.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $charges = array();
        foreach (Charge::where('product', 'loan')->get() as $key) {
            $charges[$key->id] = $key->name;
        }
        return view('loan_product.show', compact('loan_product', 'charges'));
    }


    public function edit($loan_product)
    {
        if (!Sentinel::hasAccess('products.loan_products.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('loan_product.edit',
            compact('loan_product'));
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
        if (!Sentinel::hasAccess('products.loan_products.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required',
            'short_name' => 'required',
            'description' => 'required',
            'decimals' => 'required',
            'minimum_principal' => 'required',
            'maximum_principal' => 'required',
            'minimum_loan_term' => 'required',
            'default_loan_term' => 'required',
            'maximum_loan_term' => 'required',
            'repayment_frequency' => 'required',
            'repayment_frequency_type' => 'required',
            'minimum_interest_rate' => 'required',
            'default_interest_rate' => 'required',
            'maximum_interest_rate' => 'required',
            'interest_rate_type' => 'required',
            'grace_on_interest_charged' => 'required',
            'grace_on_principal' => 'required',
            'grace_on_interest_payment' => 'required',
            'interest_method' => 'required',
            'armotization_method' => 'required',
            'year_days' => 'required',
            'month_days' => 'required',
            'loan_transaction_strategy' => 'required',
        );
        $messages = [
            'name.required' => 'Name is required',
            'gl_code.required' => 'GL Code is required',
            'gl_code.unique' => 'The GL Code already exists',
            'account_type.required' => 'Account type is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $loan_product = LoanProduct::find($id);
            $loan_product->name = $request->name;
            $loan_product->short_name = $request->short_name;
            $loan_product->description = $request->description;
            $loan_product->fund_id = $request->fund_id;
            $loan_product->decimals = $request->decimals;
            $loan_product->minimum_principal = $request->minimum_principal;
            $loan_product->default_principal = $request->default_principal;
            $loan_product->maximum_principal = $request->maximum_principal;
            $loan_product->minimum_loan_term = $request->minimum_loan_term;
            $loan_product->default_loan_term = $request->default_loan_term;
            $loan_product->maximum_loan_term = $request->maximum_loan_term;
            $loan_product->repayment_frequency = $request->repayment_frequency;
            $loan_product->repayment_frequency_type = $request->repayment_frequency_type;
            $loan_product->minimum_interest_rate = $request->minimum_interest_rate;
            $loan_product->default_interest_rate = $request->default_interest_rate;
            $loan_product->maximum_interest_rate = $request->maximum_interest_rate;
            $loan_product->interest_rate_type = $request->interest_rate_type;
            $loan_product->interest_method = $request->interest_method;
            $loan_product->grace_on_interest_charged = $request->grace_on_interest_charged;
            $loan_product->grace_on_principal = $request->grace_on_principal;
            $loan_product->grace_on_interest_payment = $request->grace_on_interest_payment;
            $loan_product->interest_method = $request->interest_method;
            $loan_product->armotization_method = $request->armotization_method;
            //$loan_product->interest_calculation_period_type = $request->interest_calculation_period_type;
            $loan_product->year_days = $request->year_days;
            $loan_product->month_days = $request->month_days;
            $loan_product->loan_transaction_strategy = $request->loan_transaction_strategy;
            $loan_product->lock_guarantee = $request->lock_guarantee;
            $loan_product->accounting_rule = $request->accounting_rule;
            $loan_product->allow_additional_charges = $request->allow_additional_charges;
            $loan_product->gl_account_fund_source_id = $request->gl_account_fund_source_id;
            $loan_product->gl_account_loan_portfolio_id = $request->gl_account_loan_portfolio_id;
            $loan_product->gl_account_receivable_interest_id = $request->gl_account_receivable_interest_id;
            $loan_product->gl_account_receivable_fee_id = $request->gl_account_receivable_fee_id;
            $loan_product->gl_account_receivable_penalty_id = $request->gl_account_receivable_penalty_id;
            $loan_product->gl_account_loan_over_payments_id = $request->gl_account_loan_over_payments_id;
            $loan_product->gl_account_suspended_income_id = $request->gl_account_suspended_income_id;
            $loan_product->gl_account_income_interest_id = $request->gl_account_income_interest_id;
            $loan_product->gl_account_income_fee_id = $request->gl_account_income_fee_id;
            $loan_product->gl_account_income_penalty_id = $request->gl_account_income_penalty_id;
            $loan_product->gl_account_income_recovery_id = $request->gl_account_income_recovery_id;
            $loan_product->gl_account_loans_written_off_id = $request->gl_account_loans_written_off_id;
            $loan_product->arrears_grace_days = $request->arrears_grace_days;
            $loan_product->save();
            LoanProductCharge::where('loan_product_id', $loan_product->id)->delete();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $loan_product_charge = new LoanProductCharge();
                    $loan_product_charge->loan_product_id = $loan_product->id;
                    $loan_product_charge->charge_id = $key;
                    $loan_product_charge->save();
                }
            }
            GeneralHelper::audit_trail("Update", "Loan Product", $loan_product->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/product/data');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('products.loan_products.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        LoanProduct::destroy($id);
        LoanProductCharge::where('loan_product_id', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Loan Product", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/product/data');
    }

    public function get_charge_detail($charge)
    {
        $json = [];
        $json["id"] = $charge->id;
        $json["name"] = $charge->name;
        if ($charge->charge_type == 'disbursement') {
            $json["collected_on"] = trans_choice('general.disbursement', 1);
        }
        if ($charge->charge_type == 'specified_due_date') {
            $json["collected_on"] = trans_choice('general.specified_due_date', 1);
        }
        if ($charge->charge_type == 'installment_fee') {
            $json["collected_on"] = trans_choice('general.installment_fee', 1);
        }
        if ($charge->charge_type == 'overdue_installment_fee') {
            $json["collected_on"] = trans_choice('general.overdue_installment_fee', 1);
        }
        if ($charge->charge_type == 'loan_rescheduling_fee') {
            $json["collected_on"] = trans_choice('general.loan_rescheduling_fee', 1);
        }
        if ($charge->charge_type == 'overdue_maturity') {
            $json["collected_on"] = trans_choice('general.overdue_maturity', 1);
        }
        if ($charge->charge_type == 'savings_activation') {
            $json["collected_on"] = trans_choice('general.savings_activation', 1);
        }
        if ($charge->charge_type == 'withdrawal_fee') {
            $json["collected_on"] = trans_choice('general.withdrawal_fee', 1);
        }
        if ($charge->charge_type == 'monthly_fee') {
            $json["collected_on"] = trans_choice('general.monthly_fee', 1);
        }
        if ($charge->charge_type == 'annual_fee') {
            $json["collected_on"] = trans_choice('general.annual_fee', 1);
        }
        $json["amount"] = $charge->amount;
        $json["override"] = $charge->override;
        if ($charge->charge_option == 'flat') {
            $json["charge_option"] = trans_choice('general.flat', 1);
        }
        if ($charge->charge_option == 'installment_principal_due') {
            $json["charge_option"] = " % " . trans_choice('general.installment_principal_due',
                    1);
        }
        if ($charge->charge_option == 'percentage') {
            $json["charge_option"] = " % " . trans_choice('general.amount',
                    1);
        }
        if ($charge->charge_option == 'installment_principal_interest_due') {
            $json["charge_option"] = " % " . trans_choice('general.installment_principal_interest_due',
                    1);
        }
        if ($charge->charge_option == 'installment_interest_due') {
            $json["charge_option"] = " % " . trans_choice('general.installment_interest_due',
                    1);
        }
        if ($charge->charge_option == 'total_due') {
            $json["charge_option"] = " % " . trans_choice('general.total',
                    1);
        }
        if ($charge->charge_option == 'original_principal') {
            $json["charge_option"] = " % " . trans_choice('general.original',
                    1) . " " . trans_choice('general.principal', 1);
        }

        return json_encode($json, JSON_UNESCAPED_SLASHES);
    }

    public function get_currency_charges($id)
    {
        $json = Charge::where('currency_id', $id)->where('active', 1)->where('charge_type', "loan")->get();
        return json_encode($json, JSON_UNESCAPED_SLASHES);
    }

}
