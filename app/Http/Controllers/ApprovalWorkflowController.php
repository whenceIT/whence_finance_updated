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
        $query = Deposit::withoutGlobalScope('approved')->select([
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
            'u.first_name AS bank_deposit_log_user_first_name',
            'u.last_name AS bank_deposit_log_user_last_name',
            'o.name AS office_name',
        ])
        ->from('deposits AS d')
        ->leftJoin('deposit_types AS dt', 'd.deposit_type', '=', 'dt.id')
        ->leftJoin('bank_deposit_log AS bdl', function ($join) {
            $join->on('bdl.deposit_type', '=', 'd.deposit_type')
                 ->whereColumn('bdl.office_id', 'd.office');
        })
        ->leftJoin('users AS u', 'u.id', '=', 'bdl.user_id')
        ->leftJoin('offices AS o', 'o.id', '=', 'd.office')
        ->whereRaw("DATE_FORMAT(bdl.created_date, '%Y-%m') = DATE_FORMAT(d.date, '%Y-%m')");

        if ($request->filled('deposit_type') && $request->deposit_type !== 'all') {
            $query->where('d.deposit_type', $request->deposit_type);
        }

        if ($request->filled('office_id') && $request->office_id !== 'all') {
            $query->where('d.office', $request->office_id);
        }
        $query->where('d.status', '!=', 1); // Only show pending/declined
        $deposits = $query->orderBy('d.date', 'desc')->paginate(50);
        $depositTypes = DepositType::orderBy('sort_order')->get();

        return view('approvals.deposit_approvals', compact('deposits', 'depositTypes'));
    }

    public function approveDecline($id, $status)
    {
        Deposit::withoutGlobalScope('approved')->where('id', $id)->update(['status' => $status]);
        return response()->json(['success' => true, 'message' => ucfirst($status == 1 ? 'Approved' : 'Declined') . ' successfully.']);
    }

    public function bulkApprove(Request $request)
    {
        $ids = $request->input('ids');
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No deposits selected.']);
        }
        
        Deposit::withoutGlobalScope('approved')->whereIn('id', $ids)->update(['status' => 1]);
        
        return response()->json(['success' => true, 'message' => count($ids) . ' deposit(s) approved successfully.']);
    }

    public function approveAll(Request $request)
    {
        $query = Deposit::withoutGlobalScope('approved')
            ->select(['d.*'])
            ->from('deposits AS d');

        if ($request->filled('deposit_type') && $request->deposit_type !== 'all') {
            $query->where('d.deposit_type', $request->deposit_type);
        }

        if ($request->filled('office_id') && $request->office_id !== 'all') {
            $query->where('d.office', $request->office_id);
        }

        $count = $query->count();
        $query->update(['status' => 1]);
        
        return response()->json(['success' => true, 'message' => $count . ' deposit(s) approved successfully.']);
    }
}