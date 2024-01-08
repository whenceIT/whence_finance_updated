<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
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

class ExpenseTypeController extends Controller
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
        if (!Sentinel::hasAccess('expenses.types.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = ExpenseType::all();
        return view('expense_type.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('expenses.types.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('expense_type.create' );
    }


    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('expenses.types.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $expense_type = new ExpenseType();
        $expense_type->name = $request->name;
        $expense_type->gl_account_asset_id = $request->gl_account_asset_id;
        $expense_type->gl_account_expense_id = $request->gl_account_expense_id;
        $expense_type->notes = $request->notes;
        $expense_type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/type/data');
    }


    public function show($expense_type)
    {
        if (!Sentinel::hasAccess('expenses.types.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('expense_type.show', compact('expense_type'));
    }


    public function edit($expense_type)
    {
        if (!Sentinel::hasAccess('expenses.types.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('expense_type.edit', compact('expense_type'));
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
        if (!Sentinel::hasAccess('expenses.types.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $expense_type = ExpenseType::find($id);
        $expense_type->name = $request->name;
        $expense_type->gl_account_asset_id = $request->gl_account_asset_id;
        $expense_type->gl_account_expense_id = $request->gl_account_expense_id;
        $expense_type->notes = $request->notes;
        $expense_type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/type/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('expenses.types.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        ExpenseType::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();
    }

}
