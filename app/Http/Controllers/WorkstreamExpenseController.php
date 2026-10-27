<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\ExpenseType;
use App\Models\GlAccount;
use App\Models\WorkstreamExpense;
use App\Models\WorkstreamExpenseDocument;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WorkstreamExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function index(Request $request)
    {


        $user = Sentinel::getUser();
        $query = WorkstreamExpense::with(['office', 'type', 'createdBy', 'latestPayment'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if (!$this->canViewAll()) {
            $query->where('office_id', $user->office_id);
        } elseif ($request->filled('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $expenses = $query->paginate(25)->appends($request->all());
        $offices = $this->canViewAll() ? Office::orderBy('name')->get() : collect();

        return view('workstream1.expenses.index', compact('expenses', 'offices'));
    }

    public function dashboard()
    {
    

        $user = Sentinel::getUser();
        $query = WorkstreamExpense::query();
        if (!$this->canViewAll()) {
            $query->where('office_id', $user->office_id);
        }

        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        $summary = [
            'pending_count' => (clone $query)->where('status', 'pending')->count(),
            'approved_count' => (clone $query)->where('status', 'approved')->count(),
            'declined_count' => (clone $query)->where('status', 'declined')->count(),
            'month_total' => (clone $query)->whereBetween('date', [$monthStart, $monthEnd])
                ->whereIn('status', ['pending', 'approved'])->sum('amount'),
        ];

        return view('workstream1.expenses.dashboard', compact('summary'));
    }

    public function create()
    {

        $user = Sentinel::getUser();
        $offices = $this->canManageAll()
            ? Office::orderBy('name')->get()
            : Office::where('id', $user->office_id)->get();

        $types = ExpenseType::orderBy('name')->get();
        $glAccounts = GlAccount::orderBy('name')->get();

        return view('workstream1.expenses.create', compact('offices', 'types', 'glAccounts'));
    }

    public function store(Request $request)
    {


        $user = Sentinel::getUser();
        $canManageAll = $this->canManageAll();

        $rules = [
            'office_id' => 'required|integer|exists:offices,id',
            'expense_type_id' => 'nullable|integer|exists:expense_types,id',
            'gl_account_id' => 'nullable|integer|exists:gl_accounts,id',
            'name' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0.01|max:999999999999.99',
            'date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:10000',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:10240',
        ];
        $validated = $request->validate($rules);

        if (!$canManageAll && (int) $validated['office_id'] !== (int) $user->office_id) {
            abort(403, 'You cannot create expenses for another branch.');
        }

        $expense = DB::transaction(function () use ($validated, $user) {
            return WorkstreamExpense::create([
                'office_id' => $validated['office_id'],
                'created_by_id' => $user->id,
                'expense_type_id' => $validated['expense_type_id'] ?? null,
                'gl_account_id' => $validated['gl_account_id'] ?? null,
                'name' => $validated['name'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
            ]);
        });

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }
                $path = $file->store('workstream1-expenses/' . $expense->id, 'local');
                WorkstreamExpenseDocument::create([
                    'expense_id' => $expense->id,
                    'uploaded_by_id' => $user->id,
                    'original_name' => basename($file->getClientOriginalName()),
                    'storage_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect('workstream1/expenses/' . $expense->id)
            ->with('success', 'Expense submitted for approval.');
    }

    public function show($id)
    {
   

        $expense = WorkstreamExpense::with([
            'office', 'type', 'glAccount', 'createdBy',
            'approvals.actor', 'payments.recordedBy', 'documents.uploadedBy'
        ])->findOrFail($id);

        $this->authorizeOffice($expense->office_id);

        return view('workstream1.expenses.show', compact('expense'));
    }

    public function downloadDocument($id, $documentId)
    {


        $expense = WorkstreamExpense::findOrFail($id);
        $this->authorizeOffice($expense->office_id);

        $document = WorkstreamExpenseDocument::where('expense_id', $expense->id)
            ->where('id', $documentId)->firstOrFail();

        if (!Storage::disk('local')->exists($document->storage_path)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            $document->storage_path,
            $document->original_name
        );
    }

    protected function requirePermission($permission)
    {
        if (!Sentinel::hasAccess($permission)) {
            abort(403, 'Permission denied.');
        }
    }

    protected function canViewAll()
    {
        return Sentinel::hasAccess('workstream1.expenses.view_all');
    }

    protected function canManageAll()
    {
        return Sentinel::hasAccess('workstream1.expenses.manage_all');
    }

    protected function authorizeOffice($officeId)
    {
        $user = Sentinel::getUser();
        if (!$this->canViewAll() && (int) $user->office_id !== (int) $officeId) {
            abort(403, 'You cannot access expenses for another branch.');
        }
    }
}
