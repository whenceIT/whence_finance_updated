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
     * Display a listing of audits
     */
    public function index(Request $request)
    {
        $filters = $request->only(['auditable_type', 'auditable_id', 'user_id', 'event', 'created_at_from', 'created_at_to']);
        $audits = $this->auditorService->getAudits($filters);
        $auditEvents = $this->auditorService->getAuditEvents();
        $auditableTypes = $this->auditorService->getAuditableTypes();

        return view('audits.index', compact('audits', 'auditEvents', 'auditableTypes', 'filters'));
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
}