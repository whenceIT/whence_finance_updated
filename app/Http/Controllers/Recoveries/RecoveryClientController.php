<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\GeneralHelper;
use App\Mail\SendLoginDetailsEmail;
use App\Models\AuditLogs;
use App\Models\BlacklistHistory;
use App\Models\BlacklistReason;
use App\Models\Client;
use App\Models\ClientIdentification;
use App\Models\ClientLocation;
use App\Models\ClientNextOfKin;
use App\Models\ClientUser;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\Document;
use App\Models\Loan;
use App\Models\Note;
use App\Models\ClientTransferRequest;
use App\Models\Office;
use App\Models\Notifix;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Http;
use App\Models\Savings;
use App\Models\Setting;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Intervention\Image\Facades\Image as InterventionImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Laracasts\Flash\Flash;
use Carbon\Carbon;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;


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

        
        $user = Sentinel::getUser();
        $userInfo = GeneralHelper::get_user_info();
        $type = request()->get('type', 'dormant');
        $search = request()->get('search', '');

        if ($type === 'recovered') {
            $clientQuery = Client::where('status', 'active')
                ->where('is_dormant_recovery', 1)
                ->with(['loans' => function ($query) {
                    $query->where('status', 'closed')->latest('created_at');
                }, 'office', 'staff']);
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
        } else {
            $clientQuery = Client::where('is_dormant_recovery', 0)->where('status', 'active')
                ->with(['loans' => function ($query) {
                    $query->latest('created_at');
                }, 'office', 'staff']);
        }

        if ($userInfo->role == 6) {
            $clientQuery->whereHas('office', function ($q) use ($user) {
                $q->where('province_id', $user->province_id);
            });
        } elseif ($userInfo->role == 4) {
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
