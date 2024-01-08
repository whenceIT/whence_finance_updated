<?php

namespace App\Http\Controllers;

use Aloha\Twilio\Twilio;
use App\Helpers\BulkSms;
use App\Helpers\GeneralHelper;
use App\Models\Borrower;

use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\GlJournalEntry;
use App\Models\OtherIncome;
use App\Models\OtherIncomeType;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Clickatell\Api\ClickatellHttp;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

class OtherIncomeController extends Controller
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
        if (!Sentinel::hasAccess('other_income')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $data = OtherIncome::all();

        return view('other_income.data', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('other_income.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('other_income.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('other_income.create')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $other_income = new OtherIncome();
        $other_income->created_by_id = Sentinel::getUser()->id;
        $other_income->other_income_type_id = $request->other_income_type_id;
        $other_income->office_id = $request->office_id;
        $other_income->amount = $request->amount;
        $other_income->notes = $request->notes;
        $other_income->name = $request->name;
        $other_income->date = $request->date;
        $date = explode('-', $request->date);
        $other_income->year = $date[0];
        $other_income->month = $date[1];
        $other_income->status = "approved";
        $other_income->save();
        //check custom fields
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'other_income')->get();
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
                $custom_field->parent_id = $other_income->id;
                $custom_field->custom_field_id = $key->id;
                $custom_field->category = "other_income";
                $custom_field->save();
            }
        }
        //debit and credit the necessary accounts
        if (!empty($other_income->type->gl_account_asset)) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->office_id = $other_income->office_id;
            $journal->gl_account_id = $other_income->type->gl_account_asset_id;
            $journal->date = $other_income->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'other_income';
            $journal->name = "Other income";
            $journal->debit = $other_income->amount;
            $journal->reference = $other_income->id;
            $journal->save();
        }
        if (!empty($other_income->type->gl_account_income)) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->office_id = $other_income->office_id;
            $journal->gl_account_id = $other_income->type->gl_account_income_id;
            $journal->date = $other_income->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'other_income';
            $journal->name = "Other income";
            $journal->credit = $other_income->amount;
            $journal->reference = $other_income->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        GeneralHelper::audit_trail("Create", "Other Income", $other_income->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('other_income/data');
    }


    public function show($other_income)
    {
        if (!Sentinel::hasAccess('other_income.view')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $users = User::all();

        return view('other_income.show', compact('other_income'));
    }


    public function edit($other_income)
    {
        if (!Sentinel::hasAccess('other_income.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('other_income.edit', compact('other_income'));
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
        if (!Sentinel::hasAccess('other_income.update')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $other_income = OtherIncome::find($id);
        $other_income->office_id = $request->office_id;
        $other_income->amount = $request->amount;
        $other_income->notes = $request->notes;
        $other_income->name = $request->name;
        $other_income->date = $request->date;
        $date = explode('-', $request->date);
        $other_income->year = $date[0];
        $other_income->month = $date[1];
        $other_income->status = "approved";
        $other_income->save();
        if (Setting::where('setting_key', 'enable_custom_fields')->first()->setting_value == 1) {
            $custom_fields = CustomField::where('category', 'other_income')->get();
            foreach ($custom_fields as $key) {
                if (!empty(CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id', $id)->where('category',
                    'other_income')->first())
                ) {
                    $custom_field = CustomFieldMeta::where('custom_field_id', $key->id)->where('parent_id',
                        $id)->where('category', 'other_income')->first();
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
                $custom_field->category = "other_income";
                $custom_field->save();
            }
        }
        GlJournalEntry::where('transaction_type', 'other_income')->where('reference', $id)->delete();
        //debit and credit the necessary accounts
        if (!empty($other_income->type->gl_account_asset)) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->office_id = $other_income->office_id;
            $journal->gl_account_id = $other_income->type->gl_account_asset_id;
            $journal->date = $other_income->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'other_income';
            $journal->name = "Other income";
            $journal->debit = $other_income->amount;
            $journal->reference = $other_income->id;
            $journal->save();
        }
        if (!empty($other_income->type->gl_account_income)) {
            $journal = new GlJournalEntry();
            $journal->created_by_id = Sentinel::getUser()->id;
            $journal->office_id = $other_income->office_id;
            $journal->gl_account_id = $other_income->type->gl_account_income_id;
            $journal->date = $other_income->date;
            $journal->year = $date[0];
            $journal->month = $date[1];
            $journal->transaction_type = 'other_income';
            $journal->name = "Other income";
            $journal->credit = $other_income->amount;
            $journal->reference = $other_income->id;
            $journal->save();
        } else {
            //alert admin that no account has been set
        }
        GeneralHelper::audit_trail("Update", "Other Income", $other_income->id);
        Flash::success(trans('general.successfully_saved'));
        return redirect('other_income/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('other_income.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        OtherIncome::destroy($id);
        GlJournalEntry::where('transaction_type', 'other_income')->where('reference', $id)->delete();
        GeneralHelper::audit_trail("Delete", "Other Income", $id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect()->back();
    }

    public function deleteFile(Request $request, $id)
    {
        if (!Sentinel::hasAccess('other_income.delete')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }
        $other_income = OtherIncome::find($id);
        $files = unserialize($other_income->files);
        @unlink(public_path() . '/uploads/' . $files[$request->id]);
        $files = array_except($files, [$request->id]);
        $other_income->files = serialize($files);
        $other_income->save();


    }


}
