<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\CashAuditWizardConfig;
use App\Models\CashAuditSubmission;
use App\Models\Office;

class CashAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RISK MANAGER: Toggle the wizard on / off and set the target office
    // POST /risk/cash-audit/toggle
    // Body: { is_active: bool, target_office_id: int|null }
    // ─────────────────────────────────────────────────────────────────────────
    public function toggle(Request $request)
    {
        $user = Sentinel::getUser();

        // Only config('role.risk') user IDs may control this
        if (!$user || !in_array((string) $user->id, array_map('strval', config('role.risk', [])))) {
            return response()->json(['success' => false, 'message' => 'Unauthorised'], 403);
        }

        $validated = $request->validate([
            'is_active'        => 'required|boolean',
            'target_office_id' => 'nullable|integer|exists:offices,id',
        ]);

        // Always upsert into a single row (id = 1 is the canonical config)
        CashAuditWizardConfig::updateOrCreate(
            ['id' => 1],
            [
                'is_active'        => $validated['is_active'],
                'target_office_id' => $validated['target_office_id'] ?? null,
                'created_by'       => $user->id,
            ]
        );

        return response()->json([
            'success'   => true,
            'is_active' => (bool) $validated['is_active'],
            'office_id' => $validated['target_office_id'] ?? null,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RISK MANAGER: Get current wizard config (for dashboard UI)
    // GET /risk/cash-audit/config
    // ─────────────────────────────────────────────────────────────────────────
    public function getConfig()
    {
        $user = Sentinel::getUser();

        if (!$user || !in_array((string) $user->id, array_map('strval', config('role.risk', [])))) {
            return response()->json(['success' => false, 'message' => 'Unauthorised'], 403);
        }

        $config  = CashAuditWizardConfig::with('targetOffice:id,name')->find(1);
        $offices = Office::select('id', 'name')
            ->where('active', 1)
            ->whereNotNull('parent_id')   // branch offices only (not head office)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success'   => true,
            'is_active' => $config ? (bool) $config->is_active : false,
            'office_id' => $config ? $config->target_office_id : null,
            'office'    => $config && $config->targetOffice ? $config->targetOffice->name : null,
            'offices'   => $offices,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BRANCH MANAGER: Submit the wizard responses
    // POST /risk/cash-audit/submit
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $user = Sentinel::getUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            // Step 1 – Branch ID
            'branch_name'            => 'required|string|max:255',
            'district_manager_name'  => 'required|string|max:255',

            // Cash denominations
            'cash_5'   => 'nullable|numeric|min:0',
            'cash_10'  => 'nullable|numeric|min:0',
            'cash_20'  => 'nullable|numeric|min:0',
            'cash_50'  => 'nullable|numeric|min:0',
            'cash_100' => 'nullable|numeric|min:0',
            'cash_200' => 'nullable|numeric|min:0',
            'cash_500' => 'nullable|numeric|min:0',
            'cash_count_datetime' => 'required|date',

            // Petty cash
            'petty_5'   => 'nullable|numeric|min:0',
            'petty_10'  => 'nullable|numeric|min:0',
            'petty_20'  => 'nullable|numeric|min:0',
            'petty_50'  => 'nullable|numeric|min:0',
            'petty_100' => 'nullable|numeric|min:0',
            'petty_200' => 'nullable|numeric|min:0',
            'petty_500' => 'nullable|numeric|min:0',
            'petty_via_mobile_wallet' => 'nullable|boolean',

            // Step 2 – Mobile money
            'mobile_ussd_reference' => 'required|string|max:255',
            'mobile_number'         => 'required|string|max:50',
            'sim_registered_name'   => 'required|string|max:255',
            'dm_using_sim'          => 'required|string|max:255',
        ]);

        // Compute totals server-side
        $denominations = [5, 10, 20, 50, 100, 200, 500];

        $cashTotal  = 0;
        $pettyTotal = 0;
        foreach ($denominations as $d) {
            $cashTotal  += (float) ($validated["cash_{$d}"]  ?? 0) * $d;
            $pettyTotal += (float) ($validated["petty_{$d}"] ?? 0) * $d;
        }

        CashAuditSubmission::create([
            'user_id'   => $user->id,
            'office_id' => $user->office_id,

            'branch_name'           => $validated['branch_name'],
            'district_manager_name' => $validated['district_manager_name'],

            'cash_5'   => $validated['cash_5']   ?? 0,
            'cash_10'  => $validated['cash_10']  ?? 0,
            'cash_20'  => $validated['cash_20']  ?? 0,
            'cash_50'  => $validated['cash_50']  ?? 0,
            'cash_100' => $validated['cash_100'] ?? 0,
            'cash_200' => $validated['cash_200'] ?? 0,
            'cash_500' => $validated['cash_500'] ?? 0,
            'cash_total'          => $cashTotal,
            'cash_count_datetime' => $validated['cash_count_datetime'],

            'petty_5'   => $validated['petty_5']   ?? 0,
            'petty_10'  => $validated['petty_10']  ?? 0,
            'petty_20'  => $validated['petty_20']  ?? 0,
            'petty_50'  => $validated['petty_50']  ?? 0,
            'petty_100' => $validated['petty_100'] ?? 0,
            'petty_200' => $validated['petty_200'] ?? 0,
            'petty_500' => $validated['petty_500'] ?? 0,
            'petty_total'             => $pettyTotal,
            'petty_via_mobile_wallet' => (bool) ($validated['petty_via_mobile_wallet'] ?? false),

            'mobile_ussd_reference' => $validated['mobile_ussd_reference'],
            'mobile_number'         => $validated['mobile_number'],
            'sim_registered_name'   => $validated['sim_registered_name'],
            'dm_using_sim'          => $validated['dm_using_sim'],
        ]);

        return response()->json(['success' => true, 'message' => 'Cash audit submitted successfully.']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RISK MANAGER: List all submissions (JSON — used by dashboard AJAX)
    // GET /risk/cash-audit/submissions
    // ─────────────────────────────────────────────────────────────────────────
    public function getSubmissions(Request $request)
    {
        $user = Sentinel::getUser();

        if (!$user || !in_array((string) $user->id, array_map('strval', config('role.risk', [])))) {
            return response()->json(['success' => false, 'message' => 'Unauthorised'], 403);
        }

        $query = CashAuditSubmission::with(['office:id,name', 'user:id,first_name,last_name'])
            ->orderByDesc('created_at');

        if ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        $submissions = $query->paginate(25);

        return response()->json(['success' => true, 'data' => $submissions]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RISK MANAGER: Full results page (HTML view)
    // GET /risk/cash-audit/results
    // ─────────────────────────────────────────────────────────────────────────
    public function results(Request $request)
    {
        $user = Sentinel::getUser();

        if (!$user || !in_array((string) $user->id, array_map('strval', config('role.risk', [])))) {
            abort(403, 'Unauthorised');
        }

        $officeId = $request->input('office_id');
        $search   = $request->input('search');
        $from     = $request->input('from');
        $to       = $request->input('to');

        $query = CashAuditSubmission::with(['office:id,name', 'user:id,first_name,last_name'])
            ->orderByDesc('created_at');

        if ($officeId) {
            $query->where('office_id', $officeId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('branch_name', 'like', "%{$search}%")
                  ->orWhere('district_manager_name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $submissions = $query->paginate(20)->withQueryString();

        $offices = Office::select('id', 'name')
            ->where('active', 1)
            ->whereNotNull('parent_id')
            ->orderBy('name')
            ->get();

        // Summary stats (unfiltered totals)
        $totalCount      = CashAuditSubmission::count();
        $totalCash       = CashAuditSubmission::sum('cash_total');
        $totalPettyCash  = CashAuditSubmission::sum('petty_total');
        $uniqueOffices   = CashAuditSubmission::distinct('office_id')->count('office_id');

        return view('risk.cash-audit-results', compact(
            'submissions',
            'offices',
            'totalCount',
            'totalCash',
            'totalPettyCash',
            'uniqueOffices'
        ));
    }
}
