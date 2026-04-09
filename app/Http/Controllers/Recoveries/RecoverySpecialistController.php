<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Models\{RecoveryCase, RecoverySpecialistTarget, Specialist, User};
use App\Services\RecoveryDashboardService;
use Illuminate\Http\Request;

class RecoverySpecialistController extends Controller
{
    public function __construct(public RecoveryDashboardService $dashboard)
    {
        $this->middleware('sentinel');
    }

    public function index(Request $request)
    {
        $period      = $request->get('period', 'month');
        $specialists = $this->dashboard->getSpecialistPerformance($period);

        return view('recoveries.specialists.index', compact('specialists', 'period'));
    }

    /**
     * Store a newly created specialist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:specialists,user_id',
            'notes'   => 'nullable|string|max:1000',
        ]);

        Specialist::create([
            'user_id'   => $request->user_id,
            'notes'     => $request->notes,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Specialist assigned successfully.');
    }

    public function show($id, Request $request)
    {
        $user   = User::findOrFail($id);
        $period = $request->get('period', 'month');

        $cases = RecoveryCase::assignedTo($user->id)
            ->forPeriod($period)
            ->with(['client', 'originBranch'])
            ->latest();
            // ->paginate(15);

        $targets = RecoverySpecialistTarget::where('specialist_id', $user->id)
            ->where('year', now()->year)
            ->orderBy('month')
            ->get();

        $activityLog = \App\Models\RecoveryActivity::where('performed_by', $user->id)
            ->with('recoveryCase.client')
            ->latest()
            ->limit(20)
            ->get();


            dd($cases->get());
        return view('recoveries.specialists.show', compact('user', 'cases', 'targets', 'activityLog', 'period'));
    }

    public function setTarget(Request $request, $id)
    {
        $request->validate([
            'category'      => 'required|in:' . implode(',', array_keys(RecoveryCase::CATEGORIES)),
            'target_amount' => 'required|numeric|min:0',
            'target_cases'  => 'required|integer|min:0',
            'month'         => 'required|integer|between:1,12',
            'year'          => 'required|integer|min:2020',
        ]);

        RecoverySpecialistTarget::updateOrCreate(
            [
                'specialist_id' => $id,
                'category'      => $request->category,
                'year'          => $request->year,
                'month'         => $request->month,
            ],
            [
                'target_amount' => $request->target_amount,
                'target_cases'  => $request->target_cases,
            ]
        );

        return redirect()->back()->with('success', 'Target updated.');
    }

    public function deleteTarget($id, $target_id)
    {
        RecoverySpecialistTarget::where('specialist_id', $id)
            ->where('id', $target_id)
            ->delete();

        return redirect()->back()->with('success', 'Target deleted.');
    }
}