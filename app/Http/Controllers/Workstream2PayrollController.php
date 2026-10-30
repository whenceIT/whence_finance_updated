<?php

namespace App\Http\Controllers;

use App\Models\Workstream2PayrollEmployee;
use App\Models\Workstream2SalaryProfile;
use App\Models\Workstream2PayrollComponent;
use App\Models\Workstream2PayrollRun;
use App\Models\Workstream2PayrollItem;
use App\Models\Workstream2PayrollApproval;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Workstream2PayrollController extends Controller
{
    private function permit($p)
    {
        if (!Sentinel::hasAccess($p)) abort(403, 'Permission denied');
    }
    private function uid()
    {
        $u = Sentinel::getUser();
        if (!$u) abort(401);
        return $u->id;
    }
    private function employeeQuery()
    {
        $q = Workstream2PayrollEmployee::query();
        if (!Sentinel::hasAccess('workstream2.payroll.view_all')) {
            $u = Sentinel::getUser();
            if (!$u || empty($u->office_id)) abort(403, 'User has no office assigned');
            $q->where('office_id', $u->office_id);
        }
        return $q;
    }

    public function index()
    {
       // $this->permit('workstream2.payroll.view');
        $runs = Workstream2PayrollRun::orderBy('id', 'desc')->paginate(20);
        return view('workstream2.payroll.index', compact('runs'));
    }
    public function dashboard()
    {
   
        $runs = Workstream2PayrollRun::orderBy('id', 'desc')->limit(10)->get();
        $employees = $this->employeeQuery()->where('status', 'active')->count();
        $pending = Workstream2PayrollRun::where('status', 'pending_approval')->count();
        return view('workstream2.payroll.dashboard', compact('runs', 'employees', 'pending'));
    }
    public function employees()
    {
        
        $employees = $this->employeeQuery()->with('currentSalary', 'office')->orderBy('last_name')->paginate(30);
        return view('workstream2.payroll.employees.index', compact('employees'));
    }
    public function createEmployee()
    {
        $offices = \App\Office::orderBy('name')->get();
        return view('workstream2.payroll.employees.create', compact('offices'));
    }
    public function storeEmployee(Request $r)
    {
        
        $d = $r->validate(['office_id' => 'required|integer', 'employee_code' => 'required|string|max:60|unique:ws2_payroll_employees,employee_code', 'first_name' => 'required|string|max:100', 'last_name' => 'required|string|max:100', 'email' => 'nullable|email|max:191', 'phone' => 'nullable|string|max:60', 'employment_type' => 'required|in:permanent,contract,temporary,casual', 'start_date' => 'nullable|date']);
        $d['status'] = 'active';
        $d['created_by_id'] = $this->uid();
        $e = Workstream2PayrollEmployee::create($d);
        return redirect('workstream2/payroll/employees/' . $e->id . '/salary')->with('success', 'Employee created. Add salary before payroll.');
    }
    public function salaryProfile($id)
    {
      
        $employee = Workstream2PayrollEmployee::with('salaryProfiles')->findOrFail($id);
        return view('workstream2.payroll.employees.salary', compact('employee'));
    }
    public function saveSalaryProfile(Request $r, $id)
    {
       
        $e = Workstream2PayrollEmployee::findOrFail($id);
        $d = $r->validate(['basic_salary' => 'required|numeric|min:0', 'currency' => 'required|size:3', 'effective_from' => 'required|date', 'notes' => 'nullable|string|max:2000']);
        DB::transaction(function () use ($e, $d) {
            Workstream2SalaryProfile::where('employee_id', $e->id)->where('is_current', 1)->update(['is_current' => 0, 'effective_to' => date('Y-m-d', strtotime($d['effective_from'] . ' -1 day'))]);
            Workstream2SalaryProfile::create(array_merge($d, ['employee_id' => $e->id, 'is_current' => 1, 'created_by_id' => $this->uid()]));
        });
        return redirect('workstream2/payroll/employees')->with('success', 'Salary profile saved.');
    }
    public function employeeComponents($id)
    {
       
        $employee = Workstream2PayrollEmployee::with('employeeComponents.component')->findOrFail($id);
        $components = Workstream2PayrollComponent::where('active', 1)->orderBy('name')->get();
        return view('workstream2.payroll.employees.components', compact('employee', 'components'));
    }
    public function saveEmployeeComponent(Request $r, $id)
    {
       
        $employee = Workstream2PayrollEmployee::findOrFail($id);
        $d = $r->validate(['component_id' => 'required|integer|exists:ws2_payroll_components,id', 'amount' => 'required|numeric|min:0', 'effective_from' => 'required|date', 'effective_to' => 'nullable|date|after_or_equal:effective_from']);
        $d['employee_id'] = $employee->id;
        $d['is_active'] = 1;
        $d['created_by_id'] = $this->uid();
        \App\Models\Workstream2EmployeeComponent::create($d);
        return redirect('workstream2/payroll/employees/' . $employee->id . '/components')->with('success', 'Employee component assigned.');
    }
    public function components()
    {
       
        $components = Workstream2PayrollComponent::orderBy('component_type')->orderBy('name')->paginate(30);
        return view('workstream2.payroll.components', compact('components'));
    }
    public function storeComponent(Request $r)
    {
       
        $d = $r->validate(['name' => 'required|string|max:120', 'component_type' => 'required|in:earning,deduction', 'calculation_type' => 'required|in:fixed,percentage', 'default_amount' => 'required|numeric|min:0']);
        $d['taxable'] = $r->has('taxable');
        $d['pensionable'] = $r->has('pensionable');
        $d['active'] = 1;
        $d['created_by_id'] = $this->uid();
        Workstream2PayrollComponent::create($d);
        return redirect('workstream2/payroll/components')->with('success', 'Component created.');
    }
    public function createRun()
    {
        $offices = Office::orderBy('name')->get();
        return view('workstream2.payroll.runs.create', compact('offices'));
    }
    public function storeRun(Request $r)
    {
        
        $d = $r->validate(['period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start', 'payment_date' => 'nullable|date', 'office_id' => 'required|integer', 'notes' => 'nullable|string|max:2000']);
        $u = Sentinel::getUser();
        if (!Sentinel::hasAccess('workstream2.payroll.view_all')) {
            if (empty($u->office_id)) abort(403, 'User has no office assigned');
            $d['office_id'] = $u->office_id;
        }
        $run = DB::transaction(function () use ($d, $u) {
            $run = Workstream2PayrollRun::create(array_merge($d, ['payroll_number' => 'PR-' . date('YmdHis') . '-' . mt_rand(100, 999), 'status' => 'draft', 'created_by_id' => $u->id]));
            $emps = Workstream2PayrollEmployee::where('status', 'active')->when($run->office_id, function ($q) use ($run) {
                $q->where('office_id', $run->office_id);
            })->with(['currentSalary', 'employeeComponents.component'])->get();
            foreach ($emps as $e) {
                if (!$e->currentSalary) continue;
                $basic = (float)$e->currentSalary->basic_salary;
                $earn = 0;
                $ded = 0;
                $comps = [];
                foreach ($e->employeeComponents as $ec) {
                    if (!$ec->component || !$ec->component->active) continue;
                    $amt = (float)$ec->amount;
                    if ($ec->component->calculation_type === 'percentage') $amt = $basic * $amt / 100;
                    if ($ec->component->component_type === 'earning') $earn += $amt;
                    else $ded += $amt;
                    $comps[] = ['component_id' => $ec->component_id, 'component_name_snapshot' => $ec->component->name, 'component_type' => $ec->component->component_type, 'amount' => round($amt, 2), 'created_at' => now(), 'updated_at' => now()];
                }
                $gross = $basic + $earn;
                $net = $gross - $ded;
                if ($net < 0) throw new \RuntimeException('Net pay cannot be negative for ' . $e->employee_code);
                $item = Workstream2PayrollItem::create(['payroll_run_id' => $run->id, 'employee_id' => $e->id, 'office_id' => $e->office_id, 'employee_code_snapshot' => $e->employee_code, 'employee_name_snapshot' => $e->first_name . ' ' . $e->last_name, 'basic_salary' => $basic, 'earnings_total' => $earn, 'deductions_total' => $ded, 'gross_pay' => $gross, 'net_pay' => $net, 'payment_status' => 'unpaid', 'paid_amount' => 0]);
                foreach ($comps as $c) {
                    $c['payroll_item_id'] = $item->id;
                    DB::table('ws2_payroll_item_components')->insert($c);
                }
            }
            $run->total_gross = $run->items()->sum('gross_pay');
            $run->total_deductions = $run->items()->sum('deductions_total');
            $run->total_net = $run->items()->sum('net_pay');
            $run->save();
            if (!$run->items()->count()) throw new \RuntimeException('No active employees with current salary profiles in this scope.');
            return $run;
        });
        return redirect('workstream2/payroll/runs/' . $run->id)->with('success', 'Draft payroll created. Review before submitting.');
    }
    public function showRun($id)
    {
       
        $run = Workstream2PayrollRun::with(['items.payments', 'approvals'])->findOrFail($id);
        if (!Sentinel::hasAccess('workstream2.payroll.view_all')) {
            $u = Sentinel::getUser();
            if (empty($u->office_id) || ($run->office_id && (int)$run->office_id !== (int)$u->office_id)) abort(403);
            if (!$run->office_id && $run->items()->where('office_id', '!=', $u->office_id)->exists()) abort(403);
        }
        return view('workstream2.payroll.runs.show', compact('run'));
    }
    public function submitRun($id)
    {
       
        $run = Workstream2PayrollRun::findOrFail($id);
        if ($run->status !== 'draft') abort(422, 'Only draft payroll can be submitted.');
        $run->status = 'pending_approval';
        $run->save();
        Workstream2PayrollApproval::create(['payroll_run_id' => $run->id, 'action' => 'submitted', 'acted_by_id' => $this->uid(), 'acted_at' => now()]);
        return redirect('workstream2/payroll/runs/' . $id)->with('success', 'Payroll submitted for approval.');
    }
}
