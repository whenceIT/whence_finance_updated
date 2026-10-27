<?php
namespace App\Services;
use App\Models\Workstream2PayrollRun;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class Workstream2PayrollIntegrationService {
 public function createLinkedExpense(Workstream2PayrollRun $run, $userId) {
  if (!Schema::hasTable('ws1_expenses') || $run->ws1_expense_id) return $run->ws1_expense_id;
  $id=DB::table('ws1_expenses')->insertGetId([
   'office_id'=>$run->office_id,'created_by_id'=>$userId,'expense_type_id'=>null,'gl_account_id'=>null,
   'name'=>'Payroll '.$run->payroll_number,'amount'=>$run->total_net,'date'=>$run->payment_date ?: $run->period_end,
   'notes'=>'Generated from Workstream 2 payroll run #'.$run->id.'. Approve through Workstream 1 before payment.',
   'status'=>'pending','created_at'=>now(),'updated_at'=>now()
  ]);
  $run->ws1_expense_id=$id; $run->save(); return $id;
 }
}
