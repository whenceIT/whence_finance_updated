<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\Collateral;
use App\Models\CollateralStatusChangeRequest;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class CollateralApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function queue()
    {
        // if (!Sentinel::hasAccess('collateral.approve')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $requests = CollateralStatusChangeRequest::with(['collateral.loan', 'requested_by'])
            ->where('approval_status', 'pending')
            ->orderBy('request_date', 'desc')
            ->get();

        return view('collateral.approval_queue', compact('requests'));
    }

    public function requestChange(Request $request, Collateral $collateral)
    {
        // if (!Sentinel::hasAccess('collateral.update')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $request->validate([
            'new_status' => 'required',
            'reason'     => 'required|string',
            'sold_price' => 'nullable|numeric|min:0',
            'penalty'    => 'nullable|numeric|min:0',
        ]);

        if ($request->new_status === $collateral->status) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['new_status' => 'The selected status must be different from the current status.']);
        }

        CollateralStatusChangeRequest::create([
            'collateral_id'   => $collateral->id,
            'requested_by_id' => Sentinel::getUser()->id,
            'old_status'      => $collateral->status,
            'new_status'      => $request->new_status,
            'reason'          => $request->reason,
            'sold_price'      => $request->sold_price ?? 0,
            'penalty'         => $request->penalty ?? 0,
            'approval_status' => 'pending',
            'request_date'    => Carbon::now(),
        ]);

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'status_change_requested',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Status change request submitted successfully.');
        return redirect()->route('collateral.show', $collateral);
    }

    public function approve(Request $request, CollateralStatusChangeRequest $collateral_status_change_request)
    {
        // if (!Sentinel::hasAccess('collateral.approve')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $collateral = $collateral_status_change_request->collateral;
        if (!$collateral) {
            Flash::warning('Collateral item not found');
            return redirect()->back();
        }

        $collateral->status = $collateral_status_change_request->new_status;
        if ($collateral_status_change_request->new_status === 'sold') {
            $collateral->sold_price = $collateral_status_change_request->sold_price;
            $collateral->penalty = $collateral_status_change_request->penalty;
            $collateral->date_resold = Carbon::now();
        }
        $collateral->save();

        $collateral_status_change_request->update([
            'approval_status' => 'approved',
            'approved_by_id'  => Sentinel::getUser()->id,
            'approval_date'   => Carbon::now(),
        ]);

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'status_change_approved',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Status change request approved successfully.');
        return redirect()->route('collateral.approvals.queue');
    }

    public function reject(Request $request, CollateralStatusChangeRequest $collateral_status_change_request)
    {
        // if (!Sentinel::hasAccess('collateral.approve')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $collateral_status_change_request->update([
            'approval_status' => 'rejected',
            'approved_by_id'  => Sentinel::getUser()->id,
            'approval_date'   => Carbon::now(),
        ]);

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'status_change_rejected',
            'table_name' => 'collateral',
            'record_id'  => $collateral_status_change_request->collateral_id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Status change request rejected.');
        return redirect()->route('collateral.approvals.queue');
    }

    public function directChange(Request $request, Collateral $collateral)
    {
        // if (!Sentinel::hasAccess('collateral.approve')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $request->validate([
            'new_status' => 'required',
        ]);

        $collateral->status = $request->new_status;
        $collateral->save();

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'status_change_approved',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Collateral status updated successfully.');
        return redirect()->route('collateral.show', $collateral);
    }
}
