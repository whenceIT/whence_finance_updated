<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Models\ClientRelationship;
use App\Models\ClientRelationshipUser;
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

class ClientRelationshipController extends Controller
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
        $data = ClientRelationship::all();
        return view('client_relationship.data', compact('data'));
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
        return view('client_relationship.create' );
    }

  
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('products.client_relationships.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client_relationship = new ClientRelationship();
        $client_relationship->name = $request->name;
        $client_relationship->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('client_relationship/data');
    }


    public function show($client_relationship)
    {
        if (!Sentinel::hasAccess('products.client_relationships.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('client_relationship.show', compact('client_relationship'));
    }


    public function edit($client_relationship)
    {
        if (!Sentinel::hasAccess('products.client_relationships.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('client_relationship.edit', compact('client_relationship'));
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
        if (!Sentinel::hasAccess('products.client_relationships.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client_relationship = ClientRelationship::find($id);
        $client_relationship->name = $request->name;
        $client_relationship->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('client_relationship/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('products.client_relationships.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        ClientRelationship::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('client_relationship/data');
    }

}
