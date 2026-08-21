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

        $loanBalances = [];
        foreach ($requests as $req) {
            if ($req->collateral && $req->collateral->loan) {
                $loanBalances[$req->collateral->loan->id] = \App\Helpers\GeneralHelper::new_new_loan_total_balance($req->collateral->loan->id);
            }
        }

        $newCollaterals = Collateral::with(['loan', 'created_by'])
            ->where('new_approval_status', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $seizurePending = Collateral::with(['loan', 'created_by'])
            ->where('status', 'seizure_pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingWrittenOff = Collateral::with(['loan', 'created_by'])
            ->where('status', 'written_off')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('collateral.approval_queue', compact('requests', 'loanBalances', 'newCollaterals', 'seizurePending', 'pendingWrittenOff'));
    }

    public function requestChange(Request $request, Collateral $collateral)
    {
        // if (!Sentinel::hasAccess('collateral.update')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $request->validate([
            'new_status' => 'required|in:pledged,seizure_pending,seized_inventory,valuation_completed,listed_for_sale,sold,written_off,released',
            'reason'     => 'required|string',
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
            'sold_price'      => 0,
            'penalty'         => 0,
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

    public function sell(Request $request, Collateral $collateral)
    {
        // if (!Sentinel::hasAccess('collateral.update')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $request->validate([
            'sold_price'     => 'required|numeric|min:0',
            'disposal_costs' => 'nullable|array',
            'disposal_costs.*.name' => 'nullable|string|max:255',
            'disposal_costs.*.amount' => 'nullable|numeric|min:0',
            'reason'         => 'required|string',
        ]);

        if ($collateral->status === 'sold' || $collateral->status === 'written_off' || $collateral->status === 'released') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['new_status' => 'This collateral is already marked as sold, written off, or released.']);
        }

        $approvedValue = $collateral->approved_value ?? $collateral->current_worth;

        if ($request->sold_price < $approvedValue) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['sold_price' => 'The disposal value must be equal to or greater than the recorded collateral value of ' . number_format($approvedValue, 2) . '.']);
        }

        $disposalCostsTotal = 0;
        if ($request->filled('disposal_costs') && is_array($request->disposal_costs)) {
            foreach ($request->disposal_costs as $item) {
                $disposalCostsTotal += (float) ($item['amount'] ?? 0);
            }
        }

        CollateralStatusChangeRequest::create([
            'collateral_id'   => $collateral->id,
            'requested_by_id' => Sentinel::getUser()->id,
            'old_status'      => $collateral->status,
            'new_status'      => 'sold',
            'reason'          => $request->reason,
            'sold_price'      => $request->sold_price,
            'penalty'         => 0,
            'disposal_costs'  => $request->disposal_costs ?? [],
            'approval_status' => 'pending',
            'request_date'    => Carbon::now(),
        ]);

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'collateral_sell_requested',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Sell request submitted successfully.');
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

        $collateral->status = 'sold';
        if ($collateral_status_change_request->new_status === 'sold') {
            $collateral->sold_price = $collateral_status_change_request->sold_price;
            $collateral->disposal_costs = $collateral_status_change_request->disposal_costs;
            $collateral->date_resold = Carbon::now();
            $collateral->sold_at = Carbon::now();
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
            'new_status' => 'required|in:pledged,seizure_pending,seized_inventory,valuation_completed,listed_for_sale,sold,written_off,released',
        ]);

        $collateral->status = $request->new_status;
        $now = Carbon::now();
        if ($request->new_status === 'pledged') {
            $collateral->pledged_at = $now;
        } elseif ($request->new_status === 'seized_inventory') {
            $collateral->seized_at = $now;
        } elseif ($request->new_status === 'valuation_completed') {
            $collateral->valuated_at = $now;
        } elseif ($request->new_status === 'listed_for_sale') {
            $collateral->listed_at = $now;
        } elseif ($request->new_status === 'sold') {
            $collateral->sold_at = $now;
            $collateral->date_resold = $now;
        } elseif ($request->new_status === 'written_off') {
            $collateral->written_off_at = $now;
        } elseif ($request->new_status === 'released') {
            $collateral->released_at = $now;
        }
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

    public function approveNewCollateral(Request $request, Collateral $collateral)
    {
        // if (!Sentinel::hasAccess('collateral.approve')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $collateral->new_approval_status = 1;
        $collateral->save();

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'new_collateral_approved',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('New collateral approved successfully.');
        return redirect()->route('collateral.approvals.queue');
    }

    public function declineNewCollateral(Request $request, Collateral $collateral)
    {
        // if (!Sentinel::hasAccess('collateral.approve')) {
        //     Flash::warning('Permission Denied');
        //     return redirect()->back();
        // }

        $collateral->new_approval_status = 2; // declined
        $collateral->save();

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'new_collateral_declined',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('New collateral declined successfully.');
        return redirect()->route('collateral.approvals.queue');
    }
}
