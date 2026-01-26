<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use App\Helpers\GeneralHelper;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\Permission;
use App\Models\Repair;
use App\Models\Setting;
use App\Models\Leave;
use App\Models\Loan;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Client;
use App\Models\Policy;
use App\Models\UserPolicyResponse;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Roles\EloquentRole;
use Cartalyst\Sentinel\Roles\RoleInterface;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\CycleDates;
use App\Models\LoanTransaction;
use App\Models\Office;
use App\Models\UserRole;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Psy\CodeCleaner\FunctionContextPass;
use App\Models\AppraisalForm;
use App\Models\AppraisalFormSection;
use App\Models\AppraisalQuestion;
use App\Models\AppraisalAnswer;
use App\Models\TargetTracker;
use stdClass;

class UserController extends Controller
{
    public function __construct()
    {

        $this->middleware('sentinel');
    }

    // Renders on dashboard
    public function dashboard(Request $request)
    {

        $role = Sentinel::getUser()->roles->first();


        $userId = Sentinel::getUser()->id;
        //BELOW THIS
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;

        if (Sentinel::getUser()->cycle_dates == null) {
            $cycle_end = 24;
        } else {
            $cycle_end = Sentinel::getUser()->cycle_dates->cycle_end_date;
        }


        if ($role->role_id != '2') {
            $userProvince = Sentinel::getUser()->province_id;
        }

        if ($role->role_id == '2') {
            $user = Sentinel::getUser();
            $client = Client::where('user_id', $user->id)->first();
            $clientBranch = Office::where('id', $client->office_id)->first();
            $staff = Sentinel::findUserById($client->staff_id);
            $clientLoan = Loan::with('transactions')->where('status', 'disbursed')->where('client_id', $client->id)->first();

        }
        if ($role->role_id == '2') {
            $userProvince = '2';
        }
        //$branch = Office::with('province')->where('id',$userBranch)->get
        $province_loans = [];
        $province_transactions = [];
        if ($role->role_id != '2') {
            $province_branches = Office::where('province_id', $userProvince)->get();
        }
        $provinces = Province::get();
        $todaysDate = date('Y-m-d');
        $use = date('Y-m-');
        $myTransactions = [];
        $branchTransactions = [];
        $branchUserLoans = [];
        $myOpenTransactions = [];
        $myOpenLoans = [];
        $allLoans = [];
        $allTransactions = [];
        $afterDate = date('Y-m-d', strtotime($todaysDate . ' - 3 months'));
        $myLoans = null;
        $newBranchLoans = null;
        $someData = [];

        if ($role->role_id == '1') {


               try {
        $endpoint = "https://lms2backend.whencefinancesystem.com/targets-met";

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // don’t slow dashboard
        curl_exec($ch);
        curl_close($ch);
    } catch (\Exception $e) {
        // Fail silently – dashboard must still load
    }

            $allLoans = Loan::with('transactions')->where('created_date', '>', $afterDate)->get();
            foreach ($allLoans as $loans) {
                foreach ($loans->transactions as $transaction) {
                    array_push($allTransactions, $transaction);
                }
            }

            $data = [];
            $start = null;
            $end = null;

        }



        if (Sentinel::getUser()->cycle_dates == null) {
            $end = 'NCI';
        } else {
            if (Sentinel::getUser()->cycle_dates->cycle_end_date < 10) {
                $end = '0' . Sentinel::getUser()->cycle_dates->cycle_end_date;
            } else {
                $end = Sentinel::getUser()->cycle_dates->cycle_end_date;
            }
        }

        $targetDate = $use . $end;
        $targetDate = date('Y-m-d', strtotime($targetDate));
        if ($todaysDate >= $targetDate) {
            $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 months'));
        }
        $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));


        if ($role->role_id == '3') {

            if (Sentinel::getUser()->cycle_dates == null) {
                $cycle_end = 24;
            } else {
                $cycle_end = Sentinel::getUser()->cycle_dates->cycle_end_date;
            }


            $today = date('Y-m-d');
            $currrent_date = date('Y-m');
            $cycle_date = $currrent_date . '-' . $cycle_end;
            $cycle_date = date('Y-m-d', strtotime($cycle_date));
            $cycle_date = date('Y-m-d', strtotime($cycle_date . ' + 1 day'));

            if($today < $cycle_date){
                $cycle_date = date('Y-m-d', strtotime($cycle_date . ' - 1 months'));
            }

             $cycle_close_date = date('Y-m-d', strtotime($cycle_date . ' + 1 months'));


            $target_tracker = TargetTracker::where('status', 'active')->where('user_id', Sentinel::getUser()->id)->first();
            if ($target_tracker == null) {
                $new_target_tracker = new TargetTracker();
                $new_target_tracker->user_id = Sentinel::getUser()->id;
                $new_target_tracker->given_out = 0;
                $new_target_tracker->brought_f = 0;
                $new_target_tracker->target = 1;
                // if ($today > $cycle_date) {
                //     $cycle_date = date('Y-m-d', strtotime($cycle_date . '+ 1 months'));
                // }
                $new_target_tracker->cycle_date = $cycle_date;
                $new_target_tracker->status = 'active';
                $new_target_tracker->save();
            } else {

                if ($cycle_date != $target_tracker->cycle_date) {

                    $target_tracker->status = 'closed';
                    $target_tracker->save();

                    $new_target_tracker = new TargetTracker();
                    $new_target_tracker->user_id = Sentinel::getUser()->id;
                    $new_target_tracker->given_out = 0;
                    $new_target_tracker->brought_f = $target_tracker->given_out;
                    $new_target_tracker->target = $target_tracker->target;
                    $new_target_tracker->cycle_date = $cycle_date;
                    $new_target_tracker->status = 'active';
                    $new_target_tracker->save();
                }


            }






            $myLoans = Loan::with('transactions')->where('loan_officer_id', $userId)->get();
            foreach ($myLoans as $myLoan) {
                foreach ($myLoan->transactions as $Transaction) {
                    array_push($myTransactions, $Transaction);
                }

                if ($myLoan->status != 'closed') {
                    array_push($myOpenLoans, $myLoan);
                }
            }




            $fixedDay = $cycle_end;
            $userId = Sentinel::getUser()->id;

            $cycle_date = date('Y-m', strtotime($cycle_date));
            $cycle_close_date = date('Y-m', strtotime($cycle_close_date));
            // Default dates
            $start = $request->input('start_month', $cycle_date) . "-$fixedDay";
            $end = $request->input('end_month', $cycle_close_date) . "-$fixedDay";


            $query = http_build_query([
                'user_id' => $userId,
                'start_date' => $start,
                'end_date' => $end,
            ]);


            $url = "https://lms2backend.whencefinancesystem.com/my-performance-new?$query";

            $json = @file_get_contents($url);
            $data = $json ? json_decode($json, true) : null;

        }


        if ($role->role_id == '4') {
            $newBranchLoans = Loan::with('transactions')->where('office_id', $userBranch)->get();
            foreach ($newBranchLoans as $branchLoan) {
                foreach ($branchLoan->transactions as $Transaction) {
                    array_push($branchTransactions, $Transaction);
                }
            }

            $data = [];
            $start = null;
            $end = null;
        }

        if ($role->role_id == '6') {
            foreach ($province_branches as $province_branch) {
                $branch_loans = Loan::with('transactions')->where('office_id', $province_branch->id)->get();
                foreach ($branch_loans as $loan) {
                    array_push($province_loans, $loan);
                    foreach ($loan->transactions as $transaction) {
                        array_push($province_transactions, $transaction);
                    }
                }
            }

            $data = [];
            $start = null;
            $end = null;
        }

        if ($role->role_id == '8') {
            $data = [];
            $start = null;
            $end = null;
        }




        $branchUsers = User::where('office_id', $userBranch)->with('loan')->with('role')->get();
        if ($role->role_id != '2') {
            return view('dashboard', compact('end', 'myLoans', 'role', 'branchUsers', 'userBranch', 'myTransactions', 'myOpenLoans', 'newBranchLoans', 'branchTransactions', 'userProvince', 'province_loans', 'province_transactions', 'province_branches', 'allLoans', 'allTransactions', 'provinces', 'cycle_end', 'userId', 'data', 'start', 'end'));
        } else {
            return view('dashboard', compact('role', 'user', 'client', 'clientBranch', 'staff', 'clientLoan'));
        }
    }


    public function detailed_dashboard()
    {

        $userId = Sentinel::getUser()->id;
        //BELOW THIS
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->province_id;
        $province_loans = [];
        $province_transactions = [];
        $provinces = Province::get();
        $todaysDate = date('Y-m-d');
        $newDate = date('Y-m-d', strtotime($todaysDate . '- 6 months'));
        $use = date('Y-m-');
        $myTransactions = [];
        $branchTransactions = [];
        $branchUserLoans = [];
        $myOpenTransactions = [];
        $myOpenLoans = [];
        $allLoans = [];
        $allTransactions = [];
        $afterDate = date('Y-m-d', strtotime($todaysDate . ' - 2 months'));
        $myLoans = null;
        $newBranchLoans = null;
        $someData = [];
        if ($role->role_id == '1') {

            $allLoans = Loan::with('transactions')->where('created_date', '>', $newDate)->get();//Loan::with('transactions')->get();
            foreach ($allLoans as $loans) {
                foreach ($loans->transactions as $transaction) {
                    array_push($allTransactions, $transaction);
                }
            }
        }
        return view('user.detailed_dashboard', compact('myLoans', 'role', 'userBranch', 'myTransactions', 'myOpenLoans', 'newBranchLoans', 'branchTransactions', 'userProvince', 'province_loans', 'province_transactions', 'allLoans', 'allTransactions', 'provinces', ));
    }


    public function performance_information()
    {
        $offices = Office::get();
        return view('user.performance_information', compact('offices'));
    }



    public function submit_appraisal(Request $request, $id)
    {
        $year = date('Y');
        $month = date('m');
        $peers = [];
        $managers = [];
        $dm_peers = [];
        $recoveries_reps = [];
        $recoveries_head = [];
        $manager_admin = [];
        $user = Sentinel::getUser()->id;
        $users = User::with('role')->where('office_id', '!=', null)->get();
        $userBranch = Sentinel::getUser()->office_id;
        $role = UserRole::where('user_id', $user)->first();
        $userProvince = Sentinel::getUser()->province_id;
        $province_branches = Office::where('province_id', $userProvince)->get();

        foreach ($province_branches as $province_branch) {
            foreach ($users as $person) {

                if ($person->role != null) {
                    if ($person->role->role_id == 6 && $person->office_id == $province_branch->id && $person->id != $user) {
                        array_push($managers, $person);
                    }

                    if ($person->role->role_id == 4 && $person->office_id == $userBranch && $person->id != $user) {
                        array_push($managers, $person);
                    }

                    if ($person->role->role_id == 4 && $person->office_id == $province_branch->id) {
                        array_push($dm_peers, $person);
                    }
                }

                if ($person->dual_role != null) {
                    if ($person->dual_role->role_id == 7 && $person->office_id == $userBranch && count($recoveries_reps) == 0) {
                        array_push($recoveries_reps, $person);
                    }
                }

                if ($person->dual_role != null) {
                    if ($person->dual_role->role_id == 8 && count($recoveries_head) == 0) {
                        array_push($recoveries_head, $person);
                    }
                }


                if ($person->dual_role != null) {
                    if ($person->dual_role->role_id == 9 && count($manager_admin) == 0) {
                        array_push($manager_admin, $person);
                    }
                }


            }
        }

        foreach ($users as $branch_person) {
            if ($branch_person->role != null) {
                if ($branch_person->role->role_id == $role->role_id && $branch_person->id != $user && $branch_person->office_id == $userBranch) {
                    array_push($peers, $branch_person);
                }
            }
        }
        $questions = AppraisalQuestion::where('form_id', $id)->get();
        foreach ($questions as $question) {
            if ($question->unit == 'p_r') {
                foreach ($peers as $peer) {
                    $key = array_search('{{$peer}}', $peers);
                    $appraisal_answer = new AppraisalAnswer();
                    $item = $peer->id . $question->id;
                    $appraisal_answer->question_id = $question->id;
                    $appraisal_answer->section_id = $question->section_id;
                    $appraisal_answer->form_id = $question->form_id;
                    $appraisal_answer->unit = $question->unit;
                    $appraisal_answer->quater_date = $month . '-' . $year;
                    $appraisal_answer->answer = $request->$item;
                    $appraisal_answer->user_id = $peer->id;
                    $appraisal_answer->save();
                }
            } elseif ($question->unit == 'sb_r') {
                foreach ($managers as $peer) {
                    $key = array_search('{{$peer}}', $managers);
                    $appraisal_answer = new AppraisalAnswer();
                    $item = $peer->id . $question->id;
                    $appraisal_answer->question_id = $question->id;
                    $appraisal_answer->section_id = $question->section_id;
                    $appraisal_answer->form_id = $question->form_id;
                    $appraisal_answer->unit = $question->unit;
                    $appraisal_answer->quater_date = $month . '-' . $year;
                    $appraisal_answer->answer = $request->$item;
                    $appraisal_answer->user_id = $peer->id;
                    $appraisal_answer->save();
                }
            } elseif ($question->unit == 'p_r_dm') {
                foreach ($dm_peers as $peer) {
                    $key = array_search('{{$peer}}', $dm_peers);
                    $appraisal_answer = new AppraisalAnswer();
                    $item = $peer->id . $question->id;
                    $appraisal_answer->question_id = $question->id;
                    $appraisal_answer->section_id = $question->section_id;
                    $appraisal_answer->form_id = $question->form_id;
                    $appraisal_answer->unit = $question->unit;
                    $appraisal_answer->quater_date = $month . '-' . $year;
                    $appraisal_answer->answer = $request->$item;
                    $appraisal_answer->user_id = $peer->id;
                    $appraisal_answer->save();
                }
            } elseif ($question->unit == 'rr_r') {
                foreach ($recoveries_reps as $peer) {
                    $key = array_search('{{$peer}}', $recoveries_reps);
                    $appraisal_answer = new AppraisalAnswer();
                    $item = $peer->id . $question->id;
                    $appraisal_answer->question_id = $question->id;
                    $appraisal_answer->section_id = $question->section_id;
                    $appraisal_answer->form_id = $question->form_id;
                    $appraisal_answer->unit = $question->unit;
                    $appraisal_answer->quater_date = $month . '-' . $year;
                    $appraisal_answer->answer = $request->$item;
                    $appraisal_answer->user_id = $peer->id;
                    $appraisal_answer->save();
                }
            } elseif ($question->unit == 'ma_r') {
                foreach ($manager_admin as $peer) {
                    $key = array_search('{{$peer}}', $manager_admin);
                    $appraisal_answer = new AppraisalAnswer();
                    $item = $peer->id . $question->id;
                    $appraisal_answer->question_id = $question->id;
                    $appraisal_answer->section_id = $question->section_id;
                    $appraisal_answer->form_id = $question->form_id;
                    $appraisal_answer->unit = $question->unit;
                    $appraisal_answer->quater_date = $month . '-' . $year;
                    $appraisal_answer->answer = $request->$item;
                    $appraisal_answer->user_id = $peer->id;
                    $appraisal_answer->save();
                }
            } elseif ($question->unit == 'rh_r') {
                foreach ($recoveries_head as $peer) {
                    $key = array_search('{{$peer}}', $recoveries_head);
                    $appraisal_answer = new AppraisalAnswer();
                    $item = $peer->id . $question->id;
                    $appraisal_answer->question_id = $question->id;
                    $appraisal_answer->section_id = $question->section_id;
                    $appraisal_answer->form_id = $question->form_id;
                    $appraisal_answer->unit = $question->unit;
                    $appraisal_answer->quater_date = $month . '-' . $year;
                    $appraisal_answer->answer = $request->$item;
                    $appraisal_answer->user_id = $peer->id;
                    $appraisal_answer->save();
                }
            } else {
                if ($question->unit != 'info') {
                    $appraisal_answer = new AppraisalAnswer();
                    $item = $question->id;
                    $appraisal_answer->question_id = $question->id;
                    $appraisal_answer->section_id = $question->section_id;
                    $appraisal_answer->form_id = $question->form_id;
                    $appraisal_answer->unit = $question->unit;
                    $appraisal_answer->quater_date = $month . '-' . $year;
                    if ($request->$item == 'Other') {
                        $appraisal_answer->answer = $request->$item . 'other';
                    } else {
                        $appraisal_answer->answer = $request->$item;
                    }
                    $appraisal_answer->user_id = $user;
                    $appraisal_answer->save();
                }
            }
        }

        Flash::success(trans('general.successfully_saved'));
        return redirect('user/' . $id . '/my_appraisal')->with('message', 'Success');
    }


       public function branch_deposits(Request $request){
        $office_id = Sentinel::getUser()->office->id;
        return view('user.branch_deposits',compact('office_id'));
    }


    public function provinces_dashboard(Request $request)
    {

        $role = Sentinel::getUser()->roles->first();

        if ($role->id == 3 && Sentinel::getUser()->office->id != 41) {
            $answer = AppraisalAnswer::where('user_id', Sentinel::getUser()->id)->where('form_id', 1)->where('question_id', 3)->where('quater_date', '>=', '06-2025')->first();
            if (empty($answer)) {
                return redirect('user/my_appraisal_forms');
            }
        } elseif ($role->id == 4) {
            $answer = AppraisalAnswer::where('user_id', Sentinel::getUser()->id)->where('form_id', 2)->where('question_id', 44)->where('quater_date', '>=', '06-2025')->first();
            if (empty($answer)) {
                return redirect('user/my_appraisal_forms');
            }
        }

        $lc_loans = [];
        $target_date = null;
        $compare_date = null;
        $userId = Sentinel::getUser()->id;
        $year = date('Y');
        $month = date('m');
        $answer = AppraisalAnswer::where('user_id', $userId)->where('quater_date', $month . '-' . $year)->first();
        //BELOW THIS
        $role = UserRole::where('user_id', $userId)->first();
        $userBranch = Sentinel::getUser()->office_id;

        $userProvince = Sentinel::getUser()->province_id;

        if (Sentinel::getUser()->cycle_dates == null) {
            $cycle_end = 24;
        } else {
            $cycle_end = Sentinel::getUser()->cycle_dates->cycle_end_date;
        }

        if ($role->role_id == '2') {
            $user = Sentinel::getUser();
            $client = Client::where('user_id', $user->id)->first();
            $clientBranch = Office::where('id', $client->office_id)->first();
            $staff = Sentinel::findUserById($client->staff_id);
            $clientLoan = Loan::with('transactions')->where('status', 'disbursed')->where('client_id', $client->id)->first();

        }
        if ($role->role_id == '2') {
            $userProvince = '2';
        }
        //$branch = Office::with('province')->where('id',$userBranch)->get
        $province_loans = [];
        $province_transactions = [];
        if ($role->role_id != '2') {
            $province_branches = Office::where('province_id', $userProvince)->get();
        }
        $provinces = Province::get();
        $todaysDate = date('Y-m-d');
        $use = date('Y-m-');
        $myTransactions = [];
        $branchTransactions = [];
        $branchUserLoans = [];
        $myOpenTransactions = [];
        $myOpenLoans = [];
        $allLoans = [];
        $allTransactions = [];
        $afterDate = date('Y-m-d', strtotime($todaysDate . ' - 3 months'));
        $myLoans = null;
        $newBranchLoans = null;
        $someData = [];
        $testloans = [];
        $afterDate = date('Y-m-d', strtotime($todaysDate . ' - 2 months'));
        $myLoans = null;
        $newBranchLoans = null;
        $someData = [];

        if ($role->role_id == '1') {

            $allLoans = Loan::with('transactions')->where('created_date', '>', $afterDate)->get();
            foreach ($allLoans as $loans) {
                foreach ($loans->transactions as $transaction) {
                    array_push($allTransactions, $transaction);
                }
            }

        }





        if (Sentinel::getUser()->cycle_dates == null) {
            $end = 'NCI';
        } else {
            if (Sentinel::getUser()->cycle_dates->cycle_end_date < 10) {
                $end = '0' . Sentinel::getUser()->cycle_dates->cycle_end_date;
            } else {
                $end = Sentinel::getUser()->cycle_dates->cycle_end_date;
            }
        }

        $targetDate = $use . $end;
        $targetDate = date('Y-m-d', strtotime($targetDate));
        if ($todaysDate >= $targetDate) {
            $targetDate = date('Y-m-d', strtotime($targetDate . ' + 1 months'));
        }
        $compareDate = date('Y-m-d', strtotime($targetDate . ' - 1 months'));


        if ($role->role_id == '3') {


            $myTransactions = [];
            $use = date('Y-m-');

            if ($request->cycle == null) {
                $target_date = $use . $cycle_end;
            } else {
                $target_date = $request->cycle;
            }

            $compare_date = date('Y-m-d', strtotime($target_date . ' - 1 months'));


            $myLoans = Loan::with('transactions')->where('loan_officer_id', $userId)->get();
            foreach ($myLoans as $myLoan) {
                if ($myLoan->cycle_date <= $target_date && $myLoan->cycle_date > $compare_date) {
                    array_push($lc_loans, $myLoan);
                }
            }

        }


        if ($role->role_id == '4') {
            $newBranchLoans = Loan::with('transactions')->where('office_id', $userBranch)->get();
            foreach ($newBranchLoans as $branchLoan) {
                foreach ($branchLoan->transactions as $Transaction) {
                    array_push($branchTransactions, $Transaction);
                }
            }
        }

        if ($role->role_id == '6') {
            foreach ($province_branches as $province_branch) {
                $branch_loans = Loan::with('transactions')->where('office_id', $province_branch->id)->get();
                foreach ($branch_loans as $loan) {
                    array_push($province_loans, $loan);
                    foreach ($loan->transactions as $transaction) {
                        array_push($province_transactions, $transaction);
                    }
                }
            }
        }




        $branchUsers = User::where('office_id', $userBranch)->with('loan')->with('role')->get();
        if ($role->role_id != '2') {
            return view('provinces_dashboard', compact('lc_loans', 'target_date', 'compare_date', 'end', 'myLoans', 'role', 'branchUsers', 'userBranch', 'myTransactions', 'myOpenLoans', 'newBranchLoans', 'branchTransactions', 'userProvince', 'province_loans', 'province_transactions', 'province_branches', 'allLoans', 'allTransactions', 'provinces', 'answer', 'userId', 'cycle_end'));
        } else {
            return view('provinces_dashboard', compact('role', 'user', 'client', 'clientBranch', 'staff', 'clientLoan', ));
        }
    }


    public function my_appraisal($id)
    {
        $peers = [];
        $managers = [];
        $dm_peers = [];
        $recoveries_reps = [];
        $recoveries_head = [];
        $manager_admin = [];
        $year = date('Y');
        $month = date('m');
        $user = Sentinel::getUser()->id;
        $users = User::with('role')->get();
        $userBranch = Sentinel::getUser()->office_id;
        $userProvince = Sentinel::getUser()->province_id;
        $role = UserRole::where('user_id', $user)->first();
        $branch_people = User::where('office_id', $userBranch)->get();
        $users = User::with('role')->with('dual_role')->with('office')->where('status', 'Inactive')->get();
        $province_branches = Office::where('province_id', $userProvince)->get();

        foreach ($province_branches as $province_branch) {
            foreach ($users as $person) {
                if ($person->role != null) {
                    if ($person->role->role_id == 6 && $person->office_id == $province_branch->id && $person->id != $user) {
                        array_push($managers, $person);
                    }

                    if ($person->role->role_id == 4 && $person->office_id == $userBranch && $person->id != $user) {
                        array_push($managers, $person);
                    }

                    if ($person->role->role_id == 4 && $person->office_id == $province_branch->id) {
                        array_push($dm_peers, $person);
                    }
                }


                if ($person->dual_role != null) {
                    if ($person->dual_role->role_id == 7 && $person->office_id == $userBranch && count($recoveries_reps) == 0) {
                        array_push($recoveries_reps, $person);
                    }
                }


                if ($person->dual_role != null) {
                    if ($person->dual_role->role_id == 8 && count($recoveries_head) == 0) {
                        array_push($recoveries_head, $person);
                    }
                }


                if ($person->dual_role != null) {
                    if ($person->dual_role->role_id == 9 && count($manager_admin) == 0) {
                        array_push($manager_admin, $person);
                    }
                }
            }
        }


        foreach ($users as $branch_person) {
            if ($branch_person->role != null) {
                if ($branch_person->role->role_id == 3 && $branch_person->id != $user && $branch_person->status = 'Active' && $branch_person->office_id == $userBranch) {
                    array_push($peers, $branch_person);
                }
            }
        }
        $answer = AppraisalAnswer::where('user_id', $user)->where('form_id', $id)->where('quater_date', $month . '-' . $year)->first();
        $form = AppraisalForm::where('id', $id)->first();
        $sections = AppraisalFormSection::where('form_id', $id)->get();
        $questions = AppraisalQuestion::where('form_id', $id)->get();
        return view('user.my_appraisal', compact('form', 'sections', 'questions', 'users', 'answer', 'peers', 'branch_people', 'role', 'user', 'managers', 'dm_peers', 'recoveries_reps', 'recoveries_head', 'manager_admin'));
    }

    public function appraisal_forms()
    {
        $forms = AppraisalForm::get();
        return view('user.appraisal_forms', compact('forms'));
    }

    public function my_appraisal_forms()
    {
        $forms = [];
        $user_id = Sentinel::getUser();
        $form1 = AppraisalForm::where('role', $user_id->role->role_id)->first();
        if ($user_id->dual_role != null) {
            $form2 = AppraisalForm::where('role', $user_id->dual_role->role_id)->first();
            array_push($forms, $form2);
        }
        array_push($forms, $form1);
        return view('user.my_appraisal_forms', compact('forms'));
    }
    public function appraisal_results(Request $request)
    {
        $forms = [];
        $users = [];
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $office_id = $request->office_id;
        $userProvince = User::where('id', $userId)->first()->province_id;
        $userBranch = User::where('id', $userId)->first()->office->id;

        if ($office_id == 0) {
            $users = User::with('role')->where('office_id', '!=', null)->get();

        } else {
            if ($role->role_id == '3') {
                $users = User::with('role')->where('id', $userId)->where('office_id', $office_id)->get();
            } else {
                $users = User::with('role')->where('office_id', $office_id)->get();
            }
        }

        return view('user.appraisal_results', compact('users', 'role', 'office_id', 'userProvince', 'userBranch', ));
    }



    public function appraisal_result($id, $form_id)
    {
        $peers = [];
        $managers = [];
        $dm_peers = [];
        $recoveries_reps = [];
        $recoveries_head = [];
        $manager_admin = [];
        $user = User::with('role')->where('id', $id)->first();
        $user_id = Sentinel::getUser()->id;
        $userBranch = User::where('id', $id)->first()->office->id;
        $users = User::with('role')->where('office_id', '!=', null)->get();
        $answers = AppraisalAnswer::where('user_id', $id)->where('form_id', $form_id)->get();
        $form = AppraisalForm::where('id', $form_id)->first();
        $sections = AppraisalFormSection::where('form_id', $form_id)->get();
        $role = UserRole::where('user_id', $user_id)->first();
        $userProvince = User::where('id', $id)->first()->province_id;
        $province_branches = Office::where('province_id', $userProvince)->get();
        $pr_questions = AppraisalQuestion::where('unit', 'p_r')->get();
        $sbr_questions = AppraisalQuestion::where('unit', 'sb_r')->get();
        $pr_answers = [];
        $sbr_questions = [];



        foreach ($users as $branch_person) {
            if ($branch_person->role != null) {
                if ($branch_person->role->role_id == 3 && $branch_person->office_id == $userBranch) {
                    array_push($peers, $branch_person);
                }
            }
        }

        foreach ($province_branches as $province_branch) {
            foreach ($users as $person) {

                if ($person->role != null) {
                    if ($person->role->role_id == 6 && $person->office_id == $province_branch->id) {
                        array_push($managers, $person);
                    }

                    if ($person->role->role_id == 4 && $person->office_id == $userBranch && count($managers) < 1 && $person->id != $id) {
                        array_push($managers, $person);
                    }

                    if ($person->role->role_id == 4 && $person->office_id == $province_branch->id) {
                        array_push($dm_peers, $person);
                    }
                }

                if ($person->dual_role != null) {
                    if ($person->dual_role->role_id == 7 && $person->office_id == $userBranch && count($recoveries_reps) == 0) {
                        array_push($recoveries_reps, $person);
                    }
                }

                if ($person->dual_role != null) {
                    if ($person->dual_role->role_id == 8 && count($recoveries_head) == 0) {
                        array_push($recoveries_head, $person);
                    }
                }


                if ($person->dual_role != null) {
                    if ($person->dual_role->role_id == 9 && count($manager_admin) == 0) {
                        array_push($manager_admin, $person);
                    }
                }


            }
        }




        return view('user.appraisal_result', compact('answers', 'form', 'sections', 'user', 'peers', 'user_id', 'pr_answers', 'peers', 'managers', 'userProvince', 'recoveries_reps', 'recoveries_head', 'manager_admin'));
    }


    public function appraisal_form($id)
    {
        $form = AppraisalForm::where('id', $id)->first();
        $sections = AppraisalFormSection::where('form_id', $id)->get();
        $questions = AppraisalQuestion::where('form_id', $id)->get();
        return view('user.appraisal_form', compact('form', 'sections', 'questions'));
    }

    public function create_form(Request $request)
    {
        $appraisal_form = new AppraisalForm();
        $appraisal_form->form_name = $request->form_name;
        $appraisal_form->role = $request->role;
        $appraisal_form->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('user/appraisal_forms');
    }

    public function add_section(Request $request, $id)
    {
        $appraisal_form_section = new AppraisalFormSection();
        $appraisal_form_section->form_id = $id;
        $appraisal_form_section->section_name = $request->section_name;
        $appraisal_form_section->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('user/' . $id . '/appraisal_form');

    }


    public function add_question(Request $request, $id, $section_id)
    {
        $appraisal_question = new AppraisalQuestion();
        $appraisal_question->form_id = $section_id;
        $appraisal_question->section_id = $id;
        $appraisal_question->question = $request->question;
        $appraisal_question->unit = $request->unit;
        $appraisal_question->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect(('user/' . $section_id . '/appraisal_form'));

    }

    public function my_details()
    {
        $user = Sentinel::getUser();
        $client = Client::where('user_id', $user->id)->first();
        return view('user.my_details', compact('client'));
    }

    public function daily_figures()
    {
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id', $userId)->first();
        $allLoans = [];
        $todaysDate = date('Y-m-d');
        $allTransactions = [];
        $afterDate = date('Y-m-d', strtotime($todaysDate . ' - 12 months'));

        if ($role->role_id == '1') {

            $allLoans = Loan::with('transactions')->where('created_date', '>', $afterDate)->get();
            foreach ($allLoans as $loans) {
                foreach ($loans->transactions as $transaction) {
                    array_push($allTransactions, $transaction);
                }
            }

        }

        return view('user.daily_figures', compact('allLoans', 'allTransactions'));

    }


    public function province_page($id)
    {
        $province_loans = [];
        $province_transactions = [];
        $province_branches = Office::where('province_id', $id)->get();
        $province = Province::where('id', $id)->first();
        foreach ($province_branches as $province_branch) {
            $branch_loans = Loan::with('transactions')->where('office_id', $province_branch->id)->get();
            foreach ($branch_loans as $loan) {
                array_push($province_loans, $loan);
                foreach ($loan->transactions as $transaction) {
                    array_push($province_transactions, $transaction);
                }
            }
        }
        return view('user.province_page', compact('province_loans', 'province_transactions', 'province_branches', 'province', ));

    }

    public function branch_page($id)
    {
        $branchTransactions = [];
        $userBranch = $id;//Sentinel::getUser()->office_id;
        $office = Office::where('id', $id)->first();
        $newBranchLoans = Loan::with('transactions')->where('office_id', $userBranch)->get();
        $branchUsers = User::where('office_id', $userBranch)->with('loan')->with('role')->get();

        foreach ($newBranchLoans as $branchLoan) {
            foreach ($branchLoan->transactions as $Transaction) {
                array_push($branchTransactions, $Transaction);
            }
        }

        return view('user.branch_page', compact('newBranchLoans', 'branchTransactions', 'branchUsers', 'office', 'id', ));
    }


    public function user_info($user)
    {
        $userTransactions = [];
        $userLoans = Loan::with('transactions')->where('loan_officer_id', $user->id)->get();
        $cycleDate = CycleDates::where('loan_officer_id', $user->id)->first();
        foreach ($userLoans as $userLoan) {
            foreach ($userLoan->transactions as $Transaction) {
                array_push($userTransactions, $Transaction);
            }
        }
        $advances = Advance::where('user_id', $user->id)->get();
        $leave_days = Leave::where('user_id', $user->id)->get();
        return view('user.user_info', compact('user', 'userLoans', 'userTransactions', 'cycleDate', 'advances', 'leave_days'));
    }


    public function collections_stats(Request $request, $user, $collection_type)
    {
        $userTransactions = [];
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $userLoans = Loan::with('transactions')->where('loan_officer_id', $user->id)->get();
        foreach ($userLoans as $userLoan) {
            foreach ($userLoan->transactions as $Transaction) {
                array_push($userTransactions, $Transaction);
            }
        }
        //TODAY AND YESTERDAY'S DATES
        $todaysDate = date('Y-m-d');
        $yesterdaysDate = date('Y-m-d', strtotime($todaysDate . ' - 1 days'));

        //LC TARGET AND COMPARE DATE
        $use = date('Y-m-');
        $num = 1;
        $cycleDate = CycleDates::where('loan_officer_id', $user->id)->first();
        if ($cycleDate != null) {
            $LC_targetDate = $use . $cycleDate->cycle_end_date;
        } else {
            $LC_targetDate = $use . $num;
        }
        $LC_targetDate = date('Y-m-d', strtotime($LC_targetDate));
        if ($todaysDate > $LC_targetDate) {
            $LC_targetDate = date('Y-m-d', strtotime($LC_targetDate . ' + 1 months'));
        }
        $LC_compareDate = date('Y-m-d', strtotime($LC_targetDate . ' - 1 months'));

        //DATES SET BY THE USER
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if (empty($start_date && $end_date)) {
            if ($collection_type == 'collections_today') {
                $targetDate = $todaysDate;
                $compareDate = $yesterdaysDate;
            } elseif ($collection_type == 'collections_cycle') {
                $targetDate = $LC_targetDate;
                $compareDate = $LC_compareDate;
            } else {
                $targetDate = $end_date;
                $compareDate = $start_date;
            }
        }
        return view('user.collections_stats', compact('collection_type', 'targetDate', 'compareDate', 'userLoans', 'userTransactions', 'user', 'start_date', 'end_date'));
    }


    public function leaderboard(Request $request)
    {
        $data = [];
        $office = $request->office;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $todaysDate = date('Y-m-d');

        $userId = Sentinel::getUser()->id;
        $user = Sentinel::getUser();
        $userRole = UserRole::where('user_id', $userId)->first();

        if ($office == 0) {
            $query = User::with('role')->with('office');
            if ($userRole->role_id == 6) {
                $query->where('province_id', $user->province_id);
            }
            $LoanConsultants = $query->get();
        } else {
            $LoanConsultants = User::with('role')->with('office')->where('office_id', $office)->get();
        }

        $consultantIds = $LoanConsultants->pluck('id')->toArray();

        $sums = LoanTransaction::join('loans', 'loan_transactions.loan_id', '=', 'loans.id')
            ->whereBetween('loan_transactions.date', [$startDate, $endDate])
            ->whereIn('loans.loan_officer_id', $consultantIds)
            ->selectRaw('loans.loan_officer_id,
                SUM(CASE WHEN loan_transactions.transaction_type = "repayment" AND loan_transactions.payment_apply_to = "full_payment" THEN loan_transactions.credit ELSE 0 END) as full_payment_total,
                SUM(CASE WHEN loan_transactions.payment_apply_to = "part_payment" THEN loan_transactions.credit ELSE 0 END) as part_payment_total,
                SUM(CASE WHEN loan_transactions.payment_apply_to = "reloan_payment" THEN loan_transactions.credit ELSE 0 END) as reloan_payments_total')
            ->groupBy('loans.loan_officer_id')
            ->get();

        $sumMap = $sums->keyBy('loan_officer_id');

        foreach ($LoanConsultants as $loanConsultant) {
            if (!empty($loanConsultant->role->role_id)) {
                if ($loanConsultant->role->role_id !== 2) {
                    $object = new stdClass();
                    $sum = $sumMap->get($loanConsultant->id);
                    $full_payment_total = $sum ? $sum->full_payment_total : 0;
                    $part_payment_total = $sum ? $sum->part_payment_total : 0;
                    $reloan_payments_total = $sum ? $sum->reloan_payments_total : 0;

                    $object->first_name = $loanConsultant->first_name;
                    $object->last_name = $loanConsultant->last_name;
                    $object->amount = $full_payment_total + $part_payment_total + $reloan_payments_total;
                    $object->role = $loanConsultant->role;
                    if (!empty($loanConsultant->office)) {
                        $object->office = $loanConsultant->office->name;
                    } else {
                        $object->office = 'no branch';
                    }

                    array_push($data, $object);
                }
            }
        }

        return view('user.leaderboard', compact('office', 'LoanConsultants', 'data', 'startDate', 'endDate'));
    }

    public function leaderboardBKP(Request $request)
    {
        $data = [];
        $office = $request->office;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $todaysDate = date('Y-m-d');
        $transactions = LoanTransaction::with('loan')->whereBetween('date', [$startDate, $endDate])->get();

        if ($office == 0) {
            $LoanConsultants = User::with('role')->with('office')->get();
        } else {
            $LoanConsultants = User::with('role')->with('office')->where('office_id', $office)->get();
        }


        foreach ($LoanConsultants as $loanConsultant) {
            if (!empty($loanConsultant->role->role_id)) {
                if ($loanConsultant->role->role_id !== 2) {
                    $object = new stdClass();
                    $full_payment_total = 0;
                    $part_payment_total = 0;
                    $reloan_payments_total = 0;
                    $charge = 0;


                    foreach ($transactions as $transaction) {
                        if (!empty($transaction->loan->loan_officer_id)) {

                            if ($transaction->loan->loan_officer_id == $loanConsultant->id) {
                                if ($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment') {
                                    $full_payment_total = $full_payment_total + $transaction->credit;
                                }

                                if ($transaction->payment_apply_to == 'part_payment') {
                                    $part_payment_total = $part_payment_total + $transaction->credit;
                                }

                                if ($transaction->payment_apply_to == 'reloan_payment') {
                                    $reloan_payments_total = $reloan_payments_total + $transaction->credit;
                                }
                            }
                        }
                    }
                    $object->first_name = $loanConsultant->first_name;
                    $object->last_name = $loanConsultant->last_name;
                    $object->amount = $full_payment_total + $part_payment_total + $reloan_payments_total;
                    $object->role = $loanConsultant->role;
                    if (!empty($loanConsultant->office)) {
                        $object->office = $loanConsultant->office->name;
                    } else {
                        $object->office = 'no branch';
                    }
                    array_push($data, $object);
                }
            }
        }

        return view('user.leaderboard', compact('office', 'LoanConsultants', 'data', 'startDate', 'endDate'));
    }


    public function given_out_stats(Request $request, $user, $given_out_type)
    {
        $userTransactions = [];
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $userLoans = Loan::with('transactions')->where('loan_officer_id', $user->id)->get();
        foreach ($userLoans as $userLoan) {
            foreach ($userLoan->transactions as $Transaction) {
                array_push($userTransactions, $Transaction);
            }
        }
        //TODAY AND YESTERDAY'S DATES
        $todaysDate = date('Y-m-d');
        $yesterdaysDate = date('Y-m-d', strtotime($todaysDate . ' - 1 days'));

        //LC TARGET AND COMPARE DATE
        $use = date('Y-m-');
        $num = 1;
        $cycleDate = CycleDates::where('loan_officer_id', $user->id)->first();
        if ($cycleDate != null) {
            $LC_targetDate = $use . $cycleDate->cycle_end_date;
        } else {
            $LC_targetDate = $use . $num;
        }
        $LC_targetDate = date('Y-m-d', strtotime($LC_targetDate));
        if ($todaysDate > $LC_targetDate) {
            $LC_targetDate = date('Y-m-d', strtotime($LC_targetDate . ' + 1 months'));
        }
        $LC_compareDate = date('Y-m-d', strtotime($LC_targetDate . ' - 1 months'));

        //DATES SET BY THE USER
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if (empty($start_date && $end_date)) {
            if ($given_out_type == 'given_out_today') {
                $targetDate = $todaysDate;
                $compareDate = $yesterdaysDate;
            } elseif ($given_out_type == 'given_out_cycle') {
                $targetDate = $LC_targetDate;
                $compareDate = $LC_compareDate;
            } else {
                $targetDate = $end_date;
                $compareDate = $start_date;
            }
        }



        return view('user.given_out_stats', compact('given_out_type', 'targetDate', 'compareDate', 'userLoans', 'userTransactions', 'user', 'start_date', 'end_date'));
    }


    public function uncollected_stats(Request $request, $user, $uncollected_type)
    {
        $userTransactions = [];

        $userLoans = Loan::with('transactions')->where('loan_officer_id', $user->id)->get();
        $targetDate = $request->end_date;

        //TODAY AND YESTERDAY'S DATES
        $todaysDate = date('Y-m-d');
        $yesterdaysDate = date('Y-m-d', strtotime($todaysDate . ' - 1 days'));
        foreach ($userLoans as $userLoan) {
            foreach ($userLoan->transactions as $Transaction) {
                array_push($userTransactions, $Transaction);
            }
        }

        //LC TARGET AND COMPARE DATE
        $use = date('Y-m-');
        $num = 1;
        $cycleDate = CycleDates::where('loan_officer_id', $user->id)->first();
        if ($cycleDate != null) {
            $LC_targetDate = $use . $cycleDate->cycle_end_date;
        } else {
            $LC_targetDate = $use . $num;
        }
        $LC_targetDate = date('Y-m-d', strtotime($LC_targetDate));
        if ($todaysDate > $LC_targetDate) {
            $LC_targetDate = date('Y-m-d', strtotime($LC_targetDate . ' + 1 months'));
        }
        $LC_compareDate = date('Y-m-d', strtotime($LC_targetDate . ' - 1 months'));

        $end_date = $request->end_date;

        if (empty($end_date)) {
            if ($uncollected_type == 'uncollected_today') {
                $targetDate = $todaysDate;
            } elseif ($uncollected_type == 'uncollected_cycle') {
                $targetDate = $LC_compareDate;
            } else {
                $targetDate = $end_date;
            }
        }


        return view('user.uncollected_stats', compact('user', 'targetDate', 'userLoans', 'todaysDate', 'userTransactions'));
    }

    public function index()
    {
        if (!Sentinel::hasAccess('users.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $user = Sentinel::getUser();
        $query = User::with('role', 'office', 'province');

        if ($user->inRole(1)) {
            // Admin sees all
        } elseif ($user->inRole(6)) {
            $query->whereIn('office_id', function ($q) use ($user) {
                $q->select('id')->from('offices')->where('province_id', $user->province_id);
            });
        } elseif ($user->inRole(4)) {
            $query->where('office_id', $user->office_id);
        }

        $data = $query->get();
        return view('user.data', compact('data'));
    }

    //client users only
    public function client_users_index()
    {
        if (!Sentinel::hasAccess('users.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $user = Sentinel::getUser();
        $query = DB::table('users')->select('users.*')
            ->join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.name', 'Client');

        if ($user->inRole(1)) {
            // Admin sees all
        } elseif ($user->inRole(6)) {
            $query->whereIn('office_id', function ($q) use ($user) {
                $q->select('id')->from('offices')->where('province_id', $user->province_id);
            });
        } elseif ($user->inRole(4)) {
            $query->where('office_id', $user->office_id);
        }

        $data = $query->get();
        return view('user.client_users_data', compact('data'));
    }



    public function Cycle()
    {
        return view('user.cycle');
    }

    public function addCycle(Request $request)
    {
        $userId = Sentinel::getUser()->id;
        $cycle_end = CycleDates::where('loan_officer_id', '=', $userId)->first();

        if ($cycle_end) {
            $cycle_end->cycle_end_date = $request->cycle_end_date;
            $cycle_end->save();
            Flash::success(trans('general.successfully_saved'));
            return redirect('dashboard');
        } else {
            $cycle = new CycleDates();
            $cycle->loan_officer_id = $userId;
            $cycle->cycle_end_date = $request->cycle_end_date;
            $cycle->save();
            Flash::success(trans('general.successfully_saved'));
            return redirect('dashboard');
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('users.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $provinces = Province::all();
        return view('user.create', compact('provinces'));
    }

    /**credentials
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    // public function create_client_account(Request $request){

    //     if (!Sentinel::hasAccess('users.create')) {
    //         Flash::warning("Permission Denied");
    //         return redirect()->back();
    //     }
    //     $rules = array(
    //         'email' => 'required|unique:users',
    //         'password' => 'required',
    //         'repeat_password' => 'required|same:password',
    //         'first_name' => 'required',
    //         'last_name' => 'required',
    //     );
    //     $validator = Validator::make($request->all(), $rules);
    //     if ($validator->fails()) {
    //         Flash::warning(trans('general.validation_error'));
    //         return redirect()->back()->withInput()->withErrors($validator);

    //     } else {
    //         $credentials = [
    //             'email' => $request->email,
    //             'password' => $request->password,
    //             'first_name' => $request->first_name,
    //             'last_name' => $request->last_name,
    //             'address' => null,
    //             'notes' => null,
    //             'gender' => $request->gender,
    //             'phone' => $request->phone,
    //             'office_id'=> $request->office_id,
    //             'permission'=>2,
    //         ];
    //         $user = Sentinel::registerAndActivate($credentials);
    //         $role = Sentinel::findRoleById(2);
    //         $role->users()->attach($user->id);
    //         //check custom fields
    //         if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
    //             $custom_fields = CustomField::where('category', 'users')->get();
    //             foreach ($custom_fields as $key) {
    //                 $custom_field = new CustomFieldMeta();
    //                 $id = "custom_field_" . $key->id;
    //                 if ($key->field_type == "checkbox") {
    //                     if (!empty($request->$id)) {
    //                         $custom_field->name = serialize($request->$id);
    //                     } else {
    //                         $custom_field->name = serialize([]);
    //                     }
    //                 } else {
    //                     $custom_field->name = $request->$id;
    //                 }
    //                 $custom_field->parent_id = $user->id;
    //                 $custom_field->custom_field_id = $key->id;
    //                 $custom_field->category = "users";
    //                 $custom_field->save();
    //             }
    //         }
    //         GeneralHelper::audit_trail("Create", "Users", $user->id);
    //         Flash::success("sfgbru");
    //        // return redirect('login');
    //     }
    // }
    public function store(Request $request)
    {

        if (!Sentinel::hasAccess('users.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'email' => 'required|unique:users',
            'password' => 'required',
            'repeat_password' => 'required|same:password',
            'first_name' => 'required',
            'last_name' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'notes' => $request->notes,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'office_id' => $request->office_id,
                'province_id' => $request->province_id,
                'nrc_id' => $request->nrc_id,
                'permission' => $request->role,
            ];
            $user = Sentinel::registerAndActivate($credentials);
            $role = Sentinel::findRoleById($request->role);
            $role->users()->attach($user->id);
            //check custom fields
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'users')->get();
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
                    $custom_field->parent_id = $user->id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "users";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Create", "Users", $user->id);
            Flash::success("Successfully Saved");
            return redirect('user/data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($user)
    {
        if (!Sentinel::hasAccess('users.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('user.show', compact('user'));
    }

    public function inactive()
    {
        if (!Sentinel::hasAccess('users.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $inactiveUsers = User::where('status', 'Inactive')->get();

        return view('user.inactive', compact('inactiveUsers'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status == 'Active' ? 'Inactive' : 'Active';
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    public function edit($user)
    {
        // if (!Sentinel::hasAccess('users.update')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }

        // $allowedEditors = config('access.allowed_user_editors');
        // if (!in_array(Sentinel::getUser()->id, $allowedEditors)) {
        //     Flash::warning("You are not authorized to edit users");
        //     return redirect()->back();
        // }

        foreach ($user->roles as $sel) {
            $selected = $sel->id;
        }
        $provinces = Province::all();
        return view('user.edit', compact('user', 'selected', 'provinces'));
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
        // if (!Sentinel::hasAccess('users.update')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }
        $user = Sentinel::findById($id);
        $credentials = [
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'notes' => $request->notes,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'office_id' => $request->office_id,
            'province_id' => $request->province_id,
            'nrc_id' => $request->nrc_id,
            'external_id' => $request->external_id,
        ];

        if (!empty($request->password)) {
            $credentials['password'] = $request->password;
        }
        if ($request->role != $request->previous_role) {

            $role = Sentinel::findRoleById($request->previous_role);
            $role->users()->detach($user->id);
            $role = Sentinel::findRoleById($request->role);
            $role->users()->attach($user->id);
        }
        $user = Sentinel::update($user, $credentials);
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'users')->get();
            foreach ($custom_fields as $key) {
                if (
                    !empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where(
                        'category',
                        'users'
                    )->first())
                ) {
                    $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where(
                        'parent_id',
                        $id
                    )->where('category', 'users')->first();
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
                $custom_field->category = "users";
                $custom_field->save();
            }
        }
        GeneralHelper::audit_trail("Update", "Users", $user->id);


        Flash::success("Successfully Saved");
        return redirect('user/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('users.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (Sentinel::getUser()->id == $id) {
            Flash::warning("You cannot delete your account");
            return redirect()->back();
        }
        $user = Sentinel::findById($id);
        $user->delete();
        GeneralHelper::audit_trail("Delete", "Users", $user->id);
        Flash::success("Successfully Deleted");
        return redirect('user/data');
    }

    public function profile()
    {

        $user = Sentinel::findById(Sentinel::getUser()->id);
        return view('user.profile', compact('user'));
    }

    //168 in live system
    public function edit_my_details()
    {
        $user = Sentinel::getUser()->id;
        $client = Client::where('user_id', $user)->first();
        return view('user.edit_my_details', compact('client'));
    }

    public function edit_profile()
    {

        $user = Sentinel::getUser();
        return view('user.edit_profile', compact('user'));
    }

    //line 175 live system
    public function update_my_details(Request $request)
    {
        $user = Sentinel::getUser()->id;
        $client = Client::where('user_id', $user)->first();
        $client->staff_id = $client->staff_id;
        $client->nrc_number = $request->external_id;
        $client->mobile = $request->mobile;
        $client->phone = $request->phone;
        $client->email = $request->email;
        $client->client_type = $request->client_type;
        $client->first_name = $request->first_name;
        $client->middle_name = $request->middle_name;
        $client->last_name = $request->last_name;
        $client->gender = $request->gender;
        $client->marital_status = $request->marital_status;
        $client->dob = $request->dob;
        $client->working_place = $request->working_place;
        $client->working_position = $request->working_position;
        $client->salary = $request->salary;
        $client->nrc_number = $request->nrc_number;
        $client->full_name = $request->full_name;
        $client->incorporation_number = $request->incorporation_number;
        $client->key_contact_person = $request->key_contact_person;
        $client->key_contact_person_nrc_number = $request->key_contact_person_nrc_number;
        $client->number_of_shareholders = $request->number_of_shareholders;
        $client->company_registration_date = $request->company_registration_date;
        $client->type_of_business = $request->type_of_business;
        $client->street = $request->street;
        $client->address = $request->address;
        $client->joined_date = $request->joined_date;
        $client->notes = $request->notes;
        $client->save();
        GeneralHelper::audit_trail("Update", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('/dashboard');

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function profile_update(Request $request)
    {
        $user = Sentinel::findById(Sentinel::getUser()->id);
        $credentials = [
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'notes' => $request->notes,
            'gender' => $request->gender,
            'phone' => $request->phone
        ];
        if (!empty($request->password)) {
            $credentials['password'] = $request->password;
        }
        $user = Sentinel::update($user, $credentials);

        if (Sentinel::inRole('client')) {
            return app('App\Http\Controllers\ClientController')->clientSelfUpdate($request);
        }


        Flash::success("Successfully Saved");
        return redirect('dashboard');
    }

    //manage permissions
    public function indexPermission()
    {
        $data = array();
        $permissions = Permission::where('parent_id', 0)->get();
        foreach ($permissions as $permission) {
            array_push($data, $permission);
            $subs = Permission::where('parent_id', $permission->id)->get();
            foreach ($subs as $sub) {
                array_push($data, $sub);
            }
        }
        return view('user.permission.data', compact('data'));
    }

    public function createPermission()
    {
        $parents = Permission::where('parent_id', 0)->get();
        $parent = array();
        $parent['0'] = "None";
        foreach ($parents as $key) {
            $parent[$key->id] = $key->name;
        }

        return view('user.permission.create', compact('parent'));
    }

    public function storePermission(Request $request)
    {
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->parent_id = $request->parent_id;
        $permission->description = $request->description;
        if (!empty($request->slug)) {
            $permission->slug = $request->slug;
        } else {
            $permission->slug = str_slug($request->name, '_');
        }

        $permission->save();
        Flash::success("Successfully Saved");
        return redirect('user/permission/data');
    }

    public function editPermission($permission)
    {
        $parents = Permission::where('parent_id', 0)->get();
        $parent = array();
        $parent['0'] = "None";
        foreach ($parents as $key) {
            $parent[$key->id] = $key->name;
        }
        if ($permission->parent_id == 0) {
            $selected = 0;
        } else {
            $selected = 1;
        }

        return view('user.permission.edit', compact('parent', 'permission', 'selected'));
    }

    public function updatePermission(Request $request, $id)
    {
        $permission = Permission::find($id);
        $permission->name = $request->name;
        $permission->parent_id = $request->parent_id;
        $permission->description = $request->description;
        if (!empty($request->slug)) {
            $permission->slug = $request->slug;
        } else {
            $permission->slug = str_slug($request->name, '_');
        }
        $permission->save();
        Flash::success("Successfully Saved");
        return redirect('user/permission/data');
    }

    //manage roles
    public function indexRole()
    {
        if (!Sentinel::hasAccess('users.roles.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = EloquentRole::all();
        return view('user.role.data', compact('data'));
    }

    public function createRole()
    {
        if (!Sentinel::hasAccess('users.roles.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = array();
        $permissions = Permission::where('parent_id', 0)->get();
        foreach ($permissions as $permission) {
            array_push($data, $permission);
            $subs = Permission::where('parent_id', $permission->id)->get();
            foreach ($subs as $sub) {
                array_push($data, $sub);
            }
        }
        return view('user.role.create', compact('data'));
    }

    public function storeRole(Request $request)
    {
        if (!Sentinel::hasAccess('users.roles.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $role = new EloquentRole();
            $role->name = $request->name;
            $role->slug = GeneralHelper::getUniqueSlug($role, $request->name);
            $role->time_limit = $request->time_limit;
            if ($request->time_limit == 1) {
                if (strtotime($request->from_time) >= strtotime($request->to_time)) {
                    Flash::success("To time must be greater than from time");
                    return redirect()->back()->withInput();
                }
                $role->from_time = $request->from_time;
                $role->to_time = $request->to_time;
                $role->access_days = json_encode($request->access_days);
            } else {
                $role->access_days = json_encode([]);
            }
            $role->save();
            if (!empty($request->permission)) {
                foreach ($request->permission as $key) {
                    $role->updatePermission($key, true, true);
                }
            }
            GeneralHelper::audit_trail("Create Role", "Users", $role->id);
            Flash::success("Successfully Saved");
            return redirect('user/role/data');
        }
    }

    public function editRole($id)
    {
        if (!Sentinel::hasAccess('users.roles.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = array();
        $permissions = Permission::where('parent_id', 0)->get();
        foreach ($permissions as $permission) {
            array_push($data, $permission);
            $subs = Permission::where('parent_id', $permission->id)->get();
            foreach ($subs as $sub) {
                array_push($data, $sub);
            }
        }
        $role = EloquentRole::find($id);
        return view('user.role.edit', compact('data', 'role'));
    }

    public function updateRole(Request $request, $id)
    {
        if (!Sentinel::hasAccess('users.roles.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $role = EloquentRole::find($id);
        $role->name = $request->name;
        $role->slug = GeneralHelper::getUniqueSlug($role, $request->name);
        $role->time_limit = $request->time_limit;
        if ($request->time_limit == 1) {
            if (strtotime($request->from_time) >= strtotime($request->to_time)) {
                Flash::warning("To time must be greater than from time");
                return redirect()->back()->withInput();
            }
            $role->from_time = $request->from_time;
            $role->to_time = $request->to_time;
            $role->access_days = json_encode($request->access_days);
        } else {
            $role->access_days = json_encode([]);
        }
        $role->permissions = array();
        $role->save();
        //remove permissions which have not been ticked
        //create and/or update permissions
        if (!empty($request->permission)) {
            foreach ($request->permission as $key) {
                $role->updatePermission($key, true, true)->save();
            }
        }

        GeneralHelper::audit_trail("Update Role", "Users", $role->id);
        Flash::success("Successfully Saved");
        return redirect('user/role/data');
    }

    public function deletePermission($id)
    {
        Permission::destroy($id);
        Flash::success("Successfully Saved");
        return redirect('user/permission/data');
    }

    public function deleteRole($id)
    {
        if (!Sentinel::hasAccess('users.roles.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        EloquentRole::destroy($id);
        GeneralHelper::audit_trail("Delete Role", "Users", $id);
        Flash::success("Successfully Saved");
        return redirect('user/role/data');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $users = User::where('first_name', 'like', '%' . $query . '%')
            ->orWhere('last_name', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->orWhere('nrc_id', 'like', '%' . $query . '%')
            ->with('office')
            ->limit(10)
            ->get();
        return response()->json($users);
    }

    public function get_offices_by_province($id)
    {
        $data = Office::where('province_id', $id)->get();
        return response()->json($data);
    }
}
