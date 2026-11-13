<?php
namespace App\Http\Controllers\Artworks;
use App\Http\Controllers\Controller;
use App\Models\Artworks\LostClientRecovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LostClientRecoveryController extends Controller
{
    public function index(Request $request)
    {
        $recoveries = LostClientRecovery::orderBy('created_at','desc')->paginate(20);
        return view('artworks.lost-client-recoveries.index', compact('recoveries'));
    }
    public function create()
    {
        return view('artworks.lost-client-recoveries.create');
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

    public function searchLoans(Request $request)
    {
        $q = trim($request->query('q', ''));
        $like = "%{$q}%";
        $loans = DB::select("
            SELECT l.id, l.account_number, l.principal, l.status,
                   c.first_name, c.last_name, c.full_name, c.nrc_number
            FROM loans l
            LEFT JOIN clients c ON c.id = l.client_id
            WHERE l.deleted_at IS NULL
              AND (l.id LIKE ? OR c.first_name LIKE ? OR c.last_name LIKE ? OR c.full_name LIKE ? OR c.nrc_number LIKE ?)
            ORDER BY l.account_number
            LIMIT 20
        ", [$like, $like, $like, $like, $like]);

        return response()->json(array_map(function ($l) {
            $name = $l->full_name ?: ($l->first_name . ' ' . $l->last_name ?: '');
            return [
                'id' => $l->id,
                'text' => $l->account_number . ' - ' . $name . ' (' . $l->nrc_number . ')',
                'account_number' => $l->account_number,
                'principal' => $l->principal,
                'status' => $l->status,
                'client_name' => $name,
                'nrc_number' => $l->nrc_number,
            ];
        }, $loans));
    }
    public function store(Request $request)
    {
        $this->validate($request, ['client_name'=>'required|string','recovery_date'=>'required|date']);
        $ref = 'LCR-' . date('Y') . '-' . str_pad(LostClientRecovery::count()+1,6,'0',STR_PAD_LEFT);
        LostClientRecovery::create([
            'recovery_reference'=>$ref,
            'branch_id'=>$request->branch_id,
            'client_id'=>$request->client_id,
            'client_name'=>$request->client_name,
            'nrc_number'=>$request->nrc_number,
            'contact_number'=>$request->contact_number,
            'recovery_date'=>$request->recovery_date,
            'responsible_staff_id'=>$request->responsible_staff_id,
            'loan_id'=>$request->loan_id,
            'repayment_status'=>'Unpaid',
            'notes'=>$request->notes,
        ]);
        return redirect()->route('lost-client-recoveries.index')->with('success','Recovery registered.');
    }
    public function repayment(Request $request)
    {
        $search = trim($request->query('search', ''));
        $status = $request->query('status', '');

        $query = DB::table('creative_lost_client_recoveries as r')
            ->leftJoin('loans as l', 'r.loan_id', '=', 'l.id')
            ->leftJoin('offices as o', 'r.branch_id', '=', 'o.id')
            ->select(
                'r.id',
                'r.recovery_reference',
                'r.client_name',
                'r.nrc_number',
                'r.contact_number',
                'r.recovery_date',
                'r.repayment_status',
                'r.amount_paid',
                'r.notes',
                'r.created_at',
                'l.account_number as loan_account',
                'l.principal as loan_principal',
                'o.name as branch_name'
            );

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function ($q) use ($like) {
                $q->where('r.client_name', 'LIKE', $like)
                    ->orWhere('r.nrc_number', 'LIKE', $like)
                    ->orWhere('r.recovery_reference', 'LIKE', $like)
                    ->orWhere('l.account_number', 'LIKE', $like);
            });
        }

        if ($status !== '') {
            $query->where('r.repayment_status', $status);
        }

        $recoveries = $query->orderBy('r.created_at', 'desc')->paginate(20);
        $recoveries->appends($request->only('search', 'status'));

        $statuses = ['Unpaid', 'Partial', 'Paid'];

        return view('artworks.lost-client-recoveries.repayment', compact('recoveries', 'search', 'status', 'statuses'));
    }

    public function profitShare(Request $request)
    {
        $search = trim($request->query('search', ''));

        $query = DB::table('creative_lost_client_recoveries as r')
            ->leftJoin('loans as l', 'r.loan_id', '=', 'l.id')
            ->leftJoin('offices as o', 'r.branch_id', '=', 'o.id')
            ->select(
                'r.id',
                'r.recovery_reference',
                'r.client_name',
                'r.nrc_number',
                'r.recovery_date',
                'r.repayment_status',
                'r.profit_share',
                'r.amount_paid',
                'r.created_at',
                'l.account_number as loan_account',
                'l.principal as loan_principal',
                'o.name as branch_name',
                DB::raw('(SELECT COALESCE(SUM(s.interest_paid), 0) FROM loan_repayment_schedules s WHERE s.loan_id = l.id) as total_interest_paid')
            )
            ->whereNotNull('r.loan_id');

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function ($q) use ($like) {
                $q->where('r.client_name', 'LIKE', $like)
                    ->orWhere('r.nrc_number', 'LIKE', $like)
                    ->orWhere('r.recovery_reference', 'LIKE', $like)
                    ->orWhere('l.account_number', 'LIKE', $like);
            });
        }

        $recoveries = $query->orderBy('r.created_at', 'desc')->paginate(20);
        $recoveries->appends($request->only('search'));

        foreach ($recoveries as $r) {
            $r->profit_share_due = round((float) $r->total_interest_paid * 0.10, 2);
            $r->profit_outstanding = round(max(0, $r->profit_share_due - (float) $r->amount_paid), 2);
            $r->profit_payable = $r->profit_outstanding > 0 && $r->repayment_status === 'Paid';
        }

        return view('artworks.lost-client-recoveries.profit-share', compact('recoveries', 'search'));
    }
    public function payments(){ return view('artworks.lost-client-recoveries.payments'); }
    public function reports(){ return view('artworks.lost-client-recoveries.reports'); }
}
