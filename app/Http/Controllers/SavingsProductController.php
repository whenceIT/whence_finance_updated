<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\Charge;
use App\Models\SavingsProduct;
use App\Models\SavingsProductCharge;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class SavingsProductController extends Controller
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
        if (!Sentinel::hasAccess('products.savings_products.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = SavingsProduct::all();

        return view('savings_product.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('products.savings_products.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $charges = Charge::where('product', 'savings')->where('active', 1)->get();

        return view('savings_product.create',
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
        if (!Sentinel::hasAccess('products.savings_products.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required',
            'short_name' => 'required',
            'description' => 'required',
            'decimals' => 'required',
            'currency_id' => 'required',
            'interest_rate' => 'required',
            'allow_overdraft' => 'required',
            'minimum_balance' => 'required',
            'interest_posting_period' => 'required',

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
            $savings_product = new SavingsProduct();
            $savings_product->created_by_id = Sentinel::getUser()->id;
            $savings_product->name = $request->name;
            $savings_product->short_name = $request->short_name;
            $savings_product->description = $request->description;
            $savings_product->currency_id = $request->currency_id;
            $savings_product->decimals = $request->decimals;
            $savings_product->interest_rate = $request->interest_rate;
            $savings_product->allow_overdraft = $request->allow_overdraft;
            $savings_product->interest_calculation_type = $request->interest_calculation_type;
            $savings_product->minimum_balance = $request->minimum_balance;
            $savings_product->interest_posting_period = $request->interest_posting_period;
            $savings_product->interest_compounding_period = $request->interest_compounding_period;
            $savings_product->opening_balance = $request->opening_balance;
            $savings_product->accounting_rule = $request->accounting_rule;
            $savings_product->gl_account_savings_reference_id = $request->gl_account_savings_reference_id;
            $savings_product->gl_account_overdraft_portfolio_id = $request->gl_account_overdraft_portfolio_id;
            $savings_product->gl_account_savings_control_id = $request->gl_account_savings_control_id;
            $savings_product->gl_account_interest_on_savings_id = $request->gl_account_interest_on_savings_id;
            $savings_product->gl_account_savings_written_off_id = $request->gl_account_savings_written_off_id;
            $savings_product->gl_account_income_interest_id = $request->gl_account_income_interest_id;
            $savings_product->gl_account_income_fee_id = $request->gl_account_income_fee_id;
            $savings_product->gl_account_income_penalty_id = $request->gl_account_income_penalty_id;
            $savings_product->save();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $savings_product_charge = new SavingsProductCharge();
                    $savings_product_charge->savings_product_id = $savings_product->id;
                    $savings_product_charge->charge_id = $key;
                    $savings_product_charge->save();
                }
            }
            GeneralHelper::audit_trail("Create", "Savings Product", $savings_product->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/product/data');
        }
    }


    public function show($savings_product)
    {
        if (!Sentinel::hasAccess('products.savings_products.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $charges = array();
        foreach (Charge::where('product', 'loan')->get() as $key) {
            $charges[$key->id] = $key->name;
        }
        return view('savings_product.show', compact('savings_product', 'charges'));
    }


    public function edit($savings_product)
    {
        if (!Sentinel::hasAccess('products.savings_products.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('savings_product.edit',
            compact('savings_product'));
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
        if (!Sentinel::hasAccess('products.savings_products.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required',
            'short_name' => 'required',
            'description' => 'required',
            'decimals' => 'required',
            'interest_rate' => 'required',
            'allow_overdraft' => 'required',
            'minimum_balance' => 'required',
            'interest_posting_period' => 'required',
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
            $savings_product = SavingsProduct::find($id);
            $savings_product->name = $request->name;
            $savings_product->short_name = $request->short_name;
            $savings_product->description = $request->description;
            $savings_product->decimals = $request->decimals;
            $savings_product->interest_rate = $request->interest_rate;
            $savings_product->allow_overdraft = $request->allow_overdraft;
            $savings_product->minimum_balance = $request->minimum_balance;
            $savings_product->interest_posting_period = $request->interest_posting_period;
            $savings_product->interest_compounding_period = $request->interest_compounding_period;
            $savings_product->interest_calculation_type = $request->interest_calculation_type;
            $savings_product->opening_balance = $request->opening_balance;
            $savings_product->accounting_rule = $request->accounting_rule;
            $savings_product->gl_account_savings_reference_id = $request->gl_account_savings_reference_id;
            $savings_product->gl_account_overdraft_portfolio_id = $request->gl_account_overdraft_portfolio_id;
            $savings_product->gl_account_savings_control_id = $request->gl_account_savings_control_id;
            $savings_product->gl_account_interest_on_savings_id = $request->gl_account_interest_on_savings_id;
            $savings_product->gl_account_savings_written_off_id = $request->gl_account_savings_written_off_id;
            $savings_product->gl_account_income_interest_id = $request->gl_account_income_interest_id;
            $savings_product->gl_account_income_fee_id = $request->gl_account_income_fee_id;
            $savings_product->gl_account_income_penalty_id = $request->gl_account_income_penalty_id;
            $savings_product->save();
            SavingsProductCharge::where('savings_product_id', $savings_product->id)->delete();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $savings_product_charge = new SavingsProductCharge();
                    $savings_product_charge->savings_product_id = $savings_product->id;
                    $savings_product_charge->charge_id = $key;
                    $savings_product_charge->save();
                }
            }
            GeneralHelper::audit_trail("Update", "Savings Product", $savings_product->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/product/data');
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
        if (!Sentinel::hasAccess('products.savings_products.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        SavingsProduct::destroy($id);
        SavingsProductCharge::where('savings_product_id', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Savings Product", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('savings/product/data');
    }

    public function get_charge_detail($charge)
    {
        $json = [];
        $json["id"] = $charge->id;
        $json["name"] = $charge->name;
        if ($charge->charge_type == 'savings_activation') {
            $json["collected_on"] = trans_choice('general.savings_activation', 1);
        }
        if ($charge->charge_type == 'specified_due_date') {
            $json["collected_on"] = trans_choice('general.specified_due_date', 1);
        }
        if ($charge->charge_type == 'withdrawal_fee') {
            $json["collected_on"] = trans_choice('general.withdrawal_fee', 1);
        }
        if ($charge->charge_type == 'annual_fee') {
            $json["collected_on"] = trans_choice('general.annual_fee', 1);
        }
        if ($charge->charge_type == 'monthly_fee') {
            $json["collected_on"] = trans_choice('general.monthly_fee', 1);
        }

        $json["amount"] = $charge->amount;
        $json["override"] = $charge->override;
        if ($charge->charge_option == 'flat') {
            $json["charge_option"] =  trans_choice('general.flat', 1);
        }
        if ($charge->charge_option == 'percentage') {
             $json["charge_option"] = " % " . trans_choice('general.amount',
                    1);
        }

        return json_encode($json, JSON_UNESCAPED_SLASHES);
    }

    public function get_currency_charges($id)
    {
        $json = Charge::where('currency_id', $id)->where('active', 1)->where('charge_type', "savings")->get();
        return json_encode($json, JSON_UNESCAPED_SLASHES);
    }

}
