<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\Office;

class RedirectHelper
{
    /**
     * Redirect user based on their role
     */
    public static function redirectBasedOnRole()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $role = $user->role_id ?? null;

        return self::redirectByRole($role);
    }

    /**
     * Redirect based on specific role ID
     */
    public static function redirecttoDashboard()
    {
        $user = Sentinel::getUser();

        $office = Office::where([
            'pscan' => 1,
            'cscan' => 1,
        ])->first();

        if(in_array($user, (array) config('role.exec', []))) {
            return null;
        }

        if ($office && $user->has_completed_profile == 0 && $user?->office?->province_id == $office->province_id) {
            return redirect()->route('user.profile.complete')->send();
        }

        if (in_array($user, (array) config('role.risk', [])) || $user->email == 'brightenockphiri@gmail.com' ) {
            return redirect()->route('risk.dashboard')->send();
        }

        return null;
    }

    /**
     * Check if user has permission to access route
     */
    public static function hasAccess($roleId, $requiredRoles = [])
    {
        if (empty($requiredRoles)) {
            return true;
        }

        return in_array($roleId, $requiredRoles);
    }

    /**
     * Redirect with error if no access
     */
    public static function ifNoAccess($roleId, $requiredRoles, $redirectRoute = 'dashboard', $message = 'You do not have permission to access this resource.')
    {
        if (!self::hasAccess($roleId, $requiredRoles)) {
            Session::flash('error', $message);
            return redirect()->route($redirectRoute)->send();
        }

        return null;
    }

    /**
     * Get dashboard route name based on role
     */
    public static function getDashboardRoute($role)
    {
        $routes = [
            1 => 'admin.dashboard',
            2 => 'bm.dashboard',
            3 => 'lo.dashboard',
            4 => 'pm.dashboard',
            5 => 'finance.dashboard',
            6 => 'risk.dashboard',
        ];

        return $routes[$role] ?? 'dashboard';
    }

    /**
     * Get default redirect path after logout or unauthorized action
     */
    public static function getDefaultRedirect()
    {
        return '/';
    }

    /**
     * Back with error message
     */
    public static function backWithError($message)
    {
        Session::flash('error', $message);
        return redirect()->back();
    }

    /**
     * Back with success message
     */
    public static function backWithSuccess($message)
    {
        Session::flash('success', $message);
        return redirect()->back();
    }

    /**
     * Redirect to route with error
     */
    public static function toRouteWithError($route, $message)
    {
        Session::flash('error', $message);
        return redirect()->route($route);
    }

    /**
     * Redirect to route with success
     */
    public static function toRouteWithSuccess($route, $message)
    {
        Session::flash('success', $message);
        return redirect()->route($route);
    }

    /**
     * Get advances without transactions in current month (This Month tab)
     */
    public static function getThisMonthAdvances($baseQuery, $currentMonth)
    {
        return (clone $baseQuery)
            ->where('status', 'approved')
            ->whereDoesntHave('transactions', function ($q) use ($currentMonth) {
                $q->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth]);
            })
            ->get();
    }
}