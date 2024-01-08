<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Models\CollateralType;
use App\Models\CollateralTypeUser;
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

class CollateralTypeController extends Controller
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
        if (!Sentinel::hasAccess('products.collateral_types.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = CollateralType::all();
        return view('collateral_type.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('products.collateral_types.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('collateral_type.create' );
    }

  
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('products.collateral_types.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $collateral_type = new CollateralType();
        $collateral_type->name = $request->name;
        $collateral_type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('collateral_type/data');
    }


    public function show($collateral_type)
    {
        if (!Sentinel::hasAccess('products.collateral_types.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('collateral_type.show', compact('collateral_type'));
    }


    public function edit($collateral_type)
    {
        if (!Sentinel::hasAccess('products.collateral_types.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('collateral_type.edit', compact('collateral_type'));
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
        if (!Sentinel::hasAccess('products.collateral_types.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $collateral_type = CollateralType::find($id);
        $collateral_type->name = $request->name;
        $collateral_type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('collateral_type/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('products.collateral_types.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        CollateralType::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('collateral_type/data');
    }

}
