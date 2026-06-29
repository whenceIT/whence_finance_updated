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
use Illuminate\Support\Facades\Http;


use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;
use Aws\S3\S3Client;

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

    $query = Expense::where('status', 'approved')
        ->orderBy('id', 'desc');

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
    $lmsExpenses = collect();
    $officesToCheck = Office::whereNotNull('withinhere_wallet_id')
    ->where('withinhere_wallet_id', '!=', '')
    ->get();

    foreach ($officesToCheck as $office) {

    try {

        $response = Http::post(
            'https://withinheremobileapi.com/api/v1/lmsuser/transactions',
            [
                'wallet_id' => $office->withinhere_wallet_id,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]
        );

        $result = $response->json();

        if (empty($result['transactions'])) {
            continue;
        }

        foreach ($result['transactions'] as $tx) {

            $charge = (float) ($tx['charge'] ?? 0);

            if ($charge <= 0) {
                continue;
            }

            $expense = new \stdClass();

            $expense->amount = $charge;

            $expense->date =
                \Carbon\Carbon::parse(
                    $tx['created_at']
                )->format('Y-m-d');

            $expense->name =
                'Wallet Charge';

            $expense->recurring = 0;

            $expense->proof_of_payment = null;

            $expense->created_by = null;

            $expense->office = $office;

            $expense->type = (object)[
                'name' => 'Withinhere Wallet Charges'
            ];

            $expense->wallet_charge = true;

            $lmsExpenses->push($expense);
        }

    } catch (\Exception $e) {

        \Log::error($e->getMessage());

    }
}

$data = $data
    ->concat($lmsExpenses)
    ->sortByDesc('date')
    ->values();



    if ($request->ajax()) {
        return response()->json($data);
    }

    return view('expense.data', compact(
        'data',
        'start_date',
        'end_date',
        'office_id',
        'offices'
    ));
}

        public function dashboard(Request $request)
    {
     $start_date = $request->start_date ?? date('Y-m-01');
$end_date = $request->end_date ?? date('Y-m-t');

        $response = Http::get(
            'https://lms2backend.whencefinancesystem.com/expense-dashboard',
            [
                'start_date' => $start_date,
                'end_date' => $end_date
            ]
        );

        $data = $response->json();

      
        return view('expense.dashboard', [

    'institution' => $data['institution'],

    'provinces' => $data['provinces'],
    'branches' => $data['branches'],
    'categories' => $data['categories'],
    'expenses' => $data['expenses'],

    'topProvince' => $data['topProvince'],
    'topBranch' => $data['topBranch'],

    'monthlyTrend' => $data['monthlyTrend'],
    'categoryBreakdown' => $data['categoryBreakdown'],

    'monthlyComparison' => $data['monthlyComparison'],

    'start_date' => $start_date,
    'end_date' => $end_date
]);
    }



public function checkDuplicate(Request $request)
{
    $expenses = Expense::where('office_id', $request->office_id)
        ->where('expense_type_id', $request->expense_type_id)
        ->where('amount', $request->amount)
        ->whereDate('date', $request->date)
        ->latest()
        ->take(5)
        ->get();

    return response()->json($expenses);
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

        $recentExpenses = Expense::with('office')
    ->latest()
    ->take(10)
    ->get();


    
        $office_id = Sentinel::getUser()->office_id;
$office = Office::find($office_id);

if ($office && $office->withinhere_wallet_id == null) {
    return redirect('/user/verify_wallet');
}

$withinhere_wallet_id = $office->withinhere_wallet_id;


      $response = Http::timeout(60)
                ->post(
                    'https://withinheremobileapi.com/api/v1/lmsuser/branch_ledger',
                    [
                        'wallet_id' => $withinhere_wallet_id,
                        'start_date' => '2025-01-01',
                        'end_date' => '2025-01-01'
                    ]
                );


                   if ($response->successful()) {
            $data = $response->json();

            $cashBalance = $data['user']['cash_balance'] ?? null;
            $user_id = $data['user']['id'] ?? null;
        }



        
        return view('expense.create', compact('offices','recentExpenses','cashBalance','user_id'));
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
// $existingExpense = Expense::where('office_id', $request->office_id)
//     ->where('expense_type_id', $request->expense_type_id)
//     ->where('amount', $request->amount)
//     ->whereDate('date', $request->date)
//     ->where('created_at', '>=', now()->subHours(24))
//     ->first();

// if ($existingExpense) {

//     Flash::warning(
//         'Possible duplicate expense detected. Expense #' .
//         $existingExpense->id .
//         ' already exists.'
//     );

//     return redirect()->back()->withInput();
// }


$currentTime = now()->format('H:i');

if ($currentTime >= '19:00' || $currentTime < '07:00') {
    Flash::warning('Expenses cannot be added between 17:30 and 07:00.');
    return redirect()->back();
}

  $paymentType = $request->payment_type;

//     if ($paymentType == 'mobile_money') {

//     $url = 'https://withinheremobileapi.com/api/v1/transfer/withdraw-to/mobile';

//     $payload = [
//         'amount' => $request->amount,
//         'phone' => $request->phone,
//         'reason' => 'new loan disbursement',
//         'user_id' => $request->user_id,
//         'operator'=> $request->hidden_operator,
//         'payout_type' => 'withinhere_to_mno',
//         'totalDeducted' => $request->total_deducted
//     ];

// } else {

//     $url = 'https://withinheremobileapi.com/api/v1/transfer/transfer-to/bank';

//     $payload = [
//         'amount' => $request->amount,
//         'user_id' => $request->user_id,
//         'bankId' => $request->bank_id,
//         'accountNumber' => $request->account_number,
//         'reason' => 'new loan disbursement',
//         'payout_type' => 'withinhere_to_bank',
//         'totalDeducted' => $request->total_deducted
//     ];
// }


// try {

//     $response = Http::post($url, $payload);

//     if (!$response->successful()) {

//          $body = $response->body();

//     Flash::success('API Error: ' . $body);
    
//     }

//     $result = $response->json();

// } catch (\Exception $e) {

//     Flash::success('Could not connect to payment service.');

//     return redirect()->back();
// }


// if (
//     !isset($result['status']) ||
//     $result['status'] !== 'pending'
// ) {

//     Flash::success('Transfer request was rejected.');

//    return redirect()->back();
// }





        $expense = new Expense();
        $expense->created_by_id = Sentinel::getUser()->id;
        $expense->office_id = $request->office_id;
        $expense->expense_type_id = $request->expense_type_id;
        $expense->amount = $request->amount;
        $expense->name = $request->name;
        $expense->notes = $request->notes;
        $expense->date = $request->date;
        $expense->deposit_method = $request->payment_type;
        $expense->reference_number = $request->payment_type;
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
//    $validator = Validator::make($request->all(), [
//     'proof_of_payment' => 'required|mimes:jpeg,png,jpg,gif,pdf|max:5120',
// ]);

// if ($validator->fails()) {
//     Flash::warning(trans('general.validation_error'));
//     return redirect()->back()->withInput()->withErrors($validator);
// }

// if ($request->hasFile('proof_of_payment')) {

//     $file = $request->file('proof_of_payment');

//     $fileName =
//         'expense_proofs/' .
//         date('Y') . '/' .
//         date('m') . '/' .
//         time() . '_' .
//         preg_replace(
//             '/[^A-Za-z0-9\.\-_]/',
//             '',
//             $file->getClientOriginalName()
//         );

//     $s3Client = new S3Client([
//         'version' => 'latest',
//         'region' => 'nyc3',
//         'endpoint' => 'https://nyc3.digitaloceanspaces.com',
//         'credentials' => [
//             'key' => 'DO00RP9FA3QZTA3JV637',
//             'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
//         ],
//     ]);

//     $result = $s3Client->putObject([
//         'Bucket' => 'wfssystem',
//         'Key' => $fileName,
//         'Body' => fopen($file->getPathname(), 'r'),
//         'ACL' => 'public-read',
//         'ContentType' => $file->getMimeType(),
//     ]);

//     $expense->proof_of_payment = $result['ObjectURL'];
// }
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
