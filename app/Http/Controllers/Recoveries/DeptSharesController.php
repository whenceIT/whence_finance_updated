<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnitShare;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class DeptSharesController extends Controller
{
    public function index(Request $request)
    {
        $unitShares = UnitShare::with(['loan.client', 'loan.recoveryCase', 'user', 'office'])
            ->orderByDesc('created_at')
            ->get();

        $totalUnitShare = $unitShares->sum('amount');

        $withLoan   = $unitShares->filter(fn($s) => $s->loan_id !== null)->count();
        $totalLoans = $unitShares->pluck('loan_id')->filter()->unique()->count();

        return view('recoveries.dept-shares', [
            'unitShares'   => $unitShares,
            'totalUnitShare' => $totalUnitShare,
            'withLoan'     => $withLoan,
            'totalLoans'   => $totalLoans,
            'loans'        => \App\Models\Loan::with(['client', 'office'])
                ->whereIn('status', ['disbursed', 'closed'])
                ->orderByDesc('id')
                ->limit(500)
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'  => 'required|numeric|min:0',
            'loan_id' => 'nullable|integer|exists:loans,id',
            'unit'    => 'nullable|string|max:255',
            'notes'   => 'nullable|string',
        ]);

        $loan = $validated['loan_id']
            ? \App\Models\Loan::find($validated['loan_id'])
            : null;

        UnitShare::create([
            'unit'      => $validated['unit'] ?? 'unit_share',
            'amount'    => $validated['amount'],
            'loan_id'   => $loan?->id,
            'office_id' => $loan?->office_id ?? Sentinel::getUser()->office_id,
            'user_id'   => Sentinel::getUser()->id,
            'notes'     => $validated['notes'] ?? null,
        ]);

        return response()->json(['message' => 'Unit Share recorded successfully']);
    }
}