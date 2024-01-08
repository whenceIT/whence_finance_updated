<?php

namespace App\Http\Controllers;


use App\Events\SavingsApproved;
use App\Events\SavingsTransactionCreated;
use App\Events\SavingsTransactionUpdated;
use App\Events\TransactionUpdated;
use App\Helpers\GeneralHelper;
use App\Mail\RepaymentScheduleEmail;
use App\Mail\SavingsStatementEmail;
use App\Models\Charge;
use App\Models\Client;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Document;
use App\Models\GlJournalEntry;
use App\Models\Savings;
use App\Models\SavingsCharge;
use App\Models\SavingsTransaction;
use App\Models\Note;
use App\Models\PaymentDetail;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;

class SavingsController extends Controller
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
        if (!Sentinel::hasAccess('savings.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Savings::where('status', 'approved')->get();

        return view('savings.data', compact('data'));
    }

    public function pending_approval()
    {
        if (!Sentinel::hasAccess('savings.pending_approval')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Savings::where('status', 'pending')->get();

        return view('savings.pending_approval', compact('data'));
    }


    public function savings_declined()
    {
        if (!Sentinel::hasAccess('savings.declined')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Savings::where('status', 'declined')->get();

        return view('savings.savings_declined', compact('data'));
    }


    public function savings_closed()
    {
        if (!Sentinel::hasAccess('savings.closed')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = Savings::where('status', 'closed')->get();

        return view('savings.savings_closed', compact('data'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('savings.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $clients = Client::where('status', 'active')->get();

        return view('savings.create');
    }

    public function create_client_savings($client, $savings_product)
    {
        if (!Sentinel::hasAccess('savings.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($client) || empty($savings_product)) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back();
        }

        return view('savings.create_client_savings',
            compact('client', 'savings_product'));
    }

    public function create_group_savings($group, $savings_product)
    {
        if (!Sentinel::hasAccess('savings.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($group) || empty($savings_product)) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back();
        }

        return view('savings.create_group_savings',
            compact('group', 'savings_product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store_client_savings(Request $request, $client, $savings_product)
    {
        if (!Sentinel::hasAccess('savings.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'field_officer_id' => 'required',
            'created_date' => 'required',
        );
        $messages = [
            'field_officer_id.required' => 'Savings Officer is required',
            'created_date.required' => 'Created date is required',
            'savings_term_type.required' => 'Savings term is required',
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
            $savings = new Savings();
            $savings->created_by_id = Sentinel::getUser()->id;
            $savings->created_date = $request->created_date;
            $savings->client_type = "client";
            $savings->savings_product_id = $savings_product->id;
            $savings->client_id = $client->id;
            $savings->office_id = $client->office_id;
            $savings->external_id = $request->external_id;
            $savings->decimals = $savings_product->decimals;
            $savings->field_officer_id = $request->field_officer_id;
            $savings->currency_id = $savings_product->currency_id;
            $savings->interest_rate = $request->interest_rate;
            $savings->minimum_balance = $savings_product->minimum_balance;
            $savings->overdraft_limit = $savings_product->overdraft_limit;
            $savings->interest_compounding_period = $savings_product->interest_compounding_period;
            $savings->interest_posting_period = $savings_product->interest_posting_period;
            $savings->allow_transfer_withdrawal_fee = $savings_product->allow_transfer_withdrawal_fee;
            $savings->opening_balance = $savings_product->opening_balance;
            $savings->allow_additional_charges = $savings_product->allow_additional_charges;
            $savings->year_days = $savings_product->year_days;
            $date = explode('-', $request->created_date);
            $savings->month = $date[1];
            $savings->year = $date[0];
            $savings->save();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $savings_charge = new SavingsCharge();
                    $savings_charge->savings_id = $savings->id;
                    $savings_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $savings_charge->amount = $request->charge_amount[$key];
                    } else {
                        $savings_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $savings_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $savings_charge->charge_type = $charge->charge_type;
                    $savings_charge->charge_option = $charge->charge_option;
                    $savings_charge->save();
                }
            }
            //check custom fields
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'savings')->get();
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
                    $custom_field->parent_id = $savings->id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "savings";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Create", "Savings", $savings->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/' . $savings->id . '/show');
        }
    }

    public function store_group_savings(Request $request, $group, $savings_product)
    {
        if (!Sentinel::hasAccess('savings.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'field_officer_id' => 'required',
            'created_date' => 'required',
        );
        $messages = [
            'field_officer_id.required' => 'Savings Officer is required',
            'created_date.required' => 'Created date is required',
            'savings_term_type.required' => 'Savings term is required',
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
            $savings = new Savings();
            $savings->created_by_id = Sentinel::getUser()->id;
            $savings->created_date = $request->created_date;
            $savings->client_type = "group";
            $savings->savings_product_id = $savings_product->id;
            $savings->group_id = $group->id;
            $savings->office_id = $group->office_id;
            $savings->external_id = $request->external_id;
            $savings->decimals = $savings_product->decimals;
            $savings->field_officer_id = $request->field_officer_id;
            $savings->currency_id = $savings_product->currency_id;
            $savings->interest_rate = $request->interest_rate;
            $savings->minimum_balance = $savings_product->minimum_balance;
            $savings->overdraft_limit = $savings_product->overdraft_limit;
            $savings->interest_compounding_period = $savings_product->interest_compounding_period;
            $savings->interest_posting_period = $savings_product->interest_posting_period;
            $savings->allow_transfer_withdrawal_fee = $savings_product->allow_transfer_withdrawal_fee;
            $savings->opening_balance = $savings_product->opening_balance;
            $savings->allow_additional_charges = $savings_product->allow_additional_charges;
            $savings->year_days = $savings_product->year_days;
            $date = explode('-', $request->created_date);
            $savings->month = $date[1];
            $savings->year = $date[0];
            $savings->save();

            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $savings_charge = new SavingsCharge();
                    $savings_charge->savings_id = $savings->id;
                    $savings_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $savings_charge->amount = $request->charge_amount[$key];
                    } else {
                        $savings_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $savings_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $savings_charge->charge_type = $charge->charge_type;
                    $savings_charge->charge_option = $charge->charge_option;
                    $savings_charge->save();
                }
            }
            //check custom fields
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'savings')->get();
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
                    $custom_field->parent_id = $savings->id;
                    $custom_field->custom_field_id = $key->id;
                    $custom_field->category = "savings";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Create", "Savings", $savings->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/' . $savings->id . '/show');
        }
    }


    public function show($savings)
    {
        if (!Sentinel::hasAccess('savings.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        return view('savings.show', compact('savings', 'charges'));
    }


    public function edit($savings)
    {
        if (!Sentinel::hasAccess('savings.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (empty($savings->savings_product)) {
            Flash::warning("Savings Product not found");
            return redirect()->back();
        }
        if ($savings->client_type == "client") {
            return view('savings.edit_client_savings',
                compact('savings'));
        }
        if ($savings->client_type == "group") {
            return view('savings.edit_group_savings',
                compact('savings'));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update_client_savings(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'field_officer_id' => 'required',
            'created_date' => 'required',
        );
        $messages = [
            'field_officer_id.required' => 'Savings Officer is required',
            'created_date.required' => 'Created date is required',
            'savings_term_type.required' => 'Savings term is required',
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
            $savings = Savings::find($id);
            $savings->created_date = $request->created_date;
            $savings->external_id = $request->external_id;
            $savings->interest_rate = $request->interest_rate;
            $savings->field_officer_id = $request->field_officer_id;
            $date = explode('-', $request->created_date);
            $savings->month = $date[1];
            $savings->year = $date[0];
            $savings->save();
            SavingsCharge::where('savings_id', $savings->id)->delete();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $savings_charge = new SavingsCharge();
                    $savings_charge->savings_id = $savings->id;
                    $savings_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $savings_charge->amount = $request->charge_amount[$key];
                    } else {
                        $savings_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $savings_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $savings_charge->charge_type = $charge->charge_type;
                    $savings_charge->charge_option = $charge->charge_option;
                    $savings_charge->save();
                }
            }
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'savings')->get();
                foreach ($custom_fields as $key) {
                    if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                        'savings')->first())
                    ) {
                        $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                            $id)->where('category', 'savings')->first();
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
                    $custom_field->category = "savings";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Update", "Savings", $savings->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/' . $savings->id . '/show');
        }
    }

    public function update_group_savings(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'field_officer_id' => 'required',
            'created_date' => 'required',
        );
        $messages = [
            'field_officer_id.required' => 'Savings Officer is required',
            'created_date.required' => 'Created date is required',
            'savings_term_type.required' => 'Savings term is required',
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
            $savings = Savings::find($id);
            $savings->created_date = $request->created_date;
            $savings->external_id = $request->external_id;
            $savings->interest_rate = $request->interest_rate;
            $savings->field_officer_id = $request->field_officer_id;
            $date = explode('-', $request->created_date);
            $date = explode('-', $request->created_date);
            $savings->month = $date[1];
            $savings->year = $date[0];
            $savings->save();

            SavingsCharge::where('savings_id', $savings->id)->delete();
            if (!empty($request->charges)) {
                //loop through the array
                foreach ($request->charges as $key) {
                    $charge = Charge::find($key);
                    $savings_charge = new SavingsCharge();
                    $savings_charge->savings_id = $savings->id;
                    $savings_charge->charge_id = $key;
                    if ($charge->override == 1) {
                        $savings_charge->amount = $request->charge_amount[$key];
                    } else {
                        $savings_charge->amount = $charge->amount;
                    }
                    if ($charge->charge_type == "specified_due_date") {
                        $savings_charge->due_date = $request->charge_date[$key];
                    } else {

                    }
                    $savings_charge->charge_type = $charge->charge_type;
                    $savings_charge->charge_option = $charge->charge_option;
                    $savings_charge->save();
                }
            }
            if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
                $custom_fields = CustomField::where('category', 'savings')->get();
                foreach ($custom_fields as $key) {
                    if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                        'savings')->first())
                    ) {
                        $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                            $id)->where('category', 'savings')->first();
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
                    $custom_field->category = "savings";
                    $custom_field->save();
                }
            }
            GeneralHelper::audit_trail("Update", "Savings", $savings->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/' . $savings->id . '/show');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('savings.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        Savings::destroy($id);
        SavingsCharge::where('savings_id', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Savings", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('savings/product/data');
    }

    //client documents
    public function store_savings_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.documents.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('savings.create')) {
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
            $savings_document = new Document();
            $savings_document->record_id = $id;
            $savings_document->type = "savings";
            $savings_document->name = $request->name;
            $savings_document->notes = $request->notes;
            if ($request->hasFile('attachment')) {
                $file = array('attachment' => $request->file('attachment'));
                $rules = array('attachment' => 'required|mimes:jpeg,jpg,bmp,png,pdf,docx,doc,xlsx,pptx,xls');
                $validator = Validator::make($file, $rules);
                if ($validator->fails()) {
                    Flash::warning(trans('general.validation_error'));
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $fname = str_slug($request->name, '_') . "" . uniqid() . '.' . $request->file('attachment')->guessExtension();
                    $savings_document->location = $fname;
                    $request->file('attachment')->move(public_path() . '/uploads',
                        $fname);
                }

            }
            $savings_document->save();
            GeneralHelper::audit_trail("Update", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_savings_document(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.documents.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        if (!Sentinel::hasAccess('savings.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $savings_document = Document::find($id);
        if (!empty($savings_document->location)) {
            @unlink(public_path() . '/uploads/' . $savings_document->location);
        }
        Document::destroy($id);
        GeneralHelper::audit_trail("Update", "Savings", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    //client notes
    public function store_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.notes.create')) {
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
            $note->type = "savings";
            $note->notes = $request->notes;
            $note->save();
            GeneralHelper::audit_trail("Update", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function delete_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.notes.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        Note::destroy($id);
        GeneralHelper::audit_trail("Update", "Savings", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();

    }

    public function show_note($note)
    {
        if (!Sentinel::hasAccess('savings.notes.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('savings.show_note', compact('note'))->render();

    }

    public function edit_note($note)
    {
        if (!Sentinel::hasAccess('savings.notes.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return View::make('savings.edit_note', compact('note'))->render();

    }

    public function update_note(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.notes.update')) {
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
            GeneralHelper::audit_trail("Update", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }


    public function approve_savings(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.approve')) {
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
            $savings = Savings::find($id);
            if ($savings->status != "pending") {
                Flash::warning("Savings not pending");
                return redirect()->back();
            }
            $savings->status = "approved";
            $savings->approved_by_id = Sentinel::getUser()->id;
            $savings->approved_date = $request->approved_date;
            $savings->approved_notes = $request->approved_notes;
            $savings->approved_notes = $request->approved_notes;
            $savings->start_interest_calculation_date = GeneralHelper::determine_next_interest_calculation_date($savings->savings_product_id);
            $savings->next_interest_calculation_date = GeneralHelper::determine_next_interest_calculation_date($savings->savings_product_id);
            $savings->next_interest_posting_date = GeneralHelper::determine_next_interest_posting_date($savings->savings_product_id);
            $savings->save();
            //check for charges
            //check for  fees
            $fees_disbursement = 0;
            $fees_installment = 0;
            $fees_due_date = [];
            $fees_due_date_amount = 0;
            foreach ($savings->charges as $key) {
                if (!empty($key->charge)) {
                    if ($key->charge->charge_type == "savings_activation") {
                        $amount = $key->charge->amount;
                        $savings_transaction = new SavingsTransaction();
                        $savings_transaction->created_by_id = Sentinel::getUser()->id;
                        $savings_transaction->office_id = $savings->office_id;
                        $savings_transaction->savings_id = $savings->id;
                        $savings_transaction->transaction_type = "bank_fees";
                        $savings_transaction->reversible = 1;
                        $savings_transaction->date = date("Y-m-d");
                        $savings_transaction->time = date("H:i");
                        $date = explode('-', date("Y-m-d"));
                        $savings_transaction->year = $date[0];
                        $savings_transaction->month = $date[1];
                        $savings_transaction->debit = $amount;
                        $savings_transaction->save();
                        if (!empty($savings->savings_product->gl_account_income_fee)) {
                            $journal = new GlJournalEntry();
                            $journal->created_by_id = Sentinel::getUser()->id;
                            $journal->gl_account_id = $savings->savings_product->gl_account_income_fee->id;
                            $journal->office_id = $savings_transaction->office_id;
                            $journal->date = date("Y-m-d");
                            $journal->year = $date[0];
                            $journal->month = $date[1];
                            $journal->transaction_type = 'pay_charge';
                            $journal->name = "Charge";
                            $journal->savings_transaction_id = $savings->id;
                            $journal->credit = $amount;
                            $journal->reference = $savings_transaction->id;
                            $journal->save();
                        }
                        if (!empty($savings->savings_product->gl_account_overdraft_portfolio)) {
                            $journal = new GlJournalEntry();
                            $journal->created_by_id = Sentinel::getUser()->id;
                            $journal->gl_account_id = $savings->savings_product->gl_account_overdraft_portfolio->id;
                            $journal->office_id = $savings_transaction->office_id;
                            $journal->date = date("Y-m-d");
                            $journal->year = $date[0];
                            $journal->month = $date[1];
                            $journal->transaction_type = 'pay_charge';
                            $journal->name = "Charge";
                            $journal->savings_transaction_id = $savings->id;
                            $journal->debit = $amount;
                            $journal->reference = $savings_transaction->id;
                            $journal->save();
                        }
                    }

                    if ($key->charge->charge_type == "specified_due_date") {
                        $amount = $key->charge->amount;
                        $savings_transaction = new SavingsTransaction();
                        $savings_transaction->created_by_id = Sentinel::getUser()->id;
                        $savings_transaction->office_id = $savings->office_id;
                        $savings_transaction->savings_id = $savings->id;
                        $savings_transaction->transaction_type = "bank_fees";
                        $savings_transaction->reversible = 1;
                        $savings_transaction->date = date("Y-m-d");
                        $savings_transaction->time = date("H:i");
                        $date = explode('-', date("Y-m-d"));
                        $savings_transaction->year = $date[0];
                        $savings_transaction->month = $date[1];
                        $savings_transaction->debit = $amount;
                        $savings_transaction->save();
                        if (!empty($savings->savings_product->gl_account_income_fee)) {
                            $journal = new GlJournalEntry();
                            $journal->created_by_id = Sentinel::getUser()->id;
                            $journal->gl_account_id = $savings->savings_product->gl_account_income_fee->id;
                            $journal->office_id = $savings_transaction->office_id;
                            $journal->date = date("Y-m-d");
                            $journal->year = $date[0];
                            $journal->month = $date[1];
                            $journal->transaction_type = 'pay_charge';
                            $journal->name = "Charge";
                            $journal->savings_transaction_id = $savings_transaction->id;
                            $journal->savings_id = $savings->id;
                            $journal->credit = $amount;
                            $journal->reference = $savings_transaction->id;
                            $journal->save();
                        }
                        if (!empty($savings->savings_product->gl_account_overdraft_portfolio)) {
                            $journal = new GlJournalEntry();
                            $journal->created_by_id = Sentinel::getUser()->id;
                            $journal->gl_account_id = $savings->savings_product->gl_account_overdraft_portfolio->id;
                            $journal->office_id = $savings_transaction->office_id;
                            $journal->date = date("Y-m-d");
                            $journal->year = $date[0];
                            $journal->month = $date[1];
                            $journal->transaction_type = 'pay_charge';
                            $journal->name = "Charge";
                            $journal->savings_transaction_id = $savings_transaction->id;
                            $journal->savings_id = $savings->id;
                            $journal->debit = $amount;
                            $journal->reference = $savings_transaction->id;
                            $journal->save();
                        }
                    }
                }
            }

            event(new SavingsApproved($savings));
            GeneralHelper::audit_trail("Approve", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function decline_savings(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.approve')) {
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
            $savings = Savings::find($id);
            if ($savings->status != "pending") {
                Flash::warning("Savings not pending");
                return redirect()->back();
            }
            $savings->status = "declined";
            $savings->declined_by_id = Sentinel::getUser()->id;
            $savings->declined_date = date("Y-m-d");
            $savings->declined_notes = $request->declined_notes;
            $savings->save();
            GeneralHelper::audit_trail("Decline", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function change_savings_officer(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $rules = array(
            'field_officer_id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $savings = Savings::find($id);
            $savings->field_officer_id = $request->field_officer_id;
            $savings->save();
            GeneralHelper::audit_trail("Update", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function unapprove_savings(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.undo_approval')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        $savings = Savings::find($id);
        if ($savings->status != "approved") {
            Flash::warning("Savings not approved");
            return redirect()->back();
        }
        $savings->status = "pending";
        $savings->approved_by_id = null;
        $savings->approved_date = null;
        $savings->approved_notes = null;
        $savings->start_interest_calculation_date = null;
        $savings->next_interest_calculation_date = null;
        $savings->next_interest_posting_date = null;
        $savings->save();
        SavingsTransaction::where('savings_id', $savings->id)->delete();
        GlJournalEntry::where('savings_id', $savings->id)->delete();
        GeneralHelper::audit_trail("Undo Approval", "Savings", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }


    //deposits
    public function create_deposit($id)
    {
        if (!Sentinel::hasAccess('savings.transactions.deposit')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('savings.deposit.create', compact('id'));
    }

    public function store_deposit(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.transactions.deposit')) {
            Flash::warning("Permission Denied");
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
            $savings = Savings::find($id);

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
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->created_by_id = Sentinel::getUser()->id;
            $savings_transaction->office_id = $savings->office_id;
            $savings_transaction->savings_id = $savings->id;
            $savings_transaction->reversible = 1;
            $savings_transaction->payment_detail_id = $payment_detail->id;
            $savings_transaction->transaction_type = "deposit";
            $savings_transaction->date = $request->date;
            $date = explode('-', $request->date);
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->credit = $request->amount;
            $savings_transaction->notes = $request->notes;
            $savings_transaction->save();

            if (!empty($savings->savings_product->gl_account_savings_reference)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_savings_reference->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($savings->savings_product->gl_account_savings_control)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_savings_control->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }

            event(new SavingsTransactionCreated($savings_transaction));

            GeneralHelper::audit_trail("Create Deposit", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/' . $savings->id . '/show');
        }
    }

    public function edit_deposit($savings_transaction)
    {
        if (!Sentinel::hasAccess('savings.transactions.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('savings.deposit.edit', compact('savings_transaction'));
    }

    public function update_deposit(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.transactions.update')) {
            Flash::warning("Permission Denied");
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
            //reverse transaction
            $savings_transaction = SavingsTransaction::find($id);
            $savings_transaction->reversible = 0;
            $savings_transaction->reversed = 1;
            $savings_transaction->reversal_type = "user";
            $savings_transaction->debit = $savings_transaction->credit;
            $savings_transaction->save();
            //reverse journal entries
            foreach (GlJournalEntry::where('savings_transaction_id', $id)->where('savings_id',
                $savings_transaction->savings_id)->where('transaction_type', 'deposit')->get() as $key) {
                $journal = GlJournalEntry::find($key->id);
                if ($key->debit > $key->credit) {
                    $journal->credit = $journal->debit;
                } else {
                    $journal->debit = $journal->credit;
                }
                $journal->reversed = 1;
                $journal->save();
            }
            $savings = $savings_transaction->savings;

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
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->created_by_id = Sentinel::getUser()->id;
            $savings_transaction->office_id = $savings->office_id;
            $savings_transaction->savings_id = $savings->id;
            $savings_transaction->reversible = 1;
            $savings_transaction->payment_detail_id = $payment_detail->id;
            $savings_transaction->transaction_type = "deposit";
            $savings_transaction->date = $request->date;
            $date = explode('-', $request->date);
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->credit = $request->amount;
            $savings_transaction->notes = $request->notes;
            $savings_transaction->save();


            if (!empty($savings->savings_product->gl_account_savings_reference)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_savings_reference->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($savings->savings_product->gl_account_savings_control)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_savings_control->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'deposit';
                $journal->name = "Deposit";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }

            event(new SavingsTransactionUpdated($savings_transaction));
            GeneralHelper::audit_trail("Update Deposit", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/' . $savings->id . '/show');
        }
    }

    public function reverse_deposit(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //reverse transaction
        $savings_transaction = SavingsTransaction::find($id);
        $savings_transaction->reversible = 0;
        $savings_transaction->reversed = 1;
        $savings_transaction->reversal_type = "user";
        $savings_transaction->debit = $savings_transaction->credit;
        $savings_transaction->save();
        //reverse journal entries
        foreach (GlJournalEntry::where('savings_transaction_id', $id)->where('savings_id',
            $savings_transaction->savings_id)->where('transaction_type', 'deposit')->get() as $key) {
            $journal = GlJournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        $savings = $savings_transaction->savings;

        event(new SavingsTransactionUpdated($savings_transaction));

        GeneralHelper::audit_trail("Reverse Deposit", "Savings", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //withdrawals
    public function create_withdrawal($id)
    {
        if (!Sentinel::hasAccess('savings.transactions.withdrawal')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('savings.withdrawal.create', compact('id'));
    }

    public function store_withdrawal(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.transactions.withdrawal')) {
            Flash::warning("Permission Denied");
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

            $savings = Savings::find($id);
            if (GeneralHelper::savings_account_balance($id) < $request->amount && $savings->allow_overdraft == 0) {
                Flash::warning("Insufficient Balance");
                return redirect()->back();
            }
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
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->created_by_id = Sentinel::getUser()->id;
            $savings_transaction->office_id = $savings->office_id;
            $savings_transaction->savings_id = $savings->id;
            $savings_transaction->reversible = 1;
            $savings_transaction->payment_detail_id = $payment_detail->id;
            $savings_transaction->transaction_type = "withdrawal";
            $savings_transaction->date = $request->date;
            $date = explode('-', $request->date);
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->debit = $request->amount;
            $savings_transaction->notes = $request->notes;
            $savings_transaction->save();

            if (!empty($savings->savings_product->gl_account_savings_control)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_savings_control->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'withdrawal';
                $journal->name = "Withdrawal";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($savings->savings_product->gl_account_savings_reference)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_savings_reference->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'withdrawal';
                $journal->name = "Withdrawal";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }

            event(new SavingsTransactionCreated($savings_transaction));

            GeneralHelper::audit_trail("Create Withdrawal", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/' . $savings->id . '/show');
        }
    }

    public function edit_withdrawal($savings_transaction)
    {
        if (!Sentinel::hasAccess('savings.transactions.update')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('savings.withdrawal.edit', compact('savings_transaction'));
    }

    public function update_withdrawal(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.transactions.update')) {
            Flash::warning("Permission Denied");
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
            //reverse transaction
            $savings_transaction = SavingsTransaction::find($id);
            $savings_transaction->reversible = 0;
            $savings_transaction->reversed = 1;
            $savings_transaction->reversal_type = "user";
            $savings_transaction->debit = $savings_transaction->credit;
            $savings_transaction->save();
            //reverse journal entries
            foreach (GlJournalEntry::where('savings_transaction_id', $id)->where('savings_id',
                $savings_transaction->savings_id)->where('transaction_type', 'withdrawal')->get() as $key) {
                $journal = GlJournalEntry::find($key->id);
                if ($key->debit > $key->credit) {
                    $journal->credit = $journal->debit;
                } else {
                    $journal->debit = $journal->credit;
                }
                $journal->reversed = 1;
                $journal->save();
            }
            $savings = $savings_transaction->savings;

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
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->created_by_id = Sentinel::getUser()->id;
            $savings_transaction->office_id = $savings->office_id;
            $savings_transaction->savings_id = $savings->id;
            $savings_transaction->reversible = 1;
            $savings_transaction->payment_detail_id = $payment_detail->id;
            $savings_transaction->transaction_type = "withdrawal";
            $savings_transaction->date = $request->date;
            $date = explode('-', $request->date);
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->debit = $request->amount;
            $savings_transaction->notes = $request->notes;
            $savings_transaction->save();


            if (!empty($savings->savings_product->gl_account_savings_control)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_savings_reference->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'withdrawal';
                $journal->name = "Withdrawal";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->debit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($savings->savings_product->gl_account_savings_reference)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_savings_control->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = date("Y-m-d");
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'withdrawal';
                $journal->name = "Withdrawal";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->credit = $request->amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }

            event(new SavingsTransactionUpdated($savings_transaction));
            GeneralHelper::audit_trail("Create Withdrawal", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect('savings/' . $savings->id . '/show');
        }
    }

    public function reverse_withdrawal(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //reverse transaction
        $savings_transaction = SavingsTransaction::find($id);
        $savings_transaction->reversible = 0;
        $savings_transaction->reversed = 1;
        $savings_transaction->reversal_type = "user";
        $savings_transaction->credit = $savings_transaction->debit;
        $savings_transaction->save();
        //reverse journal entries
        foreach (GlJournalEntry::where('savings_transaction_id', $id)->where('savings_id',
            $savings_transaction->savings_id)->where('transaction_type', 'withdrawal')->get() as $key) {
            $journal = GlJournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        $savings = $savings_transaction->savings;

        event(new SavingsTransactionUpdated($savings_transaction));

        GeneralHelper::audit_trail("Reverse Withdrawal", "Savings", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //transaction
    public function show_transaction($savings_transaction)
    {
        if (!Sentinel::hasAccess('savings.transactions.view')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('savings.transaction.show', compact('savings_transaction'));
    }

    public function print_transaction($savings_transaction)
    {
        if (!Sentinel::hasAccess('savings.transactions.view')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }

        return view('savings.transaction.print', compact('savings_transaction'));
    }

    public function pdf_transaction($savings_transaction)
    {
        if (!Sentinel::hasAccess('savings.transactions.view')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $pdf = PDF::loadView('savings.transaction.pdf', compact('savings_transaction'));
        return $pdf->download(trans_choice('general.savings', 1) . ' ' . trans_choice('general.transaction', 1) . ' ' . trans_choice('general.receipt', 1) . ".pdf");

    }

    public function withdraw_savings(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.approve')) {
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
            $savings = Savings::find($id);
            if ($savings->status != "pending") {
                Flash::warning("Savings not pending");
                return redirect()->back();
            }
            $savings->status = "withdrawn";
            $savings->withdrawn_by_id = Sentinel::getUser()->id;
            $savings->withdrawn_date = date("Y-m-d");
            $savings->withdrawn_notes = $request->withdrawn_notes;
            $savings->save();
            //GeneralHelper::audit_trail("Added client  with id:" . $client->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    public function pdf_statement(Request $request, $savings)
    {
        if (!Sentinel::hasAccess('savings.pdf_statement')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $pdf = PDF::loadView('savings.pdf_statement', compact('savings', 'start_date', 'end_date'));
        return $pdf->download(trans_choice('general.savings', 1) . ' ' . trans_choice('general.statement', 1) . ".pdf");

    }

    public function print_statement(Request $request, $savings)
    {
        if (!Sentinel::hasAccess('savings.pdf_statement')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        return view('savings.print_statement', compact('savings', 'start_date', 'end_date'));
    }

    public function email_statement(Request $request, $savings)
    {
        if (!Sentinel::hasAccess('savings.email_statement')) {
            Flash::warning(trans('general.permission_denied'));
            return redirect()->back();
        }
        $email = "";
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if ($savings->client_type == "client") {
            $email = $savings->client->email;
        }
        if ($savings->client_type == "group") {
            $email = $savings->group->email;
        }
        if (!empty($email)) {
            Mail::to($email)->send(new SavingsStatementEmail($savings, $start_date, $end_date));
        } else {
            Flash::warning("Client has no email");
        }

        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    //waive charge
    public function waive_transaction(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        //reverse transaction
        $savings_transaction = SavingsTransaction::find($id);
        $savings_transaction->reversible = 0;
        $savings_transaction->reversed = 1;
        $savings_transaction->reversal_type = "user";
        $savings_transaction->credit = $savings_transaction->debit;
        $savings_transaction->save();
        //reverse journal entries
        foreach (GlJournalEntry::where('savings_transaction_id', $id)->where('savings_id',
            $savings_transaction->savings_id)->get() as $key) {
            $journal = GlJournalEntry::find($key->id);
            if ($key->debit > $key->credit) {
                $journal->credit = $journal->debit;
            } else {
                $journal->debit = $journal->credit;
            }
            $journal->reversed = 1;
            $journal->save();
        }
        $savings = $savings_transaction->savings;
        if ($savings_transaction->transaction_type == "installment_fee") {
            $amount = $savings_transaction->debit / SavingsRepaymentSchedule::where('savings_id', $savings_transaction->savings_id)->count();
            foreach (SavingsRepaymentSchedule::where('savings_id', $savings_transaction->savings_id)->get() as $key) {
                $schedule = SavingsRepaymentSchedule::find($key->id);
                $schedule->fees = $schedule->fees - $amount;
                $schedule->save();
            }
            event(new TransactionUpdated($savings_transaction));
        }
        if ($savings_transaction->transaction_type == "specified_due_date_fee") {
            $schedule = SavingsRepaymentSchedule::where("due_date", $savings_transaction->date)->where("savings_id", $savings->id)->first();
            $schedule->fees = $schedule->fees - $savings_transaction->debit;
            $schedule->save();
            event(new TransactionUpdated($savings_transaction));
        }

        if (GeneralHelper::savings_total_balance($savings->id) >= 0) {
            $savings = Savings::find($savings->id);
            $savings->status = "disbursed";
            $savings->save();
        }
        GeneralHelper::audit_trail("Waive Transaction", "Savings", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function waive_interest(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.transactions.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $savings = Savings::find($id);
        if ($request->date > date("Y-m-d")) {
            Flash::warning(trans_choice('general.future_date_error', 1));
            return redirect()->back()->withInput();
        }
        if ($request->date < $savings->disbursement_date) {
            Flash::warning(trans_choice('general.early_date_error', 1));
            return redirect()->back()->withInput();
        }
        $savings_transaction = new SavingsTransaction();
        $savings_transaction->created_by_id = Sentinel::getUser()->id;
        $savings_transaction->office_id = $savings->office_id;
        $savings_transaction->savings_id = $savings->id;
        $savings_transaction->reversible = 0;
        $savings_transaction->transaction_type = "interest_waiver";
        $savings_transaction->date = $request->date;
        $savings_transaction->reversible = 0;
        $date = explode('-', $request->date);
        $savings_transaction->year = $date[0];
        $savings_transaction->month = $date[1];
        $savings_transaction->credit = $request->amount;
        $savings_transaction->notes = $request->notes;
        $savings_transaction->save();
        $amount = $request->amount;
        foreach (SavingsRepaymentSchedule::select('id', DB::raw("(COALESCE(interest,0)-COALESCE(interest_waived,0)-COALESCE(interest_written_off,0)-COALESCE(interest_paid,0)) as interest_due"))->where('savings_id', $savings->id)->orderBy('due_date', 'asc')->havingRaw("interest_due>0")->get() as $key) {
            if ($amount > 0) {
                if ($amount >= $key->interest_due) {
                    $schedule = SavingsRepaymentSchedule::find($key->id);
                    $schedule->interest_waived = $schedule->interest_waived + $key->interest_due;
                    $schedule->save();
                    $amount = $amount - $key->interest_due;

                } else {
                    $schedule = SavingsRepaymentSchedule::find($key->id);
                    $schedule->interest_waived = $schedule->interest_waived + $amount;
                    $schedule->save();
                    $amount = 0;
                    break;
                }

            }
            if ($amount <= 0) {
                break;
            }

        }
        event(new TransactionUpdated($savings_transaction));
        if (GeneralHelper::savings_total_balance($savings->id) >= 0) {
            $savings->status = "disbursed";
            $savings->save();
        }
        GeneralHelper::audit_trail("Waive Intereset", "Savings", $id);
        Flash::success(trans('general.successfully_saved'));
        return redirect()->back();
    }

    public function store_charge(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.charge.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $savings = Savings::find($id);
        $rules = array(
            'date' => 'required|after_or_equal:' . $savings->approved_date,
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
            $amount = $request->amount;
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->created_by_id = Sentinel::getUser()->id;
            $savings_transaction->office_id = $savings->office_id;
            $savings_transaction->savings_id = $savings->id;
            $savings_transaction->transaction_type = "bank_fees";
            $savings_transaction->reversible = 1;
            $savings_transaction->date = $request->date;
            $savings_transaction->time = date("H:i");
            $date = explode('-', $request->date);
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->debit = $amount;
            $savings_transaction->save();
            if (!empty($savings->savings_product->gl_account_income_fee)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_income_fee->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'pay_charge';
                $journal->name = "Charge";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->credit = $amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }
            if (!empty($savings->savings_product->gl_account_savings_reference)) {
                $journal = new GlJournalEntry();
                $journal->created_by_id = Sentinel::getUser()->id;
                $journal->gl_account_id = $savings->savings_product->gl_account_savings_reference->id;
                $journal->office_id = $savings_transaction->office_id;
                $journal->date = $request->date;
                $journal->year = $date[0];
                $journal->month = $date[1];
                $journal->transaction_type = 'pay_charge';
                $journal->name = "Charge";
                $journal->savings_transaction_id = $savings_transaction->id;
                $journal->savings_id = $savings->id;
                $journal->debit = $amount;
                $journal->reference = $savings_transaction->id;
                $journal->save();
            }


            //GeneralHelper::audit_trail("Added client  with id:" . $client->id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }

    //write off savings
    public function write_off_savings(Request $request, $id)
    {
        if (!Sentinel::hasAccess('savings.update')) {
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
            $savings = Savings::find($id);
            if ($savings->status != "disbursed") {
                Flash::warning("Savings not disbursed");
                return redirect()->back();
            }
            $savings->status = "written_off";
            $savings->written_off_by_id = Sentinel::getUser()->id;
            $savings->written_off_date = date("Y-m-d");
            $savings->written_off_notes = $request->withdrawn_notes;
            $savings->save();
            $savings_allocation = GeneralHelper::savings_items($savings->id);
            $principal = $savings_allocation["principal"] - $savings_allocation["principal_paid"] - $savings_allocation["principal_waived"] - $savings_allocation["principal_written_off"];
            $interest = $savings_allocation["interest"] - $savings_allocation["interest_paid"] - $savings_allocation["interest_waived"] - $savings_allocation["interest_written_off"];
            $fees = $savings_allocation["fees"] - $savings_allocation["fees_paid"] - $savings_allocation["fees_waived"] - $savings_allocation["fees_written_off"];
            $penalty = $savings_allocation["penalty"] - $savings_allocation["penalty_paid"] - $savings_allocation["penalty_waived"] - $savings_allocation["penalty_written_off"];
            $savings_transaction = new SavingsTransaction();
            $savings_transaction->created_by_id = Sentinel::getUser()->id;
            $savings_transaction->office_id = $savings->office_id;
            $savings_transaction->savings_id = $savings->id;
            $savings_transaction->reversible = 0;
            $savings_transaction->transaction_type = "write_off";
            $savings_transaction->date = $request->written_off_date;
            $date = explode('-', $request->written_off_date);
            $savings_transaction->year = $date[0];
            $savings_transaction->month = $date[1];
            $savings_transaction->credit = $principal + $interest + $penalty + $fees;
            $savings_transaction->notes = $request->notes;
            $savings_transaction->save();
            //update journals
            $savings_product = $savings->savings_product;
            if ($savings_product->accounting_rule != "none") {
                if ($principal > 0) {
                    if (!empty($savings_product->gl_account_savings_portfolio)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $savings->office_id;
                        $journal->currency_id = $savings->currency_id;
                        $journal->gl_account_id = $savings->savings_product->gl_account_savings_portfolio->id;
                        $journal->date = $request->written_off_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'write_off';
                        $journal->name = "Principal Written Off";
                        $journal->savings_id = $savings->id;
                        $journal->credit = $principal;
                        $journal->reference = $savings->id;
                        $journal->savings_transaction_id = $savings_transaction->id;
                        $journal->save();
                    }
                    if (!empty($savings_product->gl_account_savings_written_off)) {
                        $journal = new GlJournalEntry();
                        $journal->created_by_id = Sentinel::getUser()->id;
                        $journal->office_id = $savings->office_id;
                        $journal->currency_id = $savings->currency_id;
                        $journal->gl_account_id = $savings->savings_product->gl_account_savings_written_off->id;
                        $journal->date = $request->written_off_date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'write_off';
                        $journal->name = "Savings Written Off";
                        $journal->savings_id = $savings->id;
                        $journal->debit = $principal;
                        $journal->reference = $savings->id;
                        $journal->savings_transaction_id = $savings_transaction->id;
                        $journal->save();
                    }
                }
            }
            GeneralHelper::audit_trail("Writeoff", "Savings", $id);
            Flash::success(trans('general.successfully_saved'));
            return redirect()->back();
        }
    }
}
