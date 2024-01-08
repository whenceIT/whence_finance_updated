<?php

namespace App\Http\Controllers;

use App\Models\GlAccount;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class GlAccountController extends Controller
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
        if (!Sentinel::hasAccess('accounting.gl_accounts.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = GlAccount::all();
        return view('gl_account.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('gl_account.create' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required',
            'gl_code' => 'required|unique:gl_accounts',
            'account_type' => 'required'
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
            $gl_account = new GlAccount();
            $gl_account->name = $request->name;
            $gl_account->parent_id = $request->parent_id;
            $gl_account->gl_code = $request->gl_code;
            $gl_account->account_type = $request->account_type;
            $gl_account->manual_entries = $request->manual_entries;
            $gl_account->active = $request->active;
            $gl_account->notes = $request->notes;
            $gl_account->save();
            Flash::success(trans('general.successfully_saved'));
            return redirect('accounting/gl_account/data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
    }


    public function edit($gl_account)
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return View::make('gl_account.edit', compact('gl_account'))->render();
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
        if (!Sentinel::hasAccess('accounting.gl_accounts.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $gl_account = GlAccount::find($id);
        $gl_account->name = $request->name;
        $gl_account->parent_id = $request->parent_id;
        $gl_account->gl_code = $request->gl_code;
        $gl_account->account_type = $request->account_type;
        $gl_account->manual_entries = $request->manual_entries;
        $gl_account->active = $request->active;
        $gl_account->notes = $request->notes;
        $gl_account->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('accounting/gl_account/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        GlAccount::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('accounting/gl_account/data');
    }
}
