<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\GeneralHelper;
use App\Models\Office;
use App\Models\OfficeUser;
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

class OfficeController extends Controller
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
        if (!Sentinel::hasAccess('offices.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Office::all();
        return view('office.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('offices.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('office.create' );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('offices.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $office = new Office();
        $office->name = $request->name;
        $office->parent_id = $request->parent_id;
        $office->external_id = $request->external_id;
        $office->opening_date = $request->opening_date;
        $office->save();
        GeneralHelper::audit_trail("Create", "Branches", $office->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('office/data');
    }


    public function show($office)
    {
        if (!Sentinel::hasAccess('offices.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('office.show', compact('office', 'users'));
    }


    public function edit($office)
    {
        if (!Sentinel::hasAccess('offices.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('office.edit', compact('office'));
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
        if (!Sentinel::hasAccess('offices.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $office = Office::find($id);
        $office->name = $request->name;
        $office->parent_id = $request->parent_id;
        $office->external_id = $request->external_id;
        $office->opening_date = $request->opening_date;
        $office->notes = $request->notes;
        $office->save();
        GeneralHelper::audit_trail("Update", "Branches", $office->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('office/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('offices.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $office = Office::find($id);
        if ($office->default_office == 1) {
            Flash::warning("You cannot delete default office. Its needed to keep things working well.");
            return redirect()->back();
        }
        Office::destroy($id);
        GeneralHelper::audit_trail("Delete", "Branches", $office->id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('office/data');
    }


}
