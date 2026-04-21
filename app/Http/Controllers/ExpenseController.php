<?php

namespace App\Http\Controllers;
use App\Models\Office;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Helpers\GeneralHelper;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\GlJournalEntry;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Notifix;

use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
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
        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;

        $user = Sentinel::getUser();
        $role_id = $user->roles()->first()->id ?? null;

        $offices = Office::all();
        $query = Expense::orderBy('id', 'desc');

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween('date', [$start_date, $end_date]);
        }

        if ($role_id) {
            if (empty($office_id) && $office_id !== '0' && $office_id !== 0) {
                $office_id = $user->office_id;
            }
            if (!empty($office_id) && $office_id != 0) {
                $query->where('office_id', $office_id);
            }
        }

        $data = $query->latest()->get();

        
        
        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('expense.data', compact('data', 'start_date', 'end_date', 'office_id', 'offices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('expenses.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $user = Sentinel::getUser();

        //Only Admin Account can see other offices when attempting to create an expense
        if ($user->role->role_id == 1) {
            $offices = Office::all();
        } else {
            //anyone else can only see their own office, and can not add expenses for other offices
            $offices = Office::where('id', $user->office_id)->get();
        } 
        
        return view('expense.create', compact('offices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('expenses.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $expense = new Expense();
        $expense->created_by_id = Sentinel::getUser()->id;
        $expense->office_id = $request->office_id;
        $expense->expense_type_id = $request->expense_type_id;
        $expense->amount = $request->amount;
        $expense->name = $request->name;
        $expense->notes = $request->notes;
        $expense->date = $request->date;
        $date = explode('-', $request->date);
        $expense->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $expense->recur_frequency = $request->recur_frequency;
            $expense->recur_start_date = $request->recur_start_date;
            if (!empty($request->recur_end_date)) {
                $expense->recur_end_date = $request->recur_end_date;
            }
            $expense->recur_next_date = $request->recur_start_date;
            $expense->recur_type = $request->recur_type;
        }
        $expense->year = $date[0];
        $expense->month = $date[1];
        $expense->status = "approved";
        if ($request->hasFile('proof_of_payment')) {
            $validator = Validator::make($request->all(), [
                'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            }

            $image = $request->file('proof_of_payment');
            $imageName = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('proof_of_payment'), $imageName);
            $expense->proof_of_payment = $imageName;
        }
        $expense->gl_account_id = $request->gl_account_id;
        $expense->save();

        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'expenses')->get();
            foreach ($custom_fields as $key) {
                $custom_field = new CustomFieldMeta();
                $id = "custom_field_" . $key->id;
                if ($key->field_type == "checkbox") {
                    if (!empty($request->$id)) {
                        $custom_field->name = serialize($request->$id);
                    } else {
                        $custom_field->name = serialize([]);
                    }
                } else {
                    $custom_field->name = $request->$id;
                }
                $custom_field->parent_id = $expense->id;
                $custom_field->custom_field_id = $key->id;
                $custom_field->category = "expenses";
                $custom_field->save();
            }
        }

        if (!empty($expense->type->gl_account_asset)) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->office_id = $expense->office_id;
            $journal->gl_account_id = $expense->type->gl_account_asset_id;
            $journal->date = $expense->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'expense';
            $journal->name = "Expense";
            $journal->credit = $expense->amount;
            $journal->reference = $expense->id;
            $journal->save();
        }
        $gl_account_id = $expense->gl_account_id;
        if (empty($gl_account_id)) {
            $gl_account_id = $expense->type->gl_account_expense_id;
        }
        if (!empty($gl_account_id)) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->office_id = $expense->office_id;
            $journal->gl_account_id = $gl_account_id;
            $journal->date = $expense->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'expense';
            $journal->name = "Expense";
            $journal->debit = $expense->amount;
            $journal->reference = $expense->id;
            $journal->save();
        }
        
        Notifix::notifyDailyReminderToRiskManager("approved an expense with id: " . $expense->id, ". After working hours");
        GeneralHelper::audit_trail("Create", "Expenses", $expense->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/data');
    }


    public function show($expense)
    {
        if (!Sentinel::hasAccess('expenses.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('expense.show', compact('expense'));
    }

    public function edit($expense)
    {
        return view('expense.edit', compact('expense'));
        // return view('expense.edit', compact('expense', 'types', 'custom_fields', 'chart_assets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('expenses.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $expense = Expense::find($id);
        $expense->office_id = $request->office_id;
        $expense->expense_type_id = $request->expense_type_id;
        $expense->amount = $request->amount;
        $expense->name = $request->name;
        $expense->notes = $request->notes;
        $expense->date = $request->date;
        $date = explode('-', $request->date);
        $expense->recurring = $request->recurring;
        if ($request->recurring == 1) {
            $expense->recur_frequency = $request->recur_frequency;
            $expense->recur_start_date = $request->recur_start_date;
            if (!empty($request->recur_end_date)) {
                $expense->recur_end_date = $request->recur_end_date;
            }
            if (empty($expense->recur_next_date)) {
                $expense->recur_next_date = $request->recur_start_date;
            }
            $expense->recur_type = $request->recur_type;
        }
        $expense->year = $date[0];
        $expense->month = $date[1];
        $expense->status = "approved";
        $expense->gl_account_id = $request->gl_account_id;
        $expense->save();
        GlJournalEntry::where('transaction_type', 'expense')->where('reference', $id)->delete();
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'expenses')->get();
            foreach ($custom_fields as $key) {
                if (
                    !empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where(
                        'category',
                        'expenses'
                    )->first())
                ) {
                    $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where(
                        'parent_id',
                        $id
                    )->where('category', 'expenses')->first();
                } else {
                    $custom_field = new CustomFieldMeta();
                }
                $kid = "custom_field_" . $key->id;
                if ($key->field_type == "checkbox") {
                    if (!empty($request->$kid)) {
                        $custom_field->name = serialize($request->$kid);
                    } else {
                        $custom_field->name = serialize([]);
                    }
                } else {
                    $custom_field->name = $request->$kid;
                }
                $custom_field->parent_id = $id;
                $custom_field->custom_field_id = $key->id;
                $custom_field->category = "expenses";
                $custom_field->save();
            }
        }

        if (!empty($expense->type->gl_account_asset)) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->office_id = $expense->office_id;
            $journal->gl_account_id = $expense->type->gl_account_asset_id;
            $journal->date = $expense->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'expense';
            $journal->name = "Expense";
            $journal->credit = $expense->amount;
            $journal->reference = $expense->id;
            $journal->save();
        }
        $gl_account_id = $expense->gl_account_id;
        if (empty($gl_account_id)) {
            $gl_account_id = $expense->type->gl_account_expense_id;
        }
        if (!empty($gl_account_id)) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->office_id = $expense->office_id;
            $journal->gl_account_id = $gl_account_id;
            $journal->date = $expense->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'expense';
            $journal->name = "Expense";
            $journal->debit = $expense->amount;
            $journal->reference = $expense->id;
            $journal->save();
        }

        
        Notifix::notifyDailyReminderToRiskManager("updated an expense with id: " . $expense->id, ". After working hours");
        GeneralHelper::audit_trail("Update", "Expenses", $expense->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('expenses.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Expense::destroy($id);
        GlJournalEntry::where('transaction_type', 'expense')->where('reference', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Expenses", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();
    }

    public function expensesByTransactionType(Request $request)
    {

        $transactionType = $request->input('transaction_type');

        $expenses = Expense::where('transaction_type', $transactionType)
            ->get();

        return view('expense.expenses_by_transaction_type', compact('expenses', 'transactionType'));
    }

    public function showProofOfPayment($expenseId)
    {
        $expense = Expense::find($expenseId);

        if (!$expense || !$expense->proof_of_payment) {
            abort(404);
        }
        $filePath = public_path('proof_of_payment/' . $expense->proof_of_payment);
        if (!file_exists($filePath)) {
            abort(404);
        }
        return response()->file($filePath);
    }



}
