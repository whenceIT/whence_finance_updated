<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\User;
use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\Client;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Laracasts\Flash\Flash;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        if (!Sentinel::hasAccess('audit_trail')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $MonthsAgo = Carbon::now()->subMonths(1);

        $data = AuditTrail::with('user')->where('created_at', '>=', $MonthsAgo)->paginate(20);
        $users = User::all();
        return view('audit_trail.data', compact('data', 'users'));
    }

    public function user_audit($user_id)
    {
        if (!Sentinel::hasAccess('audit_trail')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $data = AuditTrail::with('user')->where('user_id', $user_id)->paginate(20);
        return view('audit_trail.user', compact('data'));
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

    public function quickAudit(Request $request)
    {
        if (!Sentinel::hasAccess('audit_trail')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $user_id = $request->user_id;
        $loans = Loan::where('loan_officer_id', $user_id)->with('client', 'transactions')->orderBy('created_at', 'desc')->get();
        return view('audit_trail.quick_audit', compact('loans'));
    }

}
