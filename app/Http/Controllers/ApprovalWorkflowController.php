<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\DepositType;
use App\Models\BankDepositLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function depositApprovals(Request $request)
    {
        $query = Deposit::select([
            'd.*',
            'dt.name AS deposit_type_name',
            'dt.monthly_amount',
            'dt.bank',
            'dt.gl_account',
            'bdl.id AS bank_deposit_log_id',
            'bdl.user_id AS bank_deposit_log_user_id',
            'bdl.amount AS bank_deposit_log_amount',
            'bdl.deposit_method AS bank_deposit_log_method',
            'bdl.reference_number AS bank_deposit_log_reference_number',
            'bdl.created_date AS bank_deposit_log_created_date',
        ])
        ->from('deposits AS d')
        ->leftJoin('deposit_types AS dt', 'd.deposit_type', '=', 'dt.id')
        ->leftJoin('bank_deposit_log AS bdl', function ($join) {
            $join->on('bdl.deposit_type', '=', 'd.deposit_type')
                 ->whereColumn('bdl.office_id', 'd.office');
        })
        ->whereRaw("DATE_FORMAT(bdl.created_date, '%Y-%m') = DATE_FORMAT(d.date, '%Y-%m')");

        if ($request->filled('deposit_type') && $request->deposit_type !== 'all') {
            $query->where('d.deposit_type', $request->deposit_type);
        }

        if ($request->filled('office_id') && $request->office_id !== 'all') {
            $query->where('d.office', $request->office_id);
        }

        $deposits = $query->orderBy('d.date', 'desc')->paginate(50);
        $depositTypes = DepositType::orderBy('sort_order')->get();

        return view('approvals.deposit_approvals', compact('deposits', 'depositTypes'));
    }

    public function approveDecline($id, $action)
    {
        $status = $action === 'approve' ? 1 : 0;
        Deposit::where('id', $id)->update(['status' => $status]);
        return back()->with('success', ucfirst($action) . 'd successfully.');
    }
}