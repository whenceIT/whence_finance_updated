<?php

namespace App\Http\Controllers;

use App\Models\OtherIncomeType;
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

class OtherIncomeTypeController extends Controller
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
        if (!Sentinel::hasAccess('other_income.types.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = OtherIncomeType::all();
        return view('other_income_type.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('other_income.types.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('other_income_type.create' );
    }


    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('other_income.types.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $other_income_type = new OtherIncomeType();
        $other_income_type->name = $request->name;
        $other_income_type->gl_account_asset_id = $request->gl_account_asset_id;
        $other_income_type->gl_account_income_id = $request->gl_account_income_id;
        $other_income_type->notes = $request->notes;
        $other_income_type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('other_income/type/data');
    }


    public function show($other_income_type)
    {
        if (!Sentinel::hasAccess('other_income.types.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('other_income_type.show', compact('other_income_type'));
    }


    public function edit($other_income_type)
    {
        if (!Sentinel::hasAccess('other_income.types.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('other_income_type.edit', compact('other_income_type'));
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
        if (!Sentinel::hasAccess('other_income.types.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $other_income_type = OtherIncomeType::find($id);
        $other_income_type->name = $request->name;
        $other_income_type->gl_account_asset_id = $request->gl_account_asset_id;
        $other_income_type->gl_account_income_id = $request->gl_account_income_id;
        $other_income_type->notes = $request->notes;
        $other_income_type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('other_income/type/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('other_income.types.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        OtherIncomeType::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();
    }

}
