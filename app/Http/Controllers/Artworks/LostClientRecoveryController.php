<?php
namespace App\Http\Controllers\Artworks;
use App\Http\Controllers\Controller;
use App\Models\Artworks\LostClientRecovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LostClientRecoveryController extends Controller
{
    public function index(Request $request)
    {
        $recoveries = LostClientRecovery::orderBy('created_at','desc')->paginate(20);
        return view('artworks.lost-client-recoveries.index', compact('recoveries'));
    }
    public function create()
    {
        $offices = \App\Models\Office::orderBy('name')->get();
        $users = \App\Models\User::orderBy('first_name')->get();
        return view('artworks.lost-client-recoveries.create', compact('offices','users'));
    }
    public function store(Request $request)
    {
        $this->validate($request, ['client_name'=>'required|string','recovery_date'=>'required|date']);
        $ref = 'LCR-' . date('Y') . '-' . str_pad(LostClientRecovery::count()+1,6,'0',STR_PAD_LEFT);
        LostClientRecovery::create([
            'recovery_reference'=>$ref,
            'branch_id'=>$request->branch_id,
            'client_name'=>$request->client_name,
            'nrc_number'=>$request->nrc_number,
            'contact_number'=>$request->contact_number,
            'recovery_date'=>$request->recovery_date,
            'responsible_staff_id'=>$request->responsible_staff_id,
            'repayment_status'=>'Unpaid',
            'notes'=>$request->notes,
        ]);
        return redirect()->route('lost-client-recoveries.index')->with('success','Recovery registered.');
    }
    public function repayment(){ return view('artworks.lost-client-recoveries.repayment'); }
    public function profitShare(){ return view('artworks.lost-client-recoveries.profit-share'); }
    public function payments(){ return view('artworks.lost-client-recoveries.payments'); }
    public function reports(){ return view('artworks.lost-client-recoveries.reports'); }
}
