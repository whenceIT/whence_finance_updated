<?php
namespace App\Http\Controllers;
use App\Models\Workstream2PayrollRun;
use App\Models\Workstream2PayrollApproval;
use App\Services\Workstream2PayrollIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
class Workstream2PayrollApprovalController extends Controller {
 public function index(){if(!Sentinel::hasAccess('workstream2.payroll.approve'))abort(403);$runs=Workstream2PayrollRun::where('status','pending_approval')->orderBy('id')->paginate(30);return view('workstream2.payroll.approvals',compact('runs'));}
 public function decide(Request $r,$id){if(!Sentinel::hasAccess('workstream2.payroll.approve'))abort(403);$d=$r->validate(['decision'=>'required|in:approved,declined','comments'=>'nullable|string|max:2000']);$u=Sentinel::getUser();$run=Workstream2PayrollRun::findOrFail($id);if($run->status!=='pending_approval')abort(422,'Not awaiting approval.');if((int)$run->created_by_id===(int)$u->id)abort(403,'You cannot approve a payroll you created.');DB::transaction(function()use($d,$u,$run){$run->status=$d['decision'];if($d['decision']==='approved'){$run->approved_by_id=$u->id;$run->approved_at=now();}$run->save();Workstream2PayrollApproval::create(['payroll_run_id'=>$run->id,'action'=>$d['decision'],'acted_by_id'=>$u->id,'comments'=>$d['comments']??null,'acted_at'=>now()]);});if($d['decision']==='approved')app(Workstream2PayrollIntegrationService::class)->createLinkedExpense($run,$u->id);return redirect('workstream2/payroll/approvals')->with('success','Payroll decision recorded.');}
}
