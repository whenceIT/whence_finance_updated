<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanTransaction;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MVLAPIController extends Controller
{
    public function getLoans(Request $request)
    {
        $query = Loan::where('loan_product_id', 0)
            ->with(['client', 'vehicle'])
            ->orderBy('created_date', 'desc');

        $page = $request->input('page', 1);
        $perPage = 20;
        $total = $query->count();
        $loans = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'success' => true,
            'records' => $loans->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'loan_id' => $loan->loan_id ?? $loan->id,
                    'client_name' => $loan->client ? $loan->client->first_name . ' ' . $loan->client->last_name : 'N/A',
                    'registration_number' => $loan->vehicle ? $loan->vehicle->registration_number : 'N/A',
                    'principal' => $loan->principal ?? 0,
                    'status' => $loan->status,
                    'created_date' => $loan->created_date ? \Carbon\Carbon::parse($loan->created_date)->format('Y-m-d H:i:s') : 'N/A',
                ];
            }),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }

    public function getVehicles(Request $request)
    {
        $query = Vehicle::whereHas('loan', function ($q) {
            $q->where('loan_product_id', 0);
        })->with(['loan', 'loan.client'])
            ->orderBy('created_at', 'desc');

        $page = $request->input('page', 1);
        $perPage = 20;
        $total = $query->count();
        $vehicles = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'success' => true,
            'records' => $vehicles->map(function ($vehicle) {
                $loan = $vehicle->loan;
                return [
                    'id' => $vehicle->id,
                    'loan_id' => $loan ? $loan->id : null,
                    'client_name' => $loan && $loan->client ? $loan->client->first_name . ' ' . $loan->client->last_name : 'N/A',
                    'registration_number' => $vehicle->registration_number ?? 'N/A',
                    'principal' => $loan ? ($loan->principal ?? 0) : 0,
                    'status' => $loan ? $loan->status : 'N/A',
                    'created_date' => $vehicle->created_at ? \Carbon\Carbon::parse($vehicle->created_at)->format('Y-m-d H:i:s') : 'N/A',
                ];
            }),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }

    public function getPortfolio(Request $request)
    {
        $query = Loan::where('loan_product_id', 0)
            ->where('status', '!=', 'closed')
            ->with(['client', 'vehicle'])
            ->orderBy('principal', 'desc');

        $page = $request->input('page', 1);
        $perPage = 20;
        $total = $query->count();
        $loans = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'success' => true,
            'records' => $loans->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'loan_id' => $loan->loan_id ?? $loan->id,
                    'client_name' => $loan->client ? $loan->client->first_name . ' ' . $loan->client->last_name : 'N/A',
                    'registration_number' => $loan->vehicle ? $loan->vehicle->registration_number : 'N/A',
                    'principal' => $loan->principal ?? 0,
                    'status' => $loan->status,
                    'created_date' => $loan->created_date ? \Carbon\Carbon::parse($loan->created_date)->format('Y-m-d H:i:s') : 'N/A',
                ];
            }),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }

    public function getCollections(Request $request)
    {
        $loanIds = Loan::where('loan_product_id', 0)
            ->whereHas('transactions', function ($q) {
                $q->where('transaction_type', 'repayment')
                    ->whereIn('payment_apply_to', ['reloan_payment', 'full_payment', 'part_payment']);
            })
            ->with(['client', 'vehicle', 'transactions' => function ($q) {
                $q->where('transaction_type', 'repayment')
                    ->whereIn('payment_apply_to', ['reloan_payment', 'full_payment', 'part_payment']);
            }])
            ->orderBy('created_date', 'desc');

        $page = $request->input('page', 1);
        $perPage = 20;
        $total = $loanIds->count();
        $loans = $loanIds->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'success' => true,
            'records' => $loans->map(function ($loan) {
                $collectionAmount = $loan->transactions->reduce(function ($sum, $t) {
                    $credit = floatval($t->credit ?? 0);
                    if ($t->payment_apply_to === 'reloan_payment') {
                        return $sum + floatval($t->balance_bf ?? 0);
                    }
                    return $sum + $credit;
                }, 0);

                return [
                    'id' => $loan->id,
                    'loan_id' => $loan->loan_id ?? $loan->id,
                    'client_name' => $loan->client ? $loan->client->first_name . ' ' . $loan->client->last_name : 'N/A',
                    'registration_number' => $loan->vehicle ? $loan->vehicle->registration_number : 'N/A',
                    'principal' => $loan->principal ?? 0,
                    'collection_amount' => $collectionAmount,
                    'status' => $loan->status,
                    'created_date' => $loan->created_date ? \Carbon\Carbon::parse($loan->created_date)->format('Y-m-d H:i:s') : 'N/A',
                ];
            }),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }

    
    public function getDefaulted(Request $request)
    {
        $query = Loan::where('loan_product_id', 0)
            ->whereNotNull('first_repayment_date')
            ->where('first_repayment_date', '<', Carbon::now())
            ->with(['client', 'vehicle', 'transactions'])
            ->orderBy('first_repayment_date', 'asc');

        $page = $request->input('page', 1);
        $perPage = 20;
        $total = $query->count();
        $loans = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return response()->json([
            'success' => true,
            'records' => $loans->map(function ($loan) {
                $defaultedTransactions = LoanTransaction::where('loan_id', $loan->id);
                $debit = floatval($defaultedTransactions->sum('debit'));
                $credit = floatval($defaultedTransactions->sum('credit'));
                $balance = $debit - $credit;

                return [
                    'id' => $loan->id,
                    'loan_id' => $loan->loan_id ?? $loan->id,
                    'client_name' => $loan->client ? $loan->client->first_name . ' ' . $loan->client->last_name : 'N/A',
                    'registration_number' => $loan->vehicle ? $loan->vehicle->registration_number : 'N/A',
                    'balance' => round($balance, 2),
                    'status' => $loan->status,
                    'created_date' => $loan->created_date ? \Carbon\Carbon::parse($loan->created_date)->format('Y-m-d H:i:s') : 'N/A',
                    'transactions' => LoanTransaction::where('loan_id', $loan->id)->get()->map(function ($t) {
                        return [
                            'id' => $t->id,
                            'date' => $t->date ? \Carbon\Carbon::parse($t->date)->format('Y-m-d') : 'N/A',
                            'transaction_type' => $t->transaction_type,
                            'debit' => floatval($t->debit ?? 0),
                            'credit' => floatval($t->credit ?? 0),
                        ];
                    }),
                ];
            }),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }
}
