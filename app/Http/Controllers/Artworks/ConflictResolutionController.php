<?php

namespace App\Http\Controllers\Artworks;

use App\Http\Controllers\Controller;
use App\Models\Artworks\ConflictResolutionCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConflictResolutionController extends Controller
{
    public function index(Request $request)
    {
        $query = ConflictResolutionCase::orderBy('created_at', 'desc');
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('case_reference', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%");
            });
        }
        $cases = $query->paginate(20);
        return view('artworks.conflict-resolution.index', compact('cases'));
    }

    public function create() {
        $offices = \App\Models\Office::orderBy('name')->get();
        $users = \App\Models\User::orderBy('first_name')->get();
        return view('artworks.conflict-resolution.create', compact('offices','users'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'client_name' => 'required|string|max:255',
            'complaint_type' => 'required|string',
            'complaint_description' => 'required|string',
            'branch_id' => 'nullable|integer',
            'date_reported' => 'required|date',
        ]);

        $caseReference = 'CR-' . date('Y') . '-' . str_pad(ConflictResolutionCase::count() + 1, 6, '0', STR_PAD_LEFT);

        ConflictResolutionCase::create([
            'case_reference' => $caseReference,
            'branch_id' => $request->branch_id,
            'client_name' => $request->client_name,
            'nrc_number' => $request->nrc_number,
            'contact_number' => $request->contact_number,
            'complaint_type' => $request->complaint_type,
            'complaint_description' => $request->complaint_description,
            'reported_by_user_id' => Auth::check() ? Auth::id() : null,
            'date_reported' => $request->date_reported,
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'status' => 'Open',
            'charge_amount' => 20.00,
            'amount_paid' => 0.00,
            'notes' => $request->notes,
        ]);

        return redirect()->route('conflict-resolution.index')->with('success', 'Conflict case registered. Charge K20.00 applied.');
    }

    public function charges()
    {
        $charges = ConflictResolutionCase::orderBy('created_at', 'desc')->paginate(20);
        return view('artworks.conflict-resolution.charges', compact('charges'));
    }

    public function payments()
    {
         $cases = ConflictResolutionCase::where('amount_paid', '>', 0)->orderBy('updated_at', 'desc')->paginate(20);
        $allCases = ConflictResolutionCase::orderBy('case_reference')->get();
        return view('artworks.conflict-resolution.payments', compact('cases','allCases'));
    }

    public function recordPayment(Request $request)
    {
        $this->validate($request, [
            'case_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
        ]);
        $case = ConflictResolutionCase::findOrFail($request->case_id);
        $case->amount_paid = $case->amount_paid + $request->amount;
        if ($case->amount_paid >= $case->charge_amount) {
            $case->status = 'Resolved';
            $case->resolution_date = $case->resolution_date ?: Carbon::now();
        }
        $case->save();
        return redirect()->route('conflict-resolution.payments')->with('success', 'Payment recorded.');
    }

    public function outstanding()
    {
        $cases = ConflictResolutionCase::whereRaw('amount_paid < charge_amount')->orderBy('created_at', 'desc')->paginate(20);
        return view('artworks.conflict-resolution.outstanding', compact('cases'));
    }

    public function reports()
    {
        $total = ConflictResolutionCase::count();
        $raised = ConflictResolutionCase::sum('charge_amount');
        $paid = ConflictResolutionCase::sum('amount_paid');
        $outstanding = max(0, $raised - $paid);
        $byBranch = ConflictResolutionCase::select('branch_id', DB::raw('sum(charge_amount) as raised'), DB::raw('sum(amount_paid) as paid'))
            ->groupBy('branch_id')->get();
        return view('artworks.conflict-resolution.reports', compact('total', 'raised', 'paid', 'outstanding', 'byBranch'));
    }
}





