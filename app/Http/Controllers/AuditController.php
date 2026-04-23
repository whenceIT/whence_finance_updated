<?php

namespace App\Http\Controllers;

use App\Services\AuditorService;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    protected $auditorService;

    public function __construct(AuditorService $auditorService)
    {
        $this->auditorService = $auditorService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        try {
            $users = \App\Models\User::query();

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $users->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $users->where('status', 'active');
                } elseif ($request->status === 'inactive') {
                    $users->where('status', '!=', 'active')->orWhereNull('status');
                }
            }

            $users = $users->paginate(20);

        } catch (\Throwable $th) {
            dd($th->getMessage());
            return back()->with('error', 'An error occurred while fetching users: ' . $th->getMessage());
        }

        return view('audits.index', compact('users'));
    }

    /**
     * Show the form for creating a new audit (not typically used, but for completeness)
     */
    public function create()
    {
        // Audits are auto-generated, so no create
        abort(404);
    }

    /**
     * Store a newly created audit (not used)
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified audit
     */
    public function show($id)
    {
        $audit = $this->auditorService->getAuditById($id);
        return view('audits.show', compact('audit'));
    }

    /**
     * Show the form for editing the specified audit (not used)
     */
    public function edit($id)
    {
        abort(404);
    }

    /**
     * Update the specified audit (not used)
     */
    public function update(Request $request, $id)
    {
        abort(404);
    }

    /**
     * Remove the specified audit from storage
     */
    public function destroy($id)
    {
        $this->auditorService->deleteAudit($id);
        return redirect()->route('audits.index')->with('success', 'Audit deleted successfully.');
    }

    /**
     * Get audits for a specific user
     */
    public function userAudits(Request $request, $userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $audits = $this->auditorService->getAudits(['user_id' => $userId]);

        if ($request->ajax()) {
            return response()->json([
                'audits' => $audits->take(20)->values() // Limit to recent 20 for AJAX
            ]);
        }

        return view('audits.user', compact('user', 'audits'));
    }
}