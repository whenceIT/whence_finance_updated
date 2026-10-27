<?php

namespace App\Http\Controllers;

use App\Models\WorkstreamExpense;
use App\Models\WorkstreamExpensePayment;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkstreamExpensePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    /*
     * Records a payment outcome that has already occurred elsewhere.
     * This action does not initiate a transfer.
     */
    public function store(Request $request, $id)
    {
        

        $validated = $request->validate([
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:191',
            'amount' => 'required|numeric|min:0.01|max:999999999999.99',
            'status' => 'required|in:pending,paid,failed',
            'notes' => 'nullable|string|max:5000',
        ]);

        if ($validated['status'] === 'paid' && empty($validated['reference_number'])) {
            return redirect()->back()->withErrors([
                'reference_number' => 'A payment reference is required for a completed payment.',
            ])->withInput();
        }

        $user = Sentinel::getUser();

        DB::transaction(function () use ($id, $validated, $user) {
            $expense = WorkstreamExpense::where('id', $id)->lockForUpdate()->firstOrFail();

            if (!Sentinel::hasAccess('workstream1.expenses.view_all')
                && (int) $user->office_id !== (int) $expense->office_id) {
                abort(403, 'You cannot record payment for another branch.');
            }

            if ($expense->status !== 'approved') {
                abort(422, 'Only approved expenses can have payment records.');
            }

            if ((float) $validated['amount'] > (float) $expense->amount) {
                abort(422, 'Payment amount cannot exceed the approved expense amount.');
            }

            WorkstreamExpensePayment::create([
                'expense_id' => $expense->id,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'amount' => $validated['amount'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'recorded_by_id' => $user->id,
                'recorded_at' => now(),
            ]);
        });

        return redirect('workstream1/expenses/' . $id)
            ->with('success', 'Payment outcome recorded. No transfer was initiated by this action.');
    }
}
