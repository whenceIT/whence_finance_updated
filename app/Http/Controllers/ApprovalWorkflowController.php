<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\DepositType;
use Illuminate\Http\Request;

class ApprovalWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function depositApprovals(Request $request)
    {
        $deposits = Deposit::withoutGlobalScope('approved')
            ->with(['depositTypeInfo', 'bankDepositLog.user', 'office'])
            ->whereNull('status')
            ->get();

        $deposits = new \Illuminate\Pagination\LengthAwarePaginator(
            $deposits,
            $deposits->count(),
            15,
            1,
            ['path' => $request->url()]
        );

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