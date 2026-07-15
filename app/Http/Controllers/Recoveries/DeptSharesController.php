<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecoveriesDeptExcalatedShare;
use App\Models\UnitShare;

class DeptSharesController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->get('type', '');
        
        $deptShares = RecoveriesDeptExcalatedShare::with(['recoveryCase.assignedSpecialist', 'createdBy', 'office'])->get()->map(function($item) {
            $item->type = 'dept_share';
            return $item;
        });
        
        $unitShares = UnitShare::with(['user', 'office'])->get()->map(function($item) {
            $item->type = 'unit_share';
            return $item;
        });
        
        if ($filterType === 'dept_share') {
            $unitShares = collect();
        } elseif ($filterType === 'unit_share') {
            $deptShares = collect();
        }
        
        $allShares = $deptShares->concat($unitShares)->sortByDesc('created_at')->values();

        $totalDeptShare = $deptShares->sum('dept_share_amount');
        $totalUnitShare = $unitShares->sum('amount');
        $overallTtDebtAttr = \App\Models\RecoveryPayment::where('status', 1)->sum('recoveries_dept_amount');

        return view('recoveries.dept-shares', compact(
            'totalDeptShare', 
            'totalUnitShare',
            'overallTtDebtAttr',
            'allShares',
            'filterType'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'share_type' => 'required|in:dept_share,unit_share',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($validated['share_type'] === 'dept_share') {
            RecoveriesDeptExcalatedShare::create([
                'dept_share_amount' => $validated['amount'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id()
            ]);
            $message = 'Recovery Dept Share recorded successfully';
        } else {
            UnitShare::create([
                'amount' => $validated['amount'],
                'notes' => $validated['notes'],
                'user_id' => auth()->id()
            ]);
            $message = 'Unit Share recorded successfully';
        }

        return response()->json(['message' => $message]);
    }
}