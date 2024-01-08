<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Models\Fund;
use App\Models\FundUser;
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

class FundController extends Controller
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
        if (!Sentinel::hasAccess('products.funds.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Fund::all();
        return view('fund.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('products.funds.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('fund.create' );
    }

  
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('products.funds.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $fund = new Fund();
        $fund->name = $request->name;
        $fund->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('fund/data');
    }


    public function show($fund)
    {
        if (!Sentinel::hasAccess('products.funds.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('fund.show', compact('fund'));
    }


    public function edit($fund)
    {
        if (!Sentinel::hasAccess('products.funds.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('fund.edit', compact('fund'));
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
        if (!Sentinel::hasAccess('products.funds.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $fund = Fund::find($id);
        $fund->name = $request->name;
        $fund->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('fund/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('products.funds.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Fund::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('fund/data');
    }

}
