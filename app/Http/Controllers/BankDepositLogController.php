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
}