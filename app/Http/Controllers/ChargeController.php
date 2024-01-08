<?php

namespace App\Http\Controllers;


use App\Helpers\GeneralHelper;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Charge;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class ChargeController extends Controller
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
        if (!Sentinel::hasAccess('products.charges.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Charge::all();

        return view('charge.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('products.charges.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('charge.create' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('products.charges.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $charge = new Charge();
        $charge->created_by_id = Sentinel::getUser()->id;
        $charge->name = $request->name;
        $charge->product = $request->product;
        $charge->currency_id = $request->currency_id;
        if ($request->product == "loan") {
            $charge->charge_type = $request->loan_charge_type;
            $charge->charge_option = $request->loan_charge_option;
        }
        if ($request->product == "savings") {
            $charge->charge_type = $request->savings_charge_type;
            $charge->charge_option = $request->savings_charge_option;
        }
        if ($request->product == "client") {
            $charge->charge_type = $request->client_charge_type;
            $charge->charge_option = $request->client_charge_option;
        }

        if ($request->charge_frequency == "1") {
            $charge->charge_frequency = $request->charge_frequency;
            $charge->charge_frequency_type = $request->charge_frequency_type;
            $charge->charge_frequency_amount = $request->charge_frequency_amount;
        }
        $charge->active = $request->active;
        $charge->override = $request->override;
        $charge->gl_account_income_id = $request->gl_account_income_id;
        $charge->amount = $request->amount;
        $charge->save();
        GeneralHelper::audit_trail("Create", "Charges", $charge->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('charge/data');
    }


    public function show($charge)
    {
        if (!Sentinel::hasAccess('products.charges.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
    }


    public function edit($charge)
    {
        if (!Sentinel::hasAccess('products.charges.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('charge.edit', compact('charge'));
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
        if (!Sentinel::hasAccess('products.charges.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $charge = Charge::find($id);
        $charge->name = $request->name;
        if ($charge->product == "loan") {
            $charge->charge_type = $request->loan_charge_type;
            $charge->charge_option = $request->loan_charge_option;
        }
        if ($charge->product == "savings") {
            $charge->charge_type = $request->savings_charge_type;
            $charge->charge_option = $request->savings_charge_option;
        }
        if ($charge->product == "client") {
            $charge->charge_type = $request->client_charge_type;
            $charge->charge_option = $request->client_charge_option;
        }
        if ($request->charge_frequency == "1") {
            $charge->charge_frequency = $request->charge_frequency;
            $charge->charge_frequency_type = $request->charge_frequency_type;
            $charge->charge_frequency_amount = $request->charge_frequency_amount;
        }
        $charge->active = $request->active;
        $charge->override = $request->override;
        $charge->gl_account_income_id = $request->gl_account_income_id;
        $charge->amount = $request->amount;
        $charge->save();
        GeneralHelper::audit_trail("Update", "Charges", $charge->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('charge/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('products.charges.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Charge::destroy($id);
        GeneralHelper::audit_trail("Delete", "Charges", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('charge/data');
    }

}
