<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Office;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Carbon\Carbon;


class RecoveryClientController extends Controller
{
    public function dormant_clients()
    {
        return redirect()->route('recovery.clients', ['type' => 'dormant']);
    }

    public function recovered_clients()
    {
        return redirect()->route('recovery.clients', ['type' => 'recovered']);
    }

    public function recovery_clients()
    {
        try {
            
            $user = Sentinel::getUser();
            dd($user);
            $type = request()->get('type', 'dormant');
            dd($type);
            $search = request()->get('search', '');
            dd($search);

            if ($type === 'recovered') {
                $clientQuery = Client::where('status', 'active')
                    ->where('is_dormant_recovery', 1)
                    ->with(['loans' => function ($query) {
                        $query->where('status', 'closed')->latest('created_at');
                    }, 'office', 'staff']);
                dd('1 ',$clientQuery);
            } elseif ($type === 'overdue') {
                $clientQuery = Client::where('status', 'active')
                    ->whereHas('loans', function ($query) {
                        $query->where('status', 'disbursed')
                            ->whereNotNull('first_repayment_date')
                            ->where('first_repayment_date', '<', Carbon::now()->toDateString());
                    })
                    ->with(['loans' => function ($query) {
                        $query->where('status', 'disbursed')
                            ->whereNotNull('first_repayment_date')
                            ->where('first_repayment_date', '<', Carbon::now()->toDateString())
                            ->latest('first_repayment_date');
                    }, 'office', 'staff']);
                dd('2 ',$clientQuery);
            } else {
                
                $clientQuery = Client::where('is_dormant_recovery', 0)->where('status', 'active')
                    ->with(['loans' => function ($query) {
                        $query->latest('created_at');
                    }, 'office', 'staff']);

                dd('3 ',$clientQuery);
            }

            if (!$user || !$user->role) {
                return redirect()->route('login');
            }
            
            if ($user->role->role_id == 6) {
                $clientQuery->whereHas('office', function ($q) use ($user) {
                    $q->where('province_id', $user->province_id);
                });
            } elseif ($user->role->role_id == 4) {
                $clientQuery->where('office_id', $user->office_id);
            }

            if ($search) {
                $clientQuery->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('nrc_number', 'like', "%{$search}%");
                });
            }

            $clientQuery->whereHas('office');
            $allClients = $clientQuery->get();

            $now = \Carbon\Carbon::now();
            $threeMonthsAgo = $now->copy()->subMonths(3);

            if ($type === 'dormant') {
                $filteredClients = $allClients->filter(function ($client) use ($threeMonthsAgo) {
                    if ($client->loans->isEmpty()) {
                        return true;
                    }
                    $lastLoan = $client->loans->first();
                    return $lastLoan && $lastLoan->created_at < $threeMonthsAgo;
                });
            } else {
                $filteredClients = $allClients;
            }

            $clientsData = $filteredClients->slice(0, 50)->values();

            return view('recoveries.dormant_clients', compact('clientsData', 'type'));
        } catch (\Throwable $th) {
            dd($th);
        }
    }
    
    public function mark_recovered($id)
    {
        $client = Client::find($id);
        
        if (!$client) {
            return response()->json(['success' => false, 'message' => 'Client not found']);
        }
        
        $client->is_dormant_recovery = 1;
        $client->save();

        return response()->json(['success' => true, 'message' => 'Client marked as recovered!']);
    }
}
