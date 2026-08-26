<?php

namespace App\Services;

use App\Models\Collateral;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class CollateralApprovalService
{
    public function getPendingForCurrentUser()
    {
        $user = Sentinel::getUser();
        if (!$user) {
            return collect();
        }

        return Collateral::where('vetted_valuation_by', $user->id)
            ->where('vetted_valuation_status', 0)
            ->with(['loan.client', 'loan.office', 'type'])
            ->get();
    }
}
