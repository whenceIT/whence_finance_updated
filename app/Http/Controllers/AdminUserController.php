<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * Display admin users and available users.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Current administrators
        $adminUsers = User::where('is_admin', 'yes')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('first_name')
            ->paginate(10, ['*'], 'admins_page');

        // Users who are not administrators
        $availableUsers = User::where('is_admin', 'no')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('first_name')
            ->paginate(10, ['*'], 'users_page');

        $totalAdmins = User::where('is_admin', 'yes')->count();
        $totalUsers = User::count();

        return view('admin_users.index', compact(
            'adminUsers',
            'availableUsers',
            'totalAdmins',
            'totalUsers',
            'search'
        ));
    }

    /**
     * Add a user to the admin list.
     */
    public function add($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_admin === 'yes') {
            return redirect()->back()
                ->with('info', 'This user is already an administrator.');
        }

        $user->is_admin = 'yes';
        $user->save();

        return redirect()->back()
            ->with('success', $user->full_name . ' has been added to the admin list.');
    }

    /**
     * Remove a user from the admin list.
     */
    public function remove($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_admin !== 'yes') {
            return redirect()->back()
                ->with('info', 'This user is not currently an administrator.');
        }

        $user->is_admin = 'no';
        $user->save();

        return redirect()->back()
            ->with('success', $user->full_name . ' has been removed from the admin list.');
    }
}
