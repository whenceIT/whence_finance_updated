<?php

namespace App\Http\ViewComposers;

use App\Models\Fleet;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\View\View;

/**
 * Injects expired-insurance alert data into the BM dashboard view.
 *
 * Only fires for authenticated branch managers (Sentinel role 4).
 * Passes a single variable:
 *   $bmInsuranceAlert — array|null
 *     null  → not a BM, or no expired insurance for their branch
 *     array → [
 *               'office_name' => string,
 *               'office_id'   => int,
 *               'vehicles'    => Collection<Fleet>  (with 'user', 'office' loaded),
 *               'count'       => int,
 *             ]
 */
class BranchInsuranceAlertComposer
{
    public function compose(View $view): void
    {
        $user = Sentinel::getUser();

        if (!$user || !$user->inRole(4)) {
            $view->with('bmInsuranceAlert', null);
            return;
        }

        $officeId = $user->office_id;

        if (!$officeId) {
            $view->with('bmInsuranceAlert', null);
            return;
        }

        // Mirror the $insurancePastDue query from GOAController::index(),
        // scoped to this manager's branch only.
        $expired = Fleet::with('user', 'office')
            ->where('office_id', $officeId)
            ->whereNotNull('insurance_expire_date')
            ->where('insurance_expire_date', '<', Carbon::now())
            ->orderBy('insurance_expire_date')
            ->get();

        if ($expired->isEmpty()) {
            $view->with('bmInsuranceAlert', null);
            return;
        }

        $office = $user->office;

        $view->with('bmInsuranceAlert', [
            'office_name' => $office ? $office->name : 'Your Branch',
            'office_id'   => $officeId,
            'vehicles'    => $expired,
            'count'       => $expired->count(),
        ]);
    }
}
