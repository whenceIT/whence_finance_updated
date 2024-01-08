<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Laracasts\Flash\Flash;

class AuditTrailController extends Controller
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
        if (!Sentinel::hasAccess('audit_trail')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = AuditTrail::all();
        return view('audit_trail.data', compact('data'));
    }


    public function delete($id)
    {
        if (!Sentinel::hasAccess('audit_trail')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        AuditTrail::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('audit_trail/data');
    }

}
