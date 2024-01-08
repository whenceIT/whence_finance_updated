<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Mail\SendLoginDetailsEmail;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Group;

use App\Models\GroupClient;
use App\Models\Country;
use App\Models\Document;
use App\Models\GroupUser;
use App\Models\Loan;
use App\Models\Note;
use App\Models\Office;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class GroupController extends Controller
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
        if (!Sentinel::hasAccess('groups')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Group::where('status', 'active')->get();

        return view('group.data', compact('data'));
    }

    public function pending_approval()
    {
        if (!Sentinel::hasAccess('groups.pending_approval')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Group::where('status', 'pending')->get();

        return view('group.pending_approval', compact('data'));
    }

    public function declined()
    {
        if (!Sentinel::hasAccess('groups.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Group::where('status', 'declined')->get();

        return view('group.declined', compact('data'));
    }

    public function closed()
    {
        if (!Sentinel::hasAccess('groups.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Group::where('status', 'closed')->get();

        return view('group.closed', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('groups.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        //get custom fields
        //$custom_fields = CustomField::where('category', 'groups')->get();
        return view('group.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('groups.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $group = new Group();

        $group->created_by_id = Sentinel::getUser()->id;
        $group->staff_id = $request->staff_id;
        $group->office_id = $request->office_id;
        $group->external_id = $request->external_id;
        $group->mobile = $request->mobile;
        $group->phone = $request->phone;
        $group->email = $request->email;
        $group->name = $request->name;
        $group->street = $request->street;
        $group->address = $request->address;
        $group->joined_date = $request->joined_date;
        $group->notes = $request->notes;
        $group->save();
        $office = Office::find($request->office_id);
        $group->account_no = $office->external_id . '-' . $group->id;
        $group->save();
        if (!empty($request->clients)) {
            foreach ($request->clients as $key) {
                $group_client = new GroupClient();
                $group_client->group_id = $group->id;
                $group_client->client_id = $key;
                $group_client->save();
            }
        }
        //check custom fields
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'groups')->get();
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
                $custom_field->parent_id = $group->id;
                $custom_field->custom_field_id = $key->id;
                $custom_field->category = "groups";
                $custom_field->save();
            }
        }
        GeneralHelper::audit_trail("Create", "Groups", $group->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('group/' . $group->id . '/show');
    }


    public function show($group)
    {
        if (!Sentinel::hasAccess('groups.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //get custom fields
        //$custom_fields = CustomFieldMeta::where('category', 'groups')->where('parent_id', $group->id)->get();
        return view('group.show', compact('group',));//'user', 'custom_fields'));
    }


    public function edit($group)
    {
        if (!Sentinel::hasAccess('groups.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }


        return view('group.edit', compact('group'));//'user', 'custom_fields', 'countries'));
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
        if (!Sentinel::hasAccess('groups.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $group = Group::find($id);
        $group->staff_id = $request->staff_id;
        $group->mobile = $request->mobile;
        $group->phone = $request->phone;
        $group->email = $request->email;
        $group->name = $request->name;
        $group->street = $request->street;
        $group->address = $request->address;
        $group->joined_date = $request->joined_date;
        $group->notes = $request->notes;
        $group->save();
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'groups')->get();
            foreach ($custom_fields as $key) {
                if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                    'groups')->first())
                ) {
                    $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                        $id)->where('category', 'groups')->first();
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
                $custom_field->category = "groups";
                $custom_field->save();
            }
        }
        GeneralHelper::audit_trail("Update", "Groups", $group->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('group/data');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('groups.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Group::destroy($id);
        Loan::where('group_id', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Groups", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('group/data');
    }

    public function picture(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $group = Group::find($id);
        if (!empty($group->picture)) {
            @unlink(public_path() . '/uploads/' . $group->picture);
        }
        if ($request->hasFile('picture')) {
            $file = array('picture' => $request->file('picture'));
            $rules = array('picture' => 'required|mimes:jpeg,jpg,bmp,png');
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {
                Flash::warning(trans('general.validation_error'));
                return redirect()->back()->withInput()->withErrors($validator);
            } else {
                $fname = str_slug($group->account_no, '_') . "" . uniqid() . '.' . $request->file('picture')->guessExtension();
                $group->picture = $fname;
                $request->file('picture')->move(public_path() . '/uploads',
                    $fname);
            }

        }
        $group->save();
        GeneralHelper::audit_trail("Update", "Groups", $group->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();

    }


    public function approve(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $group = Group::find($id);
        $group->status = "active";
        $group->activated_date = $request->activated_date;
        $group->activated_by_id = Sentinel::getUser()->id;
        $group->save();
        GeneralHelper::audit_trail("Approve", "Groups", $group->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function decline(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.approve')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $group = Group::find($id);
        $group->status = "declined";
        $group->declined_date = $request->declined_date;
        $group->declined_by_id = Sentinel::getUser()->id;
        $group->save();
        GeneralHelper::audit_trail("Decline", "Groups", $group->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function blacklist(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.blacklist')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $group = Group::find($id);
        $group->blacklisted = 1;
        $group->save();
        GeneralHelper::audit_trail("Blacklisted borrower  with id:" . $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function unBlacklist(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.blacklist')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $group = Group::find($id);
        $group->blacklisted = 0;
        $group->save();
        GeneralHelper::audit_trail("Undo Blacklist for borrower  with id:" . $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //group identification
    public function store_group_client(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.client.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'client_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            if (GroupClient::where('group_id', $id)->where('client_id', $request->client_id)->count() > 0) {
                Flash::warning("Client already added");
                return redirect()->back()->withInput()->withErrors($validator);
            }
            $group_client = new GroupClient();
            $group_client->group_id = $id;
            $group_client->client_id = $request->client_id;
            $group_client->save();
            GeneralHelper::audit_trail("Update", "Groups", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_group_client(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.client.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        GroupClient::destroy($id);
        GeneralHelper::audit_trail("Update", "Groups", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    //group documents
    public function store_group_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.documents.create')) {
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
            $group_document = new Document();
            $group_document->record_id = $id;
            $group_document->type = "group";
            $group_document->name = $request->name;
            $group_document->notes = $request->notes;
            if ($request->hasFile('attachment')) {
                $file = array('attachment' => $request->file('attachment'));
                $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,doc,xlsx,pptx,xls');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = str_slug($request->name, '_') . "" . uniqid() . '.' . $request->file('attachment')->guessExtension();
                    $group_document->location = $fname;
                    $request->file('attachment')->move(public_path() . '/uploads',
                        $fname);
                }

            }
            $group_document->save();
            GeneralHelper::audit_trail("Update", "Groups", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_group_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.documents.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $group_document = Document::find($id);
        if (!empty($group_document->location)) {
            @unlink(public_path() . '/uploads/' . $group_document->location);
        }
        Document::destroy($id);
        GeneralHelper::audit_trail("Update", "Groups", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    //group notes
    public function store_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.notes.create')) {
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
            $note->type = "group";
            $note->notes = $request->notes;
            $note->save();
            GeneralHelper::audit_trail("Update", "Groups", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.notes.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        Note::destroy($id);
        GeneralHelper::audit_trail("Update", "Groups", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_note($note)
    {
        if (!Sentinel::hasAccess('groups.notes.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('group.show_note', compact('note'))->render();

    }

    public function edit_note($note)
    {
        if (!Sentinel::hasAccess('groups.notes.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('group.edit_note', compact('note'))->render();

    }

    public function update_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.notes.update')) {
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
            GeneralHelper::audit_trail("Update", "Groups", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }
    public function add_user(Request $request, $group)
    {
        if (!Sentinel::hasAccess('groups.notes.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $group_user = new GroupUser();
        $group_user->created_by_id = Sentinel::getUser()->id;
        $group_user->client_id = $group->id;
        if ($request->existing_user == 1) {
            if (GroupUser::where('user_id', $request->user_id)->where('group_id', $group->id)->count() > 0) {
                Flash::warning("User already added");
                return redirect()->back();
            }
            $group_user->user_id = $request->user_id;
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
                    'address' => $group->address,
                    'gender' => $group->gender,
                    'phone' => $group->mobile,
                ];
                $user = Sentinel::registerAndActivate($credentials);
                $role = Sentinel::findRoleBySlug('client');
                $role->users()->attach($user->id);
                $group_user->user_id = $user->id;
                if ($request->send_login_details == 1) {
                    //send login details
                    Mail::to($user->email)->send(new SendLoginDetailsEmail($request->first_name . " " . $request->last_name, $request->email, $request->password));
                }
            }
        }
        $group_user->save();
        GeneralHelper::audit_trail("Update", "Groups", $group->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();

    }

    public function delete_user(Request $request, $id)
    {
        if (!Sentinel::hasAccess('groups.notes.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        GroupUser::destroy($id);
        GeneralHelper::audit_trail("Update", "Groups", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }
}
