<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Mail\PasswordReset;
use App\Models\Setting;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
// use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use Sentinel;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Http\Requests;
use App\Models\Client;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;



class HomeController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        } else {
            return redirect('dashboard');
        }
    }

    public function login()
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        return view('login');
    }
    
    
  
    public function create_account(){
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        return view('new_signup');
    }

    
    public function create_client_account(Request $request){

        // if (!Sentinel::hasAccess('users.create')) {
        //     Flash::warning("Permission Denied");
        //     return redirect()->back();
        // }
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
                'address' => null,
                'notes' => null,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'office_id'=> $request->office_id,
                'permission'=>2,
            ];
            $user = Sentinel::registerAndActivate($credentials);
            $role = Sentinel::findRoleById(2);
           // $role->users()->attach($user->id);
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
           
           // return redirect('login');
            Flash::success("sfgbru");
        }
    }
    

    public function create_Profile(Request $request)
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
            $id = $request->post('id');
            $user = Sentinel::findById($id);
        
        if(!$user) return redirect('login');


        return view('create_profile', compact('id'));
    }

            /**
     * clientSelfUpdate. triggered UserController if user is client on profile update
     * SEE UserController->update
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    
    public function createProfile(Request $request)
    {

             $id = $request->id;
            $user = Sentinel::findById($id);
         if(!$user) return redirect('login');

        $client = new Client();

        // $client->staff_id = $request->staff_id;
        $client->external_id = $request->external_id;
        $client->mobile = $user->mobile;
        $client->user_id = $id;
        $client->phone = $user->phone;
        $client->email = $user->email;
        $client->office_id = $request->office_id;
        $client->staff_id = $request->loan_officer_id;
    if ($request->client_type == "individual") {
            $client->first_name = $user->first_name;
            $client->middle_name = $user->middle_name;
            $client->last_name = $user->last_name;
            $client->gender = $user->gender;
            $client->marital_status = $request->marital_status;
            $client->dob = $request->dob;
            $client->client_type = $request->client_type;
        } else {
            $client->full_name = $request->full_name;
            $client->incorporation_number = $request->incorporation_number;
        }
        $client->street = $request->street;
        $client->address = $request->address;
        $client->joined_date = $request->joined_date;
        $client->notes = $request->notes;
        //activate client on profile creation
        // $client->status = 'active';
        $client->save();
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'clients')->get();
            foreach ($custom_fields as $key) {
                if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                    'clients')->first())
                ) {
                    $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                        $id)->where('category', 'clients')->first();
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
                $custom_field->category = "clients";
                $custom_field->save();
            }
        }
        Flash::success(trans('general.successfully_saved'));
        return redirect('login');

    }


    public function logout()
    {
        //GeneralHelper::audit_trail("Logged out of system");
        session()->flush();
        Sentinel::logout(null, true);
        return redirect('login');
    }

    public function process_login(Request $request)
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        $rules = array(
            'email' => 'required',
            'password' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            //process validation here
            $credentials = array(
                "email" => $request->get('email'),
                "password" => $request->get('password'),
            );
            if ($request->get('remember')) {
                $remember = true;
            } else {
                $remember = false;
            }
            try {
                if (Sentinel::authenticate($credentials, $remember)) {
                    //GeneralHelper::audit_trail("Logged in to system");
                    if (Sentinel::getUser()->blocked == 1) {
                        //prevent login
                        Flash::warning(trans('general.user_blocked'));
                        Sentinel::logout(null, true);
                        return redirect('login');
                    }
                    //check allowed roles
                    $role = Sentinel::getUser()->roles->first();
                    if (!empty($role)) {
                        if ($role->id == 3) {
                            $time = date("H:i");
                            $day = date("l");
                            if (strtotime($time) > strtotime($role->from_time) && strtotime($time) < strtotime($role->to_time)) {

                                Sentinel::logout(null, true);
                                Flash::warning(trans('The system is now closed. Kindly check back after 01:00 AM😄'));
                                return redirect()->back()->withInput()->withErrors(trans('The system is now closed. Kindly check back after 01:00 AM😄'));
                               // Sentinel::logout(null, true);
                                return redirect('login');
                            }
                        }

                    }
                    return redirect('dashboard');
                } else {
                    //return back
                    Flash::warning(trans('general.invalid_login_details'));
                    return redirect()->back()->withInput()->withErrors(trans('general.invalid_login_details'));
                }
            } catch (ThrottlingException $ex) {
                Flash::warning(trans('general.too_many_login_attempts'));
                return redirect()->back()->withInput()->withErrors(trans('general.too_many_login_attempts'));
            } catch (NotActivatedException $ex) {
                Flash::warning(trans('general.account_not_activated'));
                return redirect()->back()->withInput()->withErrors(trans('general.account_not_activated'));
            }


        }
    }
    
    
    //logoutViaApp start
    public function logoutViaApp()
    {
        //GeneralHelper::audit_trail("Logged out of system");
        session()->flush();
        Sentinel::logout(null, true);
        $data = ['isError'=>false,'msg'=>'You are now logged out'];
               die(json_encode($data));
    }
    
    //logoutViaApp end
    
    
    
    
    
    //loginViaApp start
    public function loginViaApp(Request $request)
    {
        if (Sentinel::check()) {
            $data = ['isError'=>false,'msg'=>'You are already logged in'];
                   die(json_encode($data));
        }
        $rules = array(
            'email' => 'required',
            'password' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
                $data = ['isError'=>true,'msg'=>'Missing fields'];
                       die(json_encode($data));
        } else {
            //process validation here
            $credentials = array(
                "email" => $request->get('email'),
                "password" => $request->get('password'),
            );
            if ($request->get('remember')) {
                $remember = true;
            } else {
                $remember = false;
            }
            try {
                if (Sentinel::authenticate($credentials, $remember)) {
                    //GeneralHelper::audit_trail("Logged in to system");
                    if (Sentinel::getUser()->blocked == 1) {
                        //prevent login
                        Flash::warning(trans('general.user_blocked'));
                        Sentinel::logout(null, true);
                        $data = ['isError'=>true,'msg'=>'user is blocked'];
                               die(json_encode($data));
                    }
                    //check allowed roles
                    $role = Sentinel::getUser()->roles->first();
                    if (!empty($role)) {
                        if ($role->time_limit == 1) {
                            $time = date("H:i");
                            $day = date("l");
                            if (!in_array(strtolower($day), json_decode($role->access_days)) || strtotime($time) < strtotime($role->from_time) || strtotime($time) >= strtotime($role->to_time)) {
                                Flash::warning("You are not allowed to login at this time");
                                Sentinel::logout(null, true);
                                $data = ['isError'=>true,'msg'=>'You are not allowed to login at this time'];
                                       die(json_encode($data));
                            }
                        }

                    }
                    $data = ['isError'=>false,'msg'=>'You are now logged in '];
                           die(json_encode($data));
                } else {
                    //return back
                    Flash::warning(trans('general.invalid_login_details'));
                    $data = ['isError'=>false,'msg'=>'invalid_login_details'];
                           die(json_encode($data));
                }
            } catch (ThrottlingException $ex) {
                Flash::warning(trans('general.too_many_login_attempts'));
            
                
                $data = ['isError'=>false,'msg'=>'too_many_login_attempts'];
                       die(json_encode($data));
                    
                
            } catch (NotActivatedException $ex) {
                Flash::warning(trans('general.account_not_activated'));
                $data = ['isError'=>false,'msg'=>'account_not_activated'];
                       die(json_encode($data));
            }


        }
    }
    
    
    
    //loginViaApp end
    
    //registerViaApp start
    public function registerViaApp(Request $request)
    {
//        if (Sentinel::check()) {
//            $data = ['isError'=>true,'msg'=>'You are already logged in '];
//                   die(json_encode($data));
//        }
        
        $rules = array(
            'email' => 'required|unique:users',
            'password' => 'required',
            'rpassword' => 'required|same:password',
            'first_name' => 'required',
            'last_name' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            Flash::warning(trans('login.failure'));
            $data = ['isError'=>true,'msg'=>'Sorry something went wrong'];
                   die(json_encode($data));

        } else {
            //process validation here
            $credentials = array(
                "email" => $request->get('email'),
                "password" => $request->get('password'),
                "first_name" => $request->get('first_name'),
                "last_name" => $request->get('last_name'),
            );
            $user = Sentinel::registerAndActivate($credentials);
            $role = Sentinel::findRoleByName('Client');
            $role->users()->attach($user);
            $msg = trans('login.success');
            Flash::success(trans('login.success'));

            $id = $user->id;
            

            
            $data = ['isError'=>false,'msg'=>'Account successfully created','id'=> $id];
                   die(json_encode($data));

        }
    }
    
    
    
    
    
    
    
    //registerViaApp end
    
    
    
    

      public function signup()
    {
       
        return view('signup');
    }
    

    public function register(Request $request)
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        $rules = array(
            'email' => 'required|unique:users',
            'password' => 'required',
            'rpassword' => 'required|same:password',
            'first_name' => 'required',
            'last_name' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            Flash::warning(trans('login.failure'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            //process validation here
            $credentials = array(
                "email" => $request->get('email'),
                "password" => $request->get('password'),
                "first_name" => $request->get('first_name'),
                "last_name" => $request->get('last_name'),
                'office_id'=> $request->office,
                'role'=>$request->role,
            );
            $role_Id = $request->role;
            $user = Sentinel::registerAndActivate($credentials);
            $role = Sentinel::findRoleById($role_Id);
            $role->users()->attach($user);
            $msg = trans('login.success');
            Flash::success(trans('login.success'));

            $id = $user->id;

            if($role_Id == '3'){
                return view('login');
            }else{
                return view('create_profile', compact('id'));
            }

        }
    }
    
    

    public function password_reset()
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        return view('password_reset');
    }

    /*
     * Password Resets
     */
    public function process_password_reset(Request $request)
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        $rules = array(
            'email' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            //process validation here
            $credentials = array(
                "email" => $request->get('email'),
            );
            $user = Sentinel::findByCredentials($credentials);
            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(trans_choice('general.user_email_not_found', 1));
            } else {
                Mail::to($user->email)->send(new PasswordReset($user));
                Flash::success(trans('general.password_reset_success'));
                return redirect()->back()
                    ->withSuccess(trans('general.password_reset_success'));
            }

        }
    }

    public function confirm_password_reset($id, $code)
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        return view('confirm_password_reset', compact('id', 'code'));
    }

    public function process_confirm_password_reset(Request $request, $id, $code)
    {
        if (Sentinel::check()) {
            return redirect('dashboard');
        }
        $rules = array(
            'password' => 'required',
            'repeat_password' => 'required|same:password',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            //process validation here
            $credentials = array(
                "email" => $request->get('email'),
                'password' => $request->get('password'),
            );
            $user = Sentinel::findById($id);
            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(trans_choice('general.user_email_not_found', 1));
            }
            if (!Reminder::complete($user, $code, $request->get('password'))) {
                return redirect()->to('password_reset')
                    ->withErrors(trans('general.invalid_password_reset_code'));
            }
            Sentinel::authenticate($credentials);
            Flash::success(trans('general.password_reset_complete'));
            return redirect('dashboard');

        }
    }


}
