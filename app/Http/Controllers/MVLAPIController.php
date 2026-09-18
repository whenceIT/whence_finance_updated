<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $page = $request->input('page', 1);
        $perPage = 20;
        $now = Carbon::now();

        $totalResult = DB::select("SELECT COUNT(*) as total FROM loans l WHERE l.loan_product_id = 0 AND l.first_repayment_date IS NOT NULL AND l.first_repayment_date < ?", [$now]);
        $total = (int) $totalResult[0]->total;

        $offset = ($page - 1) * $perPage;

        $loans = DB::select("
            SELECT 
                l.id,
                l.status,
                l.created_date,
                CONCAT(c.first_name, ' ', c.last_name) AS client_name,
                v.registration_number
            FROM loans l
            LEFT JOIN clients c ON c.id = l.client_id
            LEFT JOIN vehicles v ON v.loan_id = l.id
            WHERE l.loan_product_id = 0
                AND l.first_repayment_date IS NOT NULL
                AND l.first_repayment_date < ?
            ORDER BY l.first_repayment_date ASC
            LIMIT ? OFFSET ?
        ", [$now, $perPage, $offset]);

        $records = [];
        foreach ($loans as $loan) {
            $totals = DB::select("SELECT COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit FROM loan_transactions WHERE loan_id = ?", [$loan->id]);
            $debit = floatval($totals[0]->total_debit);
            $credit = floatval($totals[0]->total_credit);
            $balance = $debit - $credit;

            $transactions = DB::select("SELECT id, date, transaction_type, debit, credit FROM loan_transactions WHERE loan_id = ? ORDER BY date ASC", [$loan->id]);

            $transArr = [];
            foreach ($transactions as $t) {
                $transArr[] = [
                    'id' => $t->id,
                    'date' => $t->date ? \Carbon\Carbon::parse($t->date)->format('Y-m-d') : 'N/A',
                    'transaction_type' => $t->transaction_type,
                    'debit' => floatval($t->debit ?? 0),
                    'credit' => floatval($t->credit ?? 0),
                ];
            }

            $records[] = [
                'id' => $loan->id,
                'loan_id' => $loan->id,
                'client_name' => $loan->client_name ?? 'N/A',
                'registration_number' => $loan->registration_number ?? 'N/A',
                'balance' => round($balance, 2),
                'status' => $loan->status,
                'created_date' => $loan->created_date ? \Carbon\Carbon::parse($loan->created_date)->format('Y-m-d H:i:s') : 'N/A',
                'transactions' => $transArr,
            ];
        }

        return response()->json([
            'success' => true,
            'records' => $records,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
        ]);
    }
}
