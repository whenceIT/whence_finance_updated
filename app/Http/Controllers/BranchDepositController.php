<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Deadline;
use App\Models\Office;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class BranchDepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }

    public function branchDeposits(Request $request){
        $selectedMonth = $request->get('month', date('Y-m'));

        $parts = explode('-', $selectedMonth);
        $selectedMonth = sprintf('%02d-%s', $parts[1], $parts[0]);
        $selectedMonthForInput = $parts[0] . '-' . $parts[1];

        $status = Deposit::depositStatusByMonth(Sentinel::getUser()->office_id, $selectedMonth);
        $deadline = Deadline::first();

        return view('branch-deposits.index', compact('selectedMonth','selectedMonthForInput','status','deadline'));
    }

    public function blockages(Request $request)
    {
        $blockages = \App\Models\Blockage::with('office')->latest()->get();
        $offices = \App\Models\Office::all();
        $deadline = Deadline::first();
        
        return view('branch-deposits.standalone', compact('blockages', 'offices', 'deadline'));
    }

    public function storeBlockage(Request $request)
    {
        try {
            $officeId = $request->input('office_id');
            $officeIds = is_array($officeId) ? $officeId : [$officeId];
            
            $validated = $request->validate([
                'reason' => 'required|string|max:1000',
            ]);
            
            foreach ($officeIds as $id) {
                if (!Office::where('id', $id)->exists()) {
                    throw new \Exception('Invalid office ID: ' . $id);
                }
            }

            $createdBlockages = [];
            foreach ($officeIds as $officeId) {
                $blockage = \App\Models\Blockage::create([
                    'office_id' => $officeId,
                    'reason' => $validated['reason']
                ]);
                $createdBlockages[] = [
                    'id' => $blockage->id,
                    'office_id' => $blockage->office_id,
                    'office_name' => $blockage->office?->name ?? 'N/A',
                    'reason' => $blockage->reason,
                    'created_at' => $blockage->created_at?->format('Y-m-d H:i:s'),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Blockage records created successfully for ' . count($createdBlockages) . ' office(s)',
                'data' => $createdBlockages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blockage record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyBlockage($id)
    {
        try {
            $blockage = \App\Models\Blockage::findOrFail($id);
            $blockage->delete();

            return response()->json([
                'success' => true,
                'message' => 'Office unblocked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unblock: ' . $e->getMessage()
            ], 500);
        }
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

    public function getDeadline()
    {
        $deadline = Deadline::first();
        return response()->json([
            'success' => true,
            'data' => $deadline
        ]);
    }

    public function updateDeadline(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'countdown_date' => 'required|date',
        ]);

        $deadline = Deadline::first();

        if ($deadline) {
            $deadline->update([
                'name' => $validated['name'],
                'countdown_date' => $validated['countdown_date'],
            ]);
        } else {
            $deadline = Deadline::create([
                'name' => $validated['name'],
                'countdown_date' => $validated['countdown_date'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Deadline updated successfully',
            'data' => $deadline
        ]);
    }

    public function branchDepositTransactions(Request $request)
    {
        $selectedMonth = $request->get('month', date('Y-m'));
        $depositTypeId = $request->get('deposit_type_id');

        $parts = explode('-', $selectedMonth);
        $monthNum = $parts[1] ?? date('m');
        $year = $parts[0] ?? date('Y');

        $query = Deposit::withoutGlobalScope('approved')
            ->with(['depositTypeInfo', 'office', 'bankDepositLog.user']);

        $query->whereYear('date', $year)
            ->whereMonth('date', $monthNum);

        if ($depositTypeId) {
            $query->where('deposit_type', $depositTypeId);
        }

        $deposits = $query->orderBy('date', 'desc')->get();

        $depositTypes = \App\Models\DepositType::orderBy('name')->get();

        return view('risk.branch-deposit-transactions', compact(
            'deposits',
            'depositTypes',
            'selectedMonth',
            'depositTypeId'
        ));
    }
}
