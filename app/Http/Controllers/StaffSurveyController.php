<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffSurvey;
use Sentinel;
use App\Models\User;

class StaffSurveyController extends Controller
{
    /**
     * Display the survey form
     */
    public function show()
    {
        $user = Sentinel::getUser();
        
        // Check if user has already submitted the survey
        $existingSurvey = StaffSurvey::where('user_id', $user->id)->first();
        
        if ($existingSurvey) {
            return redirect()->route('survey.thankyou')
                ->with('success', 'You have already submitted the survey. Thank you for your feedback!');
        }
        
        return view('staff_survey.create');
    }

    /**
     * Store the survey responses
     */
    public function store(Request $request)
    {
        $user = Sentinel::getUser();
        
        // Check if user has already submitted the survey
        $existingSurvey = StaffSurvey::where('user_id', $user->id)->first();
        
        if ($existingSurvey) {
            return redirect()->route('survey.thankyou')
                ->with('success', 'You have already submitted the survey. Thank you for your feedback!');
        }

        $validated = $request->validate([
            'branch' => 'required|string',
            'length_of_service' => 'required|string',
            'bmos_consistency' => 'required|string',
            'bmos_challenges' => 'nullable|string',
            'branch_needs' => 'nullable|string',
            'tools_resources' => 'required|string',
            'operational_challenges' => 'nullable|string',
            'supervisor_support' => 'required|string',
            'manager_communication' => 'required|string',
            'manager_communication_comments' => 'nullable|string',
            'leadership_challenges' => 'nullable|string',
            'manager_effectiveness_rating' => 'required|integer|min:1|max:10',
            'workload_rating' => 'nullable|string',
            'unethical_conduct' => 'required|string',
            'policy_violation_instructions' => 'required|string',
            'policy_violation_description' => 'nullable|string',
            'top_issues' => 'nullable|string',
            'pending_loans_entry' => 'required|string',
            'longest_pending_period' => 'nullable|string',
            'missed_target_due_pending' => 'required|string',
            'pending_target_explanation' => 'nullable|string',
        ]);

        $validated['user_id'] = $user->id;

        StaffSurvey::create($validated);

        return redirect()->route('survey.thankyou')
            ->with('success', 'Thank you for completing the survey! Your feedback is valuable to us.');
    }

    /**
     * Display thank you page
     */
    public function thankyou()
    {
        return view('staff_survey.thankyou');
    }

    /**
     * Display all survey responses (Admin only)
     */
    public function index()
    {
        // Check if user is admin
        if (!Sentinel::inRole('admin') && !Sentinel::inRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $surveys = StaffSurvey::with('user')->orderBy('created_at', 'desc')->paginate(20);

        return view('staff_survey.index', compact('surveys'));
    }

    /**
     * Display a specific user's survey response
     */
    public function showUserSurvey($id)
    {
        // Check if user is admin
        if (!Sentinel::inRole('admin') && !Sentinel::inRole('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        $survey = StaffSurvey::with('user')->findOrFail($id);

        return view('staff_survey.show', compact('survey'));
    }
}
