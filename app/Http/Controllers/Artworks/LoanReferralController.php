<?php
namespace App\Http\Controllers\Artworks;
use App\Http\Controllers\Controller;
use App\Models\Artworks\LoanReferral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanReferralController extends Controller
{
    public function index(Request $request)
    {
        $referrals = LoanReferral::orderBy('created_at','desc')->paginate(20);
        return view('artworks.loan-referrals.index', compact('referrals'));
    }
    public function create()
    {
        return view('artworks.loan-referrals.create');
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

    public function searchClients(Request $request)
    {
        $q = trim($request->query('q', ''));
        $like = "%{$q}%";
        $clients = DB::select("
            SELECT id, first_name, middle_name, last_name, full_name, nrc_number, phone, mobile
            FROM clients
            WHERE deleted_at IS NULL
              AND (first_name LIKE ? OR last_name LIKE ? OR full_name LIKE ? OR nrc_number LIKE ?)
            ORDER BY nrc_number
            LIMIT 20
        ", [$like, $like, $like, $like]);

        return response()->json(array_map(function ($c) {
            $fullName = $c->full_name ?: ($c->first_name . ' ' . $c->last_name ?: $c->nrc_number);
            return [
                'id' => $c->id,
                'text' => $fullName . ' (' . $c->nrc_number . ')',
                'first_name' => $c->first_name,
                'last_name' => $c->last_name,
                'full_name' => $fullName,
                'nrc_number' => $c->nrc_number,
                'phone' => $c->phone ?: $c->mobile,
            ];
        }, $clients));
    }

    public function searchOffices(Request $request)
    {
        $q = trim($request->query('q', ''));
        $offices = \App\Models\Office::whereNull('deleted_at')
            ->where('name', 'LIKE', "%{$q}%")
            ->orderBy('name')
            ->take(50)
            ->get();
        return response()->json($offices->map(function ($o) {
            return [
                'id' => $o->id,
                'text' => $o->name,
            ];
        }));
    }

    public function searchUsers(Request $request)
    {
        $q = trim($request->query('q', ''));
        $users = \App\Models\User::whereNull('deleted_at')->where('blocked', 0)
            ->where(function ($query) use ($q) {
                $query->where('first_name', 'LIKE', "%{$q}%")
                    ->orWhere('last_name', 'LIKE', "%{$q}%")
                    ->orWhere('email', 'LIKE', "%{$q}%");
            })
            ->orderBy('first_name')
            ->take(50)
            ->get();
        return response()->json($users->map(function ($u) {
            return [
                'id' => $u->id,
                'text' => $u->first_name . ' ' . $u->last_name,
            ];
        }));
    }

    public function searchUsersByOffice(Request $request)
    {
        $officeId = $request->query('office_id', '');
        $users = \App\Models\User::whereNull('deleted_at')->where('blocked', 0)
            ->where('office_id', $officeId)
            ->orderBy('first_name')
            ->take(50)
            ->get();
        return response()->json($users->map(function ($u) {
            return [
                'id' => $u->id,
                'text' => $u->first_name . ' ' . $u->last_name,
            ];
        }));
    }
    public function pending(){ $items = LoanReferral::where('repayment_status','Unpaid')->orderBy('created_at','desc')->paginate(20); return view('artworks.loan-referrals.pending', compact('items')); }
    public function due(){ $items = LoanReferral::where('profit_share','>',0)->where('repayment_status','Unpaid')->orderBy('created_at','desc')->paginate(20); return view('artworks.loan-referrals.due', compact('items')); }
    public function payments()
    {
        $items = LoanReferral::where('amount_paid','>',0)->orderBy('updated_at','desc')->paginate(20);
        $all = LoanReferral::orderBy('referral_reference')->get();
        $newLoans = DB::table('loans')
            ->join('clients', 'clients.id', '=', 'loans.client_id')
            ->join('creative_loan_referrals as referrals', 'referrals.nrc_number', '=', 'clients.nrc_number')
            ->whereIn('referrals.id', $items->getCollection()->pluck('id'))
            ->whereColumn('loans.created_at', '>', 'referrals.created_at')
            ->whereNull('loans.deleted_at')
            ->whereNull('clients.deleted_at')
            ->select(
                'referrals.id as referral_id',
                'loans.id',
                'loans.account_number',
                'loans.status',
                'loans.approved_amount',
                'loans.principal',
                'loans.created_at'
            )
            ->orderByDesc('loans.created_at')
            ->get()
            ->groupBy('referral_id');

        $items->getCollection()->transform(function ($referral) use ($newLoans) {
            $referral->new_loans = $newLoans->get($referral->id, collect());
            return $referral;
        });

        return view('artworks.loan-referrals.payments', compact('items','all'));
    }
    public function recordPayment(Request $request){ $this->validate($request,['referral_id'=>'required|integer','amount'=>'required|numeric|min:0.01']); $r=LoanReferral::findOrFail($request->referral_id); $r->profit_share=$request->amount; if($r->amount_paid>=$r->profit_share && $r->profit_share>0){ $r->repayment_status='Paid'; } $r->save(); return redirect()->back()->with('success','Payment recorded.'); }
    public function reports(){ return view('artworks.loan-referrals.reports'); }
}


