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
        
        if ($filterType === 'dept_share') {
            $deptShares = RecoveriesDeptExcalatedShare::with(['recoveryCase' => function($q) {
                $q->with(['loan', 'originBranch', 'client', 'assignedSpecialist']);
            }])->get();
            $unitShares = collect();
        } elseif ($filterType === 'unit_share') {
            $deptShares = collect();
            $unitShares = UnitShare::with(['loan', 'office', 'user'])->get();
        } else {
            $deptShares = RecoveriesDeptExcalatedShare::with(['recoveryCase' => function($q) {
                $q->with(['loan', 'originBranch', 'client', 'assignedSpecialist']);
            }])->get();
            $unitShares = UnitShare::with(['loan', 'office', 'user'])->get();
        }

        $totalDeptShare = $deptShares->sum('dept_share_amount');
        $totalUnitShare = $unitShares->sum('amount');

        return view('recoveries.dept-shares', compact(
            'totalDeptShare', 
            'totalUnitShare',
            'deptShares',
            'unitShares',
            'filterType'
        ));
    }
}
