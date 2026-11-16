<?php
namespace App\Http\Controllers\Artworks;
use App\Http\Controllers\Controller;
use App\Models\Artworks\LostClientRecovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

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
        $ref = 'LCR-' . date('Y') . '-' . str_pad((LostClientRecovery::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);
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
                'l.id as loan_id',
                'l.account_number as loan_account',
                'l.principal as loan_principal',
                'o.name as branch_name',
                DB::raw("(SELECT lt.debit FROM loan_transactions lt WHERE lt.loan_id = l.id AND lt.transaction_type = 'interest_initial' LIMIT 1) as total_interest_paid")
            )
            ->whereNotNull('r.loan_id')
            ->where('r.amount_paid', 0);

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
        }

        return view('artworks.lost-client-recoveries.profit-share', compact('recoveries', 'search'));
    }

    public function received(Request $request, $id)
    {
        $recovery = DB::table('creative_lost_client_recoveries')->where('id', $id)->first();
        if (!$recovery || !$recovery->loan_id) {
            return redirect()->back()->with('error', 'Recovery not found.');
        }

        $totalInterest = DB::table('loan_transactions')
            ->where('loan_id', $recovery->loan_id)
            ->where('transaction_type', 'interest_initial')
            ->value('debit');

        $profitShareDue = round((float) ($totalInterest ?? 0) * 0.10, 2);

        DB::table('creative_lost_client_recoveries')
            ->where('id', $id)
            ->update(['amount_paid' => $profitShareDue]);

        return redirect()->back()->with('success', 'Amount paid updated to profit share due.');
    }
    public function payments(Request $request)
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
                'r.amount_paid',
                'r.created_at',
                'r.loan_id',
                'o.name as branch_name'
            )
            ->whereNotNull('r.amount_paid')
            ->where('r.amount_paid', '!=', 0);

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

        $totalPaid = $recoveries->getCollection()->sum(function ($r) {
            return (float) ($r->amount_paid ?? 0);
        });

        return view('artworks.lost-client-recoveries.payments', compact('recoveries', 'search', 'totalPaid'));
    }
    public function reports(){ return view('artworks.lost-client-recoveries.reports'); }

    public function lostClients(Request $request)
    {
        $search = trim($request->query('search', ''));

        $query = DB::table('clients as c')
            ->whereNull('c.deleted_at')
            ->leftJoin('offices as o', 'c.office_id', '=', 'o.id')
            ->select(
                'c.id',
                'c.full_name',
                'c.first_name',
                'c.last_name',
                'c.nrc_number',
                'c.phone_primary',
                'c.email_primary',
                'c.address_line1',
                'c.employer',
                'c.phone',
                'c.email',
                'c.address',
                'o.name as office_name',
                'c.lost_recovery',
                'c.lost_recovery_notes',
                'c.created_at as client_created_at',
                DB::raw('MAX(l.created_at) as last_loan_date'),
                DB::raw('COUNT(l.id) as total_loans')
            )
            ->leftJoin('loans as l', 'l.client_id', '=', 'c.id')
            ->whereNull('l.deleted_at')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('loans as l2')
                  ->whereColumn('l2.client_id', 'c.id')
                  ->whereNull('l2.deleted_at')
                  ->where('l2.status', '!=', 'closed');
            })
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('loans as l')
                  ->whereColumn('l.client_id', 'c.id')
                  ->whereNull('l.deleted_at')
                  ->where('l.created_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL 3 MONTH)'));
            });

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function ($q) use ($like) {
                $q->where('c.first_name', 'LIKE', $like)
                    ->orWhere('c.last_name', 'LIKE', $like)
                    ->orWhere('c.full_name', 'LIKE', $like)
                    ->orWhere('c.nrc_number', 'LIKE', $like)
                    ->orWhere('c.phone_primary', 'LIKE', $like)
                    ->orWhere('c.phone', 'LIKE', $like)
                    ->orWhere('o.name', 'LIKE', $like);
            });
        }

        $clients = $query->groupBy(
            'c.id', 'c.full_name', 'c.first_name', 'c.last_name', 'c.nrc_number', 'c.phone_primary',
            'c.email_primary', 'c.address_line1', 'c.employer', 'c.phone', 'c.email', 'c.address',
            'o.name', 'c.lost_recovery', 'c.lost_recovery_notes', 'c.created_at'
        )
        ->orderBy('last_loan_date', 'desc')
        ->paginate(20);
        $clients->appends($request->only('search'));

        return view('artworks.lost-client-recoveries.lost-clients', compact('clients', 'search'));
    }

    public function recoverClient(Request $request, $id)
    {
        $request->validate([
            'lost_recovery_notes' => 'nullable|string',
        ]);

        $client = \App\Models\Client::whereNull('deleted_at')->find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found.');
        }

        $client->update([
            'lost_recovery' => true,
            'lost_recovery_approval' => 'pending',
            'lost_recovery_notes' => $request->input('lost_recovery_notes'),
        ]);

        // Also set pending on the recovery record(s) for this client
        DB::table('creative_lost_client_recoveries')
            ->where('client_id', $id)
            ->update(['lost_recovery_approval' => 'pending']);

        return redirect()->back()->with('success', 'Client marked as recovered.');
    }

    public function lostClientsApprovals(Request $request)
    {
        $query = DB::table('clients as c')
            ->leftJoin('offices as o', 'c.office_id', '=', 'o.id')
            ->select(
                'c.id',
                'c.first_name',
                'c.last_name',
                'c.full_name',
                'c.nrc_number',
                'c.phone_primary',
                'c.lost_recovery',
                'c.lost_recovery_approval',
                'c.lost_recovery_notes',
                'c.created_at',
                'o.name as office_name'
            )
            ->where('c.lost_recovery', true)
            ->where('c.lost_recovery_approval', 'pending');

        if ($search = trim($request->query('search', ''))) {
            $like = "%{$search}%";
            $query->where(function ($q) use ($like) {
                $q->where('c.first_name', 'LIKE', $like)
                    ->orWhere('c.last_name', 'LIKE', $like)
                    ->orWhere('c.full_name', 'LIKE', $like)
                    ->orWhere('c.nrc_number', 'LIKE', $like);
            });
        }

        $approvals = $query->orderBy('c.created_at', 'desc')->paginate(20);
        $approvals->appends($request->only('search'));

        return view('artworks.lost-client-recoveries.lost-clients-approvals', compact('approvals', 'search'));
    }

    public function approveLostRecovery(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approved,lost',
        ]);

        $client = \App\Models\Client::whereNull('deleted_at')->find($id);
        if (!$client) {
            return redirect()->back()->with('error', 'Client not found.');
        }

        $client->update([
            'lost_recovery_approval' => $request->input('action'),
        ]);

        if ($request->input('action') === 'approved') {
            $user = Sentinel::getUser();
            LostClientRecovery::store([
                'branch_id' => $client->office_id,
                'client_id' => $client->id,
                'client_name' => $client->full_name ?: ($client->first_name . ' ' . $client->last_name),
                'nrc_number' => $client->nrc_number,
                'contact_number' => $client->phone_primary,
                'recovery_date' => \Carbon\Carbon::now(),
                'responsible_staff_id' => $user ? $user->id : null,
                'loan_id' => null,
                'notes' => $client->lost_recovery_notes,
            ]);
        }

        $label = $request->input('action') === 'approved' ? 'Approved' : 'Declined';
        return redirect()->back()->with('success', 'Client recovery approval ' . strtolower($label) . '.');
    }
}
