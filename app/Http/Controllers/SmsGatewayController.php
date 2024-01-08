<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\SmsGateway;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class SmsGatewayController extends Controller
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
        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = SmsGateway::all();
        return view('sms_gateway.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('sms_gateway.create' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $sms_gateway = new SmsGateway();
        $sms_gateway->name = $request->name;
        $sms_gateway->to_name = $request->to_name;
        $sms_gateway->msg_name = $request->msg_name;
        $sms_gateway->url = $request->url;
        $sms_gateway->notes = $request->notes;
        $sms_gateway->save();
        GeneralHelper::audit_trail("Create", "SMS Gateways", $sms_gateway->id);
        Flash::success("Successfully Saved");
        return redirect('sms_gateway/data');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($sms_gateway)
    {
        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('sms_gateway.edit', compact('sms_gateway'));
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
        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $sms_gateway = SmsGateway::find($id);
        $sms_gateway->name = $request->name;
        $sms_gateway->to_name = $request->to_name;
        $sms_gateway->msg_name = $request->msg_name;
        $sms_gateway->url = $request->url;
        $sms_gateway->notes = $request->notes;
        $sms_gateway->save();
        GeneralHelper::audit_trail("Update", "SMS Gateways", $sms_gateway->id);
        Flash::success("Successfully Saved");
        return redirect('sms_gateway/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        SmsGateway::destroy($id);
        GeneralHelper::audit_trail("Delete", "SMS Gateways", $id);
        Flash::success("Successfully Deleted");
        return redirect('sms_gateway/data');
    }
}
