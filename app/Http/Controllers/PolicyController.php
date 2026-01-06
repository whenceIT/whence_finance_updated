<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\Policy;
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
     * Display all policies
     *
     * @return \Illuminate\View\View
     */
    public function viewPolicies()
    {
        $user = Sentinel::getUser();

        if ($user) {
            $policies = Policy::with([
                'userPolicyResponses' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ])->latest()->get();
        } else {
            $policies = Policy::latest()->get();
        }

        return view('policies.view', compact('policies'));
    }

    public function userResponses()
    {
        $offices = \App\Models\Office::all();

        return view('policies.user-responses', compact('offices'));
    }

    /**
     * 
     *
     * @return \Illuminate\View\View
     */
    public function addPolicies()
    {
        return view('policies.add');
    }

    /**
     *
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
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);
        }

        if ($request->hasFile('policy_file')) {
            $file = $request->file('policy_file');

            // Create filename without using Str::slug
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


}
