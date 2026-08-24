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
            ->where('status', 'listed_for_sale')
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
            'action'     => 'collateral_status_changed',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Status changed successfully.');
        return redirect()->route('collateral.show', $collateral);
    }

    public function workflowNext(Request $request, Collateral $collateral)
    {
        $user = Sentinel::getUser();
        $role = \App\Models\UserRole::where('user_id', $user->id)->first();
        $roleId = $role ? $role->role_id : null;

        $workflow = [
            'pledged' => [
                'next' => 'seizure_pending',
                'roles' => [3, 4],
            ],
            'seizure_pending' => [
                'next' => 'seized_inventory',
                'roles' => [1],
            ],
            'seized_inventory' => [
                'next' => 'valuation_completed',
                'roles' => [1],
            ],
            'valuation_completed' => [
                'next' => 'listed_for_sale',
                'roles' => [1],
            ],
            'listed_for_sale' => [
                'next' => 'written_off',
                'roles' => [1],
            ],
        ];

        $current = $collateral->status;
        if (!isset($workflow[$current])) {
            Flash::warning('No next workflow step available.');
            return redirect()->route('collateral.show', $collateral);
        }

        $step = $workflow[$current];
        if (!in_array($roleId, $step['roles'])) {
            Flash::warning('Permission Denied');
            return redirect()->route('collateral.show', $collateral);
        }

        $collateral->status = $step['next'];
        $now = Carbon::now();
        if ($step['next'] === 'seized_inventory') {
            $collateral->seized_at = $now;
        } elseif ($step['next'] === 'valuation_completed') {
            $collateral->valuated_at = $now;
        } elseif ($step['next'] === 'listed_for_sale') {
            $collateral->listed_at = $now;
        } elseif ($step['next'] === 'written_off') {
            $collateral->written_off_at = $now;
        } elseif ($step['next'] === 'seizure_pending') {
            $collateral->seized_at = null;
        }
        $collateral->save();

        AuditTrail::create([
            'user_id'    => $user->id,
            'action'     => 'collateral_workflow_advanced',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Status advanced to ' . ucfirst(str_replace('_', ' ', $step['next'])) . ' successfully.');
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
            'reason'         => 'nullable|string',
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

        $collateral->status = 'sold';
        $collateral->sold_price = $request->sold_price;
        $collateral->disposal_costs = $request->disposal_costs ?? [];
        $collateral->date_resold = Carbon::now();
        $collateral->sold_at = Carbon::now();
        $collateral->save();

        AuditTrail::create([
            'user_id'    => Sentinel::getUser()->id,
            'action'     => 'collateral_sold',
            'table_name' => 'collateral',
            'record_id'  => $collateral->id,
            'ip_address' => $request->ip(),
        ]);

        Flash::success('Collateral sold successfully. Status changed to Sold.');
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
        $now = Carbon::now();
        if ($collateral_status_change_request->new_status === 'sold') {
            $collateral->sold_price = $collateral_status_change_request->sold_price;
            $collateral->disposal_costs = $collateral_status_change_request->disposal_costs;
            $collateral->date_resold = $now;
            $collateral->sold_at = $now;
        } elseif ($collateral_status_change_request->new_status === 'written_off') {
            $collateral->written_off_at = $now;
        } elseif ($collateral_status_change_request->new_status === 'released') {
            $collateral->released_at = $now;
        } elseif ($collateral_status_change_request->new_status === 'seized_inventory') {
            $collateral->seized_at = $now;
        } elseif ($collateral_status_change_request->new_status === 'valuation_completed') {
            $collateral->valuated_at = $now;
        } elseif ($collateral_status_change_request->new_status === 'listed_for_sale') {
            $collateral->listed_at = $now;
        } elseif ($collateral_status_change_request->new_status === 'pledged') {
            $collateral->pledged_at = $now;
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

        $allowedTransitions = [
            'pledged' => ['seizure_pending'],
            'seizure_pending' => ['seized_inventory'],
            'seized_inventory' => ['valuation_completed'],
            'valuation_completed' => ['listed_for_sale'],
            'listed_for_sale' => ['written_off'],
        ];

        $current = $collateral->status;
        if (!isset($allowedTransitions[$current]) || !in_array($request->new_status, $allowedTransitions[$current])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['new_status' => 'Invalid status transition from ' . ucfirst($current) . '.']);
        }

        $collateral->status = $request->new_status;
        $now = Carbon::now();
        if ($request->new_status === 'seized_inventory') {
            $collateral->seized_at = $now;
        } elseif ($request->new_status === 'valuation_completed') {
            $collateral->valuated_at = $now;
        } elseif ($request->new_status === 'listed_for_sale') {
            $collateral->listed_at = $now;
        } elseif ($request->new_status === 'written_off') {
            $collateral->written_off_at = $now;
        } elseif ($request->new_status === 'pledged') {
            $collateral->pledged_at = $now;
        } elseif ($request->new_status === 'released') {
            $collateral->released_at = $now;
        } elseif ($request->new_status === 'sold') {
            $collateral->sold_at = $now;
            $collateral->date_resold = $now;
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

        if ($collateral->status === 'seizure_pending') {
            $collateral->status = 'seized_inventory';
            $collateral->seized_at = Carbon::now();
            $collateral->save();

            AuditTrail::create([
                'user_id'    => Sentinel::getUser()->id,
                'action'     => 'seizure_approved',
                'table_name' => 'collateral',
                'record_id'  => $collateral->id,
                'ip_address' => $request->ip(),
            ]);

            Flash::success('Seizure approved. Status changed to Seized/Inventory.');
            return redirect()->route('collateral.approvals.queue');
        }

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
