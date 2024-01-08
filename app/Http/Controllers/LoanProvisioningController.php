<?php

namespace App\Http\Controllers;

use App\Models\LoanProvisioningCriteria;

use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class LoanProvisioningController extends Controller
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
       
        $data = LoanProvisioningCriteria::all();
        return view('loan_provisioning.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //get custom fields
        return view('loan_provisioning.create' );
    }


    public function store(Request $request)
    {
        $loan_provisioning = new LoanProvisioningCriteria();
        $loan_provisioning->name = $request->name;
        $loan_provisioning->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan_provisioning/data');
    }


    public function show($loan_provisioning)
    {

        return view('loan_provisioning.show', compact('loan_provisioning'));
    }


    public function edit($loan_provisioning)
    {
        return view('loan_provisioning.edit', compact('loan_provisioning'));
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
        if (!Sentinel::hasAccess('products.loan_provisioning_criteria.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan_provisioning = LoanProvisioningCriteria::find($id);
        $loan_provisioning->name = $request->name;
        $loan_provisioning->min = $request->min;
        $loan_provisioning->max = $request->max;
        $loan_provisioning->percentage = $request->percentage;
        $loan_provisioning->gl_account_liability_id = $request->gl_account_liability_id;
        $loan_provisioning->gl_account_expense_id = $request->gl_account_expense_id;
        $loan_provisioning->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan_provisioning/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('products.loan_provisioning_criteria.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        /*LoanProvisioningCriteria::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan_provisioning/data');*/
    }

}
