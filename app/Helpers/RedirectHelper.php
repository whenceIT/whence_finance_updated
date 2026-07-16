<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
    public static function redirectByRole($role)
    {
        switch ($role) {
            case 1: // Admin
                return redirect()->route('admin.dashboard');
            case 2: // Branch Manager
                return redirect()->route('bm.dashboard');
            case 3: // Loan Officer
                return redirect()->route('lo.dashboard');
            case 4: // Provincial Manager
                return redirect()->route('pm.dashboard');
            case 5: // Finance
                return redirect()->route('finance.dashboard');
            case 6: // Risk
                return redirect()->route('risk.dashboard');
            default:
                return redirect()->route('dashboard');
        }
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
}