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
use App\Models\CarryOver;
use App\Models\ClientTransferLog;
use stdClass;
use Carbon\Carbon;
use App\Models\AuditLogs;


class HRController extends Controller{
     public function __construct()
    {

        $this->middleware('sentinel');
    }

    public function employees(Request $request)
    {
        $search = trim($request->get('search'));

         $employees = User::with(['office', 'role'])
        ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                // Search by first name
                $q->where('first_name', 'like', "%{$search}%")

                // Search by last name
                ->orWhere('last_name', 'like', "%{$search}%")

                // Search full name (first + last)
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])

                // Other fields
                ->orWhere('gender', 'like', "%{$search}%")
                // ->orWhere('employment_status', 'like', "%{$search}%")

                // Office search
                ->orWhereHas('office', function ($officeQuery) use ($search) {
                    $officeQuery->where('name', 'like', "%{$search}%");
                });

                // // Role search
                // ->orWhereHas('role', function ($roleQuery) use ($search) {
                //     $roleQuery->where('name', 'like', "%{$search}%");
                // });

            });

        })
        ->orderBy('first_name')
        ->paginate(12)
        ->appends(['search' => $search]); // keeps search in pagination

        return view('hr.employees',compact('employees','search'));
    }


       public function employee($id)
    {
        $employee = User::with([
            'office',
            // 'role',
            // 'performances',
            // 'payrolls',
            // 'leaves',
            // 'advances'
        ])->findOrFail($id);

        return view('hr.employee', compact('employee'));
    }


}