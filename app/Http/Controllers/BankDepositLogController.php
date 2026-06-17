<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankDepositLogController extends Controller
{
    public function getDepositsWithRecords()
    {
        $officeId = request('office_id');

        $query = DB::table('bank_deposit_log')
            ->join('deposits', 'bank_deposit_log.deposit_id', '=', 'deposits.id')
            ->whereNotNull('bank_deposit_log.deposit_id')
            ->select([
                'bank_deposit_log.id',
                'bank_deposit_log.deposit_type',
                'bank_deposit_log.office_id',
                'bank_deposit_log.user_id',
                'bank_deposit_log.amount',
                'bank_deposit_log.deposit_method',
                'bank_deposit_log.reference_number',
                'bank_deposit_log.created_date as date',
                'bank_deposit_log.deposit_id',
                'deposits.date as deposit_date',
            ]);

        if ($officeId !== null) {
            $query->where('bank_deposit_log.office_id', $officeId);
        }

        $deposits = $query->get();

        $deposits = $deposits->map(function ($d) {
            $d->deposit_type_name = DB::table('deposit_types')->where('id', $d->deposit_type)->value('name');
            return $d;
        });

        return response()->json(['data' => $deposits]);
    }

public function getLedgerSummary()
    {
        $data = [];
        $officeId = request('office_id');
        
        $query = \App\Models\Office::query();
        if ($officeId) {
            $query->where('id', $officeId);
        }
        $offices = $query->get();
        
        foreach ($offices as $office) {
            $buildingType = \App\Models\DepositType::find(3);
            $statutoryType = \App\Models\DepositType::find(5);
             
            $ledger1 = \App\Models\DebtBalances::where('office_id', $office->id)->where('deposit_type_id', $buildingType->id )->first();
            $ledger2 = \App\Models\DebtBalances::where('office_id', $office->id)->where('deposit_type_id', $statutoryType->id )->first();
            $buildingExemption = $buildingType 
                ? \App\Models\DepositMonthExemption::get_months_exempted($office->id, $buildingType) 
                : 0;
            $statutoryExemption = $statutoryType 
                ? \App\Models\DepositMonthExemption::get_months_exempted($office->id, $statutoryType) 
                : 0;
            
            $overallPeriodBuildingMonths = date('n') - $buildingExemption;
            $overallPeriodStatutoryMonths = date('n') - $statutoryExemption;
            
            $statutory_required = 14500;
            $building_required = 10000;

            $building_paid = DB::table('deposits')
                ->join('bank_deposit_log', 'deposits.id', '=', 'bank_deposit_log.deposit_id')
                ->join('deposit_types', 'deposits.deposit_type', '=', 'deposit_types.id')
                ->where('deposit_types.id', 3)
                ->where('deposits.office', $office->id)
                ->sum('deposits.amount');

            $statutory_paid = DB::table('deposits')
                ->join('bank_deposit_log', 'deposits.id', '=', 'bank_deposit_log.deposit_id')
                ->join('deposit_types', 'deposits.deposit_type', '=', 'deposit_types.id')
                ->where('deposit_types.id', 5)
                ->where('deposits.office', $office->id)
                ->sum('deposits.amount');

                // dd($ledger);
            $data[] = [
                'office_name' => $office->name,
                'building_paid' => $building_paid,
                'building_outstanding' => ($building_required * $overallPeriodBuildingMonths) - $building_paid,
                'statutory_paid' => $statutory_paid,
                'statutory_outstanding' => ($statutory_required * $overallPeriodStatutoryMonths) - $statutory_paid,
                'ledger_balance_building' => $ledger1,
                'ledger_balance_statutory' =>  $ledger2
            ];
        }
    
        return response()->json(['data' => $data]);
    }
}