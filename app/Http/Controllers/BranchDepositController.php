<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BranchDepositController extends Controller
{
    public function branchDeposits(Request $request){
        $selectedMonth = $request->get('month', date('Y-m'));
        return view('branch-deposits.index', compact('selectedMonth'));
    }
}
