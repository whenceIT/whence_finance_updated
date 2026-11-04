<?php
namespace App\Http\Controllers\Artworks;
use App\Http\Controllers\Controller;
use App\Models\Artworks\LoanReferral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanReferralController extends Controller
{
    public function index(Request $request)
    {
        $referrals = LoanReferral::orderBy('created_at','desc')->paginate(20);
        return view('artworks.loan-referrals.index', compact('referrals'));
    }
    public function create()
    {
        $offices = \App\Models\Office::orderBy('name')->get();
        $users = \App\Models\User::orderBy('first_name')->get();
        return view('artworks.loan-referrals.create', compact('offices','users'));
    }
    public function store(Request $request)
    {
        $this->validate($request, ['client_name'=>'required|string','nrc_number'=>'nullable|string','referral_date'=>'required|date','branch_id'=>'nullable|integer']);
        $ref = 'LR-' . date('Y') . '-' . str_pad(LoanReferral::count()+1,6,'0',STR_PAD_LEFT);
        LoanReferral::create([
            'referral_reference'=>$ref,
            'branch_id'=>$request->branch_id,
            'client_name'=>$request->client_name,
            'nrc_number'=>$request->nrc_number,
            'contact_number'=>$request->contact_number,
            'referring_department'=>$request->referring_department,
            'referral_date'=>$request->referral_date,
            'referring_officer_id'=>$request->referring_officer_id,
            'loan_status'=>'Pending',
            'repayment_status'=>'Unpaid',
            'notes'=>$request->notes,
        ]);
        return redirect()->route('loan-referrals.index')->with('success','Referral registered.');
    }
    public function pending(){ $items = LoanReferral::where('repayment_status','Unpaid')->orderBy('created_at','desc')->paginate(20); return view('artworks.loan-referrals.pending', compact('items')); }
    public function due(){ $items = LoanReferral::where('profit_share','>',0)->where('repayment_status','Unpaid')->orderBy('created_at','desc')->paginate(20); return view('artworks.loan-referrals.due', compact('items')); }
    public function payments(){ $items = LoanReferral::where('amount_paid','>',0)->orderBy('updated_at','desc')->paginate(20); $all = LoanReferral::orderBy('referral_reference')->get(); return view('artworks.loan-referrals.payments', compact('items','all')); }
    public function reports(){ return view('artworks.loan-referrals.reports'); }
}



