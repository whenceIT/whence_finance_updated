<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecoveriesDeptExcalatedShare;
use App\Models\UnitShare;

class DeptSharesController extends Controller
{
    public function index()
    {
        $totalDeptShare = RecoveriesDeptExcalatedShare::sum('dept_share_amount');
        $totalUnitShare = UnitShare::sum('amount');

        return view('recoveries.dept-shares', compact(
            'totalDeptShare', 
            'totalUnitShare'
        ));
    }
}
