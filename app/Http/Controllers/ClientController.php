<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Mail\SendLoginDetailsEmail;
use App\Models\BlacklistHistory;
use App\Models\BlacklistReason;
use App\Models\Client;
use App\Models\ClientIdentification;
use App\Models\ClientNextOfKin;
use App\Models\ClientUser;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Document;
use App\Models\Loan;
use App\Models\Note;
use App\Models\Office;
use App\Models\Savings;
use App\Models\Setting;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use Carbon\Carbon;

class ClientController extends Controller
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
        if (!Sentinel::hasAccess('clients.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $data = Client::where('status', 'active')->get();

        return view('client.data', compact('data'));
    }

    public function my_index()
    {
        if (!Sentinel::hasAccess('clients.my_clients')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $staff_id = Sentinel::getUser()->id;
        $data = Client::where('status', 'active')->where('staff_id', $staff_id)->get();

        return view('client.my_clients', compact('data'));
    }

    public function branch_index()
    {
        if (!Sentinel::hasAccess('clients.branch_clients')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $office_id = Sentinel::getUser()->office_id;
        $data = Client::where('status', 'active')->when($office_id, function ($query) use ($office_id) {
            if ($office_id != 0) {
                $query->where('office_id', '=', $office_id);
            }
        })->with('staff')->with('office')->get();


        return view('client.branch_clients', compact('data'));
    }


    public function pending_approval()
    {
        if (!Sentinel::hasAccess('clients.pending_approval')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Client::where('status', 'pending')->get();

        return view('client.pending_approval', compact('data'));
    }


    public function managers_pending_approval()
    {
        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $office_id = Sentinel::getUser()->office_id;
        $data = Client::where('status', 'pending')->where('office_id',$office_id)->get();

        return view('client.pending_approval', compact('data'));
    }

    public function declined()
    {
        if (!Sentinel::hasAccess('clients.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Client::where('status', 'declined')->get();

        return view('client.declined', compact('data'));
    }

    public function closed()
    {
        if (!Sentinel::hasAccess('clients.closed')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Client::where('status', 'closed')->get();

        return view('client.closed', compact('data'));
    }

    public function clients_inactive()
    {
        if (!Sentinel::hasAccess('clients.closed')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Client::where('status', 'inactive')->get();

        return view('client.inactive', compact('data'));
    }

    public function clients_blacklisted()
    {
        if (!Sentinel::hasAccess('clients.closed')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Client::where('blacklisted', 1)->orderBy('created_at', 'desc')->get();

        return view('client.blacklisted', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('clients.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //get custom fields
        //$custom_fields = CustomField::where('category', 'clients')->get();
        return view('client.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('clients.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
       
        $client_uniqueid = Client::where('nrc_number', $request->nrc_number)->first();
        if($client_uniqueid){
            Flash::warning('A client with this nrc number' . ' ' . $request->nrc_number .' '. 'already exists');
            return redirect()->back();
        }else{
            $client = new Client();

        $client->created_by_id = Sentinel::getUser()->id;
        $client->staff_id = $request->staff_id;
        $client->office_id = $request->office_id;
        $client->external_id = $request->external_id;
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
        $fullname = $request->first_name.' '.$request->last_name;
        $name_array = explode(' ',trim($fullname));
        $office = Office::find($request->office_id);
        $firstWord = $name_array[0];
        $lastWord = $name_array[count($name_array)-1];
        $initials = $firstWord[0]."".$lastWord[0];
        $client->account_no = $initials. '-'. $office->external_id . '-' . $client->id;
        $client->save();
        //check custom fields
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'clients')->get();
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
                $custom_field->parent_id = $client->id;
                $custom_field->custom_field_id = $key->id;
                $custom_field->category = "clients";
                $custom_field->save();
            }
        }
        GeneralHelper::audit_trail("Create", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('client/' . $client->id . '/show');
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_blacklist()
    {
        if (!Sentinel::hasAccess('clients.blacklist.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $clients = Client::where('blacklisted', 0)->get();
        $reasons = BlacklistReason::get();

        return view('client.create_blacklist', ['clients' => $clients, 'reasons' => $reasons]);
    }

    public function store_blacklist(Request $request)
    {
        if (!Sentinel::hasAccess('clients.blacklist.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $request->validate([
            'client_id' => ['required'],
            'blacklist_reason_id' => ['required'],
        ]);
        $client = Client::find($request->client_id);
        $history = new BlacklistHistory();
        $history->created_by_id = Sentinel::getUser()->id;
        $history->client_id = $request->client_id;
        $history->office_id = $client->office_id;
        $history->blacklist_reason_id = $request->blacklist_reason_id;
        $history->date = $request->date;
        $history->description = $request->description;
        $history->save();
        $client->blacklisted = 1;
        $client->date_blacklisted = $history->date;
        $client->save();
        GeneralHelper::audit_trail("Blacklist", "Clients", $request->client_id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('client/clients_blacklisted');
    }

    public function remove_blacklist(Client $client)
    {
        if (!Sentinel::hasAccess('clients.blacklist.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $reasons = BlacklistReason::get();

        return view('client.remove_blacklist', ['client' => $client, 'reasons' => $reasons]);
    }

    public function store_remove_blacklist(Request $request, Client $client)
    {
        if (!Sentinel::hasAccess('clients.blacklist.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $request->validate([
            'date' => ['required'],
        ]);
        $history = new BlacklistHistory();
        $history->created_by_id = Sentinel::getUser()->id;
        $history->client_id = $client->id;
        $history->office_id = $client->office_id;
        $history->blacklist_reason_id = $request->blacklist_reason_id;
        $history->date = $request->date;
        $history->description = $request->description;
        $history->save();
        $client->blacklisted = 0;
        $client->date_blacklisted = null;
        $client->save();
        GeneralHelper::audit_trail("Remove Blacklist", "Clients", $request->client_id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('client/clients_blacklisted');
    }

    public function show($client)
    {
        if (!Sentinel::hasAccess('clients.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //get custom fields
        //$custom_fields = CustomFieldMeta::where('category', 'clients')->where('parent_id', $client->id)->get();
        return view('client.show', compact('client'));
    }

    public function edit($client)
    {
        if (!Sentinel::hasAccess('clients.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }


        return view('client.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

     public function update(Request $request, $id)
     {
         if (!Sentinel::hasAccess('clients.update')) {
             Flash::warning("Permission Denied");
             return redirect()->back();
         }
 
         $client = Client::find($id);
         $client->staff_id = $request->staff_id;
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
       //  $client->dob = Carbon::createFromFormat('Y-m-d', $request->dob)->format('m/d/Y');
     //   $client->dob = Carbon::toFormattedDateString('Y-m-d', $request->dob);
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
         GeneralHelper::audit_trail("Update", "Clients", $client->id);
         Flash::success(trans('general.successfully_saved'));
         return redirect('client/data');
     }
 
     /**
     * clientSelfUpdate. triggered UserController if user is client on profile update
     * SEE UserController->update
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    public function clientSelfUpdate(Request $request)
    {
        if (!Sentinel::inRole('client')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $id = Sentinel::getUser()->id;

        $client = Client::where('user_id', $id)->first();

        if (!$client) {
            //client profile is created when a client updates his profile
            $client = new Client();
        }

        // $client->staff_id = $request->staff_id;
        // $client->external_id = $request->external_id;
        $client->mobile = $request->mobile;
        $client->user_id = $id;
        $client->phone = $request->phone;
        $client->email = $request->email;
        if ($request->client_type == "individual") {
            $client->first_name = $request->first_name;
            $client->middle_name = $request->middle_name;
            $client->last_name = $request->last_name;
            $client->gender = $request->gender;
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
        GeneralHelper::audit_trail("Update", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('dashboard');


    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('clients.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Client::destroy($id);
        Loan::where('client_id', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Clients", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('client/data');
    }

    public function picture(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client = Client::find($id);
        if (!empty($client->picture)) {
            @unlink(public_path() . '/uploads/' . $client->picture);
        }
        if ($request->hasFile('picture')) {
            $file = array('picture' => $request->file('picture'));
            $rules = array('picture' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $fname = str_slug($client->account_no, '_') . "" . uniqid() . '.' . $request->file('picture')->guessExtension();
                $client->picture = $fname;
                $request->file('picture')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $client->save();
        GeneralHelper::audit_trail("Create Picture", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();

    }


    public function approve(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client = Client::find($id);
        $client->status = "active";
        $client->activated_date = $request->activated_date;
        $client->activated_by_id = Sentinel::getUser()->id;
        $client->save();
        GeneralHelper::audit_trail("Approve", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function active(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client = Client::find($id);
        $client->status = "active";
        $client->activated_date = $request->activated_date;
        $client->activated_by_id = Sentinel::getUser()->id;
        $client->save();
        GeneralHelper::audit_trail("Activate", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function decline(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client = Client::find($id);
        $client->status = "declined";
        $client->declined_date = $request->declined_date;
        $client->declined_by_id = Sentinel::getUser()->id;
        $client->save();
        GeneralHelper::audit_trail("Declined", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function close(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.close')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client = Client::find($id);
        $client->status = "closed";
        $client->closed_date = $request->closed_date;
        $client->closed_reason = $request->closed_reason;
        $client->closed_by_id = Sentinel::getUser()->id;
        $client->save();
        GeneralHelper::audit_trail("Closed", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function inactive(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.close')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client = Client::find($id);
        $client->status = "inactive";
        $client->inactive_date = $request->inactive_date;
        $client->inactive_by_id = Sentinel::getUser()->id;
        $client->inactive_reason = $request->inactive_reason;
        $client->save();
        GeneralHelper::audit_trail("Inactivated", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function transfer(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.transfer.client')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client = Client::find($id);
        $client->office_id = $request->office_id;
        $client->save();
        foreach (Loan::where("status", "disbursed")->where('client_id', $id)->get() as $key) {
            $loan = Loan::find($key->id);
            $loan->office_id = $client->office_id;
            $loan->save();
        }

        foreach (Savings::where("status", "approved")->where('client_id', $id)->get() as $key) {
            $savings = Savings::find($key->id);
            $savings->office_id = $client->office_id;
            $savings->save();
        }
        GeneralHelper::audit_trail("Transfer", "Clients", $client->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //client identification
    public function store_client_identification(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.identification.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'name' => 'required|unique:client_identifications',
            'client_identification_type_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $client_identification = new ClientIdentification();
            $client_identification->client_id = $id;
            $client_identification->client_identification_type_id = $request->client_identification_type_id;
            $client_identification->name = $request->name;
            $client_identification->notes = $request->notes;
            if ($request->hasFile('attachment')) {
                $file = array('attachment' => $request->file('attachment'));
                $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,doc');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = str_slug($request->name, '_') . "" . uniqid() . '.' . $request->file('attachment')->guessExtension();
                    $client_identification->attachment = $fname;
                    $request->file('attachment')->move(public_path() . '/uploads',
                        $fname);
                }

            }
            $client_identification->save();
            GeneralHelper::audit_trail("Update", "Clients", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_client_identification(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.identification.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client_identification = ClientIdentification::find($id);
        if (!empty($client_identification->attachment)) {
            @unlink(public_path() . '/uploads/' . $client_identification->attachment);
        }
        ClientIdentification::destroy($id);
        GeneralHelper::audit_trail("Update", "Clients", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    //client documents
    public function store_client_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.documents.create')) {
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
            $client_document = new Document();
            $client_document->record_id = $id;
            $client_document->type = "client";
            $client_document->name = $request->name;
            $client_document->notes = $request->notes;
            if ($request->hasFile('attachment')) {
                $file = array('attachment' => $request->file('attachment'));
                $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,doc,xlsx,pptx,xls');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = str_slug($request->name, '_') . "" . uniqid() . '.' . $request->file('attachment')->guessExtension();
                    $client_document->location = $fname;
                    $request->file('attachment')->move(public_path() . '/uploads',
                        $fname);
                }

            }
            $client_document->save();
            GeneralHelper::audit_trail("Update", "Clients", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_client_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.documents.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $client_document = Document::find($id);
        if (!empty($client_document->location)) {
            @unlink(public_path() . '/uploads/' . $client_document->location);
        }
        Document::destroy($id);
        GeneralHelper::audit_trail("Update", "Clients", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    //client next_of_kin
    public function store_next_of_kin(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.next_of_kin.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $next_of_kin = new ClientNextOfKin();
            $next_of_kin->client_id = $id;
            $next_of_kin->client_relationship_id = $request->client_relationship_id;
            $next_of_kin->first_name = $request->first_name;
            $next_of_kin->middle_name = $request->middle_name;
            $next_of_kin->last_name = $request->last_name;
            $next_of_kin->address = $request->address;
            $next_of_kin->mobile = $request->mobile;
            $next_of_kin->gender = $request->gender;
            $next_of_kin->notes = $request->notes;
            if ($request->hasFile('picture')) {
                $file = array('picture' => $request->file('picture'));
                $rules = array('picture' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,doc,xlsx,pptx,xls');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = str_slug($request->name, '_') . "" . uniqid() . '.' . $request->file('picture')->guessExtension();
                    $next_of_kin->picture = $fname;
                    $request->file('picture')->move(public_path() . '/uploads',
                        $fname);
                }

            }
            $next_of_kin->save();
            GeneralHelper::audit_trail("Update", "Clients", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_next_of_kin(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.next_of_kin.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $next_of_kin = ClientNextOfKin::find($id);
        if (!empty($next_of_kin->picture)) {
            @unlink(public_path() . '/uploads/' . $next_of_kin->picture);
        }
        ClientNextOfKin::destroy($id);
        GeneralHelper::audit_trail("Update", "Clients", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_next_of_kin($next_of_kin)
    {
        if (!Sentinel::hasAccess('clients.next_of_kin.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('client.show_next_of_kin', compact('next_of_kin'))->render();

    }

    public function edit_next_of_kin($next_of_kin)
    {
        if (!Sentinel::hasAccess('clients.next_of_kin.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('client.edit_next_of_kin', compact('next_of_kin'))->render();

    }

    public function update_next_of_kin(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.next_of_kin.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $next_of_kin = ClientNextOfKin::find($id);
            $next_of_kin->client_relationship_id = $request->client_relationship_id;
            $next_of_kin->first_name = $request->first_name;
            $next_of_kin->middle_name = $request->middle_name;
            $next_of_kin->last_name = $request->last_name;
            $next_of_kin->address = $request->address;
            $next_of_kin->mobile = $request->mobile;
            $next_of_kin->gender = $request->gender;
            $next_of_kin->notes = $request->notes;
            if ($request->hasFile('picture')) {
                $file = array('picture' => $request->file('picture'));
                $rules = array('picture' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,doc,xlsx,pptx,xls');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = str_slug($request->name, '_') . "" . uniqid() . '.' . $request->file('picture')->guessExtension();
                    $next_of_kin->picture = $fname;
                    $request->file('picture')->move(public_path() . '/uploads',
                        $fname);
                }

            }
            $next_of_kin->save();
            GeneralHelper::audit_trail("Update", "Clients", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    //client notes
    public function store_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.notes.create')) {
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
            $note->type = "client";
            $note->notes = $request->notes;
            $note->save();
            GeneralHelper::audit_trail("Update", "Clients", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.notes.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        Note::destroy($id);
        GeneralHelper::audit_trail("Update", "Clients", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_note($note)
    {
        if (!Sentinel::hasAccess('clients.notes.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('client.show_note', compact('note'))->render();

    }

    public function edit_note($note)
    {
        if (!Sentinel::hasAccess('clients.notes.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('client.edit_note', compact('note'))->render();

    }

    public function update_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.notes.update')) {
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
            GeneralHelper::audit_trail("Update", "Clients", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function add_user(Request $request, $client)
    {
        if (!Sentinel::hasAccess('clients.notes.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $client_user = new ClientUser();
        $client_user->created_by_id = Sentinel::getUser()->id;
        $client_user->client_id = $client->id;
        if ($request->existing_user == 1) {
            if (ClientUser::where('user_id', $request->user_id)->where('client_id', $client->id)->count() > 0) {
                Flash::warning("User already added");
                return redirect()->back();
            }
            $client_user->user_id = $request->user_id;
        } else {
            //create new login details
            $rules = array(
                'email' => 'required|unique:users',
                'password' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $credentials = [
                    'email' => $request->email,
                    'password' => $request->password,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'address' => $client->address,
                    'gender' => $client->gender,
                    'phone' => $client->mobile,
                ];
                $user = Sentinel::registerAndActivate($credentials);
                $role = Sentinel::findRoleBySlug('client');
                $role->users()->attach($user->id);
                $client_user->user_id = $user->id;
                if ($request->send_login_details == 1) {
                    //send login details
                    Mail::to($user->email)->send(new SendLoginDetailsEmail($request->first_name . " " . $request->last_name, $request->email, $request->password));
                }
            }
        }

        $id = $client_user->save();
        GeneralHelper::audit_trail("Update", "Clients", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();

    }

    public function delete_user(Request $request, $id)
    {
        if (!Sentinel::hasAccess('clients.notes.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        ClientUser::destroy($id);
        GeneralHelper::audit_trail("Update", "Clients", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }
}
