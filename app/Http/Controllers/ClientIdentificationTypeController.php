<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Models\ClientIdentificationType;
use App\Models\ClientIdentificationTypeUser;
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

class ClientIdentificationTypeController extends Controller
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
        if (!Sentinel::hasAccess('products.client_identification_types.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = ClientIdentificationType::all();
        return view('client_identification_type.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('products.client_identification_types.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        return view('client_identification_type.create' );
    }

  
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('products.client_identification_types.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client_identification_type = new ClientIdentificationType();
        $client_identification_type->name = $request->name;
        $client_identification_type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('client_identification_type/data');
    }


    public function show($client_identification_type)
    {
        if (!Sentinel::hasAccess('products.client_identification_types.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('client_identification_type.show', compact('client_identification_type'));
    }


    public function edit($client_identification_type)
    {
        if (!Sentinel::hasAccess('products.client_identification_types.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('client_identification_type.edit', compact('client_identification_type'));
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
        if (!Sentinel::hasAccess('products.client_identification_types.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client_identification_type = ClientIdentificationType::find($id);
        $client_identification_type->name = $request->name;
        $client_identification_type->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('client_identification_type/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('products.client_identification_types.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        ClientIdentificationType::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('client_identification_type/data');
    }

}
