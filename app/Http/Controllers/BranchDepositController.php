<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class BranchDepositController extends Controller
{
    public function branchDeposits(Request $request){

        $selectedMonth = $request->get('month', date('Y-m'));

        $parts = explode('-', $selectedMonth);
        $selectedMonth = sprintf('%02d-%s', $parts[1], $parts[0]);
        $selectedMonthForInput = $parts[0] . '-' . $parts[1];

        $status = Deposit::depositStatusByMonth(Sentinel::getUser()->office_id, $selectedMonth);

        return view('branch-deposits.index', compact('selectedMonth','selectedMonthForInput','status'));
    }

    public function getOverallHistory(Request $request)
    {
        $officeId = Sentinel::getUser()->office_id;
        
        // Get all deposits with their related data for the current office
        $deposits = Deposit::withoutGlobalScope('approved')->with(['depositTypeInfo', 'bankDepositLog.user'])
            ->where('office', $officeId)
            ->whereHas('bankDepositLog')
            ->orderBy('date', 'desc')
            ->get();
        
        // Group by month-year
        $groupedDeposits = $deposits->groupBy(function($deposit) {
            return date('F Y', strtotime($deposit->date));
        });
        
        // Format the data
        $formattedData = [];
        foreach ($groupedDeposits as $monthYear => $monthDeposits) {
            $formattedData[$monthYear] = [
                'total_amount' => $monthDeposits->sum('amount'),
                'deposits' => $monthDeposits->map(function($deposit) {
                    return [
                        'id' => $deposit->id,
                        'date' => $deposit->date,
                        'amount' => $deposit->amount,
                        'status' => $deposit->status ? 'Verified & Approved':'Pending verification',
                        'deposit_type_name' => $deposit->depositTypeInfo?->name ?? 'N/A',
                        'deposit_type_id' => $deposit->deposit_type,
                        'bank_log_amount' => $deposit->bankDepositLog?->amount ?? 0,
                        'deposit_method' => $deposit->bankDepositLog?->deposit_method ?? 'N/A',
                        'reference_number' => $deposit->bankDepositLog?->reference_number ?? 'N/A',
                        'user_name' => ($deposit->bankDepositLog?->user?->first_name ?? '') . ' ' . ($deposit->bankDepositLog?->user?->last_name ?? ''),
                        'created_date' => $deposit->bankDepositLog?->created_date ?? null,
                    ];
                })->toArray()
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $formattedData
        ]);
    }
}
