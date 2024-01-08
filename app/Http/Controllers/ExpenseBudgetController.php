<?php

namespace App\Http\Controllers;

use App\Exports\ExportReport;
use App\Helpers\GeneralHelper;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\ExpenseBudget;
use App\Models\ExpenseType;
use App\Models\GlJournalEntry;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ExpenseBudgetController extends Controller
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
    public function index()
    {
        if (!Sentinel::hasAccess('expenses.budget.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = ExpenseBudget::get();

        return view('expense_budget.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('expenses.budget.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('expense_budget.create' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('expenses.budget.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $expense_budget = new ExpenseBudget();
        $expense_budget->created_by_id = Sentinel::getUser()->id;
        $expense_budget->office_id = $request->office_id;
        $expense_budget->expense_type_id = $request->expense_type_id;
        $expense_budget->amount = $request->amount;
        $expense_budget->name = $request->name;
        $expense_budget->notes = $request->notes;
        $expense_budget->date = $request->date;
        $date = explode('-', $request->date);
        $expense_budget->year = $date[0];
        $expense_budget->month = $date[1];
        $expense_budget->status = "approved";
        $expense_budget->save();
        //check custom fields
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'expense_budgets')->get();
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
                $custom_field->parent_id = $expense_budget->id;
                $custom_field->custom_field_id = $key->id;
                $custom_field->category = "expense_budgets";
                $custom_field->save();
            }
        }

        //GeneralHelper::audit_trail("Added expense with id:" . $expense_budget->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/budget/data');
    }


    public function show($expense_budget)
    {
        if (!Sentinel::hasAccess('expenses.budget.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('expense_budget.show', compact('expense_budget'));
    }


    public function edit($expense_budget)
    {

        return view('expense_budget.edit', compact('expense_budget', 'types', 'custom_fields', 'chart_assets'));
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
        if (!Sentinel::hasAccess('expenses.budget.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $expense_budget = ExpenseBudget::find($id);
        $expense_budget->office_id = $request->office_id;
        $expense_budget->expense_type_id = $request->expense_type_id;
        $expense_budget->amount = $request->amount;
        $expense_budget->name = $request->name;
        $expense_budget->notes = $request->notes;
        $expense_budget->date = $request->date;
        $date = explode('-', $request->date);
        $expense_budget->year = $date[0];
        $expense_budget->month = $date[1];
        $expense_budget->status = "approved";
        $expense_budget->save();
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'expense_budgets')->get();
            foreach ($custom_fields as $key) {
                if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                    'expense_budgets')->first())
                ) {
                    $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                        $id)->where('category', 'expense_budgets')->first();
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
                $custom_field->category = "expense_budgets";
                $custom_field->save();
            }
        }


        // GeneralHelper::audit_trail("Updated expense with id:" . $expense_budget->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('expense/budget/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('expenses.budget.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        ExpenseBudget::destroy($id);
        //GeneralHelper::audit_trail("Deleted expense with id:" . $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();
    }

//reports
    public function report(Request $request)
    {
        if (!Sentinel::hasAccess('expenses.budget.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($request->year)) {
            $year = date("Y");
        } else {
            $year = $request->year;
        }
        $office_id = $request->office_id;
        $data = ExpenseType::get();

        return view('expense_budget.report', compact('data', 'year', 'office_id'));
    }

    public function report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_numbers_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($request->year)) {
            $year = date("Y");
        } else {
            $year = $request->year;
        }
        $office_id = $request->office_id;
        $data = ExpenseType::get();

        $pdf = PDF::loadView('expense_budget.report_pdf', compact('year',
            'office_id', 'data'));
        //$pdf->setPaper('A4', 'landscape');
        return $pdf->download(trans_choice('general.budget', 1) . ' ' . trans_choice('general.report',
                2) . ".pdf");

    }

    public function report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_numbers_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($request->year)) {
            $year = date("Y");
        } else {
            $year = $request->year;
        }
        $office_id = $request->office_id;
        $data = ExpenseType::get();
        $data = [
            'data' => $data,
            'year' => $year,
            'office_id' => $office_id,
        ];
        return Excel::download(new ExportReport("expense_budget.report_pdf", $data), trans_choice('general.budget', 1) . ' ' . trans_choice('general.report',
                2) . '.xlsx');


    }

    public function report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_numbers_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($request->year)) {
            $year = date("Y");
        } else {
            $year = $request->year;
        }
        $office_id = $request->office_id;
        $data = ExpenseType::get();
        $data = [
            'data' => $data,
            'year' => $year,
            'office_id' => $office_id,
        ];
        return Excel::download(new ExportReport("expense_budget.report_pdf", $data), trans_choice('general.budget', 1) . ' ' . trans_choice('general.report',
                2) . '.xlsx');


    }
}
