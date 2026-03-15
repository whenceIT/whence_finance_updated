<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recoveries\StoreCaseRequest;
use App\Http\Requests\Recoveries\UpdateCaseStatusRequest;
use App\Http\Requests\Recoveries\RecordPaymentRequest;
use App\Models\{RecoveryCase, RecoveryActivity, RecoveryPayment, RecoveryDocument};
use App\Services\RecoveryCaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CaseController extends Controller
{
    public function __construct(private RecoveryCaseService $caseService)
    {
        $this->middleware('auth');
    }

    /**
     * List all cases with filters.
     * GET /recoveries/cases
     */
    public function index(Request $request)
    {
        $query = RecoveryCase::with(['client', 'assignedSpecialist', 'originBranch'])
            ->latest();

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('specialist_id')) {
            $query->assignedTo($request->specialist_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($c) => $c->where('full_name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('period')) {
            $query->forPeriod($request->period);
        }

        $cases      = $query->paginate(20)->withQueryString();
        $categories = RecoveryCase::CATEGORIES;

        return view('recoveries.cases.index', compact('cases', 'categories'));
    }

    /**
     * Show case creation form.
     * GET /recoveries/cases/create
     */
    public function create()
    {
        $categories = RecoveryCase::CATEGORIES;
        return view('recoveries.cases.create', compact('categories'));
    }

    /**
     * Store new case.
     * POST /recoveries/cases
     */
    public function store(StoreCaseRequest $request)
    {
        $case = $this->caseService->openCase($request->validated());

        return redirect()
            ->route('recoveries.cases.show', $case)
            ->with('success', "Case {$case->case_number} created successfully.");
    }

    /**
     * Show individual case detail.
     * GET /recoveries/cases/{case}
     */
    public function show(RecoveryCase $case)
    {
        $case->load([
            'client',
            'loan',
            'originBranch',
            'supportingBranch',
            'assignedSpecialist',
            'escalatedBy',
            'activities.performedBy',
            'payments.receivedBy',
            'documents.uploadedBy',
        ]);

        return view('recoveries.cases.show', compact('case'));
    }

    /**
     * Show edit form.
     * GET /recoveries/cases/{case}/edit
     */
    public function edit(RecoveryCase $case)
    {
        $categories = RecoveryCase::CATEGORIES;
        return view('recoveries.cases.edit', compact('case', 'categories'));
    }

    /**
     * Update case details.
     * PUT /recoveries/cases/{case}
     */
    public function update(StoreCaseRequest $request, RecoveryCase $case)
    {
        $case->update($request->validated());

        return redirect()
            ->route('recoveries.cases.show', $case)
            ->with('success', 'Case updated successfully.');
    }

    /**
     * Update case status (AJAX or form).
     * PATCH /recoveries/cases/{case}/status
     */
    public function updateStatus(UpdateCaseStatusRequest $request, RecoveryCase $case)
    {
        $this->caseService->updateStatus(
            $case,
            $request->status,
            $request->note ?? '',
            auth()->id()
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Status updated.', 'case' => $case->fresh()]);
        }

        return back()->with('success', 'Case status updated.');
    }

    /**
     * Record a payment against a case.
     * POST /recoveries/cases/{case}/payments
     */
    public function recordPayment(RecordPaymentRequest $request, RecoveryCase $case)
    {
        $payment = $this->caseService->recordPayment($case, $request->validated());

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Payment recorded.', 'payment' => $payment]);
        }

        return back()->with('success', "Payment of K" . number_format($payment->amount, 2) . " recorded.");
    }

    /**
     * Upload a document to a case.
     * POST /recoveries/cases/{case}/documents
     */
    public function uploadDocument(Request $request, RecoveryCase $case)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'title'    => 'required|string|max:255',
            'document_type' => 'required|string',
        ]);

        $file = $request->file('document');
        $path = $file->store("recovery-documents/{$case->id}", 'private');

        $case->documents()->create([
            'uploaded_by'   => auth()->id(),
            'document_type' => $request->document_type,
            'title'         => $request->title,
            'file_path'     => $path,
            'file_name'     => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'notes'         => $request->notes,
        ]);

        $this->caseService->logActivity($case, [
            'activity_type' => 'document_uploaded',
            'description'   => "Document uploaded: {$request->title}",
        ]);

        return back()->with('success', 'Document uploaded.');
    }

    /**
     * Assign a specialist to a case.
     * PATCH /recoveries/cases/{case}/assign
     */
    public function assign(Request $request, RecoveryCase $case)
    {
        $request->validate(['specialist_id' => 'required|exists:users,id']);
        $this->caseService->assignSpecialist($case, $request->specialist_id);

        return back()->with('success', 'Specialist assigned.');
    }

    /**
     * Soft-delete (archive) a case.
     * DELETE /recoveries/cases/{case}
     */
    public function destroy(RecoveryCase $case)
    {
        $case->delete();
        return redirect()->route('recoveries.cases.index')->with('success', 'Case archived.');
    }
}
