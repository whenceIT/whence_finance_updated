<?php
namespace App\Http\Controllers;

use App\Models\WageBill;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class MyPayrollController extends Controller
{
    public function index()
    {
        $user = Sentinel::getUser();

        if (!$user) {
            abort(403);
        }

        $payrolls = WageBill::with('branch')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('cycle_start', 'desc')
            ->paginate(12);

        $latestPayroll = WageBill::with('branch')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('cycle_start', 'desc')
            ->first();

        return view('wage_bills.my_payroll', compact(
            'payrolls',
            'latestPayroll'
        ));
    }
}
