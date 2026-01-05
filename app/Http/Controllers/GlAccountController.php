<?php

namespace App\Http\Controllers;

use App\Models\GlAccount;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class GlAccountController extends Controller
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
        if (!Sentinel::hasAccess('accounting.gl_accounts.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = GlAccount::all();
        return view('gl_account.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('gl_account.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required',
            'gl_code' => 'required|unique:gl_accounts',
            'account_type' => 'required'
        );
        $messages = [
            'name.required' => 'Name is required',
            'gl_code.required' => 'GL Code is required',
            'gl_code.unique' => 'The GL Code already exists',
            'account_type.required' => 'Account type is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $gl_account = new GlAccount();
            $gl_account->name = $request->name;
            $gl_account->parent_id = $request->parent_id;
            $gl_account->gl_code = $request->gl_code;
            $gl_account->account_type = $request->account_type;
            $gl_account->manual_entries = $request->manual_entries;
            $gl_account->active = $request->active;
            $gl_account->notes = $request->notes;
            $gl_account->save();
            Flash::success(trans('general.successfully_saved'));
            return redirect('accounting/gl_account/data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
    }


    public function edit($gl_account)
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return View::make('gl_account.edit', compact('gl_account'))->render();
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
        if (!Sentinel::hasAccess('accounting.gl_accounts.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $gl_account = GlAccount::find($id);
        $gl_account->name = $request->name;
        $gl_account->parent_id = $request->parent_id;
        $gl_account->gl_code = $request->gl_code;
        $gl_account->account_type = $request->account_type;
        $gl_account->manual_entries = $request->manual_entries;
        $gl_account->active = $request->active;
        $gl_account->notes = $request->notes;
        $gl_account->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('accounting/gl_account/data');
    }

    /**
     * Export GL Accounts to CSV
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $data = GlAccount::all();

        // Set headers for CSV download
        $filename = 'chart_of_accounts_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'GL Code',
                'Name',
                'Type',
                'Balance',
                'Unreconciled Balance',
                'Notes'
            ]);

            // Add data rows
            foreach ($data as $account) {
                $transactions = \App\Helpers\GeneralHelper::gl_account_balance($account->id);
                $unreconciled_transactions = \App\Helpers\GeneralHelper::gl_account_unreconciled_balance($account->id);
                $balance = 0;
                $unreconciled_balance = 0;

                if (!empty($transactions)) {
                    if ($account->account_type == "asset" || $account->account_type == "expense") {
                        $balance = $transactions->debit - $transactions->credit;
                    }
                    if ($account->account_type == "liability" || $account->account_type == "income" || $account->account_type == "equity") {
                        $balance = $transactions->credit - $transactions->debit;
                    }
                }

                if (!empty($unreconciled_transactions)) {
                    if ($account->account_type == "asset" || $account->account_type == "expense") {
                        $unreconciled_balance = $unreconciled_transactions->debit - $unreconciled_transactions->credit;
                    }
                    if ($account->account_type == "liability" || $account->account_type == "income" || $account->account_type == "equity") {
                        $unreconciled_balance = $unreconciled_transactions->credit - $unreconciled_transactions->debit;
                    }
                }

                // Map account type to readable format
                $type_map = [
                    'expense' => trans_choice('general.expense', 1),
                    'asset' => trans_choice('general.asset', 1),
                    'equity' => trans_choice('general.equity', 1),
                    'liability' => trans_choice('general.liability', 1),
                    'income' => trans_choice('general.income', 1),
                ];
                $account_type = isset($type_map[$account->account_type]) ? $type_map[$account->account_type] : $account->account_type;

                fputcsv($file, [
                    $account->gl_code,
                    $account->name,
                    $account_type,
                    number_format($balance, 2),
                    number_format($unreconciled_balance, 2),
                    strip_tags($account->notes)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('accounting.gl_accounts.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        GlAccount::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('accounting/gl_account/data');
    }
}
