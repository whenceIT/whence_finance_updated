<?php

namespace App\Http\ViewComposers;

use App\Models\Office;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\View\View;

/**
 * Injects branch vacancy alert data into every view that uses the master layout.
 *
 * Only fires for authenticated branch managers (Sentinel role 4).
 * Passes a single variable:
 *   $bmVacancyAlert — array|null
 *     null              → not a BM, or no vacancy, or no capacity set
 *     array             → [
 *                           'office_name'  => string,
 *                           'office_id'    => int,
 *                           'approved'     => int,
 *                           'current'      => int,
 *                           'vacancy'      => int,
 *                           'vacancy_pct'  => float,
 *                         ]
 */
class BranchVacancyComposer
{
    public function compose(View $view): void
    {
        // Only run for authenticated users
        $user = Sentinel::getUser();

        if (!$user) {
            $view->with('bmVacancyAlert', null);
            return;
        }

        // Only branch managers (role 4) should see this alert
        $isBranchManager = $user->inRole(4);

        if (!$isBranchManager) {
            $view->with('bmVacancyAlert', null);
            return;
        }

        $officeId = $user->office_id;

        if (!$officeId) {
            $view->with('bmVacancyAlert', null);
            return;
        }

        $office = Office::find($officeId);

        if (!$office) {
            $view->with('bmVacancyAlert', null);
            return;
        }

        $approvedCapacity = (int) ($office->branch_capacity ?? 0);

        // No capacity has been configured yet — nothing to alert about
        if ($approvedCapacity <= 0) {
            $view->with('bmVacancyAlert', null);
            return;
        }

        $currentPersonnel = User::where('office_id', $officeId)
            ->whereIn('status', ['Active', 'active'])
            ->count();

        $vacancy = max($approvedCapacity - $currentPersonnel, 0);

        // No vacancy — suppress the popup
        if ($vacancy <= 0) {
            $view->with('bmVacancyAlert', null);
            return;
        }

        $vacancyPct = round(($vacancy / $approvedCapacity) * 100, 1);

        $view->with('bmVacancyAlert', [
            'office_name'  => $office->name,
            'office_id'    => $office->id,
            'approved'     => $approvedCapacity,
            'current'      => $currentPersonnel,
            'vacancy'      => $vacancy,
            'vacancy_pct'  => $vacancyPct,
        ]);
    }
}
