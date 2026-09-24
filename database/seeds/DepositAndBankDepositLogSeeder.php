<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepositAndBankDepositLogSeeder extends Seeder
{
    /**
     * Seed the deposits table then the bank_deposit_log table.
     *
     * Deposit.php has a global scope (status = 1) so we insert via DB::table
     * to avoid any model-level filtering during seeding.
     *
     * Prerequisites assumed to already exist:
     *   - deposit_types rows with id 1, 3, 5 (mandatory) and id 2, 4, 6 (other)
     *   - offices rows with id 1 and 2
     *   - users row with id 1
     */
    public function run(): void
    {
        // ── 1. Deposits ──────────────────────────────────────────────────────

        $today    = Carbon::today()->toDateString();
        $lastMonth = Carbon::today()->subMonth()->toDateString();

        $deposits = [
            // Mandatory types (3 = savings, 1 = building, 5 = statutory)
            [
                'deposit_type' => 3,
                'office'       => 67,
                'amount'       => 5000.00,
                'debt'         => 0,
                'status'       => 0,
                'date'         => $today,
            ],
        ];

        // Insert via DB::table so the global scope on Deposit does not interfere.
        DB::table('deposits')->insert($deposits);

        // Fetch back the IDs in insertion order so we can reference them below.
        $insertedIds = DB::table('deposits')
            ->orderByDesc('id')
            ->limit(count($deposits))
            ->pluck('id')
            ->reverse()   // restore original order
            ->values();

        // ── 2. BankDepositLog ────────────────────────────────────────────────

        $now = Carbon::now()->toDateTimeString();

        $logs = [
            [
                'deposit_type'     => 3,
                'office_id'        => 67,
                'user_id'          => 1,
                'amount'           => 5000.00,
                'deposit_method'   => 'bank_transfer',
                'reference_number' => 'REF-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'created_date'     => $now,
                'deposit_id'       => $insertedIds[0],
            ],
        ];

        DB::table('bank_deposit_log')->insert($logs);
    }
}
