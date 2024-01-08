<?php

namespace App\Http\Controllers;

use App\Models\BlacklistReason;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class BlacklistReasonsController extends Controller
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
        if (!Sentinel::hasAccess('products.client_relationships.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = BlacklistReason::all();
        return view('blacklist_reason.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('products.client_relationships.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('blacklist_reason.create' );
    }

  
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('products.client_relationships.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $reason = new BlacklistReason();
        $reason->name = $request->name;
        $reason->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('blacklist_reason/data');
    }


    public function show(BlacklistReason $reason)
    {
        if (!Sentinel::hasAccess('products.client_relationships.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('blacklist_reason.show', compact('reason'));
    }


    public function edit(BlacklistReason $reason)
    {
        if (!Sentinel::hasAccess('products.client_relationships.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('blacklist_reason.edit', compact('reason'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  BlacklistReason $reason
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BlacklistReason $reason)
    {
        if (!Sentinel::hasAccess('products.client_relationships.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $reason->name = $request->name;
        $reason->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('blacklist_reason/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  BlacklistReason $reason
     * @return \Illuminate\Http\Response
     */
    public function delete(BlacklistReason $reason)
    {
        if (!Sentinel::hasAccess('products.client_relationships.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $reason->delete();
        Flash::success(trans('general.successfully_deleted'));
        return redirect('blacklist_reason/data');
    }

}
