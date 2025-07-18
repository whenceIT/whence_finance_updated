<?php

namespace App\Http\Controllers;
use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Advance;
use App\Models\Office;
use App\Models\Province;
use App\Helpers\GeneralHelper;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdvanceApproved;
use App\Models\TopUp;
use App\Models\AdvanceTransaction;

class AdvanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function showApplyForm()
    {
        $offices = Office::all();
        $user = Sentinel::getUser();
        $firstName = $user->first_name;
        $lastName = $user->last_name;
        return view('advances.apply', compact('offices', 'firstName', 'lastName'));
    }


    public function submitAdvance(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'installments' => 'required|numeric',
        ]);

        $user = Sentinel::getUser();
        $activeAdvances = $user->advances()->where('status', 'approved')->get();

        if ($activeAdvances->isNotEmpty()) {
            return redirect()->back()->with('error', 'You already have an active advance. You cannot apply for another advance until you have finished paying off your current advance.');
        }

        $advance = new Advance();
        $advance->user_id = Sentinel::getUser()->id;
        $advance->office_id = $request->office_id;
        $advance->first_name = $request->first_name;
        $advance->last_name = $request->last_name;
        $advance->amount = $validatedData['amount'];
        $advance->installments = $validatedData['installments'];
        $advance->installment_amount = $validatedData['amount'] / $validatedData['installments'];
        //add here
        $advance->remaining_amount = $validatedData['amount'];
        $advance->purpose = $request->purpose;
        $advance->mode_of_payment = $request->mode_of_payment;
        $advance->notes = $request->notes;
        $advance->date_requested = now();
        $advance->save();
    
        GeneralHelper::audit_trail("Create", "Advances", $advance->id);
        Flash::success("Advance submitted successfully");
        return Redirect::route('advances.my_advances');
    
    }

    public function showMyAdvances()
    {
        $user = Sentinel::getUser();

	$advances = Advance::where('user_id', $user->id)
	->where('status', 'approved')
	->get();

        return view('advances.my_advances', compact('advances'));
    
   }

    public function submitTopUp(Request $request, $id)
    {
        $request->validate([

            'top_up_date' => 'required|date',
            'top_up_amount' => 'required|integer',
            'installments' => 'nullable|integer|min:1|max:3',
        ]);

        $existingTopUp = TopUp::where('advance_id', $id)
        ->whereIn('status', ['pending', 'approved'])
        ->first();

        if ($existingTopUp) {

            return redirect()->back()->with('error', 'A top-up request already exists for this advance.');

        }
        $advance = Advance::findOrFail($id);

	$user = Sentinel::getUser();
	$office_id = $user->office_id;
        //dd($user);

        TopUp::create([
            'advance_id' => $advance->id,
            'top_up_amount' => $request->input('top_up_amount'),
            'top_up_date' => $request->input('top_up_date'),
            'installments' => $request->input('installments'),
            'first_name' => $user->first_name,
	    'last_name' => $user->last_name,
	    'office_id' => $office_id,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Top-up request submitted and awaiting approval.');
    }


    public function approveTopUp(Request $request, $id)
    {
	$user = Sentinel::getUser();
        $office_id = $user->office_id;
        $topUp = TopUp::findOrFail($id);
        $advance = $topUp->advance;

        //add top-up amount to the advance once approved
        $advance->remaining_amount += $topUp->top_up_amount;
        $advance->amount += $topUp->top_up_amount;

        if ($topUp->installments && $topUp->installments > $advance->installments) {
            $advance->installments = $topUp->installments;
        }
        $advance->save();

        $topUp->status = 'approved';
        $topUp->save();

        return Redirect::route('advances.topups_pending_approval')->with('success', 'Top-up approved successfully.');
    }

    public function declineTopUp($id)
    {
	$user = Sentinel::getUser();
        $office_id = $user->office_id;
    $topUp = TopUp::findOrFail($id);
    $advance = Advance::findOrFail($id);

    $topUp->status = 'declined';
    $topUp->save();

    return redirect()->back()->with('success', 'Advance has been declined.');
}


    public function topupPendingApprovals()
    {

        $user = Sentinel::getUser();
        $office_id = $user->office_id;

        if ($user->hasAccess('groups.create')) {
            $advance_topups = TopUp::where('status', 'pending')->get();
        } elseif ($user-> hasAccess('offices')) {
            $advance_topups = TopUp::where('status', 'pending')
            ->where('office_id', $office_id)
            ->get();
        }else {
            $userOffice = $user->office;
            $provinceId = $userOffice->province_id;
            $provinceOffices = Office::where('province_id', $provinceId)->pluck('id');
            $advance_topups = TopUp::whereIn('office_id', $provinceOffices)
            ->where('status', 'pending')
            ->get();
        }
        return view('advances.topups_pending_approval', compact('advance_topups'));
    }



    public function approve(Request $request, $id)
    {
        $advance = Advance::findOrFail($id);
        $advance->status = 'approved';
        $advance->date_approved = now();
        $advance->approved_by_id = Sentinel::getUser()->id;
        //next payment date
        $nextPaymentDate = Carbon::now()->endOfMonth()->addDay();
        $advance->expected_repayment_dates = $nextPaymentDate;
        $advance->save();
    
        return Redirect::route('advances.pending_approvals')->with('success', trans('general.successfully_saved'));
    }


    public function decline(Request $request, $id)
    {
        $advance = Advance::findOrFail($id);
        $advance->status = 'declined';
        $advance->declined_by_id = Sentinel::getUser()->id;
        $advance->save();
        $request->session()->flash('success', 'Salary advance declined successfully.');
        return redirect()->back();
    }


    public function showPendingApprovals()
    {
        $user = Sentinel::getUser();
        $office_id = $user->office_id;

        if ($user->hasAccess('groups.create')) {
            $advances = Advance::where('status', 'pending')->get();
        } elseif ($user-> hasAccess('offices')) {
            $advances = Advance::where('status', 'pending')
            ->where('office_id', $office_id)
            ->get();
        }else {
            $userOffice = $user->office;
            $provinceId = $userOffice->province_id;
            $provinceOffices = Office::where('province_id', $provinceId)->pluck('id');
            $advances = Advance::whereIn('office_id', $provinceOffices)
            ->where('status', 'pending')
            ->get();
        }
        return view('advances.pending_approvals', compact('advances'));
    }

    public function showActiveAdvances()
    {
        $user = Sentinel::getUser();
        $office_id = $user->office_id;
    
        if ($user->hasAccess('groups.create')) {
            $advances = Advance::where('status', 'approved')
            ->where('remaining_amount', '>', 0) 
            ->get();
        } elseif ($user-> hasAccess('offices')) { 
            $advances = Advance::where('status', 'approved')
            ->where('office_id', $office_id)
            ->where('remaining_amount', '>', 0)
            ->get();
        } 
        else {
            $userOffice = $user->office;
            $provinceId = $userOffice->province_id;
            $provinceOffices = Office::where('province_id', $provinceId)->pluck('id');
            $advances = Advance::whereIn('office_id', $provinceOffices)
            ->where('status', 'approved')
            ->where('remaining_amount', '>', 0)
            ->get();
        }
        foreach ($advances as $advance) {
            $expectedRepaymentDate = Carbon::parse($advance->expected_repayment_dates);
            
    
            if ($expectedRepaymentDate->isToday() && !$advance->processed_today) {
                $installmentAmount = $advance->installment_amount;
                $advance->amount_paid += $installmentAmount;
    
                if ($advance->amount_paid >= $advance->amount) {
                    $advance->amount_paid = $advance->amount;
                }
                $advance->remaining_amount = $advance->amount - $advance->amount_paid;
                
                $advance->processed_today = true;
            if ($advance->remaining_amount <= 0.00) {
                $advance->status = 'closed';
                
            }
		$advance->save();

		//CreateS a new transaction in the advance_transactions table
            AdvanceTransaction::create([
                'advance_id' => $advance->id,
                'amount_paid' => $installmentAmount,
                'last_update_date' => $advance->expected_repayment_dates,
            ]);
            }
        }
        foreach ($advances as $advance) {
            $expectedRepaymentDate = Carbon::parse($advance->expected_repayment_dates);
    
            if ($expectedRepaymentDate->isToday() && !$advance->processed_today) {
                $advance->expected_repayment_dates = Carbon::now()->endOfMonth();
                $advance->save();
            }
        }
        return view('advances.active_advances', compact('advances'));
    }


    public function showDetails($id)
    {
    $advance = Advance::findOrFail($id);
    return view('advances.show', compact('advance'));
    }

    public function closeAdvance(Request $request, $id)
{
    $advance = Advance::find($id);

    if (!$advance) {
        return redirect()->back()->with('error', 'Advance not found.');
    }
//added closing advance code
    $advance->remaining_amount = $request->input('remaining_amount');
    $advance->amount_paid = $request->input('amount_paid');
    $advance->status = 'closed';
    $advance->save();

    return redirect()->route('advances.active_advances')->with('success', 'Advance closed successfully.');
}


    public function showDeclinedAdvances()
    {
    $advances = Advance::where('status', 'declined')->get(); 
    return view('advances.declined_advances', compact('advances'));
    }

    public function storeClosedAdvances()
    {
        $closedAdvances = Advance::where('status', 'closed')->get();
    
        foreach ($closedAdvances as $advance) {
            $advance->status = 'closed';
            $advance->save();
        }
    
        return view('advances.closed_advances', compact('closedAdvances'));
    }
    


}
