<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use App\Models\Office;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LearningSettingController extends Controller
{
    /**
     * Display the settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        // Get statistics for the dashboard
        $totalSettings = 4; // Total number of settings sections
        $totalCategories = CourseCategory::count();
        $totalStudents = Enrollment::distinct('user_id')->count('user_id');
        $totalTeachers = User::where('istrainer', 1)->count();

        return view('learning.settings', compact(
            'totalSettings', 
            'totalCategories', 
            'totalStudents', 
            'totalTeachers'
        ));
    }

    /**
     * Display the course categories settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to access this settings page.');
        }

        $categories = CourseCategory::orderBy('name', 'asc')->get();

        return view('learning.settings.categories', compact('categories'));
    }

    /**
     * Display the students settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function students()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to access this settings page.');
        }

        // Would need to query actual students data
        $students = [];

        return view('learning.settings.students', compact('students'));
    }

    /**
     * Display the teachers settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function teachers()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to access this settings page.');
        }

        // Get all trainers with their office
        $trainers = User::where('istrainer', 1)
            ->with('office')
            ->get();

        // Add roles to each trainer using the role_users table
        $trainers->each(function ($trainer) {
            $roleIds = \DB::table('role_users')->where('user_id', $trainer->id)->pluck('role_id');
            $trainer->roles = \Cartalyst\Sentinel\Roles\EloquentRole::whereIn('id', $roleIds)->get();
        });

        // Get all active offices (removed status filter since column doesn't exist)
        $offices = Office::orderBy('name', 'asc')->get();

        return view('learning.settings.teachers', compact('trainers', 'offices'));
    }

    /**
     * Display the platform settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function platform()
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to access this settings page.');
        }

        return view('learning.settings.platform');
    }

    /**
     * Get all roles.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRoles()
    {
        if (!Sentinel::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get all roles using Sentinel
        $roles = \Cartalyst\Sentinel\Roles\EloquentRole::all(['id', 'name', 'slug']);

        return response()->json($roles);
    }

    /**
     * Get users by role.
     *
     * @param int $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByRole($roleId)
    {
        if (!Sentinel::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get user IDs with the specified role
        $userIds = \DB::table('role_users')
            ->where('role_id', $roleId)
            ->pluck('user_id');

        // Get users by role
        $users = User::whereIn('id', $userIds)
            ->get(['id', 'first_name', 'last_name', 'email', 'designation', 'istrainer', 'office_id']);

        // Load office relationship for each user
        $users->each(function ($user) {
            $user->load('office');
        });

        return response()->json($users);
    }

    /**
     * Get roles by office.
     *
     * @param int $officeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRolesByOffice($officeId)
    {
        if (!Sentinel::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get users in this office with their role relationship
        $users = User::where('office_id', $officeId)->with('role')->get();

        // Extract unique role IDs from role_users table
        $roleIds = \DB::table('role_users')
            ->whereIn('user_id', $users->pluck('id'))
            ->pluck('role_id')
            ->unique()
            ->values();

        // Get role details using Sentinel
        $roles = \Cartalyst\Sentinel\Roles\EloquentRole::whereIn('id', $roleIds)->get(['id', 'name', 'slug']);

        return response()->json($roles);
    }

    /**
     * Get users by office and role.
     *
     * @param int $officeId
     * @param int $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByOfficeRole($officeId, $roleId)
    {
        if (!Sentinel::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get user IDs in this office with the specified role
        $userIds = \DB::table('role_users')
            ->where('role_id', $roleId)
            ->pluck('user_id');

        // Get users by office and role
        $users = User::where('office_id', $officeId)
            ->whereIn('id', $userIds)
            ->get(['id', 'first_name', 'last_name', 'email', 'designation', 'istrainer']);

        return response()->json($users);
    }

    /**
     * Update istrainer status for multiple users.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTrainerStatus(\Illuminate\Http\Request $request)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $user = Sentinel::getUser();
        $role = $user->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to perform this action.');
        }

        $request->validate([
            'user_ids' => 'required|string'
        ]);

        $userIds = explode(',', $request->user_ids);
        $count = 0;

        foreach ($userIds as $id) {
            $userToUpdate = User::find($id);
            if ($userToUpdate && $userToUpdate->istrainer != 1) {
                $userToUpdate->istrainer = 1;
                $userToUpdate->save();
                $count++;
            }
        }

        return redirect()->route('learning.settings.teachers')
            ->with('toastr_type', 'success')
            ->with('toastr_message', "Successfully granted trainer status to {$count} user(s).");
    }

    /**
     * Remove trainer status from a user.
     *
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeTrainerStatus($userId)
    {
        if (!Sentinel::check()) {
            return redirect('login');
        }

        $currentUser = Sentinel::getUser();
        $role = $currentUser->roles->first();
        $isAdmin = $role && in_array($role->id, ['1']);

        if (!$isAdmin) {
            return redirect()->route('learning.settings')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'You do not have permission to perform this action.');
        }

        $userToUpdate = User::find($userId);
        
        if (!$userToUpdate) {
            return redirect()->route('learning.settings.teachers')
                ->with('toastr_type', 'error')
                ->with('toastr_message', 'User not found.');
        }

        $userToUpdate->istrainer = 0;
        $userToUpdate->save();

        return redirect()->route('learning.settings.teachers')
            ->with('toastr_type', 'success')
            ->with('toastr_message', "Successfully revoked trainer status from {$userToUpdate->first_name} {$userToUpdate->last_name}.");
    }
}
