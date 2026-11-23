<?php
namespace App\Http\Controllers\Artworks;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Artworks\LoanReferral;
use App\Models\District;
use App\Models\Office;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LoanReferralController extends Controller
{
    public function index(Request $request)
    {
        $provinceId = $request->input('province_id');
        $districtId = $request->input('district_id');
        $officeId = $request->input('office_id');
        $search = trim($request->input('search', ''));

        $sortColumns = [
            'ref' => 'creative_loan_referrals.referral_reference',
            'client' => 'creative_loan_referrals.client_name',
            'nrc' => 'creative_loan_referrals.nrc_number',
            'office' => 'referral_offices.name',
            'department' => 'creative_loan_referrals.referring_department',
            'date' => 'creative_loan_referrals.referral_date',
            'status' => 'creative_loan_referrals.loan_status',
        ];
        $sort = $request->input('sort', 'date');
        if (!array_key_exists($sort, $sortColumns)) {
            $sort = 'date';
        }
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = LoanReferral::query()
            ->leftJoin('offices as referral_offices', 'referral_offices.id', '=', 'creative_loan_referrals.branch_id')
            ->select('creative_loan_referrals.*', 'referral_offices.name as branch_name')
            ->when($provinceId, function ($query) use ($provinceId) {
                $query->where('referral_offices.province_id', $provinceId);
            })
            ->when($districtId, function ($query) use ($districtId) {
                $query->where('referral_offices.district_id', $districtId);
            })
            ->when($officeId, function ($query) use ($officeId) {
                $query->where('creative_loan_referrals.branch_id', $officeId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($query) use ($like) {
                    $query->where('creative_loan_referrals.referral_reference', 'LIKE', $like)
                        ->orWhere('creative_loan_referrals.client_name', 'LIKE', $like)
                        ->orWhere('creative_loan_referrals.nrc_number', 'LIKE', $like)
                        ->orWhere('creative_loan_referrals.contact_number', 'LIKE', $like)
                        ->orWhere('creative_loan_referrals.referring_department', 'LIKE', $like)
                        ->orWhere('referral_offices.name', 'LIKE', $like);
                });
            });

        $referrals = $query->orderBy($sortColumns[$sort], $direction)
            ->paginate(20)
            ->appends($request->query());
        $provinces = Province::orderBy('name')->get();
        $districts = District::when($provinceId, function ($query) use ($provinceId) {
            $query->where('province_id', $provinceId);
        })->orderBy('name')->get();
        $offices = Office::when($provinceId, function ($query) use ($provinceId) {
            $query->where('province_id', $provinceId);
        })->when($districtId, function ($query) use ($districtId) {
            $query->where('district_id', $districtId);
        })->orderBy('name')->get();

        return view('artworks.loan-referrals.index', compact(
            'referrals',
            'provinces',
            'districts',
            'offices',
            'provinceId',
            'districtId',
            'officeId',
            'search',
            'sort',
            'direction'
        ));
    }
    public function create()
    {
        return view('artworks.loan-referrals.create');
    }
    public function approvals()
    {
        $this->authorizeReferralApproval();
        $user = Sentinel::getUser();
        $referrals = LoanReferral::where('approval_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        $loanOfficers = User::where('office_id', $user->office_id)
            ->where('status', 'Active')
            ->whereHas('role', function ($query) {
                $query->where('role_id', 3);
            })
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('artworks.loan-referrals.approvals', compact('referrals', 'loanOfficers'));
    }

    public function decideApproval(Request $request, $id, $decision)
    {
        try {
            $this->authorizeReferralApproval();

            if (!in_array($decision, ['approved', 'declined'], true)) {
                abort(404);
            }

            $validated = $request->validate([
                'selected_user_id' => $decision === 'approved'
                    ? 'required|integer|exists:users,id'
                    : 'nullable|integer',
            ]);
            $approver = Sentinel::getUser();

            DB::transaction(function () use ($id, $decision, $validated, $approver) {
                $referral = LoanReferral::whereKey($id)->lockForUpdate()->firstOrFail();
                if ($referral->approval_status !== 'pending') {
                    abort(422, 'Only pending referrals can be approved or declined.');
                }

                if ($decision === 'approved') {
                    $loanOfficer = User::whereKey($validated['selected_user_id'])
                        ->where('office_id', $approver->office_id)
                        ->where('status', 'Active')
                        ->whereHas('role', function ($query) {
                            $query->where('role_id', 3);
                        })
                        ->first();

                    if (!$loanOfficer) {
                        throw ValidationException::withMessages([
                            'selected_user_id' => 'Select an active loan officer from your office.',
                        ]);
                    }

                    $client = null;
                    if ($referral->nrc_number) {
                        $client = Client::where('nrc_number', $referral->nrc_number)
                            ->lockForUpdate()
                            ->first();
                    }

                    $isNewClient = !$client;
                    
                    if (!$client) {
                        $client = new Client();
                        $client->nrc_number = $referral->nrc_number;
                        $client->first_name = $referral->fn_name;
                        $client->last_name = $referral->lname;
                        $client->full_name = $referral->client_name;
                        $client->email = $referral->email;
                        $client->dob = $referral->dob;
                        $client->gender = $referral->gender;
                        $client->client_type = $referral->client_type;
                        $client->marital_status = $referral->marital_status;
                        $client->address = $referral->address;
                        $client->phone = $referral->contact_number;
                        $client->mobile = $referral->contact_number;
                        $client->working_place = $referral->working_place;
                        $client->working_position = $referral->working_position;
                        $client->salary = $referral->salary;
                        $client->employer = $referral->employer;
                        $client->business_name = $referral->business_name;
                        $client->office_id = $approver->office_id;
                        $client->created_by_id = $approver->id;

                        $client->loan_referral = true;
                    } else {
                        $client->loan_referral = true;
                    }

                    $client->staff_id = $loanOfficer->id;
                    $client->save();

                    if ($isNewClient) {
                        $office = Office::find($approver->office_id);
                        $firstName = trim((string) $referral->fn_name);
                        $lastName = trim((string) $referral->lname);
                        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                        $client->account_no = $initials . '-' . ($office ? $office->external_id : '') . '-' . $client->id;
                        $client->save();
                    }
                }

                $referral->approval_status = $decision;
                $referral->save();
            });

            return redirect()->route('loan-referrals.approvals')
                ->with('success', 'Referral ' . $decision . ' successfully.');
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    private function authorizeReferralApproval()
    {
        if (!Sentinel::hasAccess('expenses')) {
            abort(403);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'fn_name' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,unspecified',
            'client_type' => 'required|in:ngo,business,individual,other',
            'marital_status' => 'nullable|in:married,single,divorced,widowed,unspecified',
            'address' => 'nullable|string',
            'working_place' => 'nullable|string|max:255',
            'working_position' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'employer' => 'nullable|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'nrc_number' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'referral_date' => 'required|date',
            'branch_id' => 'nullable|integer',
            'referring_officer_id' => 'nullable|integer',
        ]);
        $ref = 'LR-' . date('Y') . '-' . str_pad(LoanReferral::count()+1,6,'0',STR_PAD_LEFT);
        LoanReferral::create([
            'referral_reference'=>$ref,
            'branch_id'=>$request->branch_id,
            'client_name'=>trim($request->fn_name . ' ' . $request->lname),
            'fn_name'=>$request->fn_name,
            'lname'=>$request->lname,
            'email'=>$request->email,
            'dob'=>$request->dob,
            'gender'=>$request->gender,
            'client_type'=>$request->client_type,
            'marital_status'=>$request->marital_status,
            'address'=>$request->address,
            'working_place'=>$request->working_place,
            'working_position'=>$request->working_position,
            'salary'=>$request->salary,
            'employer'=>$request->employer,
            'business_name'=>$request->business_name,
            'nrc_number'=>$request->nrc_number,
            'contact_number'=>$request->contact_number,
            'referring_department'=>$request->referring_department,
            'referral_date'=>$request->referral_date,
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
    public function due(){ $items = LoanReferral::where('profit_share','>',0)->where('repayment_status','Unpaid')->orderBy('created_at','desc')->paginate(20); return view('artworks.loan-referrals.due', compact('items')); }
    public function profitShareDue(Request $request)
    {
        $search = trim($request->query('search', ''));

        $items = DB::table('loans as l')
            ->join('clients as c', 'c.id', '=', 'l.client_id')
            ->join('creative_loan_referrals as r', 'r.nrc_number', '=', 'c.nrc_number')
            ->leftJoin('offices as o', 'o.id', '=', 'c.office_id')
            ->whereNull('l.deleted_at')
            ->whereNull('c.deleted_at')
            ->where('c.loan_referral', true)
            ->where('c.loan_referral_count', '<', 3)
            ->whereNotNull('c.nrc_number')
            ->where(function ($query) {
                $query->whereNull('r.profit_share')
                    ->orWhere('r.profit_share', 0);
            })
            ->select(
                'r.id as referral_id',
                'r.referral_reference',
                'r.referral_date',
                'r.nrc_number',
                'r.contact_number',
                'r.profit_share',
                'r.amount_paid',
                'r.repayment_status',
                'r.loan_status',
                'c.first_name',
                'c.last_name',
                'c.loan_referral_count',
                'o.name as branch_name',
                'l.id as loan_id',
                'l.status as loan_status',
                DB::raw("(SELECT lt.debit FROM loan_transactions lt WHERE lt.loan_id = l.id AND lt.transaction_type = 'interest_initial' ORDER BY lt.id LIMIT 1) as initial_interest")
            );

        if ($search !== '') {
            $like = "%{$search}%";
            $items->where(function ($query) use ($like) {
                $query->where('c.first_name', 'LIKE', $like)
                    ->orWhere('c.last_name', 'LIKE', $like)
                    ->orWhere('r.referral_reference', 'LIKE', $like)
                    ->orWhere('r.nrc_number', 'LIKE', $like)
                    ->orWhere('l.account_number', 'LIKE', $like);
            });
        }

        $items = $items->orderByDesc('l.created_at')->paginate(20);
        $items->appends($request->only('search'));

        foreach ($items as $item) {
            $item->profit_share_due = round((float) ($item->initial_interest ?? 0) * 0.20, 2);
        }

        return view('artworks.loan-referrals.profit-share', compact('items', 'search'));
    }

    public function received(Request $request, $id, $loanId)
    {
        $result = DB::transaction(function () use ($id, $loanId) {
            $referral = LoanReferral::whereKey($id)->lockForUpdate()->first();

            if (!$referral) {
                return 'not_found';
            }

            if ((float) $referral->amount_paid > 0 || (float) $referral->profit_share > 0) {
                return 'already_received';
            }

            $loan = \App\Models\Loan::whereKey($loanId)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$loan) {
                return 'loan_not_found';
            }

            $initialInterest = \App\Models\LoanTransaction::where('loan_id', $loanId)
                ->where('transaction_type', 'interest_initial')
                ->orderBy('id')
                ->value('debit');

            $profitShare = round((float) ($initialInterest ?? 0) * 0.20, 2);
            $referral->loan_id = $loanId;
            $referral->loan_status = $loan->status;
            $referral->profit_share = $profitShare;
            $referral->amount_paid = $profitShare;
            $referral->repayment_status = 'Paid';
            $referral->save();

            $client = Client::withTrashed()->where('nrc_number', $referral->nrc_number)
                ->lockForUpdate()
                ->first();

            if (!$client) {
                $client = Client::withTrashed()->lockForUpdate()->find($loan->client_id);
            }

            if ($client) {
                $client->loan_referral_count = (int) $client->loan_referral_count + 1;
                if ($client->loan_referral_count >= 3) {
                    $client->loan_referral = false;
                }
                $client->save();
            }

            return 'received';
        });

        if ($result === 'not_found') {
            return redirect()->back()->with('error', 'Referral not found.');
        }
        if ($result === 'already_received') {
            return redirect()->back()->with('error', 'Profit share has already been received.');
        }
        if ($result === 'loan_not_found') {
            return redirect()->back()->with('error', 'Loan not found.');
        }

        return redirect()->back()->with('success', 'Referral profit share received.');
    }
    public function payments()
    {
        $paidReferrals = LoanReferral::whereNotNull('profit_share')
            ->where('amount_paid', '>', 0);
        $totalAmountPaid = (clone $paidReferrals)->sum('amount_paid');
        $items = $paidReferrals
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        $all = LoanReferral::whereNotNull('profit_share')
            ->orderBy('referral_reference')
            ->get();
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

        return view('artworks.loan-referrals.payments', compact('items', 'all', 'totalAmountPaid'));
    }
    public function recordPayment(Request $request){ $this->validate($request,['referral_id'=>'required|integer','amount'=>'required|numeric|min:0.01']); $r=LoanReferral::findOrFail($request->referral_id); $r->profit_share=$request->amount; if($r->amount_paid>=$r->profit_share && $r->profit_share>0){ $r->repayment_status='Paid'; } $r->save(); return redirect()->back()->with('success','Payment recorded.'); }
    public function reports(){ return view('artworks.loan-referrals.reports'); }
}


