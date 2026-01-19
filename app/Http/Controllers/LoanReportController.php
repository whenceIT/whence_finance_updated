<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Office;
use Illuminate\Http\Request;

class LoanReportController extends Controller
{
    public function getLoanOfficersByOffice($officeId)
    {
        $loanOfficers = User::where('office_id', $officeId)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'client');
            })
            ->get(['id', 'first_name', 'last_name']);

        return response()->json($loanOfficers);
    }

    public function getLoanOfficersByProvince($provinceId)
    {
        $loanOfficers = User::where('province_id', $provinceId)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'client');
            })
            ->get(['id', 'first_name', 'last_name']);

        return response()->json($loanOfficers);
    }

    public function getAllLoanOfficers()
    {
        $loanOfficers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'client');
        })
            ->get(['id', 'first_name', 'last_name']);

        return response()->json($loanOfficers);
    }
}