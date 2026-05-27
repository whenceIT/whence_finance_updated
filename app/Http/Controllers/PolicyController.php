<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\Policy;
use App\Models\PolicyCategory;
use App\Models\PolicyOfTheDay;
use App\Models\PolicyViolation;
use App\Models\UserPolicyView;
use App\Models\UserPolicyResponse;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Storage;
//use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class PolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    /**
     * Policy Management Dashboard
     * 
     * Bento Grid Dashboard with overview of all policy management features
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Sentinel::getUser();
        $isManagerial = $this->isManagerialUser($user);
        $isAdmin = $this->isAdmin($user);

        // Get policy statistics
        $totalPolicies = Policy::count();
        $activePolicies = $totalPolicies; // All policies are considered active if no is_active column
        
        // Get response statistics
        $totalResponses = UserPolicyResponse::count();
        $acknowledgedCount = UserPolicyResponse::where('status', 'accepted')->count();
        $pendingCount = UserPolicyResponse::where('status', 'pending')->count();
        $declinedCount = UserPolicyResponse::where('status', 'declined')->count();

        // Get violations count
        $violationsCount = PolicyViolation::count();

        // Get declined policies for modal
        $declinedPolicies = UserPolicyResponse::with(['user', 'policy'])
            ->where('status', 'declined')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get categories with policy count
        $categories = PolicyCategory::withCount('policies')
            ->orderBy('sort_order')
            ->get();

        // Get recent policies
        $recentPolicies = Policy::with('category')
            ->latest()
            ->take(5)
            ->get();

        // Get recent responses
        $recentResponses = UserPolicyResponse::with(['user', 'policy'])
            ->latest()
            ->take(5)
            ->get();

        // Get pending responses count by category
        $pendingByCategory = Policy::withCount(['userPolicyResponses' => function ($q) {
            $q->where('status', 'pending');
        }])->get();

        return view('policies.dashboard', compact(
            'totalPolicies',
            'activePolicies',
            'totalResponses',
            'acknowledgedCount',
            'pendingCount',
            'declinedCount',
            'categories',
            'recentPolicies',
            'recentResponses',
            'pendingByCategory',
            'isManagerial',
            'isAdmin',
            'declinedPolicies',
            'violationsCount'
        ));
    }

    /**
     * Check if user has managerial access based on role ID
     * 
     * Role IDs:
     * 1 = Admin (sees all)
     * 3 = Loan Officer (sees only all staff documents)
     * 4 = Branch Manager (sees managerial documents)
     * 6 = Provincial Manager (sees managerial documents)
     * 
     * @param mixed $user
     * @return bool
     */
    private function isManagerialUser($user)
    {
        if (!$user) {
            return false;
        }

        $userRole = $user->roles->first();
        
        if ($userRole) {
            // Admin (1), Branch Manager (4), Provincial Manager (6) have managerial access
            $managerialRoleIds = [1, 4, 6];
            return in_array($userRole->id, $managerialRoleIds);
        }

        return false;
    }

    /**
     * Check if user is Admin
     * 
     * @param mixed $user
     * @return bool
     */
    private function isAdmin($user)
    {
        if (!$user) {
            return false;
        }

        $userRole = $user->roles->first();
        
        if ($userRole) {
            return $userRole->id == 1;
        }

        return false;
    }

    /**
     * Display all policies
     *
     * @return \Illuminate\View\View
     */
    public function viewPolicies(Request $request)
    {
        $user = Sentinel::getUser();
        $isManagerial = $this->isManagerialUser($user);
        $isAdmin = $this->isAdmin($user);
        $selectedCategory = $request->get('category');

        // Get all active categories
        $categories = PolicyCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Build query with user responses and created by user
        $query = Policy::with([
            'userPolicyResponses' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            },
            'category',
            'createdBy'
        ]);

        // Filter by category if selected
        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory);
        }

        // Filter by access level for non-managerial users
        // Role 1 (Admin) sees all, Role 4 & 6 (Managers) see all, Role 3 (Loan Officer) sees only 'all' documents
        if (!$isManagerial) {
            $query->where('access_level', Policy::ACCESS_ALL);
        }

        $policies = $query->latest()->get();

        return view('policies.view', compact('policies', 'categories', 'selectedCategory', 'isManagerial', 'isAdmin'));
    }

    public function userResponses()
    {
        $offices = \App\Models\Office::all();

        return view('policies.user-responses', compact('offices'));
    }

    /**
     * Show form to add policies
     *
     * @return \Illuminate\View\View
     */
    public function addPolicies()
    {
        $categories = PolicyCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $accessLevels = Policy::getAccessLevels();

        return view('policies.add', compact('categories', 'accessLevels'));
    }

    /**
     * Store a new policy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePolicies(Request $request)
    {

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'policy_file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
            'category_id' => 'required|exists:policy_categories,id',
            'access_level' => 'required|in:all,managerial',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);
        }

        if ($request->hasFile('policy_file')) {
            $file = $request->file('policy_file');

            // Create filename without using Str::slugo
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $sanitizedName = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $originalName);
            $fileName = $sanitizedName . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            try {
                // Initialize S3 client
                $s3Client = new S3Client([
                    'version' => 'latest',
                    'region' => 'nyc3',
                    'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                    'credentials' => [
                        'key' => 'DO00RP9FA3QZTA3JV637',
                        'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                    ],
                ]);

                // Upload file to DigitalOcean Spaces
                $result = $s3Client->putObject([
                    'Bucket' => 'wfspolicies',
                    'Key' => 'policies/' . $fileName,
                    'Body' => fopen($file->getPathname(), 'r'),
                    'ACL' => 'public-read',
                    'ContentType' => $file->getClientMimeType(),
                ]);


                $fileUrl = $result['ObjectURL'];

                // Create policy record
                Policy::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'file_path' => 'policies/' . $fileName,
                    'file_url' => $fileUrl,
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'category_id' => $request->category_id,
                    'access_level' => $request->access_level,
                    'created_by' => Sentinel::getUser()->id,
                ]);

                Flash::success(trans('general.successfully_saved'));
                return redirect()->route('policies.view_policies')
                    ->with('success', 'Policy uploaded successfully.');

            } catch (AwsException $e) {
                Log::error('Policy Upload Error: ' . $e->getMessage());
                Flash::error(trans('general.upload_failed'));
                return back()->with('error', 'Failed to upload policy to DigitalOcean Spaces.');
            }
        } else {
            Flash::error(trans('general.no_file_uploaded'));
            return back()->with('error', 'No policy file was uploaded.');
        }
    }

    /**
     * Handle user response to a policy (accept or decline)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $policy_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function respondToPolicy(Request $request, $policy_id)
    {
        $request->validate([
            'status' => 'required|in:accepted,declined',
        ]);

        $user = Sentinel::getUser();

        if (!$user) {
            Flash::error('You must be logged in to respond to policies.');
            return redirect()->route('login');
        }

        UserPolicyResponse::updateOrCreate(
            [
                'user_id' => $user->id,
                'policy_id' => $policy_id,
            ],
            [
                'status' => $request->status,
            ]
        );

        Flash::success('Policy response recorded successfully.');

        // Check if all policies are completed for induction checklist
        if (\App\Models\InductionChecklist::hasCompletedPolicies($user->id)) {
            \App\Models\InductionChecklist::where('user_id', $user->id)
                ->where('item', 'Review and sign pending company policies.')
                ->update(['completed' => true]);
        }

        return redirect()->back();
    }

    public function searchUsers(Request $request)
    {
        $query = $request->query('query');
        $officeId = $request->query('office_id');

        $users = \App\Models\User::query();

        if ($query) {
            $users->where(function ($q) use ($query) {
                $q->where('first_name', 'like', '%' . $query . '%')
                    ->orWhere('last_name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%');
            });
        }

        if ($officeId) {
            $users->where('office_id', $officeId);
        }

        $users = $users->select('id', 'first_name', 'last_name', 'email')->limit(50)->get();

        return response()->json($users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
            ];
        }));
    }

    public function getUserResponses($userId)
    {
        if (!Sentinel::getUser()) {
            return response()->json([]);
        }

        $policies = Policy::with([
            'userPolicyResponses' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }
        ])->get();

        $responses = $policies->map(function ($policy) {
            $response = $policy->userPolicyResponses->first();
            return [
                'policy_id' => $policy->id,
                'policy_title' => $policy->title,
                'status' => $response ? $response->status : 'Pending',
            ];
        });

        return response()->json($responses);
    }

    public function getDeclinedResponses(Request $request)
    {
        if (!Sentinel::getUser()) {
            return response()->json([]);
        }

        $query = UserPolicyResponse::with(['user.office', 'policy'])
            ->where('status', 'declined');

        if ($request->office_id) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('office_id', $request->office_id);
            });
        }

        if ($request->user_query) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->user_query . '%')
                    ->orWhere('last_name', 'like', '%' . $request->user_query . '%')
                    ->orWhere('email', 'like', '%' . $request->user_query . '%');
            });
        }

        $responses = $query->get();

        return response()->json($responses->map(function ($response) {
            return [
                'user_name' => $response->user->first_name . ' ' . $response->user->last_name,
                'user_email' => $response->user->email,
                'office_name' => $response->user->office ? $response->user->office->name : 'N/A',
                'policy_title' => $response->policy->title,
                'status' => $response->status,
                'responded_at' => $response->updated_at->format('Y-m-d H:i:s'),
            ];
        }));
    }

    public function resetUserResponse(Request $request, $userId, $policyId)
    {
        if (!Sentinel::getUser()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        UserPolicyResponse::where('user_id', $userId)->where('policy_id', $policyId)->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Delete a policy
     *
     * @param  int  $policyId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePolicy($policyId)
    {
        $policy = Policy::findOrFail($policyId);
        $user = Sentinel::getUser();

        // Check if user has permission to delete the policy
        $canDelete = false;
        $userRole = $user->roles->first();

        if ($userRole && $userRole->id == 1) {
            // Admin can delete any policy
            $canDelete = true;
        } elseif ($policy->created_by == $user->id) {
            // Policy creator can delete their own policy
            $canDelete = true;
        }

        if (!$canDelete) {
            Flash::error('You do not have permission to delete this policy.');
            return redirect()->back();
        }

        try {
            // Delete policy file from DigitalOcean Spaces
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => 'nyc3',
                'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                'credentials' => [
                    'key' => 'DO00RP9FA3QZTA3JV637',
                    'secret' => 'GWEj+tmCLlYb/RzX7b6vab8Kz9OjFO1PknyYyUQTnjk',
                ],
            ]);

            $s3Client->deleteObject([
                'Bucket' => 'wfspolicies',
                'Key' => $policy->file_path,
            ]);

            // Delete policy record
            $policy->delete();

            Flash::success('Policy deleted successfully.');
        } catch (AwsException $e) {
            Log::error('Policy Delete Error: ' . $e->getMessage());
            Flash::error('Failed to delete policy file from DigitalOcean Spaces.');
        }

        return redirect()->route('policies.view_policies');
    }

    /**
     * Get policy violations
     */
    public function getViolations(Request $request)
    {
        $query = PolicyViolation::with(['user.office', 'policy.category', 'reporter'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->branch_id) {
            $query->whereHas('user.office', function($q) use ($request) {
                $q->where('id', $request->branch_id);
            });
        }
        if ($request->category_id) {
            $query->whereHas('policy.category', function($q) use ($request) {
                $q->where('id', $request->category_id);
            });
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $violations = $query->get();

        return response()->json($violations->map(function ($violation) {
            return [
                'id' => $violation->id,
                'user_name' => $violation->user->first_name . ' ' . $violation->user->last_name,
                'branch_name' => $violation->user->office ? $violation->user->office->name : 'N/A',
                'policy_title' => $violation->policy->title,
                'description' => $violation->description,
                'status' => $violation->status,
                'created_at' => $violation->created_at->format('M d, Y H:i'),
                'evidence_count' => $violation->evidence ? count($violation->evidence) : 0,
            ];
        }));
    }

    /**
     * Store new violation
     */
    public function storeViolation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'policy_id' => 'required|exists:policies,id',
            'description' => 'required|string',
            'evidence.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240'
        ]);

        $evidenceFiles = [];

        // Handle evidence files upload
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $filename = uniqid() . '_' . $file->getClientOriginalName();
                $file->storeAs('violations', $filename, 'public');
                $evidenceFiles[] = $filename;
            }
        }

        PolicyViolation::create([
            'user_id' => $request->user_id,
            'policy_id' => $request->policy_id,
            'reported_by' => Sentinel::getUser()->id,
            'description' => $request->description,
            'evidence' => $evidenceFiles,
            'status' => 'pending',
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Update violation status
     */
    public function updateViolationStatus(Request $request)
    {
        $request->validate([
            'violation_id' => 'required|integer',
            'status' => 'required|in:pending,investigating,resolved,escalated'
        ]);

        $violation = PolicyViolation::findOrFail($request->violation_id);

        $updateData = ['status' => $request->status];

        if ($request->status === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        $violation->update($updateData);

        // Auto-escalation logic for repeated offenders
        if ($request->status === 'escalated') {
            $userViolationCount = PolicyViolation::where('user_id', $violation->user_id)
                ->where('status', 'escalated')
                ->count();

            // If user has 3 or more escalated violations, could trigger further actions
            if ($userViolationCount >= 3) {
                // Could send notification to HR, create incident report, etc.
                // For now, just log
                \Log::warning("User {$violation->user_id} has {$userViolationCount} escalated violations");
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Attach evidence to violation
     */
    public function attachViolationEvidence(Request $request)
    {
        $request->validate([
            'violation_id' => 'required|integer',
            'evidence.*' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240'
        ]);

        $violation = PolicyViolation::findOrFail($request->violation_id);
        $evidenceFiles = $violation->evidence ?? [];

        // Handle evidence files upload
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $filename = uniqid() . '_' . $file->getClientOriginalName();
                $file->storeAs('violations', $filename, 'public');
                $evidenceFiles[] = $filename;
            }
        }

        $violation->update(['evidence' => $evidenceFiles]);

        return response()->json(['success' => true]);
    }

    /**
     * Show violation details
     */
    public function showViolation($id)
    {
        $violation = PolicyViolation::with(['user.office', 'policy', 'reporter'])->findOrFail($id);

        return view('policies.violation-detail', compact('violation'));
    }

    /**
     * Show single policy details for preview
     */
    public function view($id)
    {
        $policy = Policy::with(['category', 'createdBy'])->findOrFail($id);
        
        return view('policies.single-view', compact('policy'));
    }

    /**
     * Get branches for violations filter
     */
    public function getViolationBranches()
    {
        $branches = \App\Models\Office::select('id', 'name')->get();
        return response()->json($branches);
    }

    /**
     * Get categories for violations filter
     */
    public function getViolationCategories()
    {
        $categories = PolicyCategory::select('id', 'name')->get();
        return response()->json($categories);
    }

    /**
     * Get users for violation reporting
     */
    public function getViolationUsers()
    {
        $users = \App\Models\User::select('id', 'first_name', 'last_name')->get();
        return response()->json($users);
    }

    /**
     * Get policies for violation reporting
     */
    public function getViolationPolicies()
    {
        $policies = Policy::select('id', 'title')->get();
        return response()->json($policies);
    }

    /**
     * Get policy of the day
     */
    public function getPolicyOfTheDay()
    {
        $policyOfTheDay = PolicyOfTheDay::getTodaysPolicy();
        return response()->json($policyOfTheDay);
    }

    /**
     * Store new policy of the day
     */
    public function storePolicyOfTheDay(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'full_content' => 'nullable|string',
            'policy_id' => 'nullable|exists:policies,id',
            'scheduled_date' => 'nullable|date',
            'is_random' => 'boolean',
        ]);

        PolicyOfTheDay::create([
            'title' => $request->title,
            'content' => $request->content,
            'full_content' => $request->full_content,
            'policy_id' => $request->policy_id,
            'created_by' => Sentinel::getUser()->id,
            'scheduled_date' => $request->scheduled_date,
            'is_random' => $request->is_random ?? false,
            'is_active' => true,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Update policy of the day
     */
    public function updatePolicyOfTheDay(Request $request, $id)
    {
        $policyOfTheDay = PolicyOfTheDay::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'full_content' => 'nullable|string',
            'policy_id' => 'nullable|exists:policies,id',
            'scheduled_date' => 'nullable|date',
            'is_random' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $policyOfTheDay->update($request->only([
            'title', 'content', 'full_content', 'policy_id',
            'scheduled_date', 'is_random', 'is_active'
        ]));

        return response()->json(['success' => true]);
    }

    /**
     * Delete policy of the day
     */
    public function deletePolicyOfTheDay($id)
    {
        $policyOfTheDay = PolicyOfTheDay::findOrFail($id);
        $policyOfTheDay->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get all policies of the day for management
     */
    public function getAllPoliciesOfTheDay()
    {
        $policies = PolicyOfTheDay::with(['policy', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($policies);
    }

    /**
     * Show full policy of the day details page
     */
    public function viewPolicyOfTheDayFull($id)
    {
        $policyOfTheDay = PolicyOfTheDay::with(['policy', 'creator'])->findOrFail($id);

        return view('policies.policy-of-the-day-full', compact('policyOfTheDay'));
    }

    /**
     * Track policy engagement (view time)
     */
    public function trackPolicyEngagement(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'policy_of_the_day_id' => 'nullable|integer|exists:policy_of_the_day,id',
            'policy_id' => 'nullable|integer|exists:policies,id',
            'engagement_time' => 'required|integer|min:0',
        ]);

        UserPolicyView::create([
            'user_id' => $request->user_id,
            'policy_of_the_day_id' => $request->policy_of_the_day_id,
            'policy_id' => $request->policy_id,
            'engagement_time' => $request->engagement_time,
        ]);

        return response()->json(['success' => true]);
    }
}
