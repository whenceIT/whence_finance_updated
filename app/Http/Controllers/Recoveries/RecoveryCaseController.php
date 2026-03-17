<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Models\RecoveryCase;
use App\Models\Loan;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use App\Services\RecoveryCaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RecoveryCaseController extends Controller
{
    public function __construct(private RecoveryCaseService $caseService) {}

    // ── Index / filtered list views ──────────────────────────────────

    public function index(Request $request)
    {
        return $this->listCases($request, null);
    }

    public function crossBranch(Request $request)
    {
        return $this->listCases($request, 'cross_branch');
    }

    public function escalated(Request $request)
    {
        return $this->listCases($request, 'escalated');
    }

    public function dormant(Request $request)
    {
        return $this->listCases($request, 'dormant');
    }

    public function legal(Request $request)
    {
        return $this->listCases($request, 'legal');
    }

    public function skipTrace(Request $request)
    {
        return $this->listCases($request, 'skip_trace');
    }

    public function resolved(Request $request)
    {
        $cases = RecoveryCase::resolved()
            ->with(['client', 'assignedSpecialist', 'originBranch'])
            ->latest()
            ->paginate(20);

        $categories = RecoveryCase::CATEGORIES;
        return view('recoveries.cases.index', compact('cases', 'categories'));
    }

    private function listCases(Request $request, ?string $category)
    {
        $query = RecoveryCase::with(['client', 'assignedSpecialist', 'originBranch'])
            ->latest();

        if ($category) {
            $query->byCategory($category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('specialist_id')) {
            $query->assignedTo($request->specialist_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($c) => $c->where('full_name', 'like', "%{$search}%"));
            });
        }

        $cases      = $query->paginate(20)->withQueryString();
        $categories = RecoveryCase::CATEGORIES;

        return view('recoveries.cases.index', compact('cases', 'categories'));
    }

    // ── CRUD ─────────────────────────────────────────────────────────

    public function create()
    {
        try {
            $categories = RecoveryCase::CATEGORIES;
            Log::info('Loading create case form with optimized loan query');

            $loans = DB::table('loans')
                ->select('loans.*', 'clients.first_name', 'clients.last_name')
                ->leftJoin('clients', 'loans.client_id', '=', 'clients.id')
                ->whereNotNull('loans.first_repayment_date')
                ->whereRaw("DATE(loans.first_repayment_date) <= ?", [Carbon::today()->subDays(7)->toDateString()])
                ->get();

            Log::info('Loading create case form with optimized loan query 2');
            $offices = Office::orderBy('name')->get();
            Log::info('Loading create case form with optimized loan query 3');
            $users = User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
            Log::info('Loading create case form with optimized loan query 4');

            return view('recoveries.cases.create', compact('categories', 'loans', 'offices', 'users'));
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }   

    public function store(Request $request)
    {
        $request->validate([
            'loan_id'                 => 'required|exists:loans,id',
            'origin_branch_id'        => 'required|exists:offices,id',
            'category'                => 'required|in:' . implode(',', array_keys(RecoveryCase::CATEGORIES)),
            'loan_outstanding_amount' => 'required|numeric|min:0',
        ]);

        // Derive client_id from the selected loan — no need to expose it in the form
        $loan = \App\Models\Loan::findOrFail($request->loan_id);
        $data = array_merge($request->except('_token'), ['client_id' => $loan->client_id]);

        $case = $this->caseService->openCase($data);

        return redirect('recovery/case/' . $case->id . '/show')
            ->with('success', "Case {$case->case_number} created successfully.");
    }

    public function show($id)
    {
        $case = RecoveryCase::with([
            'client', 'loan', 'originBranch', 'supportingBranch',
            'assignedSpecialist', 'escalatedBy',
            'activities.performedBy',
            'payments.recordedBy',
            'documents.uploadedBy',
        ])->findOrFail($id);

        return view('recoveries.cases.show', compact('case'));
    }

    public function edit($id)
    {
        $case       = RecoveryCase::findOrFail($id);
        $categories = RecoveryCase::CATEGORIES;
        $offices = \App\Models\Office::orderBy('name')->get();
        return view('recoveries.cases.edit', compact('case', 'categories', 'offices'));
    }

    public function update(Request $request, $id)
    {
        $case = RecoveryCase::findOrFail($id);
        $case->update($request->except(['_token']));

        return redirect('recovery/case/' . $case->id . '/show')
            ->with('success', 'Case updated successfully.');
    }

    public function destroy($id)
    {
        RecoveryCase::findOrFail($id)->delete();
        return redirect('recovery/case/data')->with('success', 'Case archived.');
    }

    // ── Case actions ─────────────────────────────────────────────────

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'note'   => 'nullable|string|max:500',
        ]);

        $case = RecoveryCase::findOrFail($id);
        $this->caseService->updateStatus($case, $request->status, $request->note ?? '');

        return redirect()->back()->with('success', 'Case status updated.');
    }

    public function recordPayment(Request $request, $id)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer,cheque,payroll_deduction',
            'payment_date'   => 'required|date|before_or_equal:today',
        ]);

        $case    = RecoveryCase::findOrFail($id);
        $payment = $this->caseService->recordPayment($case, $request->all());

        return redirect()->back()
            ->with('success', 'Payment of K' . number_format($payment->amount, 2) . ' recorded.');
    }

    public function recordCost(Request $request, $id)
    {
        $request->validate([
            'cost_type'   => 'required|in:recovery_costs,legal_costs_incurred,skip_trace_costs',
            'amount'      => 'required|numeric|min:0.01',
            'cost_date'   => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:500',
        ]);

        $case = RecoveryCase::findOrFail($id);
        $this->caseService->recordCost($case, $request->all());

        return redirect()->back()
            ->with('success', 'Cost of K' . number_format($request->amount, 2) . ' recorded.');
    }

    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'document'      => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'title'         => 'required|string|max:255',
            'document_type' => 'required|string',
        ]);

        $case = RecoveryCase::findOrFail($id);
        $file = $request->file('document');
        $path = $file->store('recovery-documents/' . $case->id, 'public');

        $case->documents()->create([
            'uploaded_by'   => optional(\Sentinel::getUser())->id,
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

        return redirect()->back()->with('success', 'Document uploaded.');
    }

    public function downloadDocument(Request $request, $id, $document_id)
    {
        $case     = RecoveryCase::findOrFail($id);
        $document = $case->documents()->findOrFail($document_id);

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }

    public function deleteDocument(Request $request, $id, $document_id)
    {
        $case     = RecoveryCase::findOrFail($id);
        $document = $case->documents()->findOrFail($document_id);

        \Illuminate\Support\Facades\Storage::disk('public')->delete($document->file_path);
        $document->delete();

        $this->caseService->logActivity($case, [
            'activity_type' => 'document_deleted',
            'description'   => 'Document deleted: ' . $document->title,
        ]);

        return redirect()->back()->with('success', 'Document deleted.');
    }

    public function assign(Request $request, $id)
    {
        $request->validate(['specialist_id' => 'required|exists:users,id']);
        $case = RecoveryCase::findOrFail($id);
        $this->caseService->assignSpecialist($case, $request->specialist_id);

        return redirect()->back()->with('success', 'Specialist assigned.');
    }
}