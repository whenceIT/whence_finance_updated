<?php

namespace App\Http\Controllers\Rti;

use App\Http\Controllers\Controller;
use App\Models\OfficeLoan;
use App\Models\OfficeLoanTransaction;
use App\Models\Office;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

class RtiTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    // -------------------------------------------------------------------------
    // Full transaction history across all RTI loans
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        if (!Sentinel::hasAccess('rti.view')) {
            return redirect()->back()->with('error', 'You are not authorised to view RTI transactions.');
        }

        $offices = Office::where('active', 1)->orderBy('name')->get();

        $query = OfficeLoanTransaction::with(['loan.office', 'office', 'approver'])
            ->latest();

        // Filter: by office/branch
        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        // Filter: by transaction status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: by specific loan
        if ($request->filled('loan_id')) {
            $query->where('loan_id', $request->loan_id);
        }

        // Filter: date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(30)->withQueryString();
        $statuses     = OfficeLoanTransaction::statuses();

        return view('rti.transactions.index', compact('transactions', 'offices', 'statuses'));
    }
}
