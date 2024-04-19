<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\Permission;
use App\Models\Repair;
use App\Models\Setting;
use App\Models\Loan;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Client;
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
use stdClass;

class UserController extends Controller
{
    public function __construct()
    {

        $this->middleware('sentinel');
    }

    public function dashboard()
    {   
      
        $userId = Sentinel::getUser()->id;
        //BELOW THIS
        $role = UserRole::where('user_id',$userId)->first();
        $userBranch = Sentinel::getUser()->office_id;

        if($role->role_id == '2'){
            $user = Sentinel::getUser();
            $client = Client::where('user_id',$user->id)->first();
            $clientBranch = Office::where('id',$client->office_id)->first();
            $staff = Sentinel::findUserById($client->staff_id);
            $clientLoan = Loan::with('transactions')->where('status','disbursed')->where('client_id',$client->id)->first();

        }
        if($role->role_id != '2'){
            $userProvince = '2';
        }
        //$branch = Office::with('province')->where('id',$userBranch)->get
        $province_loans = [];
        $province_transactions = [];
        if($role->role_id != '2'){
        $province_branches = Office::where('province_id',$userProvince)->get();
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
        $afterDate = date('Y-m-d',strtotime($todaysDate. ' - 3 months'));
        $myLoans = null;
        $newBranchLoans = null;
        $someData = [];

        if($role->role_id == '1'){
       
       $allLoans = Loan::with('transactions')->where('created_date' ,'>', $afterDate)->get();
       foreach($allLoans as $loans){
        foreach($loans->transactions as $transaction){
            array_push($allTransactions,$transaction);
        }
       }

        }

       
    
      if(Sentinel::getUser()->cycle_dates == null){
       $end = 'NCI';
      }else{
        if(Sentinel::getUser()->cycle_dates->cycle_end_date < 10){
            $end = '0'.Sentinel::getUser()->cycle_dates->cycle_end_date;
        }else{
            $end = Sentinel::getUser()->cycle_dates->cycle_end_date;
        }
      }

      $targetDate = $use.$end;
      $targetDate = date('Y-m-d',strtotime($targetDate));
      if($todaysDate >= $targetDate){
        $targetDate = date('Y-m-d',strtotime($targetDate. ' + 1 months'));
    }
       $compareDate = date('Y-m-d',strtotime($targetDate. ' - 1 months'));


       if($role->role_id == '3'){
        $myLoans = Loan::with('transactions')->where('loan_officer_id',$userId)->get();
        foreach($myLoans as $myLoan){
            foreach($myLoan->transactions as $Transaction){
             array_push($myTransactions,$Transaction);
            }
 
            if($myLoan->status != 'closed'){
             array_push($myOpenLoans,$myLoan);
         }
          }

     }
   

     if($role->role_id == '4'){
        $newBranchLoans = Loan::with('transactions')->where('office_id',$userBranch)->get();
        foreach($newBranchLoans as $branchLoan){
            foreach($branchLoan->transactions as $Transaction){
                array_push($branchTransactions,$Transaction);
            }
         }
     }
       
     if($role->role_id == '6'){
        foreach($province_branches as $province_branch){
            $branch_loans = Loan::with('transactions')->where('office_id',$province_branch->id)->get();
            foreach($branch_loans as $loan){
                array_push($province_loans,$loan);
                foreach($loan->transactions as $transaction){
                    array_push($province_transactions,$transaction);
                }
            }
        }
     }

        

     
         $branchUsers = User::where('office_id',$userBranch)->with('loan')->with('role')->get();
         if($role->role_id != '2'){
        return view('dashboard', compact('end','myLoans','role','branchUsers','userBranch','myTransactions','myOpenLoans','newBranchLoans','branchTransactions','userProvince','province_loans','province_transactions','province_branches','allLoans','allTransactions','provinces',));
         }else{
            return view('dashboard',compact('role','user','client','clientBranch','staff','clientLoan',));
         }
    }  


    public function my_details(){
        $user = Sentinel::getUser();
        $client = Client::where('user_id',$user->id)->first();
        return view('user.my_details',compact('client'));
    }

    public function daily_figures(){
        $userId = Sentinel::getUser()->id;
        $role = UserRole::where('user_id',$userId)->first();
        $allLoans = [];
        $todaysDate = date('Y-m-d');
        $allTransactions = [];
        $afterDate = date('Y-m-d',strtotime($todaysDate. ' - 12 months'));

        if($role->role_id == '1'){
       
            $allLoans = Loan::with('transactions')->where('created_date' ,'>', $afterDate)->get();
            foreach($allLoans as $loans){
             foreach($loans->transactions as $transaction){
                 array_push($allTransactions,$transaction);
             }
            }
     
             }

    return view('user.daily_figures',compact('allLoans','allTransactions'));

    }


    public function province_page($id){
        $province_loans = [];
        $province_transactions = [];
        $province_branches = Office::where('province_id',$id)->get();
        $province = Province::where('id',$id)->first();
        foreach($province_branches as $province_branch){
            $branch_loans = Loan::with('transactions')->where('office_id',$province_branch->id)->get();
            foreach($branch_loans as $loan){
                array_push($province_loans,$loan);
                foreach($loan->transactions as $transaction){
                    array_push($province_transactions,$transaction);
                }
            }
        }
return view('user.province_page',compact('province_loans','province_transactions','province_branches','province',));

    }

    public function branch_page($id){
        $branchTransactions = [];
        $userBranch = $id;//Sentinel::getUser()->office_id;
        $office = Office::where('id',$id)->first();
        $newBranchLoans = Loan::with('transactions')->where('office_id',$userBranch)->get();
        $branchUsers = User::where('office_id',$userBranch)->with('loan')->with('role')->get();

        foreach($newBranchLoans as $branchLoan){
            foreach($branchLoan->transactions as $Transaction){
                array_push($branchTransactions,$Transaction);
            }
         }

        return view('user.branch_page',compact('newBranchLoans','branchTransactions','branchUsers','office',));
    }


    public function user_info($user){
        $userTransactions = [];
        $userLoans = Loan::with('transactions')->where('loan_officer_id',$user->id)->get();
        $cycleDate = CycleDates::where('loan_officer_id',$user->id)->first();
        foreach($userLoans as $userLoan){
            foreach($userLoan->transactions as $Transaction){
                array_push($userTransactions,$Transaction);
            }
        }
        return view('user.user_info',compact('user','userLoans','userTransactions','cycleDate'));
    }


    public function collections_stats(Request $request,$user,$collection_type){
        $userTransactions = [];
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $userLoans = Loan::with('transactions')->where('loan_officer_id',$user->id)->get();
        foreach($userLoans as $userLoan){
            foreach($userLoan->transactions as $Transaction){
                array_push($userTransactions,$Transaction);
            }
        }
        //TODAY AND YESTERDAY'S DATES
        $todaysDate = date('Y-m-d');
        $yesterdaysDate = date('Y-m-d',strtotime($todaysDate. ' - 1 days'));

        //LC TARGET AND COMPARE DATE
        $use = date('Y-m-');
        $num = 1;
        $cycleDate = CycleDates::where('loan_officer_id',$user->id)->first();
        if($cycleDate != null){
            $LC_targetDate = $use.$cycleDate->cycle_end_date;
        }else{
            $LC_targetDate = $use.$num;
        }
        $LC_targetDate = date('Y-m-d',strtotime($LC_targetDate));
        if($todaysDate > $LC_targetDate){
            $LC_targetDate = date('Y-m-d',strtotime($LC_targetDate. ' + 1 months'));
        }
        $LC_compareDate = date('Y-m-d',strtotime($LC_targetDate. ' - 1 months'));
       
        //DATES SET BY THE USER
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        if(empty($start_date && $end_date)){
            if($collection_type == 'collections_today'){
                $targetDate = $todaysDate;
                $compareDate = $yesterdaysDate;
            }elseif($collection_type == 'collections_cycle'){
                $targetDate = $LC_targetDate;
                $compareDate = $LC_compareDate;
            }
        else{
            $targetDate = $end_date;
            $compareDate = $start_date;
        }
        }
        return view('user.collections_stats',compact('collection_type','targetDate','compareDate','userLoans','userTransactions','user','start_date','end_date'));
    }


    public function leaderboard(Request $request){
        $data = [];
        $time_period = $request->time_period;
        $office = $request->office;
        $monthUse = date('Y-m-');
        $yearUse = date('Y-');
        $todaysDate = date('Y-m-d');
        $tomorrowsDate = date('Y-m-d', strtotime($todaysDate. ' + 1 days'));
        $weekEndDate = date('Y-m-d', strtotime($todaysDate. ' - 7 days'));
       
     

        $startDate = '';
        $endDate = '';
        if($office == 0){
            $LoanConsultants = User::with('role')->with('office')->get();
            
    }else{
        $LoanConsultants = User::with('role')->with('office')->where('office_id',$office)->get();
    }
     //   if($time_period )
        if(empty($time_period)){
            $startDate = $monthUse.'01';
            $endDate = $monthUse.'31';
        }elseif($time_period == 'Daily'){
            $startDate = $todaysDate;
            $endDate = $tomorrowsDate;
        }elseif($time_period == 'Weekly'){
            $startDate = $weekEndDate;
            $endDate = $todaysDate;
        }elseif($time_period == 'Yearly'){
            $startDate = $yearUse.'01-01';
            $endDate = $yearUse.'12-31';
        }else{
            $startDate = $monthUse.'01';
            $endDate = $monthUse.'31';
        }

        foreach($LoanConsultants as $loanConsultant){
            if(!empty($loanConsultant->role->role_id)){
            if($loanConsultant->role->role_id == '3' || $loanConsultant->role->role_id == '4'){
                $object = new stdClass();
                $full_payment_total = 0;
                $part_payment_total = 0;
                $reloan_payments_total = 0;
                $charge = 0;
    
                $loans = \App\Models\Loan::with('transactions')->where('loan_officer_id',$loanConsultant->id)->get();
                
                foreach($loans as $loan){
                    foreach($loan->transactions as $transaction){
                       if($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date >= $startDate && $transaction->date <= $endDate ){
                           $full_payment_total = $full_payment_total + $transaction->credit;
                       }
    
                       if( $transaction->payment_apply_to == 'part_payment' && $transaction->date >= $startDate && $transaction->date <= $endDate ){
                        $part_payment_total = $part_payment_total +  $transaction->credit;
                    }
    
                    if($transaction->payment_apply_to == 'reloan_payment' && $transaction->date >= $startDate && $transaction->date <= $endDate){
                        $reloan_payments_total = $reloan_payments_total +  $transaction->credit;
                    }
    
    
                    }
                }
                     
                                $object->first_name = $loanConsultant->first_name;
                                $object->last_name = $loanConsultant->last_name;
                                $object->amount = $full_payment_total + $part_payment_total + $reloan_payments_total;
                                $object->role = $loanConsultant->role;
                                if(!empty($loanConsultant->office)){
                                    $object->office = $loanConsultant->office->name;
                                }else{
                                    $object->office = 'no branch';
                                }
                  
                        
                array_push($data,$object);

            }
        }
          
        }
      
        return view('user.leaderboard',compact('time_period','office','LoanConsultants','data','startDate','endDate'));
    }


    public function given_out_stats(Request $request,$user,$given_out_type){
        $userTransactions = [];
        $targetDate = $request->end_date;
        $compareDate = $request->start_date;
        $userLoans = Loan::with('transactions')->where('loan_officer_id',$user->id)->get();
        foreach($userLoans as $userLoan){
            foreach($userLoan->transactions as $Transaction){
                array_push($userTransactions,$Transaction);
            }
        }
          //TODAY AND YESTERDAY'S DATES
          $todaysDate = date('Y-m-d');
          $yesterdaysDate = date('Y-m-d',strtotime($todaysDate. ' - 1 days'));

          //LC TARGET AND COMPARE DATE
        $use = date('Y-m-');
        $num = 1;
        $cycleDate = CycleDates::where('loan_officer_id',$user->id)->first();
        if($cycleDate != null){
            $LC_targetDate = $use.$cycleDate->cycle_end_date;
        }else{
            $LC_targetDate = $use.$num;
        }
        $LC_targetDate = date('Y-m-d',strtotime($LC_targetDate));
        if($todaysDate > $LC_targetDate){
            $LC_targetDate = date('Y-m-d',strtotime($LC_targetDate. ' + 1 months'));
        }
        $LC_compareDate = date('Y-m-d',strtotime($LC_targetDate. ' - 1 months'));

          //DATES SET BY THE USER
          $start_date = $request->start_date;
          $end_date = $request->end_date;
  
          if(empty($start_date && $end_date)){
              if($given_out_type == 'given_out_today'){
                  $targetDate = $todaysDate;
                  $compareDate = $yesterdaysDate;
              }elseif($given_out_type == 'given_out_cycle'){
                  $targetDate = $LC_targetDate;
                  $compareDate = $LC_compareDate;
              }
          else{
              $targetDate = $end_date;
              $compareDate = $start_date;
          }
          }



        return view('user.given_out_stats',compact('given_out_type','targetDate','compareDate','userLoans','userTransactions','user','start_date','end_date'));
    }


    public function uncollected_stats(Request $request,$user,$uncollected_type){
          $userTransactions = [];

          $userLoans = Loan::with('transactions')->where('loan_officer_id',$user->id)->get();
          $targetDate = $request->end_date;
      
          //TODAY AND YESTERDAY'S DATES
          $todaysDate = date('Y-m-d');
          $yesterdaysDate = date('Y-m-d',strtotime($todaysDate. ' - 1 days'));
          foreach($userLoans as $userLoan){
            foreach($userLoan->transactions as $Transaction){
                array_push($userTransactions,$Transaction);
            }
        }

              //LC TARGET AND COMPARE DATE
        $use = date('Y-m-');
        $num = 1;
        $cycleDate = CycleDates::where('loan_officer_id',$user->id)->first();
        if($cycleDate != null){
            $LC_targetDate = $use.$cycleDate->cycle_end_date;
        }else{
            $LC_targetDate = $use.$num;
        }
        $LC_targetDate = date('Y-m-d',strtotime($LC_targetDate));
        if($todaysDate > $LC_targetDate){
            $LC_targetDate = date('Y-m-d',strtotime($LC_targetDate. ' + 1 months'));
        }
        $LC_compareDate = date('Y-m-d',strtotime($LC_targetDate. ' - 1 months'));

        $end_date = $request->end_date;

        if(empty($end_date)){
            if($uncollected_type == 'uncollected_today'){
                $targetDate = $todaysDate;
            }elseif($uncollected_type == 'uncollected_cycle'){
                $targetDate = $LC_compareDate;
            }
        else{
            $targetDate = $end_date;
        }
        }


        return view('user.uncollected_stats',compact('user','targetDate','userLoans','todaysDate','userTransactions'));
    }




    public function index()
    {
        if (!Sentinel::hasAccess('users.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
     

   $data = DB::table('users')->select('users.*')

->join('role_users', 'role_users.user_id', '=', 'users.id')
->join('roles', 'roles.id', '=', 'role_users.role_id')
->where('roles.name','!=', 'Client')->get();

        // $data = User::where('roles')->get();
        return view('user.data', compact('data'));
    }

    //client users only
    public function client_users_index()
    {
        if (!Sentinel::hasAccess('users.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }


   $data = DB::table('users')->select('users.*')

->join('role_users', 'role_users.user_id', '=', 'users.id')
->join('roles', 'roles.id', '=', 'role_users.role_id')
->where('roles.name', 'Client')->get();
        return view('user.client_users_data', compact('data'));
    }



    public function Cycle(){
        return view('user.cycle');
    }

    public function addCycle(Request $request){
        $userId = Sentinel::getUser()->id;
        $cycle_end_date = CycleDates::where('loan_officer_id','=',$userId)->first();

        if($cycle_end_date){
            Flash::warning('You already set a cycle date');
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

        return view('user.create');
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
                'office_id'=> $request->office_id,
                'permission'=>$request->role,
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

    public function edit($user)
    {
        if (!Sentinel::hasAccess('users.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }


        foreach ($user->roles as $sel) {
            $selected = $sel->id;
        }
        return view('user.edit', compact('user', 'selected'));
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
        if (!Sentinel::hasAccess('users.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $user = Sentinel::findById($id);
        $credentials = [
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'notes' => $request->notes,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'office_id'=> $request->office_id,
            'external_id'=> $request->external_id,
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
                if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                    'users')->first())
                ) {
                    $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                        $id)->where('category', 'users')->first();
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
    public function edit_my_details(){
        $user = Sentinel::getUser()->id;
        $client = Client::where('user_id',$user)->first();
        return view('user.edit_my_details', compact('client'));
    }

    public function edit_profile()
    {

        $user = Sentinel::getUser();
        return view('user.edit_profile', compact('user'));
    }

//line 175 live system
    public function update_my_details(Request $request){
        $user = Sentinel::getUser()->id;
        $client = Client::where('user_id',$user)->first();
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

        if(Sentinel::inRole('client')){
         return   app('App\Http\Controllers\ClientController')->clientSelfUpdate($request);
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
}
