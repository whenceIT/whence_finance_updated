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
        $totalSettings = 5; // Total number of settings sections
        $totalCategories = CourseCategory::count();
        $totalStudents = 0; // Would need to query actual student count
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

        // Get all trainers with their office and roles
        $trainers = User::where('istrainer', 1)
            ->with(['office', 'roles'])
            ->get();

        // Get all active offices
        $offices = Office::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

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

        // Get users in this office
        $users = User::where('office_id', $officeId)
            ->with('roles')
            ->get();

        // Extract unique roles
        $roles = [];
        foreach ($users as $user) {
            foreach ($user->roles as $userRole) {
                $roleId = $userRole->id;
                if (!isset($roles[$roleId])) {
                    $roles[$roleId] = [
                        'id' => $roleId,
                        'name' => $userRole->name,
                        'slug' => $userRole->slug
                    ];
                }
            }
        }

        return response()->json(array_values($roles));
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

        // Get users by office and role
        $users = User::where('office_id', $officeId)
            ->with('roles')
            ->get()
            ->filter(function($user) use ($roleId) {
                return $user->roles->contains('id', $roleId);
            });

        $userList = $users->map(function($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'designation' => $user->designation,
                'istrainer' => $user->istrainer
            ];
        })->values();

        return response()->json($userList);
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
