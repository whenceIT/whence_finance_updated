<?php

namespace App\Http\Controllers;

use App\Models\WorkstreamExpense;
use App\Models\WorkstreamExpenseApproval;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkstreamExpenseApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function index()
    {
       

        $user = Sentinel::getUser();
        $query = WorkstreamExpense::with(['office', 'type', 'createdBy'])
            ->where('status', 'pending')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc');

        if (!Sentinel::hasAccess('workstream1.expenses.view_all')) {
            $query->where('office_id', $user->office_id);
        }

        $expenses = $query->paginate(25);

        return view('workstream1.expenses.approvals', compact('expenses'));
    }

    public function update(Request $request, $id, $decision)
    {
       

        if (!in_array($decision, ['approved', 'declined'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid decision.',
            ], 422);
        }

        $request->validate([
            'comments' => 'nullable|string|max:5000',
        ]);

        $user = Sentinel::getUser();

        $expense = DB::transaction(function () use ($id, $decision, $request, $user) {
            $expense = WorkstreamExpense::where('id', $id)->lockForUpdate()->firstOrFail();
            $this->authorizeOffice($expense->office_id);

            if ($expense->status !== 'pending') {
                abort(422, 'Only pending expenses can be approved or declined.');
            }

            // Separation of duties: creator cannot approve their own expense.
            if ((int) $expense->created_by_id === (int) $user->id) {
                abort(403, 'You cannot approve or decline an expense you created.');
            }

            $expense->status = $decision;
            $expense->save();

            WorkstreamExpenseApproval::create([
                'expense_id' => $expense->id,
                'action' => $decision,
                'acted_by_id' => $user->id,
                'comments' => $request->input('comments'),
                'acted_at' => now(),
            ]);

            return $expense;
        });

        return response()->json([
            'success' => true,
            'message' => 'Expense ' . $decision . ' successfully.',
            'expense_id' => $expense->id,
            'status' => $expense->status,
        ]);
    }

    public function bulk(Request $request)
    {
       

        $validated = $request->validate([
            'ids' => 'required|array|min:1|max:100',
            'ids.*' => 'required|integer|distinct|exists:ws1_expenses,id',
            'decision' => 'required|in:approved,declined',
            'comments' => 'nullable|string|max:5000',
        ]);

        $user = Sentinel::getUser();
        $processed = 0;

        foreach ($validated['ids'] as $id) {
            DB::transaction(function () use ($id, $validated, $user, &$processed) {
                $expense = WorkstreamExpense::where('id', $id)->lockForUpdate()->first();

                if (!$expense || $expense->status !== 'pending') {
                    return;
                }

                $this->authorizeOffice($expense->office_id);

                if ((int) $expense->created_by_id === (int) $user->id) {
                    return;
                }

                $expense->status = $validated['decision'];
                $expense->save();

                WorkstreamExpenseApproval::create([
                    'expense_id' => $expense->id,
                    'action' => $validated['decision'],
                    'acted_by_id' => $user->id,
                    'comments' => $validated['comments'] ?? null,
                    'acted_at' => now(),
                ]);

                $processed++;
            });
        }

        return response()->json([
            'success' => true,
            'message' => $processed . ' expense(s) processed.',
            'processed' => $processed,
        ]);
    }

    protected function requirePermission($permission)
    {
        if (!Sentinel::hasAccess($permission)) {
            abort(403, 'Permission denied.');
        }
    }

    protected function authorizeOffice($officeId)
    {
        $user = Sentinel::getUser();
        if (!Sentinel::hasAccess('workstream1.expenses.view_all')
            && (int) $user->office_id !== (int) $officeId) {
            abort(403, 'You cannot approve expenses for another branch.');
        }
    }
}
