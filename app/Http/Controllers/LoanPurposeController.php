<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Models\LoanPurpose;
use App\Models\LoanPurposeUser;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class LoanPurposeController extends Controller
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
        if (!Sentinel::hasAccess('products.loan_purposes.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = LoanPurpose::all();
        return view('loan_purpose.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('products.loan_purposes.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('loan_purpose.create' );
    }

  
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('products.loan_purposes.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan_purpose = new LoanPurpose();
        $loan_purpose->name = $request->name;
        $loan_purpose->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan_purpose/data');
    }


    public function show($loan_purpose)
    {
        if (!Sentinel::hasAccess('products.loan_purposes.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('loan_purpose.show', compact('loan_purpose'));
    }


    public function edit($loan_purpose)
    {
        if (!Sentinel::hasAccess('products.loan_purposes.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('loan_purpose.edit', compact('loan_purpose'));
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
        if (!Sentinel::hasAccess('products.loan_purposes.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan_purpose = LoanPurpose::find($id);
        $loan_purpose->name = $request->name;
        $loan_purpose->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan_purpose/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('products.loan_purposes.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        LoanPurpose::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan_purpose/data');
    }

}
