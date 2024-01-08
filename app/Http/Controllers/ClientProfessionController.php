<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Models\ClientProfession;
use App\Models\ClientProfessionUser;
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

class ClientProfessionController extends Controller
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
        $data = ClientProfession::all();
        return view('client_profession.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        //get custom fields
        return view('client_profession.create' );
    }

  
    public function store(Request $request)
    {
        $client_profession = new ClientProfession();
        $client_profession->name = $request->name;
        $client_profession->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('client_profession/data');
    }


    public function show($client_profession)
    {

        return view('client_profession.show', compact('client_profession'));
    }


    public function edit($client_profession)
    {
        return view('client_profession.edit', compact('client_profession'));
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
        $client_profession = ClientProfession::find($id);
        $client_profession->name = $request->name;
        $client_profession->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('client_profession/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {

        ClientProfession::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('client_profession/data');
    }

}
