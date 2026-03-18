<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\BankAccount;
use App\Models\Collateral;
use App\Models\CollateralType;
use App\Models\FundMovements;
use App\Models\GlClosure;
use App\Models\GlJournalEntry;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\PaymentDetail;
use App\Models\Office;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class JournalController extends Controller
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
        if (!Sentinel::hasAccess('accounting.journals.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $gl_account_id = $request->gl_account_id;
        $data = [];
        if (!empty($start_date)) {
            $data = GlJournalEntry::where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($gl_account_id, function ($query) use ($gl_account_id) {
                if ($gl_account_id != 0) {
                    $query->where('gl_account_id', '=', $gl_account_id);
                }
            })->whereBetween('date',
                [$start_date,$end_date])->orderBy('date', 'asc')->get();
        }
        return view('journal.data',
            compact('start_date',
                'end_date', 'office_id', 'data', 'gl_account_id'));
    }


    public function create()
    {
        if (!Sentinel::hasAccess('accounting.journals.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('journal.create');
    }

    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('accounting.journals.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $payment_detail = new PaymentDetail();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->receipt_number = $request->receipt_number;
        $payment_detail->bank = $request->bank;
        $payment_detail->save();
        $journal = new GlJournalEntry();
        $journal->created_by_id = Sentinel::getUser()->id;
        $journal->gl_account_id = $request->credit;
        $journal->payment_detail_id = $payment_detail->id;
        $journal->manual_entry = 1;
        $date = explode('-', $request->date);
        $journal->date = $request->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'manual_entry';
        $journal->credit = $request->amount;
        $journal->save();

        $journal = new GlJournalEntry();
        $journal->created_by_id = Sentinel::getUser()->id;
        $journal->gl_account_id = $request->debit;
        $journal->payment_detail_id = $payment_detail->id;
        $journal->manual_entry = 1;
        $date = explode('-', $request->date);
        $journal->date = $request->date;
        $journal->year = $date[0];
        $journal->month = $date[1];
        $journal->transaction_type = 'manual_entry';
        $journal->debit = $request->amount;
        $journal->save();
        Flash::success(trans('general.successfully_saved'));
        GeneralHelper::audit_trail("Create", "Journals", $journal->id);
        if (isset($request->return_url)) {
            return redirect($request->return_url);
        }
        return redirect('accounting/journal');
    }

    public function add_fund_transfers_and_payments(){

           $user = Sentinel::getUser();

        //Only Admin Account can see other offices when attempting to create an expense
        if ($user->role->role_id == 1) {
            $offices = Office::all();
        } else {
            //anyone else can only see their own office, and can not add expenses for other offices
            $offices = Office::where('id', $user->office_id)->get();
        } 
        $bank_accounts = BankAccount::all();

        return view('journal.add_fund_transfers_and_payments',compact('offices','bank_accounts'));
    }


     public function fund_movement_approvals()
    {

        $office_id = Sentinel::getUser()->office_id;
        if(Sentinel::hasAccess('settings')){
             $fund_movements = FundMovements::where('status', 'submitted')
            ->orderBy('transaction_date', 'desc')
            ->get();
        }else{
             $fund_movements = FundMovements::where('status', 'submitted')->where('office_id',$office_id)
            ->orderBy('transaction_date', 'desc')
            ->get();
        }
       
        return view('journal.fund_movement_approvals', compact('fund_movements'));
    }

    public function show_fund_movement($id)
{
    $movement = FundMovements::findOrFail($id);

    return view('journal.show_fund_movement', compact('movement'));
}

     public function approve_fund($id)
    {
        $movement = FundMovements::findOrFail($id);

        if ($movement->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted fund movements can be approved.');
        }

        $movement->status = 'approved';
        $movement->save();

        return redirect()->back()->with('success', 'Fund movement approved successfully.');
    }

    public function reject_fund($id)
    {
      

        $movement = FundMovements::findOrFail($id);

        if ($movement->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted fund movements can be rejected.');
        }

        $movement->status = 'rejected';
        $movement->save();

        return redirect()->back()->with('success', 'Fund movement rejected successfully.');
    }


    public function store_funds_transfers_and_payments(Request $request)
    {
        // Validate input
        $request->validate([
            'office_id' => 'required',
            'movement_type' => 'required',
            'from_account' => 'required',
            'amount' => 'required',
            'date' => 'required|date',
        ]);

        $movement = new FundMovements();

        $movement->office_id = $request->office_id;
        $movement->movement_type = $request->movement_type;

        $movement->source_account = $request->from_account;
        $movement->destination_account = $request->to_account;

        $movement->payee_name = $request->payee_name;
        $movement->expense_category = $request->expense_category;

        $movement->title = $request->title;

        $movement->amount = $request->amount;

        $movement->payment_method = $request->payment_method;
        $movement->reference_no = $request->reference_no;

        $movement->transaction_date = $request->date;

        $movement->description = $request->description;
        $movement->remarks = $request->remarks;

      //  $movement->status = $request->status ?? 'draft';
       // $movement->requires_approval = $request->requires_approval ?? 1;

        $movement->created_by = Sentinel::getUser()->id;

        // Handle file upload
        if ($request->hasFile('attachment')) {

            $file = $request->file('attachment');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(public_path('uploads/fund_movements'), $filename);

            $movement->attachment = 'uploads/fund_movements/' . $filename;
        }

        $movement->document_note = $request->document_note;

        $movement->save();
    return redirect()->back();
        // return redirect('')
        //     ->with('success', 'Fund movement saved successfully');
    }

    public function reconciliation(Request $request)
    {
        if (!Sentinel::hasAccess('accounting.journals.reconciliation.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $gl_account_id = $request->gl_account_id;
        $show = $request->show;
        $data = [];
        if (!empty($start_date)) {
            $data = GlJournalEntry::where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($gl_account_id, function ($query) use ($gl_account_id) {
                if ($gl_account_id != 0) {
                    $query->where('gl_account_id', '=', $gl_account_id);
                }
            })->when($show, function ($query) use ($show) {
                if ($show == "1") {
                    $query->where('reconciled', '=', 0);
                }
                if ($show == "2") {
                    $query->where('reconciled', '=', 1);
                }
            })->whereBetween('date',
                [$start_date, $end_date])->orderBy('date', 'asc')->get();
        }
        return view('journal.reconciliation',
            compact('start_date',
                'end_date', 'office_id', 'data', 'gl_account_id', 'show'));
    }

    public function store_reconciliation(Request $request)
    {
        if (!Sentinel::hasAccess('accounting.journals.reconciliation.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!empty($request->reconcile_ids)) {
            foreach ($request->reconcile_ids as $key) {
                $journal = GlJournalEntry::find($key);
                if (!empty($journal)) {
                    $journal->reconciled = 1;
                    $journal->save();
                }
            }
        }
        Flash::success(trans('general.successfully_saved'));
        GeneralHelper::audit_trail("Create Reconciliation", "Journals","");
        if (isset($request->return_url)) {
            return redirect($request->return_url);
        }
        return redirect()->back();
    }

    public function period()
    {
        if (!Sentinel::hasAccess('accounting.period.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = GlClosure::all();
        return view('journal.period',
            compact('data'));
    }

    public function store_period(Request $request)
    {
        if (!Sentinel::hasAccess('accounting.period.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $gl_closure = new GlClosure();
        $gl_closure->created_by_id = Sentinel::getUser()->id;
        $gl_closure->office_id = $request->office_id;
        $gl_closure->closing_date = $request->closing_date;
        $gl_closure->notes = $request->notes;
        $gl_closure->save();
        Flash::success(trans('general.successfully_saved'));
        GeneralHelper::audit_trail("Create Period", "Journals","");
        if (isset($request->return_url)) {
            return redirect($request->return_url);
        }
        return redirect('accounting/period/data');
    }

    public function delete_period(Request $request, $id)
    {
        if (!Sentinel::hasAccess('accounting.period.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        GlClosure::destroy($id);
        Flash::success(trans('general.successfully_saved'));
        GeneralHelper::audit_trail("Delete Period", "Journals","");
        if (isset($request->return_url)) {
            return redirect($request->return_url);
        }
        return redirect()->back();
    }

    public function delete_journal($id)
    {
        if (!Sentinel::hasAccess('journal.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Journal::destroy($id);
        Loan::where('journal_id', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Journals", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('journal/data');
    }

    public function delete($id)
    {
         if (!Sentinel::hasAccess('accounting.journals.delete')) {
             Flash::warning("Permission Denied");
             return redirect()->back();
         }
        GlJournalEntry::destroy($id);
      //Setting::where('journal_id', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Journals",$id);
        Flash::success(trans('general.successfully_deleted'));
        //return redirect() -> back();
        return redirect('accounting/journal/data');
    }

}
