<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Models\RecoveryCase;
use App\Models\RecoveryPayment;
use App\Models\Loan;
use App\Models\Expense;
use App\Models\Office;
use App\Models\User;
use App\Models\Specialist;
use App\Models\LoanTransactionUnapproved;
use App\Models\UserRole;
use Carbon\Carbon;
use App\Services\RecoveryCaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use App\Models\LedgerIncome;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class RecoveryCaseController extends Controller
{
    public function __construct(public RecoveryCaseService $caseService)
    {
        $this->middleware('sentinel');
    }

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

    public function dormant_clients()
    {
        return redirect()->route('recovery.clients', ['type' => 'dormant']);
    }

    public function resolved(Request $request)
    {
        $allCases = RecoveryCase::resolved()
            ->with(['client', 'assignedSpecialist', 'originBranch.province'])
            ->latest()
            ->get();

        $categories = RecoveryCase::CATEGORIES;
        $categoryCounts = \App\Enums\RecoveryCategory::allCounts();
        
        // Group cases by province
        $casesByProvince = $allCases->groupBy(function($case) {
            return $case->originBranch && $case->originBranch->province 
                ? $case->originBranch->province->name 
                : 'Unknown Province';
        })->sortKeys();

        return view('recoveries.cases.index', compact('casesByProvince', 'categories', 'categoryCounts'));
    }

    private function listCases(Request $request, ?string $category)
    {
     
        $query = RecoveryCase::with(['client', 'assignedSpecialist', 'originBranch.province'])
            ->whereNotNull('approved_date')
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

        $allCases   = $query->get();
        $categories = RecoveryCase::CATEGORIES;
        $categoryCounts = \App\Enums\RecoveryCategory::allCounts();

        // Group cases by province
        $casesByProvince = $allCases->groupBy(function($case) {
            return $case->originBranch && $case->originBranch->province 
                ? $case->originBranch->province->name 
                : 'Unknown Province';
        })->sortKeys();

        return view('recoveries.cases.index', compact('casesByProvince', 'categories', 'categoryCounts'));
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
                ->where('loans.status', '!=', 'closed')
                ->whereRaw("DATE(loans.first_repayment_date) <= ?", [Carbon::today()->subDays(7)->toDateString()])
                ->whereNotIn('loans.id', function ($query) {
                    $query->select('loan_id')->from('recovery_cases')
                        ->whereNotNull('loan_id');
                })
                ->get();

            $offices = Office::whereNotIn('id', [67, 69,70,71,72,73,74,75,76,77,78]) 
                ->orderBy('name')
                ->get();
            
            // Get specialists with their user relationship
            $specialists = Specialist::with('user')->where('is_active', true)->get();
            
            // Get users with role_id = 3 (Loan Consultants) for escalation dropdown
            $users = User::whereHas('roles', function ($query) {
                $query->where('roles.id', 3);
            })->get();
            

            return view('recoveries.cases.create', compact('categories', 'loans', 'offices', 'specialists', 'users'));
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
        }
    }   

    public function store(Request $request)
    {
        try {            
            $request->validate([
                'loan_id'                 => 'required',
                'origin_branch_id'        => 'required',
                'category'                => 'required|in:' . implode(',', array_keys(RecoveryCase::CATEGORIES)),
                'loan_outstanding_amount' => 'required|numeric|min:0',
            ]);

            
            $existingCase = RecoveryCase::withTrashed()->where('loan_id', $request->loan_id)->first();
            if ($existingCase) {
                return redirect()->back()
                    ->with('error', "A case already exists for this loan (Case #{$existingCase->case_number}).");
            }

            // Derive client_id from the selected loan — no need to expose it in the form
            $loan = Loan::where('id', $request->loan_id)->first();
            $data = array_merge($request->except('_token'), ['client_id' => $loan->client_id]);
            $case = $this->caseService->openCase($data);
            
            return redirect('recovery/case/' . $case->id . '/show')
                ->with('success', "Case {$case->case_number} created successfully.");
        } catch (\Throwable $th) {
            dd($th->getMessage());
            //  return redirect()->back()->with('error', 'An error occurred while creating the case.');
        }
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

    // Recovery Case Approvals
    public function recoveryCaseApprovals(Request $request)
    {
        if (!Sentinel::hasAccess('expenses')) {
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

      
        $userId = Sentinel::getUser()->id;
        $province_id = Sentinel::getUser()->province_id;
        $office_id = Sentinel::getUser()->office_id;
        $offices = Office::get();
        $role = UserRole::where('user_id', $userId)->first();

        
        if ($role->role_id == "6") {
            // Province manager - see all offices in province
            $province_cases = [];
            foreach ($offices as $office) {
                if ($office->province_id == $province_id) {
                    $cases = \App\Models\RecoveryCase::where('origin_branch_id', $office->id)
                        ->whereNull('approved_date')
                        ->with(['client', 'loan', 'assignedSpecialist'])
                        ->get();
                    foreach ($cases as $case) {
                        $province_cases[] = $case;
                    }
                }
            }
            $data = $province_cases;
        } elseif($role->role_id == "1" || $role->role_id == "10" ) {
            // Admin sees all
            $data = RecoveryCase::whereNull('approved_date')
                ->with(['client', 'loan', 'assignedSpecialist'])
                ->get();
                // check soft deletes
        }else{
            // Regular user sees office-specific
            $data = \App\Models\RecoveryCase::where('origin_branch_id', $office_id)
                ->whereNull('approved_date')
                ->with(['client', 'loan', 'assignedSpecialist'])
                ->get();
            Flash::warning("Permission Denied");
            return redirect()->back();
        }

        return view('loan.recovery_case_approvals', compact('data'));
    }

    public function recoveryCaseApprove($id)
    {
        $case = \App\Models\RecoveryCase::findOrFail($id);
        $case->update(['approved_date' => now()]);
        Flash::success('Recovery case approved successfully.');
        return redirect('loan/recovery_case_approvals');
    }

    public function recoveryCaseDecline($id)
    {
        $case = \App\Models\RecoveryCase::findOrFail($id);
        $case->delete();
        Flash::success('Recovery case declined and deleted successfully.');
        return redirect('loan/recovery_case_approvals');
    }

    // Debt Recovery Transaction Approvals
    public function recoveriesApprovals(Request $request)
    {

        $userId = Sentinel::getUser()->id;
        $province_id = Sentinel::getUser()->province_id;
        $office_id = Sentinel::getUser()->office_id;
        $offices = Office::get();
        $role = UserRole::where('user_id', $userId)->first();

        // Query only debt_recovery payment transactions
        $province_transactions = [];
        if ($role->role_id == "6") {
            // Province manager - see all offices in province
            foreach ($offices as $office) {
                if ($office->province_id == $province_id) {
                    $transactions = RecoveryPayment::where('office_id', $office->id)
                        ->where('status', 0)
                        ->get();
                    foreach ($transactions as $transaction) {
                        array_push($province_transactions, $transaction);
                    }
                }
            }
            $data = $province_transactions;
        } else {
            if ($role->role_id == "1" || $role->role_id == "10") {
                // Admin sees all
                $data = RecoveryPayment::where('status', 0)->get();
            } else {
                // Regular user sees office-specific
                $data = RecoveryPayment::where('office_id', $office_id)
                    ->where('status', 0)
                    ->get();
            }
        }

        $data = RecoveryPayment::where('status', 0)
            ->whereHas('recoveryCase', function($query) {
                $query->whereHas('loan');
            })
            ->with([
                'recordedBy',
                'recoveryCase',
                'recoveryCase.loan',
                'recoveryCase.loan.office',
                'recoveryCase.client',
                'recoveryCase.originBranch',
                'recoveryCase.supportingBranch',
                'recoveryCase.assignedSpecialist'
            ])
            ->get();

        return view('loan.recoveries_approvals', compact('data'));
    }

    public function recoveriesApprove($id)
    {
        try {
            $payment = RecoveryPayment::where('id', $id)
                ->with([
                    'recordedBy', 
                    'recoveryCase', 
                    'recoveryCase.loan', 
                    'recoveryCase.client',
                    'recoveryCase.originBranch',
                    'recoveryCase.supportingBranch',
                    'recoveryCase.assignedSpecialist'
                ])
                ->firstOrFail();
            $loan = $payment->recoveryCase->loan;

            // Create payment detail if needed (simplified for now)
            $payment_detail = new \App\Models\PaymentDetail();
            $payment_detail->save();

            // Create approved transaction
            $loan_transaction = new \App\Models\LoanTransaction();
            $loan_transaction->created_by_id = $payment->recordedBy->id;
            $loan_transaction->office_id = $loan->office_id;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->reversible = 1;
            $loan_transaction->payment_apply_to = 'debt_recovery'; // Assuming this is debt recovery
            $loan_transaction->payment_detail_id = $payment_detail->id;
            $loan_transaction->transaction_type = "repayment";
            $loan_transaction->date = $payment->payment_date;
            $date = explode('-', $payment->payment_date);
            $loan_transaction->year = $date[0];
            $loan_transaction->month = $date[1];
            $loan_transaction->credit = $payment->amount;
            $loan_transaction->notes = $payment->notes;
            $loan_transaction->temp_id = $id;
            $loan_transaction->is_recovery = 1;
            $loan_transaction->save();

            // Record branch attributions as expenses
            $recoveryCase = RecoveryCase::where('loan_id', $loan->id)->first();
            if ($recoveryCase) {
                $amount = $loan_transaction->credit;
                $user = Sentinel::getUser();

                // Origin branch attribution
                if ($recoveryCase->origin_branch_attribution_pct > 0) {
                    $attributionAmount = $amount * $recoveryCase->origin_branch_attribution_pct / 100;
                    
                    Expense::create([
                        'office_id' => $recoveryCase->origin_branch_id,
                        'created_by_id' => $user->id,
                        'expense_type_id' => 1, // Default expense type
                        'name' => 'Recovery Attribution - Origin Branch, Case #' . $recoveryCase->case_number,
                        'amount' => $attributionAmount,
                        'date' => $loan_transaction->date,
                        'year' => $loan_transaction->year,
                        'month' => $loan_transaction->month,
                        'gl_account_id' => null,
                        'notes' => 'Recovery attribution for loan ' . $loan->id,
                        'is_attribution' => 1,
                    ]);
                }

                // Supporting branch attribution
                if ($recoveryCase->supporting_branch_attribution_pct > 0) {
                    $attributionAmount = $amount * $recoveryCase->supporting_branch_attribution_pct / 100;
                    
                    LedgerIncome::create([
                        'date' => $loan_transaction->date,
                        'amount' => $attributionAmount,
                        'from' => 'Debt Recovery Attribution - Supporting Branch, Case #' . $recoveryCase->case_number,
                    ]);
                }
            }

            // Update RecoveryPayment status
            $payment->transaction_id = $loan_transaction->id;
            $payment->status = 1;
            $payment->save();

            Flash::success('Recovery payment approved successfully.');
            return redirect('loan/recoveries_approvals');
        } catch (\Throwable $th) {
            
             Flash::error('An error occurred while approving the recovery payment.');
             return redirect('loan/recoveries_approvals');
        }
    }

    public function recoveriesDecline($id)
    {
        $payment = RecoveryPayment::findOrFail($id);
        // Delete the payment
        $payment->delete();
        Flash::success('Recovery payment declined and deleted successfully.');
        return redirect('loan/recoveries_approvals');
    }

}
