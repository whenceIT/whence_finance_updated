<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class CurrencyController extends Controller
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
        if (!Sentinel::hasAccess('products.currencies.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Currency::all();
        return view('currency.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('products.currencies.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('currency.create' );
    }


    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('products.currencies.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $currency = new Currency();
        $currency->name = $request->name;
        $currency->code = $request->code;
        $currency->symbol = $request->symbol;
        $currency->decimals = $request->decimals;
        $currency->active = $request->active;
        $currency->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('currency/data');
    }


    public function show($currency)
    {

        return view('currency.show', compact('currency'));
    }


    public function edit($currency)
    {
        if (!Sentinel::hasAccess('products.currencies.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('currency.edit', compact('currency'));
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
        if (!Sentinel::hasAccess('products.currencies.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $currency = Currency::find($id);
        $currency->name = $request->name;
        $currency->code = $request->code;
        $currency->symbol = $request->symbol;
        $currency->decimals = $request->decimals;
        $currency->active = $request->active;
        $currency->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('currency/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('products.currencies.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Currency::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('currency/data');
    }

}
