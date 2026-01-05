<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Advance;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Exports\ExportReport;
use App\Helpers\GeneralHelper;
use App\Models\Client;
use Illuminate\Support\Carbon;
use App\Models\GlAccount;
use App\Models\GlJournalEntry;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\LoanTopUp;
use App\Models\Office;
use App\Models\Savings;
use App\Models\SavingsTransaction;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\View;
use PDF;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use App\Models\UserRole;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }


    public function loan_product(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;


        $data = LoanProduct::all();

        return view(
            'report.loan_product',
            compact(
                'data',
                'start_date',
                'end_date'
            )
        );
    }


    public function loan_projection(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $monthly_collections = array();
        $start_date1 = date("Y-m-d");
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $start_date1);
            //get loans in that period
            $payments = 0;
            $payments_due = 0;
            foreach (LoanSchedule::where('branch_id', session('branch_id'))->where('year', $d[0])->where(
                'month',
                $d[1]
            )->get() as $key) {
                if (!empty($key->loan)) {
                    if ($key->loan->status == 'disbursed' || $key->loan->status == 'written_off' || $key->loan->status == 'closed') {
                        $payments_due = $payments_due + $key->principal + $key->fees + $key->interest + $key->penalty;
                    }
                }
            }
            $payments_due = round($payments_due, 2);
            $ext = ' ' . $d[0];
            array_push($monthly_collections, array(
                'month' => date_format(
                    date_create($start_date1),
                    'M' . $ext
                ),
                'due' => $payments_due

            ));
            //add 1 month to start date
            $start_date1 = date_format(
                date_add(
                    date_create($start_date1),
                    date_interval_create_from_date_string('1 months')
                ),
                'Y-m-d'
            );
        }
        $monthly_collections = json_encode($monthly_collections);
        return view(
            'report.loan_projection',
            compact(
                'monthly_collections',
                'start_date',
                'end_date'
            )
        );
    }


    public function financial_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports.financial_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view(
            'financial_report.data',
            compact(
                'start_date',
                'end_date'
            )
        );
    }

    public function loan_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports.loan_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view(
            'loan_report.data',
            compact(
                'start_date',
                'end_date'
            )
        );
    }

    public function client_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $start_date = $request->start_date;

        return view(
            'client_report.data',
            compact(
                'start_date',
                'end_date'
            )
        );
    }





    public function full_repayments(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            $data = LoanTransaction::where(
                'transaction_type',
                'repayment'
            )->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();
            $pdf = PDF::loadView('loan_report.full_payment_pdf', compact(
                'start_date',
                'end_date',
                'data'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . ".pdf");
        }
    }











    public function company_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view(
            'company_report.data',
            compact(
                'start_date',
                'end_date'
            )
        );
    }

    public function savings_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports.savings_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view(
            'savings_report.data',
            compact(
                'start_date',
                'end_date'
            )
        );
    }

    public function trial_balance(Request $request)
    {
        if (!Sentinel::hasAccess('reports.trial_balance')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = GlAccount::orderBy('gl_code', 'asc')->get();
        }
        return view(
            'financial_report.trial_balance',
            compact('start_date', 'end_date', 'data', 'office_id')
        );
    }

    ////// Consolidate Trial Balance
    public function trial_balance_consolidated(Request $request)
    {
        if (!Sentinel::hasAccess('reports.trial_balance')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = GlAccount::orderBy('gl_code', 'asc')->get();
        }
        return view(
            'financial_report.trial_balance_conso',
            compact('end_date', 'data')
        );
    }














    public function trial_balance_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.trial_balance')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = GlAccount::orderBy('gl_code', 'asc')->get();
            $pdf = PDF::loadView('financial_report.trial_balance_pdf', compact('start_date', 'end_date', 'data', 'office_id'));
            return $pdf->download(trans_choice('general.trial_balance', 1) . ' : ' . $request->end_date . ".pdf");
        }


    }

    public function trial_balance_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.trial_balance')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = GlAccount::orderBy('gl_code', 'asc')->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("financial_report.trial_balance_pdf", $data), trans_choice('general.trial_balance', 1) . '.xlsx');
        }

    }

    public function trial_balance_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.trial_balance')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = GlAccount::orderBy('gl_code', 'asc')->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("financial_report.trial_balance_pdf", $data), trans_choice('general.trial_balance', 1) . '.csv');
        }

    }

    public function income_statement(Request $request)
    {
        if (!Sentinel::hasAccess('reports.income_statement')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
        }
        return view(
            'financial_report.income_statement',
            compact('end_date', 'data', 'office_id')
        );

    }

    /////////////// consolidated income statement

    public function income_statement_consolidated(Request $request)
    {
        if (!Sentinel::hasAccess('reports.income_statement')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
        }
        return view(
            'financial_report.income_statement_conso',
            compact('end_date', 'data')
        );

    }


    public function GetCustomerStatmentReport(Request $request, Loan $loan)
    {
        if (!Sentinel::hasAccess('reports.daily_transactions_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_id = $request->loan_id;
        $credit = 0;
        $debit = 0;
        $current_balance = 0;
        $data = [];
        if (!empty($start_date)) {

            $debit_sum = LoanTransaction::where('loan_id', $loan_id)
                ->where('office_id', $office_id)
                ->where('date', '<', $start_date)
                ->sum('debit');

            $credit_sum = LoanTransaction::where('loan_id', $loan_id)
                ->where('office_id', $office_id)
                ->where('date', '<', $start_date)
                ->sum('credit');

            $current_balance = $debit_sum - $credit_sum;

            $data = LoanTransaction::where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_id, function ($query) use ($loan_id) {
                if ($loan_id != 0) {
                    $query->where('loan_id', '=', $loan_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->orderBy('date', 'asc')->get();

        }

        return view(
            'loan_report.customer_statement',
            compact(
                'start_date',
                'end_date',
                'data',
                'current_balance',
                'office_id',
                'loan_id',
                'loan'
            )
        );
    }


















    public function income_statement_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.income_statement')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer

            $pdf = PDF::loadView('financial_report.income_statement_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id'
            ));
            return $pdf->download(trans_choice('general.income', 1) . ' ' . trans_choice(
                'general.statement',
                1
            ) . ' : ' . $request->end_date . ".pdf");
        }

    }

    public function income_statement_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.income_statement')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer

            $data = [

                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("financial_report.income_statement_pdf", $data), trans_choice(
                'general.statement',
                1
            ) . ' : ' . $request->end_date . '.xlsx');

        }
    }

    public function income_statement_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.income_statement')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer

            $data = [

                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("financial_report.income_statement_pdf", $data), trans_choice(
                'general.statement',
                1
            ) . ' : ' . $request->end_date . '.csv');

        }
    }

    public function balance_sheet(Request $request)
    {
        if (!Sentinel::hasAccess('reports.balance_sheet')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];

        return view(
            'financial_report.balance_sheet',
            compact(
                'start_date',
                'end_date',
                'office_id'
            )
        );
    }



    //// consolidated Balance sheet
    public function balance_sheet_consolidated(Request $request)
    {
        if (!Sentinel::hasAccess('reports.balance_sheet')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $data = [];

        return view(
            'financial_report.balance_sheet_conso',
            compact('end_date')
        );
    }













    public function balance_sheet_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.balance_sheet')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $pdf = PDF::loadView('financial_report.balance_sheet_pdf', compact(
            'start_date',
            'end_date',
            'office_id'
        ));
        return $pdf->download(trans_choice('general.balance', 1) . ' ' . trans_choice(
            'general.sheet',
            1
        ) . ' : ' . $request->end_date . ".pdf");
    }

    public function balance_sheet_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.balance_sheet')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($end_date)) {
            $data = [

                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("financial_report.balance_sheet_pdf", $data), trans_choice('general.balance', 1) . ' ' . trans_choice(
                'general.sheet',
                1
            ) . ' as at ' . $request->end_date . '.xlsx');
        }
    }

    public function balance_sheet_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.balance_sheet')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($end_date)) {
            $data = [

                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("financial_report.balance_sheet_pdf", $data), trans_choice('general.balance', 1) . ' ' . trans_choice(
                'general.sheet',
                1
            ) . ' as at ' . $request->end_date . '.csv');
        }
    }

    public function expected_repayments(Request $request)
    {
        if (!Sentinel::hasAccess('reports.expected_repayment')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = Office::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('id', '=', $office_id);
                }
            })->get();
        }
        return view(
            'loan_report.expected_repayments',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id'
            )
        );

    }





    public function expected_repayments_cro(Request $request)
    {
        if (!Sentinel::hasAccess('reports.expected_repayment')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $officer_id = $request->officer_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = User::when($officer_id, function ($query) use ($officer_id) {
                if ($officer_id != 0) {
                    $query->where('id', '=', $officer_id);
                }
            })->get();
        }
        return view(
            'loan_report.expected_repayments_cro',
            compact(
                'start_date',
                'end_date',
                'data',
                'officer_id'
            )
        );

    }

    public function expected_repayments_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.expected_repayment')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = Office::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('id', '=', $office_id);
                }
            })->get();
            $pdf = PDF::loadView('loan_report.expected_repayments_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.expected', 1) . ' ' . trans_choice(
                'general.repayment',
                2
            ) . ".pdf");
        }

    }

    public function expected_repayments_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.expected_repayment')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            $data = Office::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('id', '=', $office_id);
                }
            })->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,

            ];
            return Excel::download(new ExportReport("loan_report.expected_repayments_pdf", $data), trans_choice('general.expected', 1) . ' ' . trans_choice(
                'general.repayment',
                2
            ) . '.xlsx');
        }
    }

    public function expected_repayments_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.expected_repayment')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            $data = Office::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('id', '=', $office_id);
                }
            })->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,

            ];
            return Excel::download(new ExportReport("loan_report.expected_repayments_pdf", $data), trans_choice('general.expected', 1) . ' ' . trans_choice(
                'general.repayment',
                2
            ) . '.csv');
        }
    }


    ///--optimizing--//////////////////////////////////////////////////////////////////////HEREREREE/////////////////////////////////////
    public function repayments_report_details(Request $request)
    {
        // Increase memory limit and execution time for large data processing
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 600); // 10 minutes

        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan = $request->id;
        $data = [];
        $part_data = [];
        $reloans_data = [];
        $top_up = [];
        $new_loans = [];
        $advances = [];
        $expenses = [];
        $pending_loans = [];
        $pending_loans_grouped = [];

        $selected_expense_type = $request->selected_expense_type ?? null;
        $expenseTypes = ExpenseType::all();
        if (!empty($start_date)) {
            if ($office_id != 0) {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->where(
                    'office_id',
                    $office_id
                )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $expenses = Expense::whereBetween('date', [$start_date, $end_date])
                    ->where('office_id', $office_id)->with('office')
                    ->get();

                $advances = Advance::whereBetween('date_approved', [$start_date, $end_date])
                    ->where('office_id', $office_id)->with('office')
                    ->get();

                $top_up = LoanTopUp::whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->where('office_id', $office_id)->with('loan')->with('office')->get();

                $pending_loans = Loan::whereIn('status', ['pending', 'approved'])->whereBetween('created_date', [$start_date, $end_date])->where('office_id', $office_id)->get();

                // $reloans_data = LoanTransaction::whereIn('reversal_type',['user','none'])->orderBy('date','asc')->orderBy('id','asc')->whereBetween('date',
                // [$start_date, $end_date])->with('loan')->with('office')->get();


                $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])->whereBetween(
                    'disbursement_date',
                    [$start_date, $end_date]
                )->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->get();


            } else {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();

                $expenses = Expense::whereBetween('date', [$start_date, $end_date])->with('office')
                    ->get();

                $advances = Advance::whereBetween('date_approved', [$start_date, $end_date])->with('office')
                    ->get();

                // $reloans_data = LoanTransaction::whereIn('reversal_type',['user','none'])->orderBy('date','asc')->orderBy('id','asc')->whereBetween('date',
                // [$start_date, $end_date])->with('loan')->with('office')->get();


                $top_up = LoanTopUp::whereBetween('date', [$start_date, $end_date])->where('status', 'approved')->get();

                $pending_loans = Loan::whereIn('status', ['pending', 'approved'])
                    ->whereBetween('created_date', [$start_date, $end_date])->get();

                $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])
                    ->whereBetween('disbursement_date', [$start_date, $end_date])->when($office_id, function ($query) use ($office_id) {
                        if ($office_id != 0) {
                            $query->where('office_id', '=', $office_id);
                        }
                    })->get();
            }

            $pending_loans_grouped = $pending_loans->groupBy('office_id');

        }

        return view('loan_report.repayment_break_down', compact('start_date', 'end_date', 'data', 'part_data', 'reloans_data', 'new_loans', 'office_id', 'top_up', 'expenses', 'advances', 'expenseTypes', 'selected_expense_type', 'pending_loans_grouped', ));
    }



    public function repayments_report_details_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            if ($office_id != 0) {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->where(
                    'office_id',
                    $office_id
                )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $expenses = Expense::whereBetween('date', [$start_date, $end_date])
                    ->where('office_id', $office_id)->with('office')
                    ->get();

                $advances = Advance::whereBetween('date_approved', [$start_date, $end_date])
                    ->where('office_id', $office_id)->with('office')
                    ->get();


                $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])->whereBetween(
                    'disbursement_date',
                    [$start_date, $end_date]
                )->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->get();


            } else {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();
                $expenses = Expense::whereBetween('date', [$start_date, $end_date])->with('office')->get();

                $advances = Advance::whereBetween('date_approved', [$start_date, $end_date])->with('office')
                    ->get();

                $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])->whereBetween(
                    'disbursement_date',
                    [$start_date, $end_date]
                )->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->get();
            }

            $pdf = PDF::loadView('loan_report.repayments_report_details_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'part_data',
                'reloans_data',
                'new_loans',
                'office_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . ".pdf");
        }


    }

    public function repayments_report_details_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            if ($office_id != 0) {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->where(
                    'office_id',
                    $office_id
                )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $expenses = Expense::whereBetween('date', [$start_date, $end_date])
                    ->where('office_id', $office_id)->with('office')
                    ->get();

                $advances = Advance::whereBetween('date_approved', [$start_date, $end_date])
                    ->where('office_id', $office_id)->with('office')
                    ->get();


                $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])->whereBetween(
                    'disbursement_date',
                    [$start_date, $end_date]
                )->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->get();
                $data = [
                    "data" => $data,
                    'part_data' => $part_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'reloans_data' => $reloans_data,
                    'office_id' => $office_id,
                    'new_loans' => $new_loans,
                    'expenses' => $expenses,
                    'advances' => $advances
                ];
            } else {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();
                $expenses = Expense::whereBetween('date', [$start_date, $end_date])->with('office')
                    ->get();

                $advances = Advance::whereBetween('date_approved', [$start_date, $end_date])->with('office')
                    ->get();

                $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])->whereBetween(
                    'disbursement_date',
                    [$start_date, $end_date]
                )->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->get();
                $data = [
                    "data" => $data,
                    'part_data' => $part_data,
                    'reloans_data' => $reloans_data,
                    'new_loans' => $new_loans,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                    'expenses' => $expenses,
                    'advances' => $advances
                ];
            }

            return Excel::download(new ExportReport("loan_report.repayments_report_details_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.xlsx');

        }


    }



    ////////////////////////////////////////////////////////////////////////////////////////////

    public function repayments_report_details_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            if ($office_id != 0) {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->where(
                    'office_id',
                    $office_id
                )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $expenses = Expense::whereBetween('date', [$start_date, $end_date])
                    ->where('office_id', $office_id)->with('office')
                    ->get();

                $advances = Advance::whereBetween('date_approved', [$start_date, $end_date])
                    ->where('office_id', $office_id)->with('office')
                    ->get();


                $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])->whereBetween(
                    'disbursement_date',
                    [$start_date, $end_date]
                )->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->get();
                $data = [
                    "data" => $data,
                    'part_data' => $part_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'reloans_data' => $reloans_data,
                    'office_id' => $office_id,
                    'new_loans' => $new_loans,
                    'expenses' => $expenses,
                    'advances' => $advances
                ];
            } else {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();
                $expenses = Expense::whereBetween('date', [$start_date, $end_date])->with('office')
                    ->get();

                $advances = Advance::whereBetween('date_approved', [$start_date, $end_date])->with('office')
                    ->get();

                $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])->whereBetween(
                    'disbursement_date',
                    [$start_date, $end_date]
                )->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->get();
                $data = [
                    "data" => $data,
                    'part_data' => $part_data,
                    'reloans_data' => $reloans_data,
                    'new_loans' => $new_loans,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                    'expenses' => $expenses,
                    'advances' => $advances
                ];
            }
            return Excel::download(new ExportReport("loan_report.repayments_report_details_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.csv');

        }


    }









    public function repayments_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($start_date)) {
            if ($office_id != 0) {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->get();
            } else {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->get();
            }

        }
        return view(
            'loan_report.repayments_report',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id'
            )
        );
    }

    public function repayments_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            $data = LoanTransaction::where(
                'transaction_type',
                'repayment'
            )->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();
            $pdf = PDF::loadView('loan_report.repayments_report_pdf', compact(
                'start_date',
                'end_date',
                'data'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . ".pdf");
        }


    }









    public function full_repayments_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            if ($office_id != 0) {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

            } else {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

            }

            $pdf = PDF::loadView('loan_report.full_payments_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . ".pdf");
        }


    }

    public function full_repayments_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            if ($office_id != 0) {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    "data" => $data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];


            } else {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    "data" => $data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];


            }
            return Excel::download(new ExportReport("loan_report.full_payments_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.xlsx');
        }


    }

    public function full_repayments_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            if ($office_id != 0) {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    "data" => $data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];


            } else {
                $data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'full_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    "data" => $data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];


            }
            return Excel::download(new ExportReport("loan_report.full_payments_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.csv');
        }


    }



    public function part_repayments_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {

            if ($office_id != 0) {
                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

            } else {
                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

            }

            $pdf = PDF::loadView('loan_report.part_payments_pdf', compact(
                'start_date',
                'end_date',
                'part_data',
                'office_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . ".pdf");
        }


    }

    public function part_repayments_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {

            if ($office_id != 0) {
                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    "part_data" => $part_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];

            } else {
                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    "part_data" => $part_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];

            }
            return Excel::download(new ExportReport("loan_report.part_payments_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.xlsx');


        }


    }

    public function part_repayments_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {

            if ($office_id != 0) {
                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->where(
                        'office_id',
                        $office_id
                    )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    "part_data" => $part_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];

            } else {
                $part_data = LoanTransaction::where(
                    'transaction_type',
                    'repayment'
                )->where('payment_apply_to', 'part_payment')->where('reversed', 0)->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    "part_data" => $part_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];

            }
            return Excel::download(new ExportReport("loan_report.part_payments_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.csv');


        }


    }


    public function reloans_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {

            if ($office_id != 0) {
                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->where(
                    'office_id',
                    $office_id
                )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();

            } else {
                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();

            }
            $pdf = PDF::loadView('loan_report.reloans_pdf', compact(
                'start_date',
                'end_date',
                'reloans_data',
                'office_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . ".pdf");
        }


    }



    public function expected_collections_report_pdf(Request $request)
    {
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->office->province_id;
        $today = date('Y-m-d');
        $last_month = date('Y-m', strtotime($today . '- 1 month'));
        $cycle_date = $last_month . '-' . '31';
        $period_start = '2024-01-01';
        $targetDate = $request->targetDate;
        $compareDate = $request->compareDate;
        $office_id = $request->office_id;
        $branch_name = \App\Models\Office::where('id', $office_id)->first();
        $transactionList = [];

        $LoanArray = [];
        $LoanArrayTwo = [];
        $transactions = [];

        if ($office_id != 0) {
            $BranchLoans = Loan::with('transactions')->where('office_id', $office_id)->whereBetween('first_repayment_date', [$period_start, $cycle_date])->get();

        } else {
            $BranchLoans = [];//Loan::with('transactions')->where('status','disbursed')->get();

        }

        foreach ($BranchLoans as $loan) {
            $reloan = 0;
            foreach ($loan->transactions as $transaction) {
                if ($transaction->payment_apply_to == 'reloan_payment') {
                    $reloan = 1;
                }
                //array_push($transactions,$transaction);
            }
            if ($reloan == 0) {
                array_push($LoanArray, $loan);
                array_push($LoanArrayTwo, $loan);
            }

        }


        $pdf = PDF::loadview('loan_report.expected_collections_pdf', compact('branch_name', 'targetDate', 'compareDate', 'role', 'office_id', 'transactionList', 'userBranch', 'LoanArray', 'LoanArrayTwo', ));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download(('branch uncollected report' . $branch_name->name . ".pdf"));


    }

    public function expected_collections_report_excel(Request $request)
    {
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->office->province_id;
        $targetDate = $request->targetDate;
        $compareDate = $request->compareDate;
        $office_id = $request->office_id;
        $branch_name = \App\Models\Office::where('id', $office_id)->first();
        $transactionList = [];


        if ($office_id != 0) {
            $transactions = LoanTransaction::whereBetween('date', [date('Y-m-d', strtotime($compareDate . ' - 1 months')), date('Y-m-d', strtotime($targetDate . ' - 1 months'))])->where('office_id', $office_id)->get();

        } else {
            $transactions = LoanTransaction::whereBetween('date', [date('Y-m-d', strtotime($compareDate . '- 1 months')), date('Y-m-d', strtotime($targetDate . ' - 1 months'))])->get();

        }

        foreach ($transactions as $transaction) {
            if ($transaction->payment_apply_to == 'reloan_payment' || $transaction->transaction_type == 'disbursement') {
                array_push($transactionList, $transaction);
            }
        }

        $data = [
            'targetDate' => $targetDate,
            'compareDate' => $compareDate,
            'role' => $role,
            'office_id' => $office_id,
            'transactionList' => $transactionList,
            'branch_name' => $branch_name
        ];

        return Excel::download(new ExportReport("loan_report.expected_collections_excel", $data), 'expected colllections report ' . $branch_name->name . '.xlsx');


    }



    public function collections_report_pdf(Request $request)
    {
        $targetDate = $request->targetDate;
        $compareDate = $request->compareDate;
        $office_id = $request->office_id;
        $branch_name = \App\Models\Office::where('id', $office_id)->first();

        if ($office_id != 0) {
            $BranchLoans = Loan::with('transactions')->where('office_id', $office_id)->where('status', 'disbursed')->get();
        } else {
            $BranchLoans = Loan::with('transactions')->where('status', 'disbursed')->get();
        }

        $LoanArray = [];
        $LoanArrayTwo = [];

        foreach ($BranchLoans as $loan) {

            array_push($LoanArray, $loan);
            array_push($LoanArrayTwo, $loan);
        }

        $pdf = PDF::loadView('loan_report.collections_pdf', compact('targetDate', 'compareDate', 'office_id', 'LoanArray', 'LoanArrayTwo', 'branch_name', ));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download(('collections report ' . $branch_name->name . ".pdf"));

    }


    public function collections_report_excel(Request $request)
    {
        $targetDate = $request->targetDate;
        $compareDate = $request->compareDate;
        $office_id = $request->office_id;
        $branch_name = \App\Models\Office::where('id', $office_id)->first();

        if ($office_id != 0) {
            $BranchLoans = Loan::with('transactions')->where('office_id', $office_id)->where('status', 'disbursed')->get();
        } else {
            $BranchLoans = Loan::with('transactions')->where('status', 'disbursed')->get();
        }

        $LoanArray = [];
        $LoanArrayTwo = [];
        foreach ($BranchLoans as $loan) {
            array_push($LoanArray, $loan);
            array_push($LoanArrayTwo, $loan);
        }

        $data = [
            'targetDate' => $targetDate,
            'compareDate' => $compareDate,
            'office_id' => $office_id,
            'branch_name' => $branch_name,
            'LoanArray' => $LoanArray,
            'LoanArrayTwo' => $LoanArrayTwo,
        ];

        return Excel::download(new ExportReport("loan_report.collections_excel", $data), 'colllections report ' . $branch_name->name . '.xlsx');

    }






    public function reloans_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {

            if ($office_id != 0) {
                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->where(
                    'office_id',
                    $office_id
                )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    'reloans_data' => $reloans_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];

            } else {
                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();
                $data = [
                    'reloans_data' => $reloans_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];

            }
            return Excel::download(new ExportReport("loan_report.reloans_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.xlsx');

        }


    }

    public function reloans_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {

            if ($office_id != 0) {
                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->where(
                    'office_id',
                    $office_id
                )->whereBetween(
                        'date',
                        [$start_date, $end_date]
                    )->with('loan')->with('office')->get();
                $data = [
                    'reloans_data' => $reloans_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];

            } else {
                $reloans_data = LoanTransaction::where('transaction_type', 'repayment')->where('payment_apply_to', 'reloan_payment')->where('reversed', 0)->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();
                $data = [
                    'reloans_data' => $reloans_data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'office_id' => $office_id,
                ];

            }
            return Excel::download(new ExportReport("loan_report.reloans_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.csv');

        }


    }


    public function new_loans_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])->whereBetween(
            'disbursement_date',
            [$start_date, $end_date]
        )->when($office_id, function ($query) use ($office_id) {
            if ($office_id != 0) {
                $query->where('office_id', '=', $office_id);
            }
        })->get();

        $pdf = PDF::loadView('loan_report.new_loans_pdf', compact(
            'start_date',
            'end_date',
            'new_loans',
            'office_id'
        ));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download(trans_choice('general.repayment', 2) . ' ' . trans_choice(
            'general.report',
            1
        ) . ".pdf");
    }

    public function new_loans_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $new_loans = Loan::whereIn('status', ['disbursed', 'closed'])->whereBetween(
            'disbursement_date',
            [$start_date, $end_date]
        )->when($office_id, function ($query) use ($office_id) {
            if ($office_id != 0) {
                $query->where('office_id', '=', $office_id);
            }
        })->get();
        $data = [
            'new_loans' => $new_loans,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'office_id' => $office_id,
        ];

        return Excel::download(new ExportReport("loan_report.new_loans_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
            'general.report',
            1
        ) . '.xlsx');

    }



    public function new_loans_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $new_loans = Loan::where('status', 'disbursed', 'closed')->whereBetween(
            'disbursement_date',
            [$start_date, $end_date]
        )->when($office_id, function ($query) use ($office_id) {
            if ($office_id != 0) {
                $query->where('office_id', '=', $office_id);
            }
        })->get();
        $data = [
            'new_loans' => $new_loans,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'office_id' => $office_id,
        ];

        return Excel::download(new ExportReport("loan_report.new_loans_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
            'general.report',
            1
        ) . '.csv');

    }


    public function expense_report_pdf(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $selectedExpenseType = $request->selectedExpenseType;

        $office = Office::find($office_id);

        $expenseTypes = Expense::select('expense_type')->distinct()->get();


        $expensesQuery = Expense::whereBetween('date', [$start_date, $end_date])
            ->where('office_id', $office_id);

        //filter by expense from drpdown
        if (!empty($selectedExpenseType)) {
            $expensesQuery->where('expense_type', $selectedExpenseType);
        }

        $expenses = $expensesQuery->get();


        $pdf = PDF::loadView('loan_report.expense_report_pdf', [
            'expenses' => $expenses,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'office_id' => $office_id,
            'office' => $office,
            'expense_type' => $selectedExpenseType



        ]);
        return $pdf->download('loan_report.expense_report.pdf');
    }

    public function expense_report_excel(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $selectedExpenseType = $request->expense_type_filter;

        $expensesQuery = Expense::whereBetween('date', [$start_date, $end_date])
            ->where('office_id', $office_id)->where('expense_type', $selectedExpenseType)->get();


        if ($selectedExpenseType) {
            $expensesQuery->where('expense_type', $selectedExpenseType);
        }

        $expenses = $expensesQuery->get();

        $data = [
            'expenses' => $expenses,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'office_id' => $office_id,
        ];


        return Excel::download(new ExportReport("loan_report.expense_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice('general.report', 1) . '.xlsx');
    }

    public function expense_report_csv(Request $request)
    {

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $selectedExpenseType = $request->selectedExpenseType;


        $expensesQuery = Expense::whereBetween('date', [$start_date, $end_date])
            ->where('office_id', $office_id);


        if ($selectedExpenseType) {
            $expensesQuery->where('expense_type_id', $selectedExpenseType);
        }

        $expenses = $expensesQuery->get();

        $data = [
            'expenses' => $expenses,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'office_id' => $office_id,
        ];


        return Excel::download(new ExportReport("loan_report.expense_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice('general.report', 1) . '.csv');
    }

    /////////////////////////////////////////////ADVANCES/////////////////////////////////
    public function advance_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;

        $advancesQuery = Advance::whereBetween('date_approved', [$start_date, $end_date])
            ->where('office_id', $office_id);
        $advances = $advancesQuery->get();

        $pdf = PDF::loadView('loan_report.advance_report_pdf', [
            'advances' => $advances,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'office_id' => $office_id,

        ]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download(trans_choice('Salary advances', 2) . ' ' . trans_choice(
            'general.report',
            1
        ) . ".pdf");
    }



    public function advance_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;

        $advances = Advance::where('status', 'active')->whereBetween('date_approved', [$start_date, $end_date])
            ->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->get();
        // Prepare data for export
        $data = [
            'advances' => $advances,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'office_id' => $office_id,
        ];

        return Excel::download(new ExportReport("loan_report.advance_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice('general.report', 1) . '.xlsx');
    }

    public function advance_report_csv(Request $request)
    {

        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;

        $advances = Advance::where('status', 'active')->whereBetween('date_approved', [$start_date, $end_date])
            ->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->get();

        $data = [
            'advances' => $advances,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'office_id' => $office_id,
        ];

        return Excel::download(new ExportReport("loan_report.advance_excel", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice('general.report', 1) . '.csv');
    }








    public function repayments_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            $data = LoanTransaction::where(
                'transaction_type',
                'repayment'
            )->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date
            ];
            return Excel::download(new ExportReport("loan_report.repayments_report_pdf", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.xlsx');

        }


    }

    public function repayments_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.repayments_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            $data = LoanTransaction::where(
                'transaction_type',
                'repayment'
            )->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->with('loan')->with('office')->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date
            ];
            return Excel::download(new ExportReport("loan_report.repayments_report_pdf", $data), trans_choice('general.repayment', 2) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.csv');

        }


    }

    public function collection_sheet(Request $request)
    {
        if (!Sentinel::hasAccess('reports.collection_sheet')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->join("loan_repayment_schedules", 'loans.id', '=', 'loan_repayment_schedules.loan_id')->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date])->get();
        }
        return view(
            'loan_report.collection_sheet',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'loan_officer_id'
            )
        );
    }

    public function collection_sheet_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.collection_sheet')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->join("loan_repayment_schedules", 'loans.id', '=', 'loan_repayment_schedules.loan_id')->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date])->get();
            $pdf = PDF::loadView('loan_report.collection_sheet_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'loan_officer_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.collection', 1) . ' ' . trans_choice(
                'general.sheet',
                2
            ) . ".pdf");
        }


    }

    public function collection_sheet_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.collection_sheet')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->join("loan_repayment_schedules", 'loans.id', '=', 'loan_repayment_schedules.loan_id')->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date])->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'loan_officer_id' => $loan_officer_id
            ];
            return Excel::download(new ExportReport("loan_report.collection_sheet_pdf", $data), trans_choice('general.collection', 1) . ' ' . trans_choice(
                'general.sheet',
                1
            ) . '.xlsx');
        }


    }

    public function collection_sheet_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.collection_sheet')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->join("loan_repayment_schedules", 'loans.id', '=', 'loan_repayment_schedules.loan_id')->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date])->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'loan_officer_id' => $loan_officer_id
            ];
            return Excel::download(new ExportReport("loan_report.collection_sheet_pdf", $data), trans_choice('general.collection', 1) . ' ' . trans_choice(
                'general.sheet',
                1
            ) . '.csv');
        }

    }


    public function age_analysis_principle(Request $request)
    {
        if (!Sentinel::hasAccess('reports.age_analysis_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if (!empty($loan_officer_id)) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

        }

        return view(
            'loan_report.age_analysis_principle',
            compact('end_date', 'data', 'office_id', 'loan_officer_id')
        );
    }




    public function age_analysis_outstanding(Request $request)
    {
        if (!Sentinel::hasAccess('reports.age_analysis_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if (!empty($loan_officer_id)) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

        }

        return view(
            'loan_report.age_analysis_outstanding',
            compact('end_date', 'data', 'office_id', 'loan_officer_id')
        );
    }








    public function age_analysis(Request $request)
    {
        if (!Sentinel::hasAccess('reports.age_analysis_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if (!empty($loan_officer_id)) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

        }

        return view(
            'loan_report.age_analysis',
            compact('end_date', 'data', 'office_id', 'loan_officer_id')
        );
    }








    public function age_analysis_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.age_analysis_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
            $pdf = PDF::loadView('loan_report.age_analysis_pdf', compact(
                'office_id',
                'end_date',
                'data'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.age', 1) . ' ' . trans_choice(
                'general.report',
                2
            ) . ".pdf");
        }
    }


    public function age_analysis_principle_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.age_analysis_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
            $data = [
                "data" => $data,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("loan_report.age_analysis_principle_pdf", $data), trans_choice('general.age_analysis', 1) . ' ' . trans_choice('general.report', 1) . '.xlsx');
        }

    }









    public function age_analysis_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.age_analysis_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
            $data = [
                "data" => $data,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("loan_report.age_analysis_pdf", $data), trans_choice('general.age', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.xlsx');
        }

    }
    public function age_analysis_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.age_analysis_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
            $data = [
                "data" => $data,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("loan_report.age_analysis_pdf", $data), trans_choice('general.age', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.csv');
        }


    }














    public function arrears_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports.arrears_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if (!empty($loan_officer_id)) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

        }

        return view(
            'loan_report.arrears_report',
            compact('end_date', 'data', 'office_id', 'loan_officer_id')
        );
    }

    public function arrears_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.arrears_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }

            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if (!empty($loan_officer_id)) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();



            $pdf = PDF::loadView('loan_report.arrears_report_pdf', compact(
                'office_id',
                'end_date',
                'data'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.arrears', 1) . ' ' . trans_choice(
                'general.report',
                2
            ) . ".pdf");
        }
    }

    public function arrears_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.arrears_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
            $data = [
                "data" => $data,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("loan_report.arrears_report_pdf", $data), trans_choice('general.arrears', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.xlsx');
        }

    }

    public function arrears_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.arrears_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
            $data = [
                "data" => $data,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("loan_report.arrears_report_pdf", $data), trans_choice('general.arrears', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.csv');
        }


    }

    public function loan_portfolio(Request $request)
    {
        if (!Sentinel::hasAccess('reports.loan_portfolio')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $loan_product_id = $request->loan_product_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Office::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('id', '=', $office_id);
                }
            })->get();

        }

        return view(
            'loan_report.loan_portfolio',
            compact('end_date', 'data', 'office_id', 'loan_officer_id', 'loan_product_id')
        );
    }



    public function loan_portfolio_cro(Request $request)
    {
        if (!Sentinel::hasAccess('reports.loan_portfolio')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $loan_officer_id = $request->loan_officer_id;
        $loan_product_id = $request->loan_product_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = User::when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('id', '=', $loan_officer_id);
                }
            })->get();

        }

        return view(
            'loan_report.loan_portfoliocro',
            compact('end_date', 'data', 'loan_officer_id', 'loan_product_id')
        );
    }












    public function loan_portfolio_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.loan_portfolio')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_product_id = $request->loan_product_id;
        $data = [];
        if (!empty($end_date)) {
            $data = Office::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('id', '=', $office_id);
                }
            })->get();
            $pdf = PDF::loadView('loan_report.loan_portfolio_pdf', compact(
                'office_id',
                'end_date',
                'data',
                'loan_product_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.portfolio', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . ".pdf");
        }


    }

    public function loan_portfolio_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.loan_portfolio')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_product_id = $request->loan_product_id;
        $data = [];
        if (!empty($end_date)) {
            $data = Office::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('id', '=', $office_id);
                }
            })->get();
            $data = [
                "data" => $data,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'loan_product_id' => $loan_product_id,
            ];
            return Excel::download(new ExportReport("loan_report.loan_portfolio_pdf", $data), trans_choice('general.portfolio', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.xlsx');
        }

    }

    public function loan_portfolio_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.loan_portfolio')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_product_id = $request->loan_product_id;
        $data = [];
        if (!empty($end_date)) {
            $data = Office::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('id', '=', $office_id);
                }
            })->get();
            $data = [
                "data" => $data,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'loan_product_id' => $loan_product_id,
            ];
            return Excel::download(new ExportReport("loan_report.loan_portfolio_pdf", $data), trans_choice('general.portfolio', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.csv');
        }


    }
    public function loan_book(Request $request)
    {
        if (!Sentinel::hasAccess('reports.loan_book')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_officer_id = $request->loan_officer_id;
        $office_id = $request->office_id;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        $data = [];

        if (!empty($start_date)) {

            $data = Loan::whereBetween(
                'disbursement_date',
                [$start_date, $end_date]
            )->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                if ($loan_product_id != 0) {
                    $query->where('loan_product_id', '=', $loan_product_id);
                }
            })->when($status, function ($query) use ($status) {
                if ($status != 0) {
                    $query->where('status', '=', $status);
                } else {
                    $query->whereNotIn('status', ['pending', 'approved', 'declined', 'withdrawn', 'new']);
                }
            })->with('loan_officer')->with('office')->with('fund')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
        }

        return view(
            'loan_report.loan_book',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'loan_product_id',
                'loan_officer_id',
                'status'
            )
        );
    }


    public function daily_transaction(Request $request)
    {
        if (!Sentinel::hasAccess('reports.daily_transactions_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_officer_id = $request->loan_officer_id;
        $office_id = $request->office_id;
        $gl_account_id = $request->gl_account_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = GlJournalEntry::orderBy('date')->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id)->with('loan')->get();
                }
            })->when($gl_account_id, function ($query) use ($gl_account_id) {
                if ($gl_account_id != 0) {
                    $query->where('gl_account_id', '=', $gl_account_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->orderBy('created_at', 'asc')->get()->groupBy(function ($item) {

                    return Carbon::parse($item->date)->format('Y-m-d');
                });
        }

        return view(
            'financial_report.daily_transaction_report',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'gl_account_id',
                'loan_officer_id'
            )
        );

        return view(
            'financial_report.daily_transaction_report',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'gl_account_id',
                'loan_officer_id'
            )
        );
    }

    public function daily_transaction_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.daily_transactions_reports')) {
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
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->orderBy('date', 'asc')->get();
            $pdf = PDF::loadView('financial_report.daily_transaction_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'gl_account_id'
            ));
            return $pdf->download(trans_choice('general.Transactions', 2) . ' : ' . $request->end_date . ".pdf");
        }
    }




    public function GetAccountStatmentReport(Request $request)
    {
        if (!Sentinel::hasAccess('reports.daily_transactions_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $gl_account_id = $request->gl_account_id;
        $data = [];
        $credit = 0;
        $debit = 0;
        $opcredit = 0;
        $opdebit = 0;
        $current_balance = 0;
        $current_balance2 = 0;
        if (!empty($start_date)) {

            $transactions = GlJournalEntry::where('gl_account_id', $gl_account_id)
                ->where('office_id', $office_id)
                ->whereDate('date', '<', $start_date)
                ->get();

            $credit = $transactions->sum('credit');
            $debit = $transactions->sum('debit');
            $opcredit = $transactions->sum('op_balance_cr');
            $opdebit = $transactions->sum('op_balance_dr');
            $current_balance = $debit - $credit;
            $current_balance2 = $opdebit - $opcredit;


            $data = GlJournalEntry::groupBy('loan_id', true)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($gl_account_id, function ($query) use ($gl_account_id) {
                if ($gl_account_id != 0) {
                    $query->where('gl_account_id', '=', $gl_account_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->orderBy('date', 'asc')->get();
        }
        return view(
            'financial_report.ledger_statement',
            compact(
                'start_date',
                'end_date',
                'data',
                'credit',
                'debit',
                'current_balance',
                'current_balance2',
                'office_id',
                'gl_account_id',
                'opcredit',
                'opdebit'
            )
        );
    }








    public function disbursed_loans_pmec(Request $request)
    {
        if (!Sentinel::hasAccess('reports.disbursed_loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_officer_id = $request->loan_officer_id;
        $office_id = $request->office_id;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        $data = [];
        if (!empty($start_date)) {

            $data = Loan::where('status', 'disbursed')->whereBetween(
                'disbursement_date',
                [$start_date, $end_date]
            )->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                if ($loan_product_id != 0) {
                    $query->where('loan_product_id', '=', $loan_product_id);
                }
            })->when($status, function ($query) use ($status) {
                if ($status != 0) {
                    $query->where('status', '=', $status);
                } else {
                    $query->whereNotIn('status', ['pending', 'approved', 'declined', 'withdrawn', 'new']);
                }
            })->with('loan_officer')->with('office')->with('fund')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
        }

        return view(
            'loan_report.disbursed_loans_pmec',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'loan_product_id',
                'loan_officer_id',
                'status'
            )
        );
    }
































    public function disbursed_loans(Request $request)
    {
        if (!Sentinel::hasAccess('reports.disbursed_loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_officer_id = $request->loan_officer_id;
        $office_id = $request->office_id;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        $data = [];
        if (!empty($start_date)) {

            $data = Loan::where('status', 'disbursed')->whereBetween(
                'disbursement_date',
                [$start_date, $end_date]
            )->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                if ($loan_product_id != 0) {
                    $query->where('loan_product_id', '=', $loan_product_id);
                }
            })->when($status, function ($query) use ($status) {
                if ($status != 0) {
                    $query->where('status', '=', $status);
                } else {
                    $query->whereNotIn('status', ['pending', 'approved', 'declined', 'withdrawn', 'new']);
                }
            })->with('loan_officer')->with('office')->with('fund')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
        }

        return view(
            'loan_report.disbursed_loans',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'loan_product_id',
                'loan_officer_id',
                'status'
            )
        );
    }

    public function disbursed_loans_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.disbursed_loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_officer_id = $request->loan_officer_id;
        $office_id = $request->office_id;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        if (!empty($end_date)) {
            $data = Loan::where('status', 'disbursed')->whereBetween(
                'disbursement_date',
                [$start_date, $end_date]
            )->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                if ($loan_product_id != 0) {
                    $query->where('loan_product_id', '=', $loan_product_id);
                }
            })->when($status, function ($query) use ($status) {
                if ($status != 0) {
                    $query->where('status', '=', $status);
                } else {
                    $query->whereNotIn('status', ['pending', 'approved', 'declined', 'withdrawn', 'new']);
                }
            })->with('loan_officer')->with('office')->with('fund')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

            $pdf = PDF::loadView(
                'loan_report.disbursed_loans_pdf',
                compact(
                    'start_date',
                    'end_date',
                    'data',
                    'office_id',
                    'loan_product_id',
                    'loan_officer_id',
                    'status'
                )
            );
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.disbursed', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . ".pdf");
        }


    }

    public function disbursed_loans_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.disbursed_loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_officer_id = $request->loan_officer_id;
        $office_id = $request->office_id;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        if (!empty($end_date)) {
            $data = Loan::where('status', 'disbursed')->whereBetween(
                'disbursement_date',
                [$start_date, $end_date]
            )->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                if ($loan_product_id != 0) {
                    $query->where('loan_product_id', '=', $loan_product_id);
                }
            })->when($status, function ($query) use ($status) {
                if ($status != 0) {
                    $query->where('status', '=', $status);
                } else {
                    $query->whereNotIn('status', ['pending', 'approved', 'declined', 'withdrawn', 'new']);
                }
            })->with('loan_officer')->with('office')->with('fund')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'loan_product_id' => $loan_product_id,
                'status' => $status,
                'loan_officer_id' => $loan_officer_id,
            ];
            return Excel::download(new ExportReport("loan_report.disbursed_loans_pdf", $data), trans_choice('general.disbursement', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.xlsx');
        }


    }


    public function workings(Request $request)
    {
        $year_use = $request->year;
        $start_month = $request->start_month;
        $end_month = $request->end_month;
        $period_use = $request->period;
        $start_date = $year_use . '-' . $start_month . '-' . '01';
        $end_date = $year_use . '-' . $end_month . '-' . '25';
        $end_date = date("Y-m-t", strtotime($end_date));
        $transactions = [];

        if ($start_month > $end_month) {
            $year_use = null;
        }
        $loans = Loan::with('transactions')->whereBetween('created_date', [$start_date, $end_date])->get();
        $unrecovered_loans = Loan::with('transactions')->where('status', 'disbursed')->whereBetween('first_repayment_date', [$start_date, $end_date])->get();
        $expenses = Expense::with('type')->whereBetween('date', [$start_date, $end_date])->get();
        $expense_types = ExpenseType::get();
        foreach ($loans as $loan) {
            if (date('m', strtotime($loan->created_date)) == date('m', strtotime($end_date))) {
                foreach ($loan->transactions as $transaction) {
                    array_push($transactions, $transaction);
                }

            }
        }

        return view('financial_report.workings', compact('year_use', 'loans', 'transactions', 'start_date', 'end_date', 'expenses', 'expense_types', 'unrecovered_loans', ));
    }



    public function statement_of_financial_position(Request $request)
    {
        $add_depreciation = 0;
        $disallowed_expenses = 0;
        $deferred_tax_allowance = 3583285.68;
        $total = 0;
        $total2 = 0;
        $cost_total = 0;
        $cost_of_sales = [];
        $revenues = [];
        $expense_total = 0;
        $monthly_expense_total = [];
        $revenue_total = 0;
        $years = [];
        $expenses = [];
        $expense_index = [];
        $expense_type_list = [];
        $distribution_expense_type_list = [];
        $distribution_expense_index = [];
        $distribution_array_of_totals = [];
        $distribution_expenses_totals = [];
        $array_of_totals = [];
        $yearly_expenses_totals = [];
        $test_amount = [];
        $yearly_revenue_totals = [];
        $yearly_final_expense_totals = [];
        $yearly_final_distribution_total = [];
        $yearly_cost_total = [];
        $expenses_totals = [];
        $num = 0;
        $years = [];
        $year_index = [];
        $current_year = date('Y-m-d');
        $asset_totals = [];
        $asset_type_index = [];
        $test = [];
        $revenue_total = 0;
        $revenues = [];
        $num = 0;
        $dnum = 0;
        $total = 0;
        $total2 = 0;
        $asset_type_intangible_index = [];
        $yearly_asset_totals = [];
        $yearly_intangible_assets_totals = [];
        $asset_totals_1 = [];
        $intangible_asset_totals = [];
        $intangible_asset_totals_1 = [];
        $yearly_revenue_totals = [];
        $year_total = 0;
        $cash_total = 0;
        $unrecovered_montly_total = 0;
        $intagible_year_total = 0;
        $dates = [];
        $dates_2 = [];
        $yearly_cash_and_cash_equivalent_totals = [];
        $yearly_unrecovered_amounts = [];
        $expense_types = ExpenseType::get();

        $month_dates = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        for ($x = 0; $x < 3; $x++) {
            if ($x != 0) {
                $current_year = date('Y-m', strtotime($current_year . ' - 1 year'));
            }

            array_push($years, date('Y', strtotime($current_year)));
            array_push($year_index, $x);

        }
        $asset_types_intangible = AssetType::where('type', 'INTANGIBLE')->get();
        $asset_types_tangible = AssetType::where('type', 'TANGIBLE')->get();
        $assets = Asset::get();
        $total = 0;
        $total_intangible = 0;
        $index = 0;
        $index_intangible = 0;
        foreach ($asset_types_tangible as $asset_type) {
            array_push($asset_type_index, $index);
            $index = $index + 1;
        }

        foreach ($asset_types_intangible as $asset_type) {
            array_push($asset_type_intangible_index, $index_intangible);
            $index_intangible = $index_intangible + 1;
        }
        foreach ($years as $year) {
            $cash_and_cash_equivalent = Asset::where('asset_type_id', '6')->whereBetween('purchase_date', [$year . '-01-01', $year . '-12-31'])->get();
            foreach ($cash_and_cash_equivalent as $amount) {
                $cash_total = $cash_total + $amount->value;
            }
            array_push($yearly_cash_and_cash_equivalent_totals, $cash_total);
            $cash_total = 0;
            $unrecovered_loans = Loan::with('transactions')->where('status', 'disbursed')->whereBetween('created_date', [$year . '-01-01', $year . '-12-31'])->get();
            $month_dates = [$year . '-' . '01', $year . '-' . '02', $year . '-' . '03', $year . '-' . '04', $year . '-' . '05', $year . '-' . '06', $year . '-' . '07', $year . '-' . '08', $year . '-' . '09', $year . '-' . '10', $year . '-' . '11', $year . '-' . '12'];
            foreach ($month_dates as $date) {
                $start_date = $date . '-' . '01';
                $end_date = $date . '-' . '25';
                $end_date = date("Y-m-t", strtotime($end_date));
                $loans = Loan::with('transactions')->whereBetween('created_date', [$start_date, $end_date])->get();
                foreach ($loans as $loan) {
                    if (date('Y-m', strtotime($loan->created_date)) == $date) {
                        foreach ($loan->transactions as $transaction) {
                            if ($transaction->transaction_type == 'disbursement') {
                                $interest = $transaction->debit * 0.4;
                                $total = $total + $transaction->debit + $interest;
                                $total2 = $total2 + $transaction->debit;
                            }

                            if ($transaction->transaction_type == 'interest_initial') {
                                $total = $total + $transaction->debit / 0.4 + $transaction->debit;
                                $total2 = $total2 + $transaction->debit / 0.4;
                            }
                        }
                    }
                }

                array_push($revenues, $total);
                array_push($cost_of_sales, $total2);

                $total = 0;
                $total2 = 0;

            }

            $out = 0;
            $in = 0;

            foreach ($unrecovered_loans as $loan) {
                foreach ($loan->transactions as $transaction) {
                    if ($transaction->transaction_type == 'disbursement') {
                        $original_balance = $transaction->debit + ($transaction->debit * 0.4);
                    }
                    $out = $out + $transaction->debit;
                    $in = $in + $transaction->credit;
                }
                $current_balance = $out - $in;
                if ($current_balance < $original_balance) {
                    $unrecovered_montly_total = $unrecovered_montly_total + $current_balance;
                }
                $out = 0;
                $in = 0;
                $current_balance = 0;
            }


            foreach ($revenues as $revenue) {
                $revenue_total = $revenue_total + $revenue;
            }

            $revenues = [];

            foreach ($cost_of_sales as $sale) {
                $cost_total = $cost_total + $sale;
            }

            $cost_of_sales = [];

            foreach ($asset_types_tangible as $asset_type) {
                foreach ($assets as $asset) {
                    if ($asset->asset_type_id == $asset_type->id && date('Y', strtotime($asset->created_at)) == $year) {
                        $total = $total + $asset->value;

                    }

                }
                array_push($asset_totals, $total);
                array_push($asset_totals_1, $total);
                $total = 0;
            }


            foreach ($expense_types as $expense_type) {
                foreach ($month_dates as $date) {
                    $start_date = $date . '-' . '01';
                    $end_date = $date . '-' . '25';
                    $end_date = date("Y-m-t", strtotime($end_date));
                    $expenses = Expense::with('type')->whereBetween('date', [$start_date, $end_date])->get();
                    foreach ($expenses as $expense) {

                        if ($expense->expense_type_id == $expense_type->id && date('Y-m', strtotime($expense->date)) == $date) {
                            $expense_total = $expense_total + $expense->amount;
                        }
                    }
                    array_push($monthly_expense_total, $expense_total);
                    $expense_total = 0;
                }

                if ($expense_type->distribution_cost !== 1) {
                    array_push($expense_type_list, $expense_type->name);
                    array_push($expense_index, $num);
                    $num = $num + 1;
                    array_push($array_of_totals, $monthly_expense_total);
                } else {
                    array_push($distribution_expense_type_list, $expense_type->name);
                    array_push($distribution_expense_index, $dnum);
                    $dnum = $dnum + 1;
                    array_push($distribution_array_of_totals, $monthly_expense_total);
                }
                $monthly_expense_total = [];

            }

            $s2 = 0;

            foreach ($array_of_totals as $totals) {
                foreach ($totals as $total) {
                    $s2 = $s2 + $total;
                }
                array_push($expenses_totals, $s2);
                $s2 = 0;
            }

            foreach ($distribution_array_of_totals as $totals) {
                foreach ($totals as $total) {
                    $s2 = $s2 + $total;
                }
                array_push($distribution_expenses_totals, $s2);
                $s2 = 0;
            }

            $final_distribution_expense_total = 0;
            foreach ($distribution_expenses_totals as $item) {
                $final_distribution_expense_total = $final_distribution_expense_total + $item;
            }






            foreach ($asset_types_intangible as $asset_type) {
                foreach ($assets as $asset) {

                    if ($asset->asset_type_id == $asset_type->id && date('Y', strtotime($asset->created_at)) == $year) {
                        $total_intangible = $total_intangible + $asset->value;

                    }

                }
                array_push($intangible_asset_totals_1, $total_intangible);
                array_push($intangible_asset_totals, $total_intangible);

                $total_intangible = 0;
            }


            foreach ($asset_totals_1 as $total) {
                $year_total = $year_total + $total;
            }

            foreach ($intangible_asset_totals_1 as $total_1) {
                $intagible_year_total = $intagible_year_total + $total_1;

            }

            $final_expense_total = 0;
            foreach ($expenses_totals as $item) {
                $final_expense_total = $final_expense_total + $item;

            }
            array_push($yearly_final_distribution_total, $final_distribution_expense_total);
            array_push($yearly_final_expense_totals, $final_expense_total);
            array_push($yearly_revenue_totals, $revenue_total);
            array_push($yearly_intangible_assets_totals, $intagible_year_total);
            array_push($yearly_asset_totals, $year_total);
            array_push($yearly_expenses_totals, $expenses_totals);
            array_push($yearly_cost_total, $cost_total);
            array_push($yearly_unrecovered_amounts, ($unrecovered_montly_total * pow(1.05, 7)));
            $unrecovered_montly_total = 0;
            $asset_totals_1 = [];
            $intangible_asset_totals_1 = [];
            $year_total = 0;
            $intagible_year_total = 0;
            $revenue_total = 0;
            $expenses_totals = [];
            $final_expense_total = 0;
            $final_distribution_expense_total = 0;


        }



        return view('financial_report.statement_of_fin_position', compact(
            'test',
            'dates',
            'dates_2',
            'years',
            'assets',
            'asset_totals',
            'intangible_asset_totals',
            'asset_types_tangible',
            'asset_types_intangible',
            'asset_type_index',
            'yearly_asset_totals',
            'year_index',
            'asset_type_intangible_index',
            'yearly_intangible_assets_totals',
            'yearly_revenue_totals',
            'yearly_expenses_totals',
            'yearly_cost_total',
            'yearly_final_expense_totals',
            'yearly_final_distribution_total',
            'add_depreciation',
            'disallowed_expenses',
            'deferred_tax_allowance',
            'yearly_unrecovered_amounts',
            'yearly_cash_and_cash_equivalent_totals'
        ));
    }

    public function statement_of_comp_income(Request $request)
    {
        $total = 0;
        $total2 = 0;
        $cost_total = 0;
        $cost_of_sales = [];
        $revenues = [];
        $expense_total = 0;
        $monthly_expense_total = [];
        $revenue_total = 0;
        $years = [];
        $expenses = [];
        $expense_index = [];
        $expense_type_list = [];
        $distribution_expense_type_list = [];
        $distribution_expense_index = [];
        $distribution_array_of_totals = [];
        $distribution_expenses_totals = [];
        $array_of_totals = [];
        $yearly_expenses_totals = [];
        $test_amount = [];
        $yearly_revenue_totals = [];
        $yearly_final_expense_totals = [];
        $yearly_final_distribution_total = [];
        $yearly_cost_total = [];
        $expenses_totals = [];
        $num = 0;
        $dnum = 0;
        $year_index = [];
        $month_dates = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        $current_year = date('Y-m-d');
        $expense_types = ExpenseType::get();
        for ($x = 0; $x < 5; $x++) {
            if ($x != 0) {
                $current_year = date('Y-m', strtotime($current_year . ' - 1 year'));
            }
            array_push($years, date('Y', strtotime($current_year)));
            array_push($year_index, $x);

        }

        foreach ($years as $year_use) {
            $month_dates = [$year_use . '-' . '01', $year_use . '-' . '02', $year_use . '-' . '03', $year_use . '-' . '04', $year_use . '-' . '05', $year_use . '-' . '06', $year_use . '-' . '07', $year_use . '-' . '08', $year_use . '-' . '09', $year_use . '-' . '10', $year_use . '-' . '11', $year_use . '-' . '12'];
            foreach ($month_dates as $date) {
                $start_date = $date . '-' . '01';
                $end_date = $date . '-' . '25';
                $end_date = date("Y-m-t", strtotime($end_date));
                $loans = Loan::with('transactions')->whereBetween('created_date', [$start_date, $end_date])->get();
                foreach ($loans as $loan) {
                    if (date('Y-m', strtotime($loan->created_date)) == $date) {
                        foreach ($loan->transactions as $transaction) {
                            if ($transaction->transaction_type == 'disbursement') {
                                $interest = $transaction->debit * 0.4;
                                $total = $total + $transaction->debit + $interest;
                                $total2 = $total2 + $transaction->debit;
                            }

                            if ($transaction->transaction_type == 'interest_initial') {
                                $total = $total + $transaction->debit / 0.4 + $transaction->debit;
                                $total2 = $total2 + $transaction->debit / 0.4;
                            }
                        }
                    }
                }

                array_push($revenues, $total);
                array_push($cost_of_sales, $total2);

                $total = 0;
                $total2 = 0;

            }

            foreach ($revenues as $revenue) {
                $revenue_total = $revenue_total + $revenue;
            }
            $revenues = [];


            foreach ($cost_of_sales as $sale) {
                $cost_total = $cost_total + $sale;
            }
            $cost_of_sales = [];


            foreach ($expense_types as $expense_type) {
                foreach ($month_dates as $date) {
                    $start_date = $date . '-' . '01';
                    $end_date = $date . '-' . '25';
                    $end_date = date("Y-m-t", strtotime($end_date));
                    $expenses = Expense::with('type')->whereBetween('date', [$start_date, $end_date])->get();
                    foreach ($expenses as $expense) {

                        if ($expense->expense_type_id == $expense_type->id && date('Y-m', strtotime($expense->date)) == $date) {
                            $expense_total = $expense_total + $expense->amount;
                        }
                    }
                    array_push($monthly_expense_total, $expense_total);
                    $expense_total = 0;
                }

                if ($expense_type->distribution_cost !== 1) {
                    array_push($expense_type_list, $expense_type->name);
                    array_push($expense_index, $num);
                    $num = $num + 1;
                    array_push($array_of_totals, $monthly_expense_total);
                } else {
                    array_push($distribution_expense_type_list, $expense_type->name);
                    array_push($distribution_expense_index, $dnum);
                    $dnum = $dnum + 1;
                    array_push($distribution_array_of_totals, $monthly_expense_total);
                }
                $monthly_expense_total = [];

            }


            $s2 = 0;

            foreach ($array_of_totals as $totals) {
                foreach ($totals as $total) {
                    $s2 = $s2 + $total;
                }
                array_push($expenses_totals, $s2);
                $s2 = 0;
            }

            foreach ($distribution_array_of_totals as $totals) {
                foreach ($totals as $total) {
                    $s2 = $s2 + $total;
                }
                array_push($distribution_expenses_totals, $s2);
                $s2 = 0;
            }
            $final_distribution_expense_total = 0;
            foreach ($distribution_expenses_totals as $item) {
                $final_distribution_expense_total = $final_distribution_expense_total + $item;
            }

            $final_expense_total = 0;
            foreach ($expenses_totals as $item) {
                $final_expense_total = $final_expense_total + $item;

            }
            array_push($yearly_final_distribution_total, $final_distribution_expense_total);
            array_push($yearly_final_expense_totals, $final_expense_total);
            array_push($yearly_expenses_totals, $expenses_totals);
            array_push($yearly_revenue_totals, $revenue_total);
            array_push($yearly_cost_total, $cost_total);

            $final_distribution_expense_total = 0;
            $final_expense_total = 0;
            $revenue_total = 0;
            $cost_total = 0;
            $expenses_totals = [];
            $array_of_totals = [];
            $distribution_array_of_totals = [];
            $distribution_expenses_totals = [];


        }

        return view('financial_report.statement_of_comp_income', compact('yearly_expenses_totals', 'yearly_revenue_totals', 'years', 'year_index', 'cost_total', 'yearly_cost_total', 'yearly_final_expense_totals', 'expenses_totals', 'yearly_final_distribution_total'));
    }


    public function disbursed_loans_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.disbursed_loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_officer_id = $request->loan_officer_id;
        $office_id = $request->office_id;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        if (!empty($end_date)) {
            $data = Loan::where('status', 'disbursed')->whereBetween(
                'disbursement_date',
                [$start_date, $end_date]
            )->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->when($loan_product_id, function ($query) use ($loan_product_id) {
                if ($loan_product_id != 0) {
                    $query->where('loan_product_id', '=', $loan_product_id);
                }
            })->when($status, function ($query) use ($status) {
                if ($status != 0) {
                    $query->where('status', '=', $status);
                } else {
                    $query->whereNotIn('status', ['pending', 'approved', 'declined', 'withdrawn', 'new']);
                }
            })->with('loan_officer')->with('office')->with('fund')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'loan_product_id' => $loan_product_id,
                'status' => $status,
                'loan_officer_id' => $loan_officer_id,
            ];
            return Excel::download(new ExportReport("loan_report.disbursed_loans_pdf", $data), trans_choice('general.disbursement', 1) . ' ' . trans_choice(
                'general.report',
                1
            ) . '.csv');
        }

    }

    public function client_numbers(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_numbers_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $data = $request->data;


        return view(
            'client_report.client_numbers',
            compact(
                'start_date',
                'end_date',
                'data'
            )
        );
    }

    public function client_numbers_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_numbers_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $data = $request->data;

        if (!empty($end_date)) {
            $pdf = PDF::loadView('client_report.client_numbers_pdf', compact(
                'start_date',
                'end_date',
                'data'
            ));
            //$pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.client', 1) . ' ' . trans_choice(
                'general.number',
                2
            ) . ".pdf");
        }

    }

    public function client_numbers_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_numbers_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [
                'end_date' => $end_date,
                'start_date' => $start_date,
            ];
            return Excel::download(new ExportReport("client_report.client_numbers_pdf", $data), trans_choice('general.client', 1) . ' ' . trans_choice(
                'general.number',
                2
            ) . '.xlsx');
        }


    }

    public function client_numbers_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_numbers_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [
                'end_date' => $end_date,
                'start_date' => $start_date,
            ];
            return Excel::download(new ExportReport("client_report.client_numbers_pdf", $data), trans_choice('general.client', 1) . ' ' . trans_choice(
                'general.number',
                2
            ) . '.csv');

        }


    }

    public function client_listing(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_list_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get a list of clients
            $data = Client::where('status', 'active')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->with('staff')->with('office')->get();

        }

        return view(
            'client_report.client_listing',
            compact('end_date', 'data', 'office_id')
        );
    }

    public function client_listing_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.client_list_reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Client::where('status', 'active')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->with('staff')->with('office')->get();
            $pdf = PDF::loadView('client_report.client_listing_pdf', compact(
                'office_id',
                'end_date',
                'data'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.client', 1) . ' ' . trans_choice(
                'general.report',
                2
            ) . ".pdf");
        }



    }








    public function provisioning(Request $request)
    {
        if (!Sentinel::hasAccess('reports.provisioning')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

        }


        return view(
            'financial_report.provisioning',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'loan_officer_id'
            )
        );
    }

    public function provisioning_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.provisioning')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

            $pdf = PDF::loadView('financial_report.provisioning_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'loan_officer_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.provisioning', 1) . ' ' . trans_choice(
                'general.report',
                2
            ) . ".pdf");
        }


    }

    public function provisioning_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.provisioning')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

            $data = [
                "data" => $data,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'loan_officer_id' => $loan_officer_id,
            ];
            return Excel::download(new ExportReport("financial_report.provisioning_pdf", $data), trans_choice('general.provisioning', 1) . ' ' . trans_choice(
                'general.report',
                2
            ) . '.xlsx');
        }


    }

    public function provisioning_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.provisioning')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $loan_officer_id = $request->loan_officer_id;
        $data = [];
        if (!empty($end_date)) {
            //get disbursed loans within specified period and officer
            $data = Loan::where('status', 'disbursed')->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                if ($loan_officer_id != 0) {
                    $query->where('loan_officer_id', '=', $loan_officer_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();

            $data = [
                "data" => $data,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'loan_officer_id' => $loan_officer_id,
            ];
            return Excel::download(new ExportReport("financial_report.provisioning_pdf", $data), trans_choice('general.provisioning', 1) . ' ' . trans_choice(
                'general.report',
                2
            ) . '.csv');
        }

    }

    public function products_summary(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;


        return view(
            'company_report.products_summary',
            compact(
                'start_date',
                'end_date',
                'data'
            )
        );
    }

    public function products_summary_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if (!empty($end_date)) {
            $pdf = PDF::loadView('company_report.products_summary_pdf', compact(
                'start_date',
                'end_date',
                'data'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.product', 2) . ' ' . trans_choice(
                'general.summary',
                1
            ) . ".pdf");
        }

    }

    public function products_summary_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];

        }


    }

    public function products_summary_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($end_date)) {

            $data = [];

        }


    }

    public function general_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (isset($request->end_date)) {
            $date = $request->end_date;
        } else {
            $date = date("Y-m-d");
        }
        //loan product pie data
        $loan_product_data = [];
        foreach (LoanProduct::all() as $key) {
            if (empty($start_date)) {
                $count = Loan::where('loan_product_id', $key->id)->where(
                    'branch_id',
                    session('branch_id')
                )->whereIn(
                        'status',
                        ['disbursed', 'closed', 'written_off', 'rescheduled']
                    )->count();
            } else {
                $count = Loan::where('loan_product_id', $key->id)->where(
                    'branch_id',
                    session('branch_id')
                )->whereIn(
                        'status',
                        ['disbursed', 'closed', 'written_off', 'rescheduled']
                    )->whereBetween(
                        'release_date',
                        [$start_date, $end_date]
                    )->count();
            }
            array_push($loan_product_data, array(
                'product' => $key->name,
                'value' => $count

            ));
        }
        $monthly_net_income_data = array();
        $loop_date = date_format(
            date_sub(
                date_create($date),
                date_interval_create_from_date_string('1 years')
            ),
            'Y-m-d'
        );
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $loop_date);
            //get loans in that period
            $total_income = 0;
            foreach (GlAccount::where('account_type', 'income')->get() as $key) {
                $cr = GlJournalEntry::where('account_id', $key->id)->where(
                    'year',
                    $d[0]
                )->where(
                        'month',
                        $d[1]
                    )->where(
                        'branch_id',
                        session('branch_id')
                    )->sum('credit');
                $dr = GlJournalEntry::where('account_id', $key->id)->where(
                    'year',
                    $d[0]
                )->where(
                        'month',
                        $d[1]
                    )->where(
                        'branch_id',
                        session('branch_id')
                    )->sum('debit');
                $balance = $cr - $dr;
                $total_income = $total_income + $balance;
            }
            $total_expenses = 0;
            foreach (GlAccount::where('account_type', 'expense')->get() as $key) {
                $cr = GlJournalEntry::where('account_id', $key->id)->where(
                    'year',
                    $d[0]
                )->where(
                        'month',
                        $d[1]
                    )->where(
                        'branch_id',
                        session('branch_id')
                    )->sum('credit');
                $dr = GlJournalEntry::where('account_id', $key->id)->where(
                    'year',
                    $d[0]
                )->where(
                        'month',
                        $d[1]
                    )->where(
                        'branch_id',
                        session('branch_id')
                    )->sum('debit');
                $balance = $dr - $cr;
                $total_expenses = $total_expenses + $balance;
            }
            array_push($monthly_net_income_data, array(
                'month' => date_format(
                    date_create($loop_date),
                    'M' . ' ' . $d[0]
                ),
                'income' => $total_income,
                'expenses' => $total_expenses
            ));
            //add 1 month to start date
            $loop_date = date_format(
                date_add(
                    date_create($loop_date),
                    date_interval_create_from_date_string('1 months')
                ),
                'Y-m-d'
            );
        }
        //user registrations
        $monthly_borrower_data = [];
        $loop_date = date_format(
            date_sub(
                date_create($date),
                date_interval_create_from_date_string('1 years')
            ),
            'Y-m-d'
        );
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $loop_date);
            //get loans in that period
            $count = Client::where(
                'year',
                $d[0]
            )->where(
                    'month',
                    $d[1]
                )->where(
                    'branch_id',
                    session('branch_id')
                )->count();
            array_push($monthly_borrower_data, array(
                'month' => date_format(
                    date_create($loop_date),
                    'M' . ' ' . $d[0]
                ),
                'value' => $count,
            ));
            //add 1 month to start date
            $loop_date = date_format(
                date_add(
                    date_create($loop_date),
                    date_interval_create_from_date_string('1 months')
                ),
                'Y-m-d'
            );
        }
        $monthly_repayments_data = [];
        $loop_date = date_format(
            date_sub(
                date_create($date),
                date_interval_create_from_date_string('1 years')
            ),
            'Y-m-d'
        );
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $loop_date);
            //get loans in that period
            $amount = LoanTransaction::where(
                'transaction_type',
                'repayment'
            )->where('reversed', 0)->where(
                    'year',
                    $d[0]
                )->where(
                    'month',
                    $d[1]
                )->where(
                    'branch_id',
                    session('branch_id')
                )->sum('credit');
            array_push($monthly_repayments_data, array(
                'month' => date_format(
                    date_create($loop_date),
                    'M' . ' ' . $d[0]
                ),
                'value' => $amount,
            ));
            //add 1 month to start date
            $loop_date = date_format(
                date_add(
                    date_create($loop_date),
                    date_interval_create_from_date_string('1 months')
                ),
                'Y-m-d'
            );
        }
        $monthly_actual_expected_data = [];
        $monthly_disbursed_loans_data = [];
        $loop_date = date_format(
            date_sub(
                date_create($date),
                date_interval_create_from_date_string('1 years')
            ),
            'Y-m-d'
        );
        for ($i = 1; $i < 14; $i++) {
            $d = explode('-', $loop_date);
            $actual = 0;
            $expected = 0;
            $principal = 0;
            $actual = $actual + LoanTransaction::where(
                'transaction_type',
                'repayment'
            )->where('reversed', 0)->where(
                    'year',
                    $d[0]
                )->where(
                    'month',
                    $d[1]
                )->where(
                    'branch_id',
                    session('branch_id')
                )->sum('credit');
            foreach (Loan::select(
                "loan_schedules.principal",
                "loan_schedules.interest",
                "loan_schedules.penalty",
                "loan_schedules.fees"
            )->where(
                    'loans.branch_id',
                    session('branch_id')
                )->whereIn(
                    'loans.status',
                    ['disbursed', 'closed', 'written_off']
                )->join(
                    'loan_schedules',
                    'loans.id',
                    '=',
                    'loan_schedules.loan_id'
                )->where('loan_schedules.deleted_at', NULL)->where(
                    'loan_schedules.year',
                    $d[0]
                )->where(
                    'loan_schedules.month',
                    $d[1]
                )->get() as $key) {
                $expected = $expected + $key->interest + $key->penalty + $key->fees + $key->principal;
                $principal = $principal + $key->principal;

            }
            array_push($monthly_actual_expected_data, array(
                'month' => date_format(
                    date_create($loop_date),
                    'M' . ' ' . $d[0]
                ),
                'actual' => $actual,
                'expected' => $expected
            ));
            array_push($monthly_disbursed_loans_data, array(
                'month' => date_format(
                    date_create($loop_date),
                    'M' . ' ' . $d[0]
                ),
                'value' => $principal,
            ));
            //add 1 month to start date
            $loop_date = date_format(
                date_add(
                    date_create($loop_date),
                    date_interval_create_from_date_string('1 months')
                ),
                'Y-m-d'
            );
        }

        $loan_product_data = json_encode($loan_product_data);
        $monthly_net_income_data = json_encode($monthly_net_income_data);
        $monthly_borrower_data = json_encode($monthly_borrower_data);
        $monthly_repayments_data = json_encode($monthly_repayments_data);
        $monthly_actual_expected_data = json_encode($monthly_actual_expected_data);
        $monthly_disbursed_loans_data = json_encode($monthly_disbursed_loans_data);
        return view(
            'company_report.general_report',
            compact(
                'loan_product_data',
                'monthly_net_income_data',
                'monthly_borrower_data',
                'monthly_repayments_data',
                'monthly_actual_expected_data',
                'monthly_disbursed_loans_data',
                'start_date',
                'end_date'
            )
        );
    }

    public function journal(Request $request)
    {
        if (!Sentinel::hasAccess('reports.journals_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view(
            'financial_report.journal',
            compact(
                'start_date',
                'end_date'
            )
        );
    }

    public function ledger(Request $request)
    {
        if (!Sentinel::hasAccess('reports.journals_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view(
            'financial_report.ledger',
            compact(
                'start_date',
                'end_date'
            )
        );
    }

    public function savings_transactions(Request $request)
    {
        if (!Sentinel::hasAccess('reports.savings_transactions')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $data = $request->data;

        if (!empty($start_date)) {
            $data = SavingsTransaction::where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->get();
        }
        return view(
            'savings_report.savings_transactions',
            compact(
                'start_date',
                'end_date',
                'office_id',
                'data'
            )
        );
    }

    public function savings_transactions_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.savings_transactions')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            $data = SavingsTransaction::where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->get();
            $pdf = PDF::loadView('savings_report.savings_transactions_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.savings', 2) . ' ' . trans_choice(
                'general.transaction',
                2
            ) . ".pdf");
        }


    }

    public function savings_transactions_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.savings_transactions')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            $data = SavingsTransaction::where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("savings_report.savings_transactions_pdf", $data), trans_choice('general.savings', 2) . ' ' . trans_choice(
                'general.transaction',
                2
            ) . '.xlsx');
        }


    }

    public function savings_transactions_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.savings_transactions')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        if (!empty($start_date)) {
            $data = SavingsTransaction::where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
            ];
            return Excel::download(new ExportReport("savings_report.savings_transactions_pdf", $data), trans_choice('general.savings', 2) . ' ' . trans_choice(
                'general.transaction',
                2
            ) . '.csv');
        }
    }

    public function savings_accounts(Request $request)
    {
        if (!Sentinel::hasAccess('reports.savings_accounts_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $field_officer_id = $request->field_officer_id;
        $savings_product_id = $request->savings_product_id;
        $data = $request->data;
        if (!empty($start_date)) {
            $data = Savings::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($savings_product_id, function ($query) use ($savings_product_id) {
                if ($savings_product_id != 0) {
                    $query->where('savings_product_id', '=', $savings_product_id);
                }
            })->when($field_officer_id, function ($query) use ($field_officer_id) {
                if ($field_officer_id != 0) {
                    $query->where('field_officer_id', '=', $field_officer_id);
                }
            })->whereBetween(
                    'approved_date',
                    [$start_date, $end_date]
                )->get();
        }
        return view(
            'savings_report.savings_accounts',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'savings_product_id',
                'field_officer_id'
            )
        );
    }

    public function savings_accounts_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $field_officer_id = $request->field_officer_id;
        $savings_product_id = $request->savings_product_id;
        if (!empty($start_date)) {
            $data = Savings::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($savings_product_id, function ($query) use ($savings_product_id) {
                if ($savings_product_id != 0) {
                    $query->where('savings_product_id', '=', $savings_product_id);
                }
            })->when($field_officer_id, function ($query) use ($field_officer_id) {
                if ($field_officer_id != 0) {
                    $query->where('field_officer_id', '=', $field_officer_id);
                }
            })->whereBetween(
                    'approved_date',
                    [$start_date, $end_date]
                )->get();
            $pdf = PDF::loadView('savings_report.savings_accounts_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'field_officer_id',
                'savings_product_id'
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download(trans_choice('general.savings', 2) . ' ' . trans_choice(
                'general.account',
                2
            ) . ".pdf");
        }


    }

    public function savings_accounts_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.savings_accounts_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $field_officer_id = $request->field_officer_id;
        $savings_product_id = $request->savings_product_id;
        if (!empty($start_date)) {
            $data = Savings::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($savings_product_id, function ($query) use ($savings_product_id) {
                if ($savings_product_id != 0) {
                    $query->where('savings_product_id', '=', $savings_product_id);
                }
            })->when($field_officer_id, function ($query) use ($field_officer_id) {
                if ($field_officer_id != 0) {
                    $query->where('field_officer_id', '=', $field_officer_id);
                }
            })->whereBetween(
                    'approved_date',
                    [$start_date, $end_date]
                )->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'field_officer_id' => $field_officer_id,
                'savings_product_id' => $savings_product_id,
            ];
            return Excel::download(new ExportReport("savings_report.savings_accounts_pdf", $data), trans_choice('general.savings', 2) . ' ' . trans_choice(
                'general.account',
                2
            ) . '.xlsx');
        }


    }

    public function savings_accounts_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.savings_accounts_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $field_officer_id = $request->field_officer_id;
        $savings_product_id = $request->savings_product_id;
        if (!empty($start_date)) {
            $data = Savings::when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($savings_product_id, function ($query) use ($savings_product_id) {
                if ($savings_product_id != 0) {
                    $query->where('savings_product_id', '=', $savings_product_id);
                }
            })->when($field_officer_id, function ($query) use ($field_officer_id) {
                if ($field_officer_id != 0) {
                    $query->where('field_officer_id', '=', $field_officer_id);
                }
            })->whereBetween(
                    'approved_date',
                    [$start_date, $end_date]
                )->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'field_officer_id' => $field_officer_id,
                'savings_product_id' => $savings_product_id,
            ];
            return Excel::download(new ExportReport("savings_report.savings_accounts_pdf", $data), trans_choice('general.savings', 2) . ' ' . trans_choice(
                'general.account',
                2
            ) . '.csv');
        }
    }

    public function ledger_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $gl_account_id = $request->gl_account_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = GlAccount::orderBy('gl_code', 'asc')->get();
        }
        return view(
            'financial_report.ledger_report',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'gl_account_id'
            )
        );
    }

    public function ledger_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $gl_account_id = $request->gl_account_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = GlAccount::orderBy('gl_code', 'asc')->get();
            $pdf = PDF::loadView('financial_report.ledger_report_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'gl_account_id'
            ));
            return $pdf->download(trans_choice('general.ledger', 1) . ' : ' . $request->end_date . ".pdf");
        }


    }

    public function ledger_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $gl_account_id = $request->gl_account_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = GlAccount::orderBy('gl_code', 'asc')->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'gl_account_id' => $gl_account_id,
            ];
            return Excel::download(new ExportReport("financial_report.ledger_report_pdf", $data), trans_choice('general.ledger', 1) . '.xlsx');
        }

    }

    public function ledger_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $gl_account_id = $request->gl_account_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = GlAccount::orderBy('gl_code', 'asc')->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'gl_account_id' => $gl_account_id,
            ];
            return Excel::download(new ExportReport("financial_report.ledger_report_pdf", $data), trans_choice('general.ledger', 1) . '.csv');
        }

    }

    public function journals_report(Request $request)
    {
        if (!Sentinel::hasAccess('reports.journals_report')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $office_id = $request->office_id;
        $gl_account_id = $request->gl_account_id;
        $data = [];
        if (!empty($start_date)) {
            //get disbursed loans within specified period and officer
            $data = GlJournalEntry::where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                if ($office_id != 0) {
                    $query->where('office_id', '=', $office_id);
                }
            })->when($gl_account_id, function ($query) use ($gl_account_id) {
                if ($gl_account_id != 0) {
                    $query->where('gl_account_id', '=', $gl_account_id);
                }
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->orderBy('date', 'asc')->get();
        }
        return view(
            'financial_report.journals_report',
            compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'gl_account_id'
            )
        );
    }

    public function journals_report_pdf(Request $request)
    {
        if (!Sentinel::hasAccess('reports.journals_report')) {
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
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->orderBy('date', 'asc')->get();
            $pdf = PDF::loadView('financial_report.journals_report_pdf', compact(
                'start_date',
                'end_date',
                'data',
                'office_id',
                'gl_account_id'
            ));
            return $pdf->download(trans_choice('general.journal', 2) . ' : ' . $request->end_date . ".pdf");
        }


    }

    public function journals_report_excel(Request $request)
    {
        if (!Sentinel::hasAccess('reports.journals_report')) {
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
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->orderBy('date', 'asc')->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'gl_account_id' => $gl_account_id,
            ];
            return Excel::download(new ExportReport("financial_report.journals_report_pdf", $data), trans_choice('general.journal', 1) . '.xlsx');
        }

    }

    public function journals_report_csv(Request $request)
    {
        if (!Sentinel::hasAccess('reports.journals_report')) {
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
            })->whereBetween(
                    'date',
                    [$start_date, $end_date]
                )->orderBy('date', 'asc')->get();
            $data = [
                "data" => $data,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'office_id' => $office_id,
                'gl_account_id' => $gl_account_id,
            ];
            return Excel::download(new ExportReport("financial_report.journals_report_pdf", $data), trans_choice('general.journal', 1) . '.csv');
        }

    }

}
