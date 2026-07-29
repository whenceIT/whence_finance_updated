<?php

namespace App\Http\Controllers;

use App\Events\LoanApproved;
use App\Events\LoanCreated;
use App\Events\LoanDisbursed;
use App\Events\RepaymentCreated;
use App\Models\Payroll;
use App\Events\RepaymentUpdated;
use App\Events\TransactionUpdated;
use App\Helpers\GeneralHelper;
use App\Mail\RepaymentScheduleEmail;
use App\Mail\RepaymentCreatedEmail;
use App\Mail\LoanStatementEmail;
use App\Models\Charge;
use App\Models\Client;
use App\Models\Collateral;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Document;
use App\Models\GlJournalEntry;
use App\Models\GroupLoanAllocation;
use App\Models\Guarantor;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanCharge;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionUnapproved;
use App\Models\LoanTransactionsPending;
use App\Services\BulkSMS;
use App\Services\AuditorService;
use App\Models\LoanTopUp;
use App\Models\Note;
use App\Models\PaymentDetail;
use App\Models\Savings;
use App\Models\SavingsTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Models\WaiverTransactionUnapproved;
use App\Models\ChargeTransactionUnapproved;
use Illuminate\Support\Facades\DB;
use PDF;
use App\Models\Office;
use App\Models\PaymentType;
use App\Models\UserRole;
use App\Models\TargetTracker;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use App\Models\PayrollApplicant;
use App\Models\AppraisalAnswer;
use Illuminate\Support\Facades\Http;
use App\Models\CarryOver;
use App\Models\Province;
use App\Models\Notifix;
use App\Services\NotifixService;
use App\Models\ClientAppLoanApplications;
use App\Models\ClientAppUsers;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;


class LoanController extends Controller
{
    protected $bulkSms;
    protected $auditorService;

    public function __construct(AuditorService $auditorService)
    {
        $this->middleware('sentinel');
        $this->bulkSms = app(BulkSMS::class);
        $this->auditorService = $auditorService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Sentinel::getUser();

        // Log audit for accessing active loans
        $this->auditorService->logCustomAudit(
            'App\Models\User',
            $user->id,
            'accessed active loans view',
            $user->id,
            $request,
            [],
            [
                'action' => 'viewed_active_loans',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'query' => $request->input('query', '')
            ],
            'loan_access'
        );

        $query = $request->input('query');
        $loans = [];

        if ($query) {
            $loans = Loan::where('status', 'disbursed')
                ->where(function ($q) use ($query) {
                    $q->whereHas('client', function ($q) use ($query) {
                        $q->where('first_name', 'like', "%{$query}%")
                            ->orWhere('last_name', 'like', "%{$query}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"]);
                    })
                        ->orWhereHas('office', function ($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%");
                        })
                        ->orWhere('id', 'like', "%{$query}%");
                })
                ->with('repayment_schedules')
                ->get();
        }

        return view('loan.data', compact('loans', 'query'));
    }

    public function my_index()
    {
        if (!Sentinel::hasAccess('loans.my_loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $myTransactions = [];
        $staff_id = Sentinel::getUser()->id;
        $data = Loan::where('status', 'disbursed')->with('transactions')->where('loan_officer_id', $staff_id)->get();
        foreach ($data as $loan) {
            foreach ($loan->transactions as $transaction) {
                array_push($myTransactions, $transaction);
            }
        }
        //$loan_transaction = LoanTransaction::get();
        return view('loan.my_loans', compact('data', 'myTransactions'));
    }


    public function branch_index()
    {

        if (!Sentinel::hasAccess('loans.branch_loans')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }


        $user = Sentinel::getUser();
        $userId = $user->id;
        $role = UserRole::where('user_id', $userId)->first();
        $roleId = $role ? $role->role_id : null;

        $query = Loan::where('status', 'disbursed')->with('loan_officer')->with('office')->with('transactions');

        // --- Role-based scope ---
        if ($roleId == 1) {
            // Admin — sees ALL loans
        } elseif ($roleId == 4) {
            // Loan Officer / Branch Manager — own office only
            $officeId = $user->office_id;
            $query->where('office_id', $officeId);
        } elseif ($roleId == 12) {
            // DM Manager — own district
            $userOffice = $user->office;
            $districtId = $userOffice ? $userOffice->district_id : null;
            $query->whereHas('office', function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            });
        } elseif ($roleId == 6) {
            // Provincial Manager — own province
            $provinceId = $user->office->province_id;
            $query->whereHas('office', function ($q) use ($provinceId) {
                $q->where('province_id', $provinceId);
            });
        } else {
            // Default: scope to loans created by the user (Loan Consultants) or assigned to them
            $query->where(function ($q) use ($userId) {
                $q->where('created_by_id', $userId)
                  ->orWhere('loan_officer_id', $userId);
            });
        }

        // Log audit for branch active loans
        $this->auditorService->logBranchLoanAccess($user, request());
        $data = $query->get();
        return view('loan.branch_loans', compact('data'));
    }


public function search(Request $request)
{
    $users = \App\Models\User::where(function ($q) use ($request) {
            $q->where('first_name', 'like', '%' . $request->search . '%')
              ->orWhere('last_name', 'like', '%' . $request->search . '%');
        })
        ->get();

    $results = [];

    foreach ($users as $user) {

        if (!Sentinel::findUserById($user->id)->inRole('client')) {

            $results[] = [
                'id' => $user->id,
                'text' => $user->first_name . ' ' . $user->last_name
            ];
        }
    }

    return response()->json([
        'results' => $results
    ]);
}


    public function reloan_approvals()
    {


        $user = Sentinel::getUser();
        // Log audit for accessing reloan approvals
        $this->auditorService->logReloanApprovalsAccess($user, request());

        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

         $userId = Sentinel::getUser()->id;
        $office_id = Sentinel::getUser()->office_id;
          $roleNew = UserRole::where('user_id', $userId)->first();



          
      if($roleNew->role_id == '4' || $roleNew->role_id == '12' || $roleNew->role_id == "6"){

 $branchStaffCount = User::where('office_id', $office_id)->where('status','Active')->count();
       $existing_payroll_count = Payroll::where('office_id', $office_id)
    ->whereYear('payroll_date', now()->year)
    ->whereMonth('payroll_date', now()->month)
    ->count();

//     if ($branchStaffCount !== $existing_payroll_count) {
//     return redirect('/payroll/create_wage_bill');
// }
      }

        if (Sentinel::hasAccess('settings')) {
            $data = LoanTransactionsPending::get();
        } else {
            $data = LoanTransactionsPending::where('office_id', $office_id)->get();
        }

        return view('loan.reloan_approvals', compact('data'));
    }


    public function dormant_loans(){

         if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }


        $provinces = Province::get();

        return view('loan.dormant_loans',compact('provinces'));
    }


    public function collections()
    {
        $userBranch = Sentinel::getUser()->office_id; //
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userProvince = Sentinel::getUser()->province_id;
        $province_branches = Office::where('province_id', $userProvince)->get();
        $BranchLoans = [];

        if ($role->role_id == '6') {
            foreach ($province_branches as $province_branch) {
                $branch_loans = Loan::with('transactions')->where('office_id', $province_branch->id)->where('status', 'disbursed')->get();
                foreach ($branch_loans as $loan) {
                    array_push($BranchLoans, $loan);
                }
            }
        }


        if ($role->role_id == '4') {
            $branch_loans = Loan::with('transactions')->where('office_id', $userBranch)->where('status', 'disbursed')->get();
            foreach ($branch_loans as $loan) {
                array_push($BranchLoans, $loan);
            }
        }


        if ($role->role_id == '1') {
            $branch_loans = Loan::with('transactions')->where('status', 'disbursed')->get();
            foreach ($branch_loans as $loan) {
                array_push($BranchLoans, $loan);
            }

        }



        //$BranchLoans = Loan::with('transactions')->where('office_id',$userBranch)->where('status','disbursed')->get();
        $LoanArray = [];
        $LoanArrayTwo = [];
        foreach ($BranchLoans as $loan) {
            array_push($LoanArray, $loan);
            array_push($LoanArrayTwo, $loan);
        }

        return view('loan.collections', compact('role', 'LoanArray', 'BranchLoans', 'LoanArrayTwo', ));
    }



    public function my_collections(Request $request)
    {

        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->province_id;
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $office_id = $request->office_id;
        $bf_loans = [];
        $expected_loans = [];
        $reloan_count = 0;
        $LoanArray = [];


        $loans = Loan::with('transactions')->where('status', 'disbursed')->whereBetween('first_repayment_date', [$compareDate, $targetDate])->where('loan_officer_id', $userId)->get();



        foreach ($loans as $loan) {
            array_push($LoanArray, $loan);
        }


        $branch_name = \App\Models\Office::where('id', $office_id)->first();

        return view('loan.my_collections', compact('targetDate', 'compareDate', 'branch_name', 'office_id', 'userProvince', 'role', 'userBranch', 'LoanArray'));

    }

    public function my_expected_collections(Request $request)
    {

        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->province_id;
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $office_id = $request->office_id;
        $transactionList = [];


        $transactions = LoanTransaction::whereBetween('date', [date('Y-m-d', strtotime($compareDate . ' - 1 months')), date('Y-m-d', strtotime($targetDate . ' - 1 months'))])->where('created_by_id', $userId)->get();



        foreach ($transactions as $transaction) {
            if ($transaction->payment_apply_to == 'reloan_payment' || $transaction->transaction_type == 'disbursement') {
                array_push($transactionList, $transaction);
            }
        }




        return view('loan.my_expected_collections', compact('targetDate', 'compareDate', 'role', 'office_id', 'transactionList', 'userBranch', 'userProvince'));

    }




    public function detailed_collections(Request $request, $id)
    {
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $BranchLoans = Loan::with('transactions')->where('office_id', $id)->where('status', 'disbursed')->get();
        $branch_name = \App\Models\Office::where('id', $id)->first();
        $LoanArray = [];
        $LoanArrayTwo = [];
        foreach ($BranchLoans as $loan) {
            array_push($LoanArray, $loan);
            array_push($LoanArrayTwo, $loan);
        }
        return view('loan.detailed_collections', compact('LoanArray', 'BranchLoans', 'LoanArrayTwo', 'targetDate', 'compareDate', 'branch_name'));
    }




    public function adjust_next_repayment()
    {
        $reloan_count = 0;
        $loans = Loan::with('transactions')->where('status', 'disbursed')->get();
        foreach ($loans as $loan) {
            foreach ($loan->transactions as $transaction) {
                if ($transaction->payment_apply_to == 'reloan_payment') {
                    $reloan_count = $reloan_count + 1;
                }
            }

            $adjusted_date = date('Y-m-d', strtotime($loan->created_date . '+ 1 month'));
            $adjusted_date = date('Y-m-d', strtotime($adjusted_date . '+' . $reloan_count . 'month'));
            $loan->first_repayment_date = $adjusted_date;
            $loan->save();
            $reloan_count = 0;

            //GET LOAN USING ID AND CHANGE THE NEXT REPAYMENT DATE TO THE RELOAN COUNT NUMBER OF MONTHs
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('dashboard');
    }

    public function notification_test()
    {
        Http::post('https://notifications.whencefinancesystem.com/emit', [
            'event' => 'notification.created',
            'data' => [
                'test' => 'test',
            ]
        ]);
        Flash::success(trans('general.successfully_saved'));
        return redirect('setting/fail_safe');
    }


    public function payroll_applicant($id)
    {

        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $applicant = PayrollApplicant::where('id', $id)->first();

        return view('payroll_application.payroll_applicant', compact('applicant'));
    }


    public function decline_applicant($id)
    {
        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $applicant = PayrollApplicant::find($id);
        $applicant->status = 'declined';
        $applicant->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/payroll_loan/' . $id . '/payroll_applicant');
    }


    public function approve_applicant($id)
    {

        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $applicant = PayrollApplicant::find($id);
        $applicant->status = 'approved';
        $applicant->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/payroll_loan/' . $id . '/payroll_applicant');
    }

    public function pending_list()
    {

        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $data = PayrollApplicant::where('status', 'pending')->get();
        return view('payroll_application.pending_list', compact('data'));
    }


    public function approved_list()
    {

        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $data = PayrollApplicant::where('status', 'approved')->get();
        return view('payroll_application.approved_list', compact('data'));
    }


    public function declined_list()
    {

        if (!Sentinel::hasAccess('settings')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $data = PayrollApplicant::where('status', 'declined')->get();
        return view('payroll_application.declined_list', compact('data'));
    }


    public function new_collections(Request $request)
    {
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->province_id;
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $office_id = $request->office_id;
        $bf_loans = [];
        $expected_loans = [];
        $reloan_count = 0;
        $LoanArray = [];


        if ($office_id != 0) {
            $loans = Loan::with('transactions')->where('status', 'disbursed')->whereBetween('first_repayment_date', [$compareDate, $targetDate])->where('office_id', $office_id)->get();

        } else {

            $loans = Loan::with('transactions')->where('status', 'disbursed')->whereBetween('first_repayment_date', [$compareDate, $targetDate])->get();

        }

        foreach ($loans as $loan) {
            array_push($LoanArray, $loan);
        }


        $branch_name = \App\Models\Office::where('id', $office_id)->first();

        return view('loan.new_collections', compact('targetDate', 'compareDate', 'branch_name', 'office_id', 'userProvince', 'role', 'userBranch', 'LoanArray'));
    }


    public function branch_uncollected(Request $request)
    {
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->province_id;
        $today = date('Y-m-d');
        $last_month = date('Y-m', strtotime($today . '- 1 month'));
        $cycle_date = $last_month . '-' . '31';
        $period_start = '2024-01-01';
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $office_id = $request->office_id;
        $transactionList = [];

        $LoanArray = [];
        $LoanArrayTwo = [];
        $full_loans = [];
        $part_loans = [];
        $transactions = [];


        if ($office_id != 0) {
            $BranchLoans = Loan::with('transactions')->where('office_id', $office_id)->where('status', 'disbursed')->whereBetween('first_repayment_date', [$compareDate, $targetDate])->get();


        } else {
            $BranchLoans = Loan::with('transactions')->whereBetween('first_repayment_date', [$compareDate, $targetDate])->get();
        }



        foreach ($BranchLoans as $loan) {
            $reloan = 0;
            $part_payment = 0;
            foreach ($loan->transactions as $transaction) {
                if ($transaction->payment_apply_to == 'reloan_payment') {
                    $reloan = 1;
                }

                if ($transaction->payment_apply_to == 'part_payment') {
                    $part_payment = 1;
                }
                //array_push($transactions,$transaction);
            }
            if ($reloan == 0) {
                array_push($LoanArray, $loan);
                array_push($LoanArrayTwo, $loan);
            }

            if ($part_payment == 1) {
                array_push($part_loans, $loan);
            }

            if ($part_payment == 0 && $reloan == 0) {
                array_push($full_loans, $loan);
            }
        }


        return view('loan.branch_uncollected', compact('targetDate', 'compareDate', 'role', 'office_id', 'transactionList', 'userBranch', 'LoanArray', 'LoanArrayTwo', 'full_loans', 'part_loans', 'userProvince'));
    }



    public function branch_graph()
    {
        $todaysDate = date('Y-m-d');
        $office_loans = [];
        $office_names = [];
        $newDate = date('Y-m-d', strtotime($todaysDate . '- 13 months'));
        $offices = Office::get();
        foreach ($offices as $office) {
            $loans = Loan::with('transactions')->where('created_date', '>', $newDate)->where('office_id', $office->id)->get();
            array_push($office_loans, $loans);
            array_push($office_names, $office->name);
        }


        return view('loan.branch_graph', compact('offices', 'office_loans', 'office_names'));
    }

    public function lusaka_graph()
    {

        $todaysDate = date('Y-m-d');
        $office_loans = [];
        $office_names = [];
        $newDate = date('Y-m-d', strtotime($todaysDate . '- 13 months'));
        $offices = Office::where('province_id', 1)->get();
        foreach ($offices as $office) {
            $loans = Loan::with('transactions')->whereBetween('created_date', ['2025-01-01', '2025-12-31'])->where('office_id', $office->id)->get();
            array_push($office_loans, $loans);
            array_push($office_names, $office->name);
        }

        return view('loan.lusaka_graph', compact('offices', 'office_loans', 'office_names'));

    }

    public function expected_collections(Request $request)
    {
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->province_id;
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $office_id = $request->office_id;
        $transactionList = [];


        $LoanArray = [];
        $LoanArrayTwo = [];


        if ($office_id != 0) {
            $BranchLoans = Loan::with('transactions')->where('office_id', $office_id)->where('status', 'disbursed')->get();

        } else {
            $BranchLoans = Loan::with('transactions')->where('status', 'disbursed')->get();

        }



        foreach ($BranchLoans as $loan) {
            $reloan = 0;
            foreach ($loan->transactions as $transaction) {
                if ($transaction->payment_apply_to == 'reloan_payment') {
                    $reloan = 1;
                }

            }
            if ($reloan == 0) {
                array_push($LoanArray, $loan);
                array_push($LoanArrayTwo, $loan);
            }

        }




        return view('loan.expected_collections', compact('targetDate', 'compareDate', 'role', 'office_id', 'transactionList', 'userBranch', 'LoanArray', 'LoanArrayTwo', 'userProvince'));
    }

    //PART PAYMENT AND FULL PAYMENT APPROVALS
    public function transaction_approvals()
    {

        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
           $userId = Sentinel::getUser()->id;
          $role = Sentinel::getUser()->roles->first();
        $office_id = Sentinel::getUser()->office_id;
          $roleNew = UserRole::where('user_id', $userId)->first();

      if($roleNew->role_id == '4' || $roleNew->role_id == '12' || $roleNew->role_id == "6"){

 $branchStaffCount = User::where('office_id', $office_id)->where('status','Active')->count();
       $existing_payroll_count = Payroll::where('office_id', $office_id)
    ->whereYear('payroll_date', now()->year)
    ->whereMonth('payroll_date', now()->month)
    ->count();

//     if ($branchStaffCount !== $existing_payroll_count) {
//     return redirect('/payroll/create_wage_bill');
// }
      }

         $HasPendingCarryOvers = false;
        $carry_overs = 0;

        
        $province_transactions = [];
     
        $province_id = Sentinel::getUser()->province_id;
        $offices = Office::get();
        $role = UserRole::where('user_id', $userId)->first();

        
        if ($role->role_id == '4') {
             $carry_overs = CarryOver::where('status','pending')->where('office_id',$office_id)->count();
        }


        if ($role->role_id == "6") {

            foreach ($offices as $office) {
                if ($office->province_id == $province_id) {
                    $transactions = LoanTransactionUnapproved::where('office_id', $office->id)->with('loan')->get();
                    foreach ($transactions as $transaction) {
                        array_push($province_transactions, $transaction);
                    }
                }
            }
            $data = $province_transactions;

        } else {
            if (Sentinel::hasAccess('settings')) {
                $data = LoanTransactionUnapproved::with('loan')->get();
            } else {
                $data = LoanTransactionUnapproved::where('office_id', $office_id)->with('loan')->get();
            }
        }
        
        // Log audit for accessing loan transactions approvals page
        $this->auditorService->logTransactionApprovalsPage(Sentinel::getUser(), request());
        return view('loan.transactions', compact('data'));
    }


    public function pending_client_app_applications()
    {
        
            $province_transactions = [];


         $userId = Sentinel::getUser()->id;
         $offices = Office::get();
         $province_id = Sentinel::getUser()->province_id;
         $role = UserRole::where('user_id', $userId)->first();
         $office_id = Sentinel::getUser()->office_id;



    if ($role->role_id == "3") {
    $userId = Sentinel::getUser()->id;

    $data = Client::where('status', 'active')
        ->where('staff_id', $userId)
        ->whereIn('id', function ($query) {
            $query->select('client_id')
                ->from('client_app_loan_applications')
                ->where('status', 'pending');
        })
        ->get();
}

        if($role->role_id == "6"){
              foreach ($offices as $office) {
                if ($office->province_id == $province_id) {
                    $transactions = ClientAppLoanApplications::where('branch', $office->id)->get();
                    foreach ($transactions as $transaction) {
                        array_push($province_transactions, $transaction);
                    }
                }
            }

            $data = $province_transactions;

        }else{
             if (Sentinel::hasAccess('settings')) {
                $data =  ClientAppLoanApplications::where('status','pending')->get();
            } else {
                $data = ClientAppLoanApplications::where('branch', $office_id)->where('status','pending')->get();
            }
        }

         return view('loan.client_app_loan_applications', compact('data',));

    }
public function client_app_dashboard(Request $request)
{
    $client_app_users = ClientAppUsers::with('client')->get();
    $client_app_loan_applications = ClientAppLoanApplications::with('client')->get();
    $client_app_transactions = LoanTransaction::where('client_app','Yes')->get();

    $provinceClientCounts = [];
    $provinceClientUsers = [];
    $provinceClientTransactions = [];

    // Applications
    try {
        $response = Http::get('https://lms2backend.whencefinancesystem.com/client-app-applications');

        if ($response->successful()) {
            $provinceClientCounts = $response->json()['data'] ?? [];
        }
    } catch (\Exception $e) {
        \Log::error('Client App Applications API Error: ' . $e->getMessage());
    }

    // Users
    try {
        $response = Http::get('https://lms2backend.whencefinancesystem.com/client-app-users');

        if ($response->successful()) {
            $provinceClientUsers = $response->json()['data'] ?? [];
        }
    } catch (\Exception $e) {
        \Log::error('Client App Users API Error: ' . $e->getMessage());
    }


    // Transactions
try {

    $response = Http::get('https://lms2backend.whencefinancesystem.com/client-app-transactions');

    if ($response->successful()) {

        $provinceClientTransactions = $response->json()['data'] ?? [];

    }

} catch (\Exception $e) {

    \Log::error('Client App Transactions API Error: ' . $e->getMessage());

}

    return view(
        'loan.client_app_dashboard',
        compact(
            'client_app_users',
            'client_app_loan_applications',
            'provinceClientCounts',
            'provinceClientUsers',
            'provinceClientTransactions',
            'client_app_transactions'
        )
    );
}



    public function top_up_approvals()
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $user = Sentinel::getUser();
        $role = UserRole::where('user_id', $user->id)->first();

        if (!$role) {
            Flash::warning('No role assigned.');
            return redirect()->back();
        }

        $query = LoanTopUp::with(['loan.client', 'office', 'createdBy'])
            ->where('status', 'pending');

        if ($role->role_id == "6") {
            $officeIds = Office::where('province_id', $user->province_id)->pluck('id');
            $data = $query->whereIn('office_id', $officeIds)->get();
        } else if($role->role_id == '4') {
            $data = $query->where('office_id', $user->office_id)->get();
        }else{
            $data = $query->get();
        }

        return view('advances.top_up_approvals', compact('data'));
    }

    public function pending_approval()
    {
        if (!Sentinel::hasAccess('loans.pending_approval')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $staff_id = Sentinel::getUser()->id;
        $data = Loan::where('status', 'pending')->get();

        return view('loan.pending_approval', compact('data'));
    }

    public function managers_pending_approval()
    {
        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $office_id = Sentinel::getUser()->office_id;

        $data = [];
        $offices = Office::get();
       
        $province_id = Sentinel::getUser()->province_id;
        $role = UserRole::where('user_id', $userId)->first();
        $office_id = Sentinel::getUser()->office_id;
        if ($role->role_id == '1') {

            $data = Loan::whereIn('status', ['pending', 'approved'])->where('loan_product_id',2)->get();
        } elseif ($role->role_id == "6") {
            foreach ($offices as $office) {
                if ($office->province_id == $province_id) {
                    $loans = Loan::whereIn('status', ['pending', 'approved'])->where('office_id', $office->id)->where('loan_product_id',2)->get();
                    foreach ($loans as $loan) {
                        array_push($data, $loan);
                    }
                }
            }
        } else {
            $data = Loan::whereIn('status', ['pending', 'approved'])->where('office_id', $office_id)->where('loan_product_id',2)->get();
        }

        return view('loan.managers_pending_approval', compact('data'));
    }

    public function awaiting_disbursement()
    {
        if (!Sentinel::hasAccess('loans.awaiting_disbursement')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        
        $data = Loan::where('status', 'approved')->get();

        return view('loan.awaiting_disbursement', compact('data'));
    }

    public function loans_declined()
    {
        if (!Sentinel::hasAccess('loans.declined')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->province_id;
        $province_branches = Office::where('province_id', $userProvince)->get();
        $data = [];

        if ($role->role_id == '6') {
            foreach ($province_branches as $province_branch) {
                $loans = Loan::where('office_id', $province_branch->id)->where('status', 'declined')->get();
                foreach ($loans as $loan) {
                    array_push($data, $loan);
                }
            }
        } elseif ($role->role_id == '4') {
            $loans = Loan::where('office_id', $userBranch)->where('status', 'declined')->get();
            foreach ($loans as $loan) {
                array_push($data, $loan);
            }
        } elseif ($role->role_id == '1') {
            $data = Loan::where('status', 'declined')->get();
        }

        return view('loan.loans_declined', compact('data'));
    }

    public function loans_written_off()
    {
        if (!Sentinel::hasAccess('loans.written_off')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Loan::where('status', 'written_off')->get();

        return view('loan.loans_written_off', compact('data'));
    }

    public function loans_closed(Request $request)
    {
        if (!Sentinel::hasAccess('loans.closed')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $query = $request->input('query');
        $loans = [];
        if ($query) {
            $loans = Loan::where('status', 'closed')
                ->where(function ($q) use ($query) {
                    $q->whereHas('client', function ($q) use ($query) {
                        $q->where('first_name', 'like', "%$query%")
                            ->orWhere('last_name', 'like', "%$query%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE '%$query%'");
                    })
                        ->orWhereHas('office', function ($q) use ($query) {

                            $q->where('name', 'like', "%$query%");
                        })
                        ->orWhere('id', 'like', "%$query%");
                })
                ->with('repayment_schedules')
                ->get();
        }
        //$data = Loan::where('status', 'closed')->get();

        return view('loan.loans_closed', compact('loans', 'query'));
    }

    public function loans_rescheduled()
    {
        if (!Sentinel::hasAccess('loans.rescheduled')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Loan::where('status', 'rescheduled')->get();

        return view('loan.loans_rescheduled', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
public function create()
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

           $pendingApproval = false;
           $launchNewCarryOver = false;
        $province_clients = [];
        $user = Sentinel::getUser();
        $userBranch = $user->office_id;
        $userId = $user->id;
        $role = UserRole::where('user_id', $userId)->first();

        $userProvince = $user->province_id;
        $province_branches = Office::where('province_id', $userProvince)->get();

        // Fetch data for selects with role-based filtering
        $clients_query = Client::where('status', 'active')->where('blacklisted', 0);
        if ($role->role_id == '6') {
            $clients_query->whereIn('office_id', $province_branches->pluck('id'));
        } elseif ($role->role_id == '4' || $role->role_id == '3') {
            $clients_query->where('office_id', $userBranch);
        }
        // role 1 sees all
        $clients = $clients_query->get();

        if ($role->role_id == '6') {
            $province_clients = $clients;
        }

        $groups_query = \App\Models\Group::where('status', 'active');
        if ($role->role_id == '6') {
            $groups_query->whereIn('office_id', $province_branches->pluck('id'));
        } elseif ($role->role_id == '4' || $role->role_id == '3') {
            $groups_query->where('office_id', $userBranch);
        }
        // role 1 sees all
        $groups = $groups_query->get();

        $loan_products = \App\Models\LoanProduct::all();

        return view('loan.create', compact('userBranch', 'role', 'userId', 'province_branches', 'province_clients', 'clients', 'groups', 'loan_products','launchNewCarryOver','pendingApproval'));
    }

    public function ajaxClients(Request $request)
    {
        $user = Sentinel::getUser();
        $userId = $user->id;
        $role = UserRole::where('user_id', $userId)->first();
        $userProvince = $user->province_id;
        $province_branches = Office::where('province_id', $userProvince)->pluck('id');

        $query = Client::where('status', 'active')->where('blacklisted', 0);

        if ($role->role_id == '6') {
            $query->whereIn('office_id', $province_branches);
        }

        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('account_no', 'like', "%{$search}%")
                  ->orWhere('nrc_number', 'like', "%{$search}%");
            });
        }

        $clients = $query->paginate(30);

        $results = [];
        foreach ($clients as $client) {
            $text = '';
            if ($client->client_type == "individual") {
                $text = $client->first_name . ' ' . $client->middle_name . ' ' . $client->last_name . ' (' . $client->account_no . ')(' . $client->nrc_number . ')';
            } else {
                $text = $client->full_name . ' (' . $client->account_no . ')';
            }
            $results[] = [
                'id' => $client->id,
                'text' => $text
            ];
        }

        return response()->json([
            'items' => $results,
            'total_count' => $clients->total()
        ]);
    }

    public function ajaxGroups(Request $request)
    {
        $query = \App\Models\Group::where('status', 'active');

        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('account_no', 'like', "%{$search}%");
            });
        }

        $groups = $query->paginate(30);

        $results = [];
        foreach ($groups as $group) {
            $results[] = [
                'id' => $group->id,
                'text' => $group->name . '(' . $group->account_no . ')'
            ];
        }

        return response()->json([
            'items' => $results,
            'total_count' => $groups->total()
        ]);
    }

    public function ajaxLoanProducts(Request $request)
    {
        $query = \App\Models\LoanProduct::query();

        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where('name', 'like', "%{$search}%");
        }

        $loanProducts = $query->paginate(30);

        $results = [];
        foreach ($loanProducts as $product) {
            $results[] = [
                'id' => $product->id,
                'text' => $product->name
            ];
        }

        return response()->json([
            'items' => $results,
            'total_count' => $loanProducts->total()
        ]);
    }
    ///////////////////////////////////////
    public function create_client_loan(Request $request,$client, $loan_product)
    {
        $userBranch = Sentinel::getUser()->office_id;
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        if (empty($client) || empty($loan_product)) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back();
        }
        if ($client->blacklisted) {
            Flash::warning(trans_choice('general.client', 1) . ' ' . trans_choice('general.blacklisted', 1));
            return redirect()->back();
        }

        $client_loan = Loan::where('client_id', '=', $client->id)->where('loan_product_id', '=', $loan_product->id)->where('status', '!=', 'closed')->where('status', '!=', 'declined')->first();
        if ($client_loan) {
            Flash::warning($client->first_name . '  ' . $client->last_name . ' ' . 'already has a loan on' . ' ' . $loan_product->name);
            return redirect()->back();
        } else {

$number = $request->query('number');
$amount = $request->query('amount');
            
            // Log audit for creating a new client loan, log client information
           $this->auditorService->logCreateClientLoan(Sentinel::getUser(), request(), $client);
            return view(
                'loan.create_client_loan',
                compact('client', 'loan_product', 'userBranch','number','amount')
            );
        }
    }


    public function decline_client_application($id)
    {
     $application = ClientAppLoanApplications::find($id);
     $application->status = 'declined';
     $application->save();
     return redirect('loan/pending_client_app_applications');
    }

    public function create_group_loan($group, $loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($group) || empty($loan_product)) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back();
        }

        return view(
            'loan.create_group_loan',
            compact('group', 'loan_product')
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store_client_loan(Request $request, $client, $loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $client_loan = Loan::where('client_id', '=', $client->id)->where('loan_product_id', '=', $loan_product->id)->where('status', '!=', 'closed')->where('status', '!=', 'declined')->first();
        if ($client_loan) {
            Flash::warning($client->first_name . '  ' . $client->last_name . ' ' . 'already has a loan on' . ' ' . $loan_product->name);
            return redirect('loan/create');
        }


            $rules = array(
                'loan_officer_id' => 'required',
                'principal' => 'required',
                'loan_term' => 'required',
                'loan_term_type' => 'required',
                'repayment_frequency' => 'required',
                'repayment_frequency_type' => 'required',
                'external_id' => 'required',
                'interest_rate' => 'required',
                'expected_disbursement_date' => 'required',
                'expected_first_repayment_date' => 'required|after_or_equal:expected_disbursement_date',
            );
            $messages = [
                'loan_officer_id.required' => 'Loan Officer is required',
                'principal.required' => 'Principal is required',
                'loan_term_type.required' => 'Loan term is required',
                'external_id.required' => 'External ID is required',
                'repayment_frequency.required' => 'repayment frequency is required',
                'repayment_frequency_type.required' => 'repayment frequency type is required',
                'interest_rate.required' => 'interest rate is required',
                'interest_rate_type.required' => 'interest rate type is required',
                'expected_disbursement_date.required' => 'Expected disbursement date is required',
                'expected_first_repayment_date.required' => 'Expected first repayment date is required',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {



                   if (Sentinel::getUser()->cycle_dates == null) {
                $cycle_end = 24;
            } else {
                $cycle_end = Sentinel::getUser()->cycle_dates->cycle_end_date;
            }

              $use = date('Y-m-');
              $todaysDate = date('Y-m-d');
              $targetDate = $use . $cycle_end;
              $targetDate = date('Y-m-d', strtotime($targetDate));
          if ($todaysDate < $targetDate) {
                $targetDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));
            }

            $next_cycle = date('Y-m-d', strtotime($targetDate . ' + 1 months'));


                $loan = new Loan();
                $loan->created_by_id = Sentinel::getUser()->id;
                $loan->created_date = $request->created_date;
                $loan->client_type = "client";
                $loan->loan_product_id = $loan_product->id;
                $loan->client_id = $client->id;
                $loan->office_id = $client->office_id;
                $loan->fund_id = $request->fund_id;
                $loan->decimals = $loan_product->decimals;
                $loan->loan_officer_id = $request->loan_officer_id;
                $loan->loan_purpose_id = $request->loan_purpose_id;
		$loan->external_id = $request->external_id;
		$loan->vetted_by = $request->vetted_by;
		  $loan->verified_by = $request->verified_by;
        //   if($request->carry_over == 1){
        //     $loan->cycle_date = $targetDate;
        //   }else{
        //      $loan->cycle_date = $next_cycle;
        //   }
                $loan->principal = $request->principal;
                $loan->applied_amount = $request->principal;
                $loan->currency_id = $loan_product->currency_id;
                $loan->loan_term = $request->loan_term;
                $loan->loan_term_type = $request->loan_term_type;
                $loan->repayment_frequency = $request->repayment_frequency;
                $loan->repayment_frequency_type = $request->repayment_frequency_type;
                $loan->interest_rate = $request->interest_rate;
                $loan->interest_rate_type = $loan_product->interest_rate_type;
                $loan->override_interest = $request->override_interest;
                $loan->override_interest_rate = $loan_product->override_interest_rate;
                $loan->expected_disbursement_date = $request->expected_disbursement_date;
                 $todaysDate = date('Y-m-d');
                $loan->expected_first_repayment_date = date('Y-m-d',strtotime($todaysDate. '+ 1 month'));
                $loan->interest_method = $loan_product->interest_method;
                $loan->armotization_method = $loan_product->armotization_method;
                $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
                $loan->grace_on_principal = $loan_product->grace_on_principal;
                $loan->grace_on_interest_payment = $loan_product->grace_on_interest_payment;
                $date = explode('-', $request->created_date);
                $loan->month = $date[1];
                $loan->year = $date[0];
                $loan->phone_number = $request->phone_number;
                if($loan_product->id == 0)
                {
                    $loan->referrer = $request->referrer;
                    $loan->referrer_branch = $request->office_id;
                }
		        $loan->save();

             

                $application = ClientAppLoanApplications::where('client_id', $client->id)
    ->where('status', 'pending')
    ->first();

if ($application) {
    $application->status = 'approved';
    $application->save();
}


    if($loan_product->id == 0)
            {



 $vehicle = new Vehicle();
$vehicle->vehicle_code = 'VH' . time();
$vehicle->client_id = $client->id;
$vehicle->loan_id = $loan->id;
$vehicle->make = $request->make;
$vehicle->model = $request->model;
$vehicle->year = $request->year;
$vehicle->registration_number = $request->registration_number;
$vehicle->market_value = $request->market_value;
$vehicle->engine_number = $request->engine_number;
$vehicle->chassis_number = $request->chassis_number;
$vehicle->insurance_policy_number = $request->insurance_policy_number;

$vehicle->save();

            }

            // Broadcast loan created event for real-time updates
            \Illuminate\Support\Facades\Log::info('LoanCreated event firing for loan ID: ' . $loan->id);
            // event(new LoanCreated($loan));

            Http::post('https://notifications.whencefinancesystem.com/emit', [
                'event' => 'loan.created',
                'data' => [
                    'created_by' => Sentinel::getUser()->first_name . ' ' . Sentinel::getUser()->last_name,
                    'office_id' => Sentinel::getUser()->office->id,
                    'client' => $client->first_name . ' ' . $client->last_name,
                    'amount' => $request->principal,
                    'type' => 'New Loan',
                    'loan' => $loan->toArray()
                ]
            ]);
            // Notify Branch Manager for new loan approval
            // Notifix::notifyBmToApproveNewLoan($loan, $client, $request->principal);


            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $loan_charge = new LoanCharge();
                    $loan_charge->loan_id = $loan->id;
                    $loan_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $loan_charge->amount = $request->charge_amount[$key];
                    } else {
                        $loan_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $loan_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $loan_charge->charge_type = $charge->charge_type;
                    $loan_charge->charge_option = $charge->charge_option;
                    $loan_charge->save();
                }
            }
            //check custom fields
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'loans')->get();
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
                    $custom_field->parent_id = $loan->id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "loans";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Create", "Loans", $loan->id);
        // Log audit for creating a new client loan, log client and loan information
            $user = Sentinel::getUser();
            $this->auditorService->logStoreClientLoan($user, request(), $loan, $client);
            
            // Check if loan has collateral and redirect to collateral create page
            if ($request->has('has_collateral') && $request->has_collateral == '1' && $request->has('redirect_to_collateral') && $request->redirect_to_collateral == '1') {
                Flash::success(trans('general.successfully_saved'));
                return redirect('collateral/create?loan_id=' . $loan->id);
            }
            
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }

    }

    public function store_group_loan(Request $request, $group, $loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $rules = array(
            'loan_officer_id' => 'required',
            'principal' => 'required',
            'loan_term' => 'required',
            'loan_term_type' => 'required',
            'repayment_frequency' => 'required',
            'repayment_frequency_type' => 'required',
            'interest_rate' => 'required',
            'expected_disbursement_date' => 'required',
            'expected_first_repayment_date' => 'required|after_or_equal:expected_disbursement_date',
        );
        $messages = [
            'loan_officer_id.required' => 'Loan Officer is required',
            'principal.required' => 'Principal is required',
            'loan_term_type.required' => 'Loan term is required',
            'repayment_frequency.required' => 'repayment frequency is required',
            'repayment_frequency_type.required' => 'repayment frequency type is required',
            'interest_rate.required' => 'interest rate is required',
            'interest_rate_type.required' => 'interest rate type is required',
            'expected_disbursement_date.required' => 'Expected disbursement date is required',
            'expected_first_repayment_date.required' => 'Expected first repayment date is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if (empty($request->client)) {
            Flash::warning("Group must have clients");
            return redirect()->back()->withInput();
        }
        if (!empty($request->client)) {
            $total = 0;
            foreach ($request->client as $key) {
                $total = $total + $key;
            }
            if ($request->principal != $total) {
                Flash::warning("Group allocation total must be equal to the principal");
                return redirect()->back()->withInput();
            }
        }
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $loan = new Loan();
            $loan->created_by_id = Sentinel::getUser()->id;
            $loan->created_date = $request->created_date;
            $loan->client_type = "group";
            $loan->loan_product_id = $loan_product->id;
            $loan->group_id = $group->id;
            $loan->office_id = $group->office_id;
            $loan->fund_id = $request->fund_id;
            $loan->decimals = $loan_product->decimals;
            $loan->loan_officer_id = $request->loan_officer_id;
            $loan->loan_purpose_id = $request->loan_purpose_id;
            $loan->external_id = $request->external_id;
            $loan->principal = $request->principal;
            $loan->applied_amount = $request->applied_amount;
            $loan->currency_id = $loan_product->currency_id;
            $loan->loan_term = $request->loan_term;
            $loan->loan_term_type = $request->loan_term_type;
            $loan->repayment_frequency = $request->repayment_frequency;
            $loan->repayment_frequency_type = $request->repayment_frequency_type;
            $loan->interest_rate = $request->interest_rate;
            $loan->interest_rate_type = $loan_product->interest_rate_type;
            $loan->override_interest = $request->override_interest;
            $loan->override_interest_rate = $request->override_interest_rate;
            $loan->expected_disbursement_date = $request->expected_disbursement_date;
            if (!empty($request->expected_first_repayment_date)) {
                $loan->expected_first_repayment_date = $request->expected_first_repayment_date;
            }
            $loan->interest_method = $loan_product->interest_method;
            $loan->armotization_method = $loan_product->armotization_method;
            $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
            $loan->grace_on_principal = $loan_product->grace_on_principal;
            $loan->grace_on_interest_payment = $loan_product->grace_on_interest_payment;
            $date = explode('-', $request->created_date);
            $loan->month = $date[1];
            $loan->year = $date[0];
            $loan->save();
            //save loan allocation
            foreach ($request->client as $key => $value) {
                $group_loan_allocation = new GroupLoanAllocation();
                $group_loan_allocation->loan_id = $loan->id;
                $group_loan_allocation->group_id = $group->id;
                $group_loan_allocation->client_id = $key;
                $group_loan_allocation->amount = $value;
                $group_loan_allocation->save();
            }
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $loan_charge = new LoanCharge();
                    $loan_charge->loan_id = $loan->id;
                    $loan_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $loan_charge->amount = $request->charge_amount[$key];
                    } else {
                        $loan_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $loan_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $loan_charge->charge_type = $charge->charge_type;
                    $loan_charge->charge_option = $charge->charge_option;
                    $loan_charge->save();
                }
            }
            //check custom fields
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'loans')->get();
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
                    $custom_field->parent_id = $loan->id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "loans";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Create", "Loans", $loan->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }
    }


    public function show($loan)
    {
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        // Log audit for accessing and viewing loan details page
        $user = Sentinel::getUser();
        $this->auditorService->logAccessedLoanDetail($user, request(), $loan);
        
        // Get ledger blocker status for debugging
        $ledgerBlocker = \App\Helpers\BlockerHelper::ledger_blocker();

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

        $vehicle = Vehicle::with('client')->where('loan_id',$loan->id)->first();



        
        return view('loan.show', compact('loan', 'ledgerBlocker','cashBalance','user_id','vehicle'));
    }


    public function edit($loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($loan->loan_product)) {
            Flash::warning("Loan Product not found");
            return redirect()->back();
        }
        if ($loan->client_type == "client") {
            return view(
                'loan.edit_client_loan',
                compact('loan')
            );
        }
        if ($loan->client_type == "group") {
            return view(
                'loan.edit_group_loan',
                compact('loan')
            );
        }

    }


    public function activate($loan)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan = Loan::find($loan->id);
        $loan->status = "disbursed";
        $loan->save();
        return redirect('loan/' . $loan->id . '/show');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */



    public function set_defaulted(Request $request, $id)
    {

        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $loan = Loan::find($id);
        $loan->defaulted = 'yes';
        $loan->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }


    public function add_top_up(Request $request, $id)
    {
        $loan = Loan::find($id);
        $loanTransDisbursed = LoanTransaction::where('loan_id', $id)->where('transaction_type', 'disbursement')->first();
        $loanTransInterest = LoanTransaction::where('loan_id', $id)->where('transaction_type', 'interest_initial')->first();
        $loan_topup = new LoanTopUp();
        if ($request->top_up_date == null) {
            $loan_topup->date = date("Y-m-d");
        } else {
            $loan_topup->date = $request->top_up_date;
        }
        $loan_topup->loan_id = $loan->id;
        $loan_topup->office_id = $loan->office_id;
        $loan_topup->created_by = Sentinel::getUser()->id;
        $loan_topup->amount = $request->amount;
        $loan_topup->balance_bf = $loanTransDisbursed->debit;
        $loan_topup->balance_new = $loanTransDisbursed->debit + $request->amount;
        $am = $loanTransDisbursed->debit + $request->amount;
        $loanTransDisbursed->debit = $loanTransDisbursed->debit + $request->amount;
        $loanTransInterest->debit = 0.4 * $am;
        //Create top up database model
        $loan_topup->save();
        $loanTransDisbursed->save();
        $loanTransInterest->save();
        
        // Log audit for adding/approving top up
        $user = Sentinel::getUser();
        $this->auditorService->logAddedTopUp($user, request(), $loan);
        return redirect('loan/' . $loan->id . '/show');
    }



    public function add_top_up_request(Request $request, $id)
    {
        $loan = Loan::find($id);
        $loanTransDisbursed = LoanTransaction::where('loan_id', $id)->where('transaction_type', 'disbursement')->first();
        $loanTransInterest = LoanTransaction::where('loan_id', $id)->where('transaction_type', 'interest_initial')->first();
        $loan_topup = new LoanTopUp();
        if ($request->top_up_date == null) {
            $loan_topup->date = date("Y-m-d");
        } else {
            $loan_topup->date = $request->top_up_date;
        }
        $loan_topup->loan_id = $loan->id;
        $loan_topup->office_id = $loan->office_id;
        $loan_topup->created_by = Sentinel::getUser()->id;
        $loan_topup->amount = $request->amount;
        $loan_topup->balance_bf = $loanTransDisbursed->debit;
        $loan_topup->balance_new = $loanTransDisbursed->debit + $request->amount;
        $loan_topup->status = 'pending';
        $loan_topup->save();
        $client_id = $loan->client_id;
        $client = \App\Models\Client::find($client_id);
        Http::post('https://notifications.whencefinancesystem.com/emit', [
            'event' => 'loan.created',
            'data' => [
                'created_by' => Sentinel::getUser()->first_name . ' ' . Sentinel::getUser()->last_name,
                'office_id' => Sentinel::getUser()->office->id,
                'client' => $client->first_name . ' ' . $client->last_name,
                'amount' => $request->amount,
                'type' => 'Top-Up',
                'loan' => $loan->toArray(),
                'loan_topup' => $loan_topup->toArray()
            ]
        ]);
        // Notifix::notifyBmForTopUpApprovalByOffice($loan, $loan_topup);
        // Notifix::notifyRkForTopUpCloseToMaturity($loan, $loan_topup, $client);

        
        // Log audit for accessing and viewing top up approval requests page
        $user = Sentinel::getUser();
        $this->auditorService->logTopUpApproval($user, request(), $loan);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $loan->id . '/show');
    }



    public function approve_top_up(Request $request, $id, $trans_id)
    {
        $topup = LoanTopUp::find($trans_id);
        $loan = Loan::where('id', $id)->first();
        $client = Client::where('id', $loan->client_id)->first();
        $loanTransDisbursed = LoanTransaction::where('loan_id', $id)->where('transaction_type', 'disbursement')->first();
        $loanTransInterest = LoanTransaction::where('loan_id', $id)->where('transaction_type', 'interest_initial')->first();
        $am = $loanTransDisbursed->debit + $topup->amount;
        $loanTransDisbursed->debit = $loanTransDisbursed->debit + $topup->amount;
        $loanTransInterest->debit = 0.4 * $am;
        $topup->status = 'approved';
        $topup->save();
        $loanTransDisbursed->save();
        $loanTransInterest->save();
        // Notifix::notifyLoanOfficerTopUpApproved($loan, $topup, $client);
        Flash::success(trans('general.successfully_saved'));
        
        
        // Log audit for approving the topup
        $user = Sentinel::getUser();
        $this->auditorService->logTopUpApproved($user, request(), $loan);
        return redirect('loan/' . $id . '/show');
    }


    public function decline_top_up(Request $request, $id)
    {
        $topup = LoanTopUp::find($id);
        $loan = Loan::where('id', $topup->loan_id)->first();
        $client = Client::where('id', $loan->client_id)->first();
        $topup->status = 'declined';
        $topup->save();
        // Notifix::notifyLoanOfficerTopUpDeclined($loan, $topup, $client);
        Flash::success(trans('general.successfully_saved'));
        return redirect('loan/' . $topup->loan_id . '/show');
    }


    public function update_client_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'loan_officer_id' => 'required',
            'principal' => 'required',
            'loan_term' => 'required',
            'loan_term_type' => 'required',
            'repayment_frequency' => 'required',
            'repayment_frequency_type' => 'required|same:loan_term_type',
            'interest_rate' => 'required',
            'expected_disbursement_date' => 'required',
            'expected_first_repayment_date' => 'required|after_or_equal:expected_disbursement_date',
        );
        $messages = [
            'loan_officer_id.required' => 'Loan Officer is required',
            'principal.required' => 'Principal is required',
            'loan_term_type.required' => 'Loan term is required',
            'repayment_frequency.required' => 'repayment frequency is required',
            'repayment_frequency_type.required' => 'repayment frequency type is required',
            'interest_rate.required' => 'interest rate is required',
            'interest_rate_type.required' => 'interest rate type is required',
            'expected_disbursement_date.required' => 'Expected disbursement date is required',
            'expected_first_repayment_date.required' => 'Expected first repayment date is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $loan = Loan::find($id);
            $loan->created_by_id = Sentinel::getUser()->id;
            $loan->created_date = $request->created_date;
            $loan->fund_id = $request->fund_id;
            $loan->loan_officer_id = $request->loan_officer_id;
            $loan->loan_purpose_id = $request->loan_purpose_id;
            $loan->principal = $request->principal;
            $loan->applied_amount = $request->principal;
            $loan->loan_term = $request->loan_term;
            $loan->external_id = $request->external_id;
            $loan->loan_term_type = $request->loan_term_type;
            $loan->repayment_frequency = $request->repayment_frequency;
            $loan->repayment_frequency_type = $request->repayment_frequency_type;
            $loan->interest_rate = $request->interest_rate;
            $loan->override_interest = $request->override_interest;
            $loan->override_interest_rate = $request->override_interest_rate;
            $loan->expected_disbursement_date = $request->expected_disbursement_date;
            if (!empty($request->expected_first_repayment_date)) {
                $loan->expected_first_repayment_date = $request->expected_first_repayment_date;
            }
            $date = explode('-', $request->created_date);
            $loan->month = $date[1];
            $loan->year = $date[0];
            $loan->save();
            LoanCharge::where('loan_id', $loan->id)->delete();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $loan_charge = new LoanCharge();
                    $loan_charge->loan_id = $loan->id;
                    $loan_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $loan_charge->amount = $request->charge_amount[$key];
                    } else {
                        $loan_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $loan_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $loan_charge->charge_type = $charge->charge_type;
                    $loan_charge->charge_option = $charge->charge_option;
                    $loan_charge->save();
                }
            }
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'loans')->get();
                foreach ($custom_fields as $key) {
                    if (
                        !empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where(
                            'category',
                            'loans'
                        )->first())
                    ) {
                        $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where(
                            'parent_id',
                            $id
                        )->where('category', 'loans')->first();
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
                    $custom_field->category = "loans";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Update", "Loans", $loan->id);
            Flash::success(trans('general.successfully_saved'));
            
        
            // Log audit for updating the client's loan
            $user = Sentinel::getUser();
            $this->auditorService->logLoanUpdated($user, request(), $loan);
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    public function update_group_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!empty($request->client)) {
            $total = 0;
            foreach ($request->client as $key) {
                $total = $total + $key;
            }
            if ($request->principal != $total) {
                Flash::warning("Group allocation total must be equal to the principal");
                return redirect()->back()->withInput();
            }
        }
        $rules = array(
            'loan_officer_id' => 'required',
            'principal' => 'required',
            'loan_term' => 'required',
            'loan_term_type' => 'required',
            'repayment_frequency' => 'required',
            'repayment_frequency_type' => 'required|same:loan_term_type',
            'interest_rate' => 'required',
            'expected_disbursement_date' => 'required',
            'expected_first_repayment_date' => 'required|after_or_equal:expected_disbursement_date',
        );
        $messages = [
            'loan_officer_id.required' => 'Loan Officer is required',
            'principal.required' => 'Principal is required',
            'loan_term_type.required' => 'Loan term is required',
            'repayment_frequency.required' => 'repayment frequency is required',
            'repayment_frequency_type.required' => 'repayment frequency type is required',
            'interest_rate.required' => 'interest rate is required',
            'interest_rate_type.required' => 'interest rate type is required',
            'expected_disbursement_date.required' => 'Expected disbursement date is required',
            'expected_first_repayment_date.required' => 'Expected first repayment date is required',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $loan = Loan::find($id);
            $loan->created_by_id = Sentinel::getUser()->id;
            $loan->created_date = $request->created_date;
            $loan->fund_id = $request->fund_id;
            $loan->loan_officer_id = $request->loan_officer_id;
            $loan->loan_purpose_id = $request->loan_purpose_id;
            $loan->principal = $request->principal;
            $loan->applied_amount = $request->principal;
            $loan->external_id = $request->external_id;
            $loan->loan_term = $request->loan_term;
            $loan->loan_term_type = $request->loan_term_type;
            $loan->repayment_frequency = $request->repayment_frequency;
            $loan->repayment_frequency_type = $request->repayment_frequency_type;
            $loan->interest_rate = $request->interest_rate;
            $loan->override_interest = $request->override_interest;
            $loan->override_interest_rate = $request->override_interest_rate;
            $loan->expected_disbursement_date = $request->expected_disbursement_date;
            if (!empty($request->expected_first_repayment_date)) {
                $loan->expected_first_repayment_date = $request->expected_first_repayment_date;
            }
            $date = explode('-', $request->created_date);
            $loan->month = $date[1];
            $loan->year = $date[0];
            $loan->save();
            //save loan allocation
            GroupLoanAllocation::where('loan_id', $loan->id)->delete();
            foreach ($request->client as $key => $value) {
                $group_loan_allocation = new GroupLoanAllocation();
                $group_loan_allocation->loan_id = $loan->id;
                $group_loan_allocation->group_id = $loan->group_id;
                $group_loan_allocation->client_id = $key;
                $group_loan_allocation->amount = $value;
                $group_loan_allocation->save();
            }
            LoanCharge::where('loan_id', $loan->id)->delete();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $loan_charge = new LoanCharge();
                    $loan_charge->loan_id = $loan->id;
                    $loan_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $loan_charge->amount = $request->charge_amount[$key];
                    } else {
                        $loan_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $loan_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $loan_charge->charge_type = $charge->charge_type;
                    $loan_charge->charge_option = $charge->charge_option;
                    $loan_charge->save();
                }
            }
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'loans')->get();
                foreach ($custom_fields as $key) {
                    if (
                        !empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where(
                            'category',
                            'loans'
                        )->first())
                    ) {
                        $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where(
                            'parent_id',
                            $id
                        )->where('category', 'loans')->first();
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
                    $custom_field->category = "loans";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Update", "Loans", $loan->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Loan::destroy($id);
        LoanCharge::where('loan_id', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('loan/product/data');
    }

    //client documents
    public function store_loan_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.documents.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required',
            'attachment' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan_document = new Document();
            $loan_document->record_id = $id;
            $loan_document->type = "loan";
            $loan_document->name = $request->name;
            $loan_document->notes = $request->notes;
            if ($request->hasFile('attachment')) {
                $file = array('attachment' => $request->file('attachment'));
                $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,doc,xlsx,pptx,xls');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = str_slug($request->name, '_') . "" . uniqid() . '.' . $request->file('attachment')->guessExtension();
                    $loan_document->location = $fname;
                    $request->file('attachment')->move(
                        public_path() . '/uploads',
                        $fname
                    );
                }

            }
            $loan_document->save();
            GeneralHelper::audit_trail("Create Document", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_loan_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.documents.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan_document = Document::find($id);
        if (!empty($loan_document->location)) {
            @unlink(public_path() . '/uploads/' . $loan_document->location);
        }
        Document::destroy($id);
        GeneralHelper::audit_trail("Delete Document", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    //client notes
    public function store_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.notes.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $note = new Note();
            $note->reference_id = $id;
            $note->created_by_id = Sentinel::getUser()->id;
            $note->type = "loan";
            $note->notes = $request->notes;
            $note->save();
            GeneralHelper::audit_trail("Create Note", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.notes.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        Note::destroy($id);
        GeneralHelper::audit_trail("Delete Note", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_note($note)
    {
        if (!Sentinel::hasAccess('loans.notes.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.show_note', compact('note'))->render();

    }

    public function edit_note($note)
    {
        if (!Sentinel::hasAccess('loans.notes.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.edit_note', compact('note'))->render();

    }

    public function update_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.notes.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $note = Note::find($id);
            $note->notes = $request->notes;

            $note->save();
            GeneralHelper::audit_trail("Update Note", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    //loan collateral
    public function store_collateral(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.collateral.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'collateral_type_id' => 'required',
            'description' => 'required',
            'serial' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $collateral = new Collateral();
            $collateral->loan_id = $id;
            //$collateral->created_by_id = Sentinel::getUser()->id;
            $collateral->collateral_type_id = $request->collateral_type_id;
            $collateral->description = $request->description;
            $collateral->value = $request->value;
            $collateral->serial = $request->serial;
            $collateral->save();
            GeneralHelper::audit_trail("Create Collateral", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_collateral(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.collateral.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        Collateral::destroy($id);
        GeneralHelper::audit_trail("Delete Collateral", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_collateral($collateral)
    {
        if (!Sentinel::hasAccess('loans.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.show_collateral', compact('collateral'))->render();

    }

    public function edit_collateral($collateral)
    {
        if (!Sentinel::hasAccess('loans.collateral.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.edit_collateral', compact('collateral'))->render();

    }

    public function update_collateral(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.collateral.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'collateral_type_id' => 'required',
            'description' => 'required',
            'serial' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $collateral = Collateral::find($id);
            $collateral->collateral_type_id = $request->collateral_type_id;
            $collateral->description = $request->description;
            $collateral->value = $request->value;
            $collateral->serial = $request->serial;
            $collateral->save();
            GeneralHelper::audit_trail("Update Collateral", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    //loan collateral
    public function store_guarantor(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.guarantors.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'is_client' => 'required',
            'amount' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $guarantor = new Guarantor();
            $guarantor->loan_id = $id;
            //$collateral->created_by_id = Sentinel::getUser()->id;
            $guarantor->client_relationship_id = $request->client_relationship_id;
            $guarantor->is_client = $request->is_client;
            if ($request->client_id == 1) {
                $guarantor->client_id = $request->client_id;
                $guarantor->lock_funds = $request->lock_funds;
                $client = Client::find($request->client_id);
                $savings = Savings::where('client_id', $client->id)->first();
                if ($request->lock_funds == 1 && !empty($savings)) {
                    if (GeneralHelper::savings_account_balance($savings->id) < $request->amount) {
                        Flash::warning("Savings balance low");
                        return redirect()->back();
                    }
                    $savings_transaction = new SavingsTransaction();
                    $savings_transaction->created_by_id = Sentinel::getUser()->id;
                    $savings_transaction->office_id = $client->office_id;
                    $savings_transaction->savings_id = $savings->id;
                    $savings_transaction->transaction_type = "guarantee";
                    $savings_transaction->reversible = 1;
                    $savings_transaction->date = date("Y-m-d");
                    $savings_transaction->time = date("H:i");
                    $date = explode('-', date("Y-m-d"));
                    $savings_transaction->year = $date[0];
                    $savings_transaction->month = $date[1];
                    $savings_transaction->debit = $request->amount;
                    $savings_transaction->save();
                    $guarantor->savings_id = $savings->id;
                }
            } else {
                $guarantor->first_name = $request->first_name;
                $guarantor->middle_name = $request->middle_name;
                $guarantor->last_name = $request->last_name;
                $guarantor->mobile = $request->mobile;
            }

            $guarantor->amount = $request->amount;
            $guarantor->save();
            GeneralHelper::audit_trail("Create Guarantor", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_guarantor(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.guarantors.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $guarantor = Guarantor::find($id);
        Guarantor::destroy($id);
        foreach (SavingsTransaction::where('savings_id', $guarantor->savings_id)->where('transaction_type', 'guarantee')->get() as $key) {
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->created_by_id = Sentinel::getUser()->id;
            $savings_transaction->office_id = $key->office_id;
            $savings_transaction->savings_id = $key->savings_id;
            $savings_transaction->transaction_type = "guarantee_restored";
            $savings_transaction->reversible = 1;
            $savings_transaction->date = date("Y-m-d");
            $savings_transaction->time = date("H:i");
            $date = explode('-', date("Y-m-d"));
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->credit = $key->amount;
            $savings_transaction->save();

        }
        GeneralHelper::audit_trail("Delete Guarantor", "Loans", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_guarantor($guarantor)
    {
        if (!Sentinel::hasAccess('loans.guarantors.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.show_guarantor', compact('guarantor'))->render();

    }

    public function edit_guarantor($guarantor)
    {
        if (!Sentinel::hasAccess('loans.guarantors.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('loan.edit_guarantor', compact('guarantor'))->render();

    }

    public function update_guarantor(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.guarantors.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'amount' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $guarantor = Guarantor::find($id);
            $guarantor->amount = $request->amount;
            $guarantor->save();
            GeneralHelper::audit_trail("Update Guarantor", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function approve_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'approved_amount' => 'required',
            'approved_date' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            $client = $loan->client; // Get the client for notification
            if ($loan->status != "pending") {
                Flash::warning("Loan not pending");
                return redirect()->back();
            }
            $loan->status = "approved";
            $loan->approved_by_id = Sentinel::getUser()->id;
            $loan->approved_amount = $request->approved_amount;
            $loan->principal = $request->approved_amount;
            $loan->approved_date = $request->approved_date;
            $loan->approved_notes = $request->approved_notes;
            $loan->save();

            // Notify loan officer that their loan has been approved
            // Notifix::notifyLoanOfficerLoanApproved($loan, $client);
            // Log audit for updating the client's loan
            $user = Sentinel::getUser();
            $this->auditorService->logLoanUpdated($user, request(), $loan);
            event(new LoanApproved($loan));
            GeneralHelper::audit_trail("Approve", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function decline_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'declined_notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            $client = $loan->client; // Get the client for notification
            if ($loan->status != "pending") {
                Flash::warning("Loan not pending");
                return redirect()->back();
            }
            $loan->status = "declined";
            $loan->declined_by_id = Sentinel::getUser()->id;
            $loan->declined_date = date("Y-m-d");
            $loan->declined_notes = $request->declined_notes;
            $loan->save();

            // Notify loan officer that their loan has been declined
            // Notifix::notifyLoanOfficerLoanDeclined($loan, $client);  
            
            // Log audit for declining the client's loan
            $user = Sentinel::getUser();
            $this->auditorService->logDeclinedLoan($user, request(), $loan);

            GeneralHelper::audit_trail("Decline", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function change_loan_officer(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'loan_officer_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            $loan->loan_officer_id = $request->loan_officer_id;
            $loan->save();
            GeneralHelper::audit_trail("Update", "Loans", $id);
            
            // Log audit for changing the client's loan officer
            $user = Sentinel::getUser();
        
            $this->auditorService->logChangedLoanOfficer($user, request(), $loan);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }

    }

    public function change_branch(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $rules = array(
            'office' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            $loan->office_id = $request->office;
            $loan->save();
            GeneralHelper::audit_trail("Update", "Loans", $id);
                        
            // Log audit for changed branch
            $user = Sentinel::getUser();
        
            $this->auditorService->logChangedBranch($user, request(), $loan);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }

    }

    public function unapprove_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.undo_approval')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $loan = Loan::find($id);
        if ($loan->status != "approved") {
            Flash::warning("Loan not approved");
            return redirect()->back();
        }
        $loan->status = "pending";
        $loan->approved_by_id = null;
        $loan->approved_amount = null;
        $loan->approved_date = null;
        $loan->approved_notes = null;
        $loan->save();
        GeneralHelper::audit_trail("Unapprove", "Loans", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }



    public function verifyPassword(Request $request)
{
    $user = Sentinel::getUser();

    if (!$user) {
        return response()->json([
            'success' => false
        ]);
    }

    if (Hash::check($request->password, $user->password)) {

        return response()->json([
            'success' => true
        ]);
    }

    return response()->json([
        'success' => false
    ]);
}

    public function disburse_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.disburse')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'disbursement_date' => 'required',
            'first_repayment_date' => 'required|after_or_equal:disbursement_date',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            if ($loan->status != "approved") {
                Flash::warning("Loan not approved");
                return redirect()->back();
            }


//         if($loan->loan_product->id == 1 || $loan->loan_product->id == 2) {

     

//             $paymentType = $request->payment_type;

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

//    }



            $loan->status = "disbursed";
            $loan->disbursed_by_id = Sentinel::getUser()->id;
            $loan->disbursed_notes = $request->disbursed_notes;
            $todaysDate = date('Y-m-d');
            $loan->disbursement_date = $todaysDate;
            $loan->first_repayment_date = date('Y-m-d', strtotime($todaysDate . '+ 1 month'));
            $loan->expected_maturity_date = date_format(
                date_add(
                    date_create($loan->first_repayment_date),
                    date_interval_create_from_date_string($loan->loan_term . ' ' . $loan->loan_term_type)
                ),
                'Y-m-d'
            );
            $loan->save();

            //save repayment schedule
            $interest_rate = GeneralHelper::determine_interest_rate($loan->id);
            $period = $loan->loan_term / $loan->repayment_frequency;
            $next_payment = $loan->first_repayment_date;
            $balance = $loan->principal;
            $decimals = $loan->loan_product->decimals;
            $rounded_interest = 0;
            $loan_product = $loan->loan_product;
            $grace_on_principal = $loan_product->grace_on_principal;
            $grace_on_interest_charged = $loan_product->grace_on_interest_charged;
            $grace_on_interest_payment = $loan_product->grace_on_interest_payment;
            for ($i = 0; $i < $period; $i++) {
                $loan_repayment_schedule = new LoanRepaymentSchedule();
                $loan_repayment_schedule->loan_id = $loan->id;
                $loan_repayment_schedule->due_date = $next_payment;
                $date = explode('-', $next_payment);
                $loan_repayment_schedule->month = $date[1];
                $loan_repayment_schedule->year = $date[0];
                //determine which method to use
                if ($loan->interest_method == "declining_balance") {
                    if ($loan->armotization_method == "equal_installment") {
                        if ($loan_product->grace_on_principal > 0) {
                            $due = round(GeneralHelper::amortized_payment($loan->id, $loan->principal, $period - $loan_product->grace_on_principal), $decimals);
                        } else {
                            $due = round(GeneralHelper::amortized_payment($loan->id, $loan->principal, $period), $decimals);
                        }
                        $interest = ($interest_rate * $balance);


                        //determine next balance
                        if ($i == $period - 1) {
                            //last record, balance rounded figures
                            $principal_due = $balance;
                            $interest = round($interest + $rounded_interest);
                        } else {
                            if ($grace_on_principal > 0) {
                                $grace_on_principal--;
                                $principal_due = 0;
                            } else {
                                $principal_due = $due - round($interest, $decimals);
                            }
                            if ($grace_on_interest_payment > 0) {
                                $interest = 0;
                                $grace_on_interest_payment--;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                            if ($grace_on_interest_charged > 0) {
                                $interest = 0;
                                $grace_on_interest_charged--;
                                $principal_due = $due;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                        }
                        $loan_repayment_schedule->principal = $principal_due;
                        $loan_repayment_schedule->interest = $interest;
                        $balance = ($balance - $principal_due);
                        $rounded_interest = $rounded_interest + ($interest - round($interest, $decimals));
                    }
                    if ($loan->armotization_method == "equal_principal") {
                        $interest = ($interest_rate * $balance);
                        if ($loan_product->grace_on_principal > 0) {
                            $principal_due = round($loan->principal / ($period - $loan_product->grace_on_principal), $decimals);
                        } else {
                            $principal_due = round($loan->principal / $period, $decimals);
                        }
                        //determine next balance
                        if ($i == $period - 1) {
                            //last record, balance rounded figures
                            $principal_due = $balance;
                            $interest = round($interest + $rounded_interest);
                        } else {
                            if ($grace_on_principal > 0) {
                                $grace_on_principal--;
                                $principal_due = 0;
                            } else {

                            }
                            if ($grace_on_interest_payment > 0) {
                                $interest = 0;
                                $grace_on_interest_payment--;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                            if ($grace_on_interest_charged > 0) {
                                $interest = 0;
                                $grace_on_interest_charged--;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                        }
                        $loan_repayment_schedule->principal = $principal_due;
                        $loan_repayment_schedule->interest = $interest;
                        $balance = ($balance - $principal_due);
                        $rounded_interest = $rounded_interest + ($interest - round($interest, $decimals));
                    }
                }
                if ($loan->interest_method == "flat") {
                    $interest = ($interest_rate * $loan->principal);

                    if ($loan_product->grace_on_principal > 0) {
                        $principal_due = round($loan->principal / ($period - $loan_product->grace_on_principal), $decimals);
                    } else {
                        $principal_due = round($loan->principal / $period, $decimals);
                    }
                    //determine next balance

                    if ($i == $period - 1) {
                        //last record, balance rounded figures
                        $principal_due = $balance;
                        $interest = round($interest + $rounded_interest);
                    } else {
                        if ($grace_on_principal > 0) {
                            $grace_on_principal--;
                            $principal_due = 0;
                        } else {

                        }
                        if ($grace_on_interest_payment > 0) {
                            $interest = 0;
                            $grace_on_interest_payment--;
                        } else {
                            $interest = round($interest, $decimals);
                        }
                        if ($grace_on_interest_charged > 0) {
                            $interest = 0;
                            $grace_on_interest_charged--;
                        } else {
                            $interest = round($interest, $decimals);
                        }
                        $loan_repayment_schedule->principal = $principal_due;
                        $loan_repayment_schedule->interest = round($interest, $decimals);
                    }
                    $loan_repayment_schedule->principal = $principal_due;
                    $loan_repayment_schedule->interest = $interest;
                    $rounded_interest = $rounded_interest + ($interest - round($interest, $decimals));
                    $balance = ($balance - $principal_due);
                }

                $loan_repayment_schedule->save();
                $next_payment = date_format(
                    date_add(
                        date_create($next_payment),
                        date_interval_create_from_date_string($loan->repayment_frequency . ' ' . $loan->repayment_frequency_type)
                    ),
                    'Y-m-d'
                );
            }
            $loan->expected_maturity_date = $next_payment;
            $loan->save();

            $total_interest = LoanRepaymentSchedule::where('loan_id', $loan->id)->sum('interest');
            $payment_detail = new PaymentDetail();
            $payment_detail->payment_type_id = 3; //$request->payment_type_id;
            $payment_detail->account_number = $request->account_number;
            $payment_detail->cheque_number = $request->cheque_number;
            $payment_detail->routing_code = $request->routing_code;
            $payment_detail->receipt_number = $request->receipt_number;
            $payment_detail->bank = $request->bank;
            $payment_detail->save();
            //loan disbursement transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->transaction_type = "disbursement";
            $loan_transaction->date = $request->disbursement_date;
            $date = explode('-', $request->disbursement_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $loan->principal;
            $loan_transaction->save();
            //add interest transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->transaction_type = "interest_initial";
            $loan_transaction->date = $request->disbursement_date;
            $date = explode('-', $request->disbursement_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->debit = $total_interest;
            $loan_transaction->save();



            //check for  fees
            $fees_disbursement = 0;
            $fees_installment = 0;
            $fees_due_date = [];
            $fees_due_date_amount = 0;
            foreach ($loan->charges as $key) {
                if (!empty($key->charge)) {
                    if ($key->charge->charge_type == "disbursement") {
                        if ($key->charge->charge_option == "flat") {
                            $fees_disbursement = $fees_disbursement + $key->amount;
                        } else {
                            if ($key->charge->charge_option == "installment_principal_due") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * $loan->principal) / 100;
                            }
                            if ($key->charge->charge_option == "installment_principal_interest_due") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            }
                            if ($key->charge->charge_option == "installment_interest_due") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * $total_interest) / 100;
                            }
                            if ($key->charge->charge_option == "original_principal") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * $loan->principal) / 100;
                            }
                            if ($key->charge->charge_option == "total_due") {
                                $fees_disbursement = $fees_disbursement + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            }
                        }
                    }
                    if ($key->charge->charge_type == "installment_fee") {
                        if ($key->charge->charge_option == "flat") {
                            $fees_installment = $fees_installment + $key->amount;
                        } else {
                            if ($key->charge->charge_option == "installment_principal_due") {
                                $fees_installment = $fees_installment + ($key->amount * $loan->principal) / 100;
                            }
                            if ($key->charge->charge_option == "installment_principal_interest_due") {
                                $fees_installment = $fees_installment + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            }
                            if ($key->charge->charge_option == "installment_interest_due") {
                                $fees_installment = $fees_installment + ($key->amount * $total_interest) / 100;
                            }
                            if ($key->charge->charge_option == "original_principal") {
                                $fees_installment = $fees_installment + ($key->amount * $loan->principal) / 100;
                            }
                            if ($key->charge->charge_option == "total_due") {
                                $fees_installment = $fees_installment + ($key->amount * ($loan->principal + $total_interest)) / 100;
                            }
                        }
                    }
                    if ($key->charge->charge_type == "specified_due_date") {
                        if ($key->charge->charge_option == "flat") {
                            $fees_due_date_amount = $fees_due_date_amount + $key->amount;
                            $fees_due_date[$key->id] = $key->charge->id;
                        } else {
                            if ($key->charge->charge_option == "installment_principal_due") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $loan->principal) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                            if ($key->charge->charge_option == "installment_principal_interest_due") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * ($loan->principal + $total_interest)) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                            if ($key->charge->charge_option == "installment_interest_due") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $total_interest) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                            if ($key->charge->charge_option == "original_principal") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * $loan->principal) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                            if ($key->charge->charge_option == "total_due") {
                                $fees_due_date_amount = $fees_due_date_amount + ($key->amount * ($loan->principal + $total_interest)) / 100;
                                $fees_due_date[$key->id] = $key->charge->id;
                            }
                        }
                    }
                }
            }
            if ($fees_disbursement > 0) {
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->transaction_type = "disbursement_fee";
                $loan_transaction->date = $request->disbursement_date;
                $date = explode('-', $request->disbursement_date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->debit = $fees_disbursement;
                $loan_transaction->save();

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->transaction_type = "repayment_disbursement";
                $loan_transaction->date = $request->disbursement_date;
                $date = explode('-', $request->disbursement_date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->credit = $fees_disbursement;
                $loan_transaction->save();
                if ($loan->loan_product->accounting_rule != "none") {
                    //add journal entry for payment and charge
                    if (!empty($loan->loan_product->gl_account_income_fee)) {
                        $journal = new GlJournalEntry();
                        $journal->gl_account_id = $loan->loan_product->gl_account_income_fee->id;
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->date = $request->disbursement_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->loan_transaction_id = $loan_transaction->id;
                        $journal->transaction_type = 'fee';
                        $journal->name = "Fee Income";
                        $journal->loan_id = $loan->id;
                        $journal->credit = $fees_disbursement;
                        $journal->reference = $loan_transaction->name;
                        $journal->save();
                    }
                    if (!empty($loan->loan_product->gl_account_fund_source)) {
                        $journal = new GlJournalEntry();
                        $journal->gl_account_id = $request->fund;
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->date = $request->disbursement_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->loan_transaction_id = $loan_transaction->id;
                        $journal->transaction_type = 'fee';
                        $journal->name = "Fee Income";
                        $journal->loan_id = $loan->id;
                        $journal->debit = $fees_disbursement;
                        $journal->reference = $loan_transaction->id;
                        $journal->save();
                    }
                }
            }
            if ($fees_installment > 0) {
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->transaction_type = "installment_fee";
                $loan_transaction->reversible = 1;
                $loan_transaction->date = $request->disbursement_date;
                $date = explode('-', $request->disbursement_date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->debit = $fees_installment;
                $loan_transaction->save();
                //add installment to schedules
                foreach (LoanRepaymentSchedule::where('loan_id', $loan->id)->get() as $key) {
                    $schedule = LoanRepaymentSchedule::find($key->id);
                    $schedule->fees = $fees_installment;
                    $schedule->save();
                }
            }
            if ($fees_due_date_amount > 0) {
                foreach ($fees_due_date as $key => $value) {
                    $charge = Charge::find($value);
                    $loan_charge = LoanCharge::find($key);
                    $amount = 0;
                    if ($charge->charge_option == "flat") {
                        $amount = $loan_charge->amount;
                    } else {
                        if ($charge->charge_option == "installment_principal_due") {
                            $amount = ($loan_charge->amount * $loan->principal) / 100;
                        }
                        if ($charge->charge_option == "installment_principal_interest_due") {
                            $amount = ($loan_charge->amount * ($loan->principal + $total_interest)) / 100;
                        }
                        if ($charge->charge_option == "installment_interest_due") {
                            $amount = ($loan_charge->amount * $total_interest) / 100;
                        }
                        if ($charge->charge_option == "original_principal") {
                            $amount = ($loan_charge->amount * $loan->principal) / 100;
                        }
                        if ($charge->charge_option == "total_due") {
                            $amount = ($loan_charge->amount * ($loan->principal + $total_interest)) / 100;
                        }
                    }
                    $due_date = GeneralHelper::determine_due_date($loan->id, $loan_charge->due_date);
                    if (!empty($due_date)) {
                        $schedule = LoanRepaymentSchedule::where('loan_id', $loan->id)->where('due_date', $due_date)->first();
                        $schedule->fees = $schedule->fees + $amount;
                        $schedule->save();
                        $loan_transaction = new LoanTransaction();
                        $loan_transaction->created_by_id = Sentinel::getUser()->id;
                        $loan_transaction->office_id = $loan->office_id;
                        $loan_transaction->loan_id = $loan->id;
                        $loan_transaction->loan_repayment_schedule_id = $schedule->id;
                        $loan_transaction->reversible = 1;
                        $loan_transaction->transaction_type = "specified_due_date_fee";
                        $loan_transaction->date = $due_date;
                        $date = explode('-', $due_date);
                        $loan_transaction->year = $date[0];
                        $loan_transaction->month = $date[1];
                        $loan_transaction->debit = $amount;
                        $loan_transaction->save();
                    }
                }

            }
            if ($loan->loan_product->accounting_rule != "none") {
                if (!empty($loan->loan_product->gl_account_fund_source)) {
                    $journal = new GlJournalEntry();
                    $journal->created_by_id = Sentinel::getUser()->id;
                    $journal->office_id = $loan->office_id;
                    $journal->currency_id = $loan->currency_id;
                    $journal->gl_account_id = $loan->fund_id;
                    $journal->date = $request->disbursement_date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->transaction_type = 'disbursement';
                    $journal->name = "Loan Disbursement";
                    $journal->loan_id = $loan->id;
                    $journal->credit = $loan->principal;
                    $journal->reference = $loan->name;
                    $journal->save();
                }
                if (!empty($loan->loan_product->gl_account_loan_portfolio)) {
                    $journal = new GlJournalEntry();
                    $journal->created_by_id = Sentinel::getUser()->id;
                    $journal->office_id = $loan->office_id;
                    $journal->currency_id = $loan->currency_id;
                    $journal->gl_account_id = $loan->loan_product->gl_account_loan_portfolio->id;
                    $journal->date = $request->disbursement_date;
                    $journal->year = $date[0];
                    $journal->month = $date[1];
                    $journal->transaction_type = 'disbursement';
                    $journal->name = "Loan Disbursement";
                    $journal->loan_id = $loan->id;
                    $journal->debit = $loan->principal;
                    $journal->reference = $loan->name;
                    $journal->save();
                }
                if ($loan->loan_product->accounting_rule != "accrual_upfront") {
                    if (!empty($loan->loan_product->gl_account_receivable_interest)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_receivable_interest->id;
                        $journal->date = $request->disbursement_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'accrual';
                        $journal->name = "Accrued Interest";
                        $journal->loan_id = $loan->id;
                        $journal->debit = $total_interest;
                        $journal->reference = $loan->name;
                        $journal->save();
                    }
                    if (!empty($loan->loan_product->gl_account_income_interest)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_income_interest->id;
                        $journal->date = $request->disbursement_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'accrual';
                        $journal->name = "Accrued Interest";
                        $journal->loan_id = $loan->id;
                        $journal->credit = $total_interest;
                        $journal->reference = $loan->name;
                        $journal->save();
                    }
                }
            }

            $client = $loan->client; // Get the client for notification
            $payment_type = 3;//PaymentType::find($request->payment_type_id);
            $payment_type_name = $request->payment_type;

            // Notify loan officer that their loan has been disbursed
            // Notifix::notifyLoanOfficerLoanDisbursed($loan, $client, Sentinel::getUser(), $payment_type_name);
            // Notifix::notifyDailyReminderToRiskManager('disbused a loan amount of K'.$loan->principal);
            //define Log audit for disbursing loan
            $user = Sentinel::getUser();
            $this->auditorService->logDisbursedLoan($user, request(), $loan);
            event(new LoanDisbursed($loan));
            GeneralHelper::audit_trail("Disburse", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function undisburse_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.undo_disbursement')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $loan = Loan::find($id);
        if ($loan->status != "disbursed") {
            Flash::warning("Loan not disbursed");
            return redirect()->back();
        }
        $loan->status = "approved";
        $loan->disbursed_by_id = null;
        $loan->disbursed_notes = null;
        $loan->disbursement_date = null;
        $loan->first_repayment_date = null;
        $loan->save();
        LoanRepaymentSchedule::where('loan_id', $loan->id)->delete();
        LoanTransaction::where('loan_id', $loan->id)->delete();
        GlJournalEntry::where('loan_id', $loan->id)->delete();
        GeneralHelper::audit_trail("Undo Disburse", "Loans", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //repayments
    public function create_repayment($loan)
    {
        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        // Get the loan model from the ID - ensure we have an ID, not a model instance
        $loanId = $loan instanceof \App\Models\Loan ? $loan->id : $loan;
        $loan = Loan::find($loanId);
        
        if (!$loan) {
            Flash::warning("Loan not found");
            return redirect()->back();
        }

        // Get active recovery cases for this loan with client eager loading
        $recoveryCases = \App\Models\RecoveryCase::with('client')
            ->where('loan_id', $loan->id)
            ->whereNotNull('approved_date')
            ->get();

        return view('loan.repayment.create', compact('loan', 'recoveryCases'));
    }




    public function transaction_fp_pp(Request $request, $id)
    {
       try {
         if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $todaysDate = date('Y-m-d');

        if ($request->date < $todaysDate) {
            Flash::warning("Enter a date equal or greater than today's date");
            return redirect()->back();
        }

        $rules = array(
            'date' => 'required|before_or_equal:' . date("Y-m-d"),
            'payment_type_id' => 'required',
            'amount' => 'required',
        );
        $messages = [
            'date.required' => 'Date is required',
            'payment_type_id.required' => 'Payment type is required',
            'amount.required' => 'Amount is required',
            'date.before_or_equal' => 'Date must not be a future date',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            $pending_transaction = LoanTransactionUnapproved::where('loan_id', $loan->id)->first();
            if (!empty($pending_transaction)) {

                Flash::warning("This loan already has a transaction pending!!");
                return redirect('loan/' . $loan->id . '/show');
            } else {
                $loan->loan_product->gl_account_fund_source = $request->gl_account_fund_source_id;

                $loan_transaction = new LoanTransactionUnapproved();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->reversible = 1;

                if ($request->has('is_recovery')) {
                    //loan approval is marked for recoveries unit to see and approve or decline
                    $loan_transaction->is_recovery = 1;
                    //if the recovery transaction is full payment
                    if ($request->is_settlement == 1) {
                        $loan_transaction->payment_apply_to = 'full_payment';
                    }else{
                        $loan_transaction->payment_apply_to = 'part_payment';
                    }
                } else {
                    //else if not recovery transaction, then check if the user has selected payment apply to
                    $loan_transaction->payment_apply_to = $request->payment_apply_to;
                }

                $loan_transaction->payment_detail_id = null;
                $loan_transaction->payment_detail_id = null;
                $loan_transaction->transaction_type = "repayment";
                $loan_transaction->date = $request->date;
                $date = explode('-', $request->date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->credit = $request->amount;
                $loan_transaction->notes = $request->notes;
                $loan_transaction->payment_type_id = $request->payment_type_id;
                $loan_transaction->account_number = $request->account_number;
                $loan_transaction->cheque_number = $request->cheque_number;
                $loan_transaction->routing_code = $request->routing_code;
                $loan_transaction->receipt_number = $request->receipt_number;
                $loan_transaction->bank = $request->bank;
                $loan_transaction->notes_pd = $request->notes;
                // $loan_transaction->request_id = $request->$id;
                $loan_transaction->save();
                $client_id = $loan->client_id;
                $client = \App\Models\Client::find($client_id);
                Http::post('https://notifications.whencefinancesystem.com/emit', [
                    'event' => 'loan.created',
                    'data' => [
                        'created_by' => Sentinel::getUser()->first_name . ' ' . Sentinel::getUser()->last_name,
                        'office_id' => Sentinel::getUser()->office->id,
                        'client' => $client->first_name . ' ' . $client->last_name,
                        'amount' => $request->amount,
                        'type' => $request->has('is_recovery') ? ($request->is_settlement == 1 ? 'Recovery full payment' : 'Recovery part payment') : $request->payment_apply_to,
                        'loan' => $loan->toArray(),
                        'transaction' => $loan_transaction->toArray()
                    ]
                ]);

                // Notify managers for transaction approval
                // Notifix::notifyBmToApproveTransaction($loan, $client, $request->amount);
                // Notifix::notifyRiskToReviewLoan($loan, $client, $request->amount);
                //define Log audit for entering a transaction for approval, include $loan, client details in the log message
                $user = Sentinel::getUser();
                $this->auditorService->logEnteredTransaction($user, request(), $loan);
                Flash::success(trans('general.successfully_saved'));
                return redirect('loan/' . $loan->id . '/show');
            }
        }
       } catch (\Throwable $th) {
         dd($th);
       }
    }



    public function store_repayment(Request $request, $id, $trans_id)
    {
        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan = Loan::find($id);
        $pending_transactions = LoanTransactionUnapproved::where('loan_id', $id)->get();
        $count = count($pending_transactions);
        $Trans = LoanTransactionUnapproved::find($trans_id);
        $loan_balance = GeneralHelper::loan_total_balance($loan->id);
   $new_balance = 0;
$debit_amount = 0;
$credit_amount = 0;

foreach (LoanTransaction::where('loan_id', $loan->id)->get() as $transaction) {
    $debit_amount += $transaction->debit;
    $credit_amount += $transaction->credit;
}

$new_balance = $debit_amount - $credit_amount;
        // $existing_transaction = LoanTransaction::where('loan_id', $id)->where('date', $Trans->date)->where('credit', $Trans->credit)->where('transaction_type', '!=', 'interest_waiver')->first();
        //disabled because client failing to add  2 transactions with same amount and date, we can add more checks to ensure its not a duplicate transaction instead of blocking all transactions with same amount and date
        // $existing_transaction = [];

        if ($count > 1) {
            Flash::warning("This loan has more than one pending transaction!!");
            return redirect('loan/transaction_approvals');

        } else {

            if (!empty($existing_transaction)) {
                Flash::warning("This transaction has already been entered!!");
                return redirect('loan/transaction_approvals');

            } else {

                $Trans = LoanTransactionUnapproved::find($trans_id);

                if(!$Trans) {
                    Flash::warning("This transaction failed!!");
                    return redirect('loan/transaction_approvals');
                }

                $loan->loan_product->gl_account_fund_source = $request->gl_account_fund_source_id;

                $payment_detail = new PaymentDetail();
                $payment_detail->payment_type_id = $Trans->payment_type_id_pd;
                $payment_detail->account_number = $Trans->account_number;
                $payment_detail->cheque_number = $Trans->cheque_number;
                $payment_detail->routing_code = $Trans->routing_code;
                $payment_detail->receipt_number = $Trans->receipt_number;
                $payment_detail->bank = $Trans->bank;
                $payment_detail->notes = $Trans->notes_pd;
                $payment_detail->save();


                //repayment  transaction
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->reversible = 1;
                $loan_transaction->payment_apply_to = $Trans->payment_apply_to;
                $loan_transaction->payment_detail_id = $payment_detail->id;
                $loan_transaction->payment_detail_id = $payment_detail->id;
                $loan_transaction->transaction_type = "repayment";
                $loan_transaction->date = $Trans->date;
                $date = explode('-', $Trans->date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->credit = $Trans->credit;
                $loan_transaction->notes = $Trans->notes;
                $loan_transaction->temp_id = $trans_id;
                $loan_transaction->save();
                //check custom fields
                if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                    $custom_fields = CustomField::where('category', 'repayments')->get();
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
                        $custom_field->parent_id = $loan_transaction->id;
                        $custom_field->custom_field_id = $key->id;
                        $custom_field->category = "repayments";
                        $custom_field->save();
                    }
                }

                LoanTransactionUnapproved::where('id', $trans_id)->delete();


                $new_loan_balance = $new_balance - $Trans->credit;

                event(new RepaymentCreated($loan_transaction));

                if ($Trans->payment_apply_to == 'full_payment' && $new_loan_balance <= 0) {
                    $loan = Loan::find($loan->id);
                    $loan->status = 'closed';
                    $loan->save();
                }

 
                //define Log audit for approving a transaction for approval, include $loan, client details in the log message
                $user = Sentinel::getUser();
                $this->auditorService->logApprovedTransaction($user, request(), $loan);
                // Send SMS to client about the transaction
                $amount = number_format($Trans->credit, 2);
                $date = $Trans->date;
                $paymentType = $Trans->payment_apply_to;
                $client = Client::where('id', $loan->client_id)->first();

                if($loan->office_id == 8){
                    $balance = GeneralHelper::loan_total_balance($loan->id);
                    $inline = ', your new loan balance is ZMW ' . number_format($balance, 2) . '.';
                }else{
                    $inline = '';
                }
                // Create a message based on the payment type
                if ($paymentType == 'full_payment') {
                    $message = "Dear {$client->first_name}, thank you for paying ZMW {$amount}. Your loan is now fully settled. For any queries, call 0773425477.";
                } elseif ($paymentType == 'part_payment') {
                    $message = "Dear {$client->first_name}, thank you for paying ZMW {$amount}. {$inline} For any queries, call 0773425477.";
                } else {
                    $message = "Dear {$client->first_name}, thank you for paying ZMW {$amount}. {$inline} For any queries, call 0773425477.";
                }

                // Send SMS to client about the transaction (only for enabled offices)
                $enabledOffices = config('smsoffices.enabled_offices', []);
                if (in_array($loan->office_id, $enabledOffices)) {
                    $this->bulkSms->sendToClients([$client], $message);
                }
                
                


                GeneralHelper::audit_trail("Create Repayment", "Loans", $id);

                Flash::success(trans('general.successfully_saved'));
                return redirect('loan/' . $loan->id . '/show');
            }
        }
    }

    public function store_debt_recovery(Request $request, $loan){
          
        try {
            $loan = Loan::where('id', $loan)->first();
                        // Get the recovery case
            $recoveryCase = \App\Models\RecoveryCase::find($request->recovery_case_id);
            
            if ($recoveryCase) {
                // Get payment details from form
                $amount = $request->amount;
                
                // Get attribution details from recovery case (not from form)
                $recoveriesDeptPct = $recoveryCase->recoveries_dept_attribution_pct ?? 0;
                $originBranchPct = $recoveryCase->origin_branch_attribution_pct ?? 0;
                $supportingBranchPct = $recoveryCase->supporting_branch_attribution_pct ?? 0;
                
                // Get branch details from recovery case
                $originBranchId = $recoveryCase->origin_branch_id;
                $supportingBranchId = $recoveryCase->supporting_branch_id;
                $assignedSpecialistId = $recoveryCase->assigned_specialist_id;
                
                // Get outstanding from recovery case (not from form)
                $outstandingBefore = $recoveryCase->loan_outstanding_amount;
                $previousRecovered = $recoveryCase->amount_recovered ?? 0;
                $outstandingAfter = max(0, $outstandingBefore - $amount);
                
                // Calculate attribution amounts from recovery case percentages
                $recoveriesDeptAmount = $amount * ($recoveriesDeptPct / 100);
                $originBranchAmount = $amount * ($originBranchPct / 100);
                $supportingBranchAmount = $amount * ($supportingBranchPct / 100);
                
                // Create recovery payment record
                $recoveryPayment = new \App\Models\RecoveryPayment();
                $recoveryPayment->recovery_case_id = $recoveryCase->id;
                $recoveryPayment->transaction_id = null;
                $recoveryPayment->recorded_by = Sentinel::getUser()->id;
                $recoveryPayment->receipt_number = $request->receipt_number ?? \App\Models\RecoveryPayment::generateReceiptNumber();
                $recoveryPayment->amount = $amount;
                
                // Handle payment method - validate against allowed enum values
                $allowedPaymentMethods = ['cash', 'mobile_money', 'bank_transfer', 'cheque', 'payroll_deduction'];
                $paymentMethodInput = $request->payment_type_id;
                
                // If payment_method is numeric, get the actual name from PaymentType
                if (is_numeric($paymentMethodInput)) {
                    $paymentType = \App\Models\PaymentType::find($paymentMethodInput);
                    if ($paymentType) {
                        $paymentMethodInput = strtolower(str_replace(' ', '_', $paymentType->name));
                    }
                }
                
                // Validate and set payment method
                if (in_array($paymentMethodInput, $allowedPaymentMethods)) {
                    $recoveryPayment->payment_method = $paymentMethodInput;
                } else {
                    // Default to 'cash' if invalid value provided
                    $recoveryPayment->payment_method = 'cash';
                }
                $recoveryPayment->payment_date = $request->date ?? date('Y-m-d');
                $recoveryPayment->payment_reference = $request->payment_reference;
                $recoveryPayment->bank_name = $request->bank_name;
                $recoveryPayment->recoveries_dept_amount = $recoveriesDeptAmount;
                $recoveryPayment->origin_branch_amount = $originBranchAmount;
                $recoveryPayment->supporting_branch_amount = $supportingBranchAmount;
                $recoveryPayment->is_settlement = $request->is_settlement ?? false;
                $recoveryPayment->outstanding_before = $outstandingBefore;
                $recoveryPayment->outstanding_after = $outstandingAfter;
                $recoveryPayment->notes = $request->notes;
                $recoveryPayment->save();

                // Handle dept_share_amount
                if ($request->filled('dept_share_amount') && $request->dept_share_amount > 0) {
                    \App\Models\RecoveriesDeptExcalatedShare::create([
                        'recovery_case_id' => $recoveryCase->id,
                        'recovery_payment_id' => $recoveryPayment->id,
                        'dept_share_amount' => $request->dept_share_amount,
                        'notes' => $request->notes,
                        'created_by' => Sentinel::getUser()->id,
                    ]);
                }
                
                // Update recovery case with amount recovered (from case, not form)
                $recoveryCase->amount_recovered = $previousRecovered + $amount;
                $recoveryCase->last_payment_date = $request->date ?? date('Y-m-d');
                $recoveryCase->save();
                
                // If settlement, update case status
                if ($request->is_settlement == 1 || $request->is_settlement == '1') {
                    $recoveryCase->status = 'recovered_runaway';
                    $recoveryCase->settlement_amount = $amount;
                    $recoveryCase->resolved_date = date('Y-m-d');
                    $recoveryCase->save();

                    $loan->status = 'closed';
                    $loan->save();
                }
                // Log audit for entering a Debt recovery transaction for approval
                $user = Sentinel::getUser();
                $this->auditorService->logEntereedRecoveryTransactionForApproval($user, request(), $loan);

                Flash::success(trans('general.successfully_saved'));
                return redirect('loan/' . $loan->id . '/show');
            }
        } catch (\Throwable $th) {
            dd($th.' Contact IT Support');
            return null;
        }
    }

    public function edit_repayment($loan_transaction)
    {
        if (!Sentinel::hasAccess('loans.transactions.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.repayment.edit', compact('loan_transaction'));
    }

    public function update_repayment(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'date' => 'required|before_or_equal:' . date("Y-m-d"),
            //  'payment_type_id' => 'required',
            'amount' => 'required',
        );
        $messages = [
            'date.required' => 'Date is required',
            //  'payment_type_id.required' => 'Payment type is required',
            'amount.required' => 'Amount is required',
            'date.before_or_equal' => 'Date must not be a future date',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        $loanT = LoanTransaction::find($id);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            //   //  reverse transaction
            $loan_transaction = LoanTransaction::find($id);
            $loan_transaction->reversible = 0;
            $loan_transaction->reversed = 1;
            $loan_transaction->reversal_type = "user";
            $loan_transaction->debit = $loan_transaction->credit;
            $loan_transaction->save();
            //reverse journal entries
            foreach (GlJournalEntry::where('loan_transaction_id', $id)->where(
                'loan_id',
                $loan_transaction->loan_id
            )->where('transaction_type', 'repayment')->get() as $key) {
                $journal = GlJournalEntry::find($key->id);
                if ($key->debit > $key->credit) {
                    $journal->credit = $journal->debit;
                } else {
                    $journal->debit = $journal->credit;
                }
                $journal->reversed = 1;
                $journal->save();
            }
            $loan = $loan_transaction->loan;

            $payment_detail = new PaymentDetail();
            $payment_detail->payment_type_id = $request->payment_type_id;
            $payment_detail->account_number = $request->account_number;
            $payment_detail->cheque_number = $request->cheque_number;
            $payment_detail->routing_code = $request->routing_code;
            $payment_detail->receipt_number = $request->receipt_number;
            $payment_detail->bank = $request->bank;
            $payment_detail->notes = $request->notes;
            $payment_detail->save();

            //repayment  transaction
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->reversible = 1;
            $loan_transaction->payment_apply_to = $loanT->payment_apply_to;
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->transaction_type = $loanT->transaction_type;//"repayment";
            $loan_transaction->date = $request->date;
            $date = explode('-', $request->date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            if ($loan_transaction->payment_apply_to == 'full_payment') {
                $loan_transaction->debit = $request->amount;
            } else {
                $loan_transaction->credit = $request->amount;
            }
            $loan_transaction->notes = $request->notes;
            $loan_transaction->save();
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'repayments')->get();
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
                    $custom_field->parent_id = $loan_transaction->id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "repayments";
                    $custom_field->save();
                }
            }
            event(new RepaymentUpdated($loan_transaction));
            GeneralHelper::audit_trail("Update Repayment", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }
    }

    public function reverse_repayment(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //reverse transaction
        $loan_transaction = LoanTransaction::find($id);
        $loan_transaction->reversible = 0;
        $loan_transaction->reversed = 1;
        $loan_transaction->reversal_type = "user";
        $loan_transaction->debit = $loan_transaction->credit;
        $loan_transaction->save();
        //reverse journal entries
        foreach (GlJournalEntry::where('loan_transaction_id', $id)->where(
            'loan_id',
            $loan_transaction->loan_id
        )->where('transaction_type', 'repayment')->get() as $key) {
            $journal = GlJournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        $loan = $loan_transaction->loan;

        event(new RepaymentUpdated($loan_transaction));
        if (GeneralHelper::loan_total_balance($loan->id) <= 0) {
            $loan = Loan::find($loan->id);
            $loan->status = "closed";
            $loan->save();
        }
        GeneralHelper::audit_trail("Reverse Repayment", "Loans", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //transaction
    public function show_transaction($loan_transaction)
    {
        if (!Sentinel::hasAccess('loans.transactions.view')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.transaction.show', compact('loan_transaction'));
    }

    public function print_transaction($loan_transaction)
    {
        if (!Sentinel::hasAccess('loans.transactions.view')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.transaction.print', compact('loan_transaction'));
    }

    public function pdf_transaction($loan_transaction)
    {
        // if (!Sentinel::hasAccess('loans.transactions.view')) {
        //     Flash::warning(trans('general.permission_denied'));
        //     return redirect()->back();
        // }
        $current_balance = 0;
        $out = 0;
        $in = 0;
        $Loan = Loan::with('transactions')->where('id', $loan_transaction->loan_id)->first();
        foreach ($Loan->transactions as $transaction) {
            $out = $out + $transaction->debit;
            $in = $in + $transaction->credit;
        }
        $current_balance = $out - $in;
        // Handle reloan payment
        if ($loan_transaction->payment_apply_to == 'reloan_payment') {
            $current_balance += 0.4 * $current_balance;
        }

        // Ensure balance is not negative
        if ($current_balance < 0) {
            $current_balance = 0;
        }
        $due_date = $Loan->first_repayment_date;
        $transaction_type = $loan_transaction->payment_apply_to;
        ////////////////////////////////////////////
        $pdf = PDF::loadView('loan.transaction.pdf', compact('loan_transaction', 'due_date', 'current_balance'));
        return $pdf->download(trans_choice('general.loan', 1) . ' ' . trans_choice('general.transaction', 1) . ' ' . trans_choice('general.receipt', 1) . ".pdf");

    }

    public function withdraw_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'withdrawn_notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            if ($loan->status != "pending") {
                Flash::warning("Loan not pending");
                return redirect()->back();
            }
            $loan->status = "withdrawn";
            $loan->withdrawn_by_id = Sentinel::getUser()->id;
            $loan->withdrawn_date = date("Y-m-d");
            $loan->withdrawn_notes = $request->withdrawn_notes;
            $loan->save();
            GeneralHelper::audit_trail("Withdraw", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function pdf_schedule($loan)
    {
        if (!Sentinel::hasAccess('loans.pdf_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $pdf = PDF::loadView('loan.pdf_schedule', compact('loan'));
        return $pdf->download(trans_choice('general.loan', 1) . ' ' . trans_choice('general.schedule', 1) . ".pdf");

    }
    public function pdf_statement($loan)
    {
        if (!Sentinel::hasAccess('loans.pdf_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $pdf = PDF::loadView('loan.pdf_statement', compact('loan'));
        return $pdf->download(trans_choice('general.client', 1) . ' ' . trans_choice('general.statement', 1) . ".pdf");

    }

    public function print_schedule($loan)
    {
        if (!Sentinel::hasAccess('loans.pdf_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.print_schedule', compact('loan'));
    }

    public function statement($loan)
    {
        if (!Sentinel::hasAccess('loans.pdf_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('loan.statement', compact('loan'));
    }


    public function email_schedule($loan)
    {
        if (!Sentinel::hasAccess('loans.email_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $email = "";
        if ($loan->client_type == "client") {
            $email = $loan->client->email;
        }
        if ($loan->client_type == "group") {
            $email = $loan->group->email;
        }
        if (!empty($email)) {
            Mail::to($email)->send(new RepaymentScheduleEmail($loan));
        } else {
            Flash::warning("Client has no email");
        }

        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function email_statement($loan)
    {
        if (!Sentinel::hasAccess('loans.email_schedule')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $email = "";
        if ($loan->client_type == "client") {
            $email = $loan->client->email;
        }
        if ($loan->client_type == "group") {
            $email = $loan->group->email;
        }
        if (!empty($email)) {
            Mail::to($email)->send(new LoanStatementEmail($loan));
        } else {
            Flash::warning("Client has no email");
        }

        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }
    //waive charge
    public function waive_transaction(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //reverse transaction
        $loan_transaction = LoanTransaction::find($id);
        $loan_transaction->reversible = 0;
        $loan_transaction->reversed = 1;
        $loan_transaction->reversal_type = "user";
        $loan_transaction->credit = $loan_transaction->debit;
        $loan_transaction->save();
        //reverse journal entries
        foreach (GlJournalEntry::where('loan_transaction_id', $id)->where(
            'loan_id',
            $loan_transaction->loan_id
        )->get() as $key) {
            $journal = GlJournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        $loan = $loan_transaction->loan;
        if ($loan_transaction->transaction_type == "installment_fee") {
            $amount = $loan_transaction->debit / LoanRepaymentSchedule::where('loan_id', $loan_transaction->loan_id)->count();
            foreach (LoanRepaymentSchedule::where('loan_id', $loan_transaction->loan_id)->get() as $key) {
                $schedule = LoanRepaymentSchedule::find($key->id);
                $schedule->fees = $schedule->fees - $amount;
                $schedule->save();
            }
            event(new TransactionUpdated($loan_transaction));
        }
        if ($loan_transaction->transaction_type == "specified_due_date_fee") {
            $schedule = LoanRepaymentSchedule::where("due_date", $loan_transaction->date)->where("loan_id", $loan->id)->first();
            $schedule->fees = $schedule->fees - $loan_transaction->debit;
            $schedule->save();
            event(new TransactionUpdated($loan_transaction));
        }

        if (GeneralHelper::loan_total_balance($loan->id) >= 0) {
            $loan = Loan::find($loan->id);
            $loan->status = "disbursed";
            $loan->save();
        }
        GeneralHelper::audit_trail("Waive Transaction", "Loans", $id);
        //Define this in audit servicevi In store_debt_recovery
        $user = Sentinel::getUser();
        $this->auditorService->logEnteredWaiverTransaction($user, request(), $loan);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //interest waiver
    public function waive_interest(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.waive_interest')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan = Loan::find($id);
        if ($request->date > date("Y-m-d")) {
            Flash::warning(trans_choice('general.future_date_error', 1));
            return redirect()->back()->withInput();
        }
        if ($request->date < $loan->disbursement_date) {
            Flash::warning(trans_choice('general.early_date_error', 1));
            return redirect()->back()->withInput();
        }
        $waiver_transaction = new WaiverTransactionUnapproved();
        $waiver_transaction->created_by_id = Sentinel::getUser()->id;
        $waiver_transaction->office_id = $loan->office_id;
        $waiver_transaction->loan_id = $loan->id;
        $waiver_transaction->reversible = 0;
        $waiver_transaction->transaction_type = "interest_waiver";
        $waiver_transaction->date = $request->date;
        $waiver_transaction->reversible = 0;
        $date = explode('-', $request->date);
        $waiver_transaction->year = $date[0];
        $waiver_transaction->month = $date[1];
        $waiver_transaction->credit = $request->amount;
        $waiver_transaction->notes = $request->notes;
        $waiver_transaction->status = 'pending';
        $waiver_transaction->save();
        //$amount = $request->amount;

        GeneralHelper::audit_trail("Waive Interest Request", "Loans", $id);
        Flash::success("Waiver added to pending waivers for approval.");
        return redirect()->back();
    }

    public function showWaiver(Request $request)
    {
        $user = Sentinel::getUser();
        $office_id = $user->office_id;

        if ($user->hasAccess('groups.create')) {
            $pendingWaivers = WaiverTransactionUnapproved::where('status', 'pending')
                ->where('transaction_type', 'interest_waiver')
                ->get();
        } elseif ($user->hasAccess('offices')) {
            $pendingWaivers = WaiverTransactionUnapproved::where('status', 'pending')
                ->where('transaction_type', 'interest_waiver')
                ->where('office_id', $office_id)
                ->get();
        } else {
            $provinceId = $user->province_id;
            $provinceOffices = Office::where('province_id', $provinceId)->pluck('id');
            $pendingWaivers = WaiverTransactionUnapproved::whereIn('office_id', $provinceOffices)
                ->where('status', 'pending')
                ->where('transaction_type', 'interest_waiver')
                ->get();
        }

        return view('loan.waiver_approvals', compact('pendingWaivers'));
    }


    public function approveWaiver($waiverTransactionId)
    {

        $waiver_transaction = WaiverTransactionUnapproved::find($waiverTransactionId);

        if (!$waiver_transaction || $waiver_transaction->status !== 'pending') {
            Flash::warning("Invalid or already processed waiver.");
            return redirect()->back();
        }

        $loan = $waiver_transaction->loan;
        $amount = $waiver_transaction->credit;

        //add waiver to Loan Repayment Schedule?
        foreach (LoanRepaymentSchedule::select('id', DB::raw("(COALESCE(interest,0)-COALESCE(interest_waived,0)-COALESCE(interest_written_off,0)-COALESCE(interest_paid,0)) as interest_due"))
            ->where('loan_id', $loan->id)
            ->orderBy('due_date', 'asc')
            ->havingRaw("interest_due>0")
            ->get() as $key) {

            if ($amount > 0) {
                $schedule = LoanRepaymentSchedule::find($key->id);
                if ($amount >= $key->interest_due) {
                    $schedule->interest_waived += $key->interest_due;
                    $amount -= $key->interest_due;
                } else {
                    $schedule->interest_waived += $amount;
                    $amount = 0;
                }
                $schedule->save();
            }

            if ($amount <= 0) {
                break;
            }
        }

        //approved transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = $waiver_transaction->created_by_id;
        $loan_transaction->office_id = $loan->office_id;
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->transaction_type = "interest_waiver";
        $loan_transaction->date = $waiver_transaction->date;
        $loan_transaction->year = $waiver_transaction->year;
        $loan_transaction->month = $waiver_transaction->month;
        $loan_transaction->credit = $waiver_transaction->credit;
        $loan_transaction->notes = $waiver_transaction->notes;
        $loan_transaction->save();

        //delete temp waiver
        $waiver_transaction->delete();

        if (GeneralHelper::loan_total_balance($loan->id) >= 0) {
            $loan->status = "disbursed";
            $loan->save();
        }

        event(new TransactionUpdated($loan_transaction));

        Flash::success("Interest waiver approved successfully.");
        return redirect()->back();
    }

    public function declineWaiver($id)
    {
        $waiver_transaction = WaiverTransactionUnapproved::find($id);

        if (!$waiver_transaction || $waiver_transaction->status !== 'pending') {
            Flash::warning("Invalid or already processed waiver.");
            return redirect()->back();
        }

        $waiver_transaction->status = 'declined';
        $waiver_transaction->save();

        Flash::success("Interest waiver declined successfully.");
        return redirect()->back();
    }

    /* public function store_charge(Request $request, $id)
     {
         if (!Sentinel::hasAccess('loans.charge.create')) {
             Flash::warning("Permission Denied");
             return redirect()->back();
         }
         $loan = Loan::find($id);
         $rules = array(
             'date' => 'required|after_or_equal:' . $loan->disbursement_date,
             'charge_id' => 'required',
             'amount' => 'required',
         );
         $messages = [
             'date.required' => 'Date is required',
             'payment_type_id.required' => 'Payment type is required',
             'amount.required' => 'Amount is required',
             'date.after_or_equal' => 'Date must not be after disbursement date',
         ];
         $charge = Charge::find($request->charge_id);
         $validator = Validator::make($request->all(), $rules, $messages);
         if ($validator->fails()) {
             return redirect()->back()->withInput()->withErrors($validator);
         } else {
             $due_date = GeneralHelper::determine_due_date($loan->id, $request->date);
             if (!empty($due_date)) {
                 $amount = $request->amount;
                 $schedule = LoanRepaymentSchedule::where("due_date", $due_date)->where("loan_id", $loan->id)->first();
                 $schedule->fees = $schedule->fees + $amount;
                 $schedule->save();
                 //fees  transaction
                 $loan_transaction = new LoanTransaction();
                 $loan_transaction->created_by_id = Sentinel::getUser()->id;
                 $loan_transaction->office_id = $loan->office_id;
                 $loan_transaction->loan_id = $loan->id;
                 $loan_transaction->reversible = 1;
                 $loan_transaction->loan_repayment_schedule_id = $schedule->id;
                 $loan_transaction->transaction_type = "specified_due_date_fee";
                 $loan_transaction->date = $due_date;
                 $date = explode('-', $due_date);
                 $loan_transaction->year = $date[0];
                 $loan_transaction->month = $date[1];
                 $loan_transaction->debit = $amount;
                 $loan_transaction->notes = $request->notes;
                 $loan_transaction->save();
                 event(new TransactionUpdated($loan_transaction));
                 if (GeneralHelper::loan_total_balance($loan->id) <= 0) {
                     $loan = Loan::find($loan->id);
                     $loan->status = "closed";
                     $loan->save();
                 }
             }


             GeneralHelper::audit_trail("Create Charge", "Loans", $id);
             Flash::success(trans('general.successfully_saved'));
             return redirect()->back();
         }
    }*/

    //create temp charge
    public function store_charge(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.charge.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $loan = Loan::find($id);
        $rules = [
            'date' => 'required|after_or_equal:' . $loan->disbursement_date,
            'charge_id' => 'required',
            'amount' => 'required',
        ];
        $messages = [
            'date.required' => 'Date is required',
            'charge_id.required' => 'Charge type is required',
            'amount.required' => 'Amount is required',
            'date.after_or_equal' => 'Date must not be before disbursement date',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $temporary_charge = new ChargeTransactionUnapproved();
        $temporary_charge->created_by_id = Sentinel::getUser()->id;
        $temporary_charge->office_id = $loan->office_id;
        $temporary_charge->loan_id = $loan->id;
        $temporary_charge->transaction_type = "specified_due_date_fee";
        $temporary_charge->date = $request->date;
        $temporary_charge->debit = $request->amount;
        $temporary_charge->notes = $request->notes;
        $temporary_charge->status = 'pending';
        $temporary_charge->save();
        // Notifix::notifyBmAndRkForNewCharge($loan, $temporary_charge);
        GeneralHelper::audit_trail("Temporary Charge Created", "Loans", $id);
        Flash::success("Charge added to pending charges for approval.");
        return redirect()->back();
    }

    public function approveCharge($id)
    {
        $temporary_charge = ChargeTransactionUnapproved::find($id);

        if (!$temporary_charge || $temporary_charge->status !== 'pending') {
            Flash::warning("Invalid or already processed charge.");
            return redirect()->back();
        }

        $loan = $temporary_charge->loan;
        $due_date = GeneralHelper::determine_due_date($loan->id, $temporary_charge->date);
        $amount = $temporary_charge->debit;



        //add to actual loan
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = $temporary_charge->created_by_id;
        $loan_transaction->office_id = $temporary_charge->office_id;
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->transaction_type = "specified_due_date_fee";
        $loan_transaction->date = $due_date;
        // $loan_transaction->loan_repayment_schedule_id = $schedule->id;
        $loan_transaction->year = date('Y', strtotime($due_date));
        $loan_transaction->month = date('m', strtotime($due_date));
        $loan_transaction->debit = $amount;
        $loan_transaction->notes = $temporary_charge->notes;
        $loan_transaction->status = 'approved';
        $loan_transaction->save();

        $temporary_charge->delete();

        Flash::success("Charge approved and applied to the loan.");
        return redirect()->back();
    }

    public function showPendingCharges(Request $request)
    {
        $user = Sentinel::getUser();
        $office_id = $user->office_id;

        if ($user->hasAccess('groups.create')) {
            $pendingCharges = ChargeTransactionUnapproved::where('status', 'pending')
                ->where('transaction_type', 'specified_due_date_fee')
                ->get();
        } elseif ($user->hasAccess('offices')) {
            $pendingCharges = ChargeTransactionUnapproved::where('status', 'pending')
                ->where('transaction_type', 'specified_due_date_fee')
                ->where('office_id', $office_id)
                ->get();
        } else {
            $provinceId = $user->province_id;
            $provinceOffices = Office::where('province_id', $provinceId)->pluck('id');
            $pendingCharges = ChargeTransactionUnapproved::whereIn('office_id', $provinceOffices)
                ->where('status', 'pending')
                ->where('transaction_type', 'specified_due_date_fee')
                ->get();
        }

        return view('loan.charge_approvals', compact('pendingCharges'));
    }



    public function declineCharge($id)
    {

        $chargeTransaction = ChargeTransactionUnapproved::find($id);


        if (!$chargeTransaction || $chargeTransaction->status != 'pending') {
            Flash::warning("Invalid or already processed charge.");
            return redirect()->back();
        }

        $chargeTransaction->status = 'declined';
        $chargeTransaction->save();

        Flash::success("Charge declined successfully.");
        return redirect()->back();
    }


    /////////////////////////////////////////////////////////////26376387384949/////////////////
    public function reschedule_loan(Request $request, $id)
    {

        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $todaysDate = date('Y-m-d');

        if ($request->submitte_on_date < $todaysDate) {
            Flash::warning("Enter a date equal or greater than today's date");
            return redirect()->back();
        }

        $balance = \App\Helpers\GeneralHelper::new_loan_total_balance($id);
        $loan = Loan::find($id);

        $pending_transaction = LoanTransactionsPending::where('loan_id', $loan->id)->first();
        if (!empty($pending_transaction)) {
            Flash::warning("This loan already has a reloan pending!!");
            return redirect('loan/' . $loan->id . '/show');
        }else{

          if (Sentinel::getUser()->cycle_dates == null) {
                $cycle_end = 24;
            } else {
                $cycle_end = Sentinel::getUser()->cycle_dates->cycle_end_date;
            }

            $use = date('Y-m-');
              $todaysDate = date('Y-m-d');
              $targetDate = $use . $cycle_end;
              $targetDate = date('Y-m-d', strtotime($targetDate));
          if ($todaysDate < $targetDate) {
                $targetDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));
            }

            $next_cycle = date('Y-m-d', strtotime($targetDate . ' + 1 months'));


        $loan_transaction = new LoanTransactionsPending();
        $loan_transaction->created_by_id = Sentinel::getUser()->id;
        $loan_transaction->office_id = $loan->office_id;
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->balance_bf = $balance;
        $loan_transaction->transaction_type = "repayment";
        $loan_transaction->payment_apply_to = "reloan_payment";
        //   if($request->carry_over == 1){
        //     $loan->cycle_date = $targetDate;
        //   }else{
        //      $loan->cycle_date = $next_cycle;
        //   }
        $loan_transaction->date = $request->submitte_on_date;
        $date = explode('-', $request->submitte_on_date);
        $loan_transaction->year = $date[0];
        $loan_transaction->month = $date[1];
        $loan_transaction->credit = $request->paid;
        $loan_transaction->interest = $request->interest;
	    $loan_transaction->save();
             $client_id = $loan->client_id;
                $client = \App\Models\Client::find($client_id);
            Http::post('https://notifications.whencefinancesystem.com/emit', [
                'event' => 'loan.created',
                'data' => [
                    'created_by' => Sentinel::getUser()->first_name . ' ' . Sentinel::getUser()->last_name,
                    'office_id' => Sentinel::getUser()->office->id,
                    'client' => $client->first_name . ' ' . $client->last_name,
                    'amount' => $request->paid,
                    'type' => 'reloan_payment',
                    'loan' => $loan->toArray(),
                    'transaction' => $loan_transaction->toArray()
                ]
            ]);

            // Notify Branch Manager of pending reloan transaction approval
            // Notifix::notifyBmToApproveTransaction($loan, $client, $request->paid);

            GeneralHelper::audit_trail("Update Repayment", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('loan/' . $loan->id . '/show');
        }
    }


    public function new_reschedule_loan(Request $request, $id, $trans_id)
    {

        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $balance = \App\Helpers\GeneralHelper::new_loan_total_balance($id);

        $loan = Loan::find($id);
        $pending_transactions = LoanTransactionsPending::where('loan_id', $id)->get();
        $count = count($pending_transactions);
        $Trans = LoanTransactionsPending::find($trans_id);
        if (!$Trans) {
            Flash::warning("Pending transaction not found!!");
            return redirect('loan/reloan_approvals');
        }
        $existing_transaction = LoanTransaction::where('loan_id', $id)->where('date', $Trans->date)->where('credit', $Trans->credit)->where('transaction_type', '!=', 'interest_waiver')->first();
        if ($count > 1) {
            Flash::warning("This loan has more than one pending reloan!!");
            return redirect('loan/reloan_approvals');

        } else {


            if (!empty($existing_transaction)) {
                Flash::warning("This transaction has already been entered!!");
                return redirect('loan/reloan_approvals');

            } else {
                $new_repayment_date = date('Y-m-d', strtotime($loan->first_repayment_date . ' + 1 months'));
                $decimals = $loan->loan_product->decimals;
                $loan->status = "disbursed";
                $loan->disbursed_by_id = Sentinel::getUser()->id;
                $loan->disbursed_notes = $request->disbursed_notes;
                $loan->disbursement_date = $loan->disbursement_date;
                $loan->first_repayment_date = $new_repayment_date;//$Trans->date;//$request->next_repayment;
                $loan->expected_maturity_date = date_format(
                    date_add(
                        date_create($Trans->date),//$request->next_repayment),
                        date_interval_create_from_date_string($loan->loan_term . ' ' . $loan->loan_term_type)
                    ),
                    'Y-m-d'
                );
                $loan->save();
                //  $next_payment = $request->next_repayment;
                $next_payment = $Trans->date;
                $period = $loan->loan_term / $loan->repayment_frequency;
                for ($i = 0; $i < $period; $i++) {
                    $loan_repayment_schedule = new LoanRepaymentSchedule();
                    $loan_repayment_schedule->loan_id = $loan->id;
                    $loan_repayment_schedule->due_date = $next_payment;
                    $date = explode('-', $next_payment);
                    $loan_repayment_schedule->month = $date[1];
                    $loan_repayment_schedule->year = $date[0];
                    $loan_repayment_schedule->principal = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest;
                    $loan_repayment_schedule->interest = $Trans->interest;
                    $loan->expected_maturity_date = $next_payment;
                    //determine which method to use        
                    $loan_repayment_schedule->save();
                    $next_payment = date_format(
                        date_add(
                            date_create($next_payment),
                            date_interval_create_from_date_string($loan->repayment_frequency . ' ' . $loan->repayment_frequency_type)
                        ),
                        'Y-m-d'
                    );
                }

                $loan->save();

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->balance_bf = $balance;
                $loan_transaction->transaction_type = "repayment";
                $loan_transaction->payment_apply_to = "reloan_payment";
                $loan_transaction->date = $Trans->date;
                $date = explode('-', $Trans->date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->credit = $Trans->credit;//$request->paid;
                $loan_transaction->temp_id = $trans_id;
                $loan_transaction->save();
                event(new RepaymentCreated($loan_transaction));
                if (GeneralHelper::loan_total_balance($loan->id) <= 0) {
                    $loan = Loan::find($loan->id);
                    $loan->status = "closed";
                    $loan->save();
                }

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Sentinel::getUser()->id;
                $loan_transaction->office_id = $loan->office_id;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->transaction_type = "interest";
                $loan_transaction->date = $Trans->date;
                $date = explode('-', $Trans->date);
                $loan_transaction->year = $date[0];
                $loan_transaction->month = $date[1];
                $loan_transaction->debit = $Trans->interest;
                $loan_transaction->save();


                // $target_tracker = TargetTracker::where('status','active')->where('user_id',$loan->loan_officer_id)->first();
                // $target_tracker->given_out = $target_tracker->given_out + $balance;
                // $target_tracker->save();

                LoanTransactionsPending::where('id', $trans_id)->delete();

                // Notify Loan Officer that reloan request has been approved
                $client = \App\Models\Client::find($loan->client_id);
                // Notifix::notifyLoanOfficerReloanApproved($loan, $client);

                GeneralHelper::audit_trail("Update Repayment", "Loans", $id);
                Flash::success(trans('general.successfully_saved'));
                return redirect('loan/' . $loan->id . '/show');
            }
        }
    }

    public function delete_pending_transactions(Request $request, $trans_id)
    {
        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }


//        $target_tracker = TargetTracker::where('status','active')->where('user_id',$loan->loan_officer_id)->first();
  //      $target_tracker->given_out = $target_tracker->given_out + $balance;
    //    $target_tracker->save();

	LoanTransactionsPending::where('id', $trans_id)->delete();
	   return redirect('loan/reloan_approvals');

    }


    public function delete_pending_transactions_fp_pp(Request $request, $trans_id)
    {
        if (!Sentinel::hasAccess('loans.transactions.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $trans = LoanTransactionUnapproved::find($trans_id);
        if ($trans) {
            $loan = Loan::find($trans->loan_id);
            if ($loan) {
                $client = \App\Models\Client::find($loan->client_id);
                // Notifix::notifyLoanOfficerTransactionDeclined($loan, $client, $trans->payment_apply_to);
            }
        }

        LoanTransactionUnapproved::where('id', $trans_id)->delete();
        return redirect('loan/transaction_approvals');

    }













    public function print_statement($loan)
    {
        // if (!Sentinel::hasAccess('loans.pdf_schedule')) {
        //     Flash::warning(trans('general.permission_denied'));
        //     return redirect()->back();
        // }

        return view('loan.print_statement', compact('loan'));
    }












    //write off loan
    public function write_off_loan(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.write_off')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'written_off_notes' => 'required',
            'written_off_date' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan = Loan::find($id);
            if ($loan->status != "disbursed") {
                Flash::warning("Loan not disbursed");
                return redirect()->back();
            }
            $loan->status = "written_off";
            $loan->written_off_by_id = Sentinel::getUser()->id;
            $loan->written_off_date = date("Y-m-d");
            $loan->written_off_notes = $request->withdrawn_notes;
            $loan->save();
            $loan_allocation = GeneralHelper::loan_items($loan->id);
            $principal = $loan_allocation["principal"] - $loan_allocation["principal_paid"] - $loan_allocation["principal_waived"] - $loan_allocation["principal_written_off"];
            $interest = $loan_allocation["interest"] - $loan_allocation["interest_paid"] - $loan_allocation["interest_waived"] - $loan_allocation["interest_written_off"];
            $fees = $loan_allocation["fees"] - $loan_allocation["fees_paid"] - $loan_allocation["fees_waived"] - $loan_allocation["fees_written_off"];
            $penalty = $loan_allocation["penalty"] - $loan_allocation["penalty_paid"] - $loan_allocation["penalty_waived"] - $loan_allocation["penalty_written_off"];
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Sentinel::getUser()->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->reversible = 0;
            $loan_transaction->transaction_type = "write_off";
            $loan_transaction->date = $request->written_off_date;
            $date = explode('-', $request->written_off_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $principal + $interest + $penalty + $fees;
            $loan_transaction->notes = $request->notes;
            $loan_transaction->save();
            //update journals
            $loan_product = $loan->loan_product;
            if ($loan_product->accounting_rule != "none") {
                if ($principal > 0) {
                    if (!empty($loan_product->gl_account_loan_portfolio)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_loan_portfolio->id;
                        $journal->date = $request->written_off_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'write_off';
                        $journal->name = "Principal Written Off";
                        $journal->loan_id = $loan->id;
                        $journal->credit = $principal;
                        $journal->reference = $loan->name;
                        $journal->loan_transaction_id = $loan_transaction->id;
                        $journal->save();
                    }
                    if (!empty($loan_product->gl_account_loans_written_off)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_loans_written_off->id;
                        $journal->date = $request->written_off_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'write_off';
                        $journal->name = "Loan Written Off";
                        $journal->loan_id = $loan->id;
                        $journal->debit = $principal;
                        $journal->reference = $loan->name;
                        $journal->loan_transaction_id = $loan_transaction->id;
                        $journal->save();
                    }
                }
            }
            GeneralHelper::audit_trail("Writeoff Loan", "Loans", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    //loan calculator
    public function create_calculator()
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('loan_calculator.create');
    }

    public function create_calculator_page($loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view(
            'loan_calculator.create_page',
            compact('loan_product')
        );
    }

    public function create_calculator_show(Request $request, $loan_product)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view(
            'loan_calculator.show',
            compact('loan_product', 'request')
        );
    }

    //loan applications
    public function index_application()
    {
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = LoanApplication::get();

        return view('loan.application.data', compact('data'));
    }

    //LIVE SYSTEM LINE 87
    public function my_applications()
    {
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $loan_officer_id = Sentinel::getUser()->id;
        $data = LoanApplication::where('staff_id', $loan_officer_id)->where('status', 'pending')->get();
        return view('loan.application.my_data', compact('data'));
    }

    public function show_application($loan_application)
    {
        if (!Sentinel::hasAccess('loans.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }


        return view('loan.application.show', compact('loan_application'));
    }

    public function decline_application(Request $request, $id)
    {
        if (!Sentinel::hasAccess('loans.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'declined_notes' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $loan_application = LoanApplication::find($id);
            if ($loan_application->status != "pending") {
                Flash::warning("Loan application not pending");
                return redirect()->back();
            }
            $loan_application->status = "declined";
            $loan_application->declined_by_id = Sentinel::getUser()->id;
            $loan_application->declined_date = date("Y-m-d");
            $loan_application->declined_notes = $request->declined_notes;
            $loan_application->save();
            GeneralHelper::audit_trail("Decline", "Loan Application", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }



    public function approve_application(Request $request, $id)
    {
        $loan_application = LoanApplication::find($id);
        $client_loan = Loan::where('client_id', '=', $loan_application->client_id)->where('loan_product_id', '=', $loan_application->loan_product_id)->where('status', '!=', 'closed')->where('status', '!=', 'declined')->first();

        if ($client_loan) {
            Flash::warning('This client already has a loan');
            return redirect()->back();
        } else {

            if (!Sentinel::hasAccess('loans.create')) {
                Flash::warning("Permission Denied");
                return redirect()->back();
            }
            $rules = array(
                'approved_date' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                if ($loan_application->status != "pending") {
                    Flash::warning("Loan application not pending");
                    return redirect()->back();
                }
                $loan_application->status = "approved";
                $loan_application->approved_by_id = Sentinel::getUser()->id;
                $loan_application->approved_date = $request->approved_date;
                $loan_application->approved_notes = $request->approved_notes;
                $loan_application->save();
                $loan_product = $loan_application->loan_product;
                //create loan
                $loan = new Loan();
                // $name = $loan->client->firstname;
                //   $name_array = explode(' ',trim($name));

                //   $firstWord = $name_array[0];
                // $lastWord = $name_array[count($name_array)-1];

                $loan->account_number = $loan->id;
                $loan->created_by_id = Sentinel::getUser()->id;
                $loan->created_date = $request->approved_date;
                $loan->client_type = $loan_application->client_type;
                $loan->loan_product_id = $loan_product->id;
                $loan->loan_officer_id = $loan_application->staff_id;
                $loan->group_id = $loan_application->group_id;
                $loan->client_id = $loan_application->client_id;
                $loan->office_id = $loan_application->office_id;
                $loan->decimals = $loan_product->decimals;
                $loan->loan_purpose_id = $loan_application->loan_purpose_id;
                $loan->principal = $loan_application->amount;
                $loan->applied_amount = $loan_application->amount;
                $loan->currency_id = $loan_product->currency_id;
                $loan->loan_term = $loan_product->default_loan_term;
                $loan->loan_term_type = $loan_product->repayment_frequency_type;
                $loan->repayment_frequency = $loan_product->repayment_frequency;
                $loan->repayment_frequency_type = $loan_product->repayment_frequency_type;
                $loan->interest_rate = $loan_product->default_interest_rate;
                $loan->interest_rate_type = $loan_product->interest_rate_type;
                $loan->interest_method = $loan_product->interest_method;
                $loan->armotization_method = $loan_product->armotization_method;
                $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
                $loan->grace_on_principal = $loan_product->grace_on_principal;
                $loan->grace_on_interest_payment = $loan_product->grace_on_interest_payment;
                $date = explode('-', $request->approved_date);
                $loan->month = $date[1];
                $loan->year = $date[0];



                $loan->save();
                $loan_application->loan_id = $loan->id;
                $loan_application->save();
                GeneralHelper::audit_trail("Approve", "Loan Application", $id);
                Flash::success(trans('general.successfully_saved'));
                return redirect('loan/' . $loan->id . '/edit');
            }
        }

    }
}
