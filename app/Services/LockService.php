<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Blockage;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LockService
{
    public function fire_building_functions()
    {
        // placeholder for building-related functions
    }

    public function lock_deposits($selectedMonth = null)
    {
        $selectedMonth = $selectedMonth ?? date('Y-m');

        $parts = explode('-', $selectedMonth);
        if (count($parts) === 2) {
            $selectedMonth = sprintf('%02d-%s', $parts[1], $parts[0]);
        }

        $officeId = Sentinel::getUser()?->office_id ?? null;
        if (!$officeId) {
            return null;
        }

        $status = Deposit::depositStatusByMonth($officeId, $selectedMonth);


        // normalize collection
        if ($status instanceof \Illuminate\Support\Collection) {
            $status = $status->toArray();
        }

        // Get the arrays of deposit statuses that are not fully paid
        $notFullyPaidStatuses = array_filter($status, function ($item) {
            $s = strtolower(trim($item['status'] ?? ''));
            return $s !== 'fully paid';
        });

        // Remove blockage if all deposits are fully paid
        if (empty($notFullyPaidStatuses)) {
            Blockage::where('office_id', $officeId)->delete();
        } else {
            // then create Blockage record for the office if the status is not fully paid
            $year = $parts[0] ?? date('Y');
            $month = $parts[1] ?? date('m');
            $currentMonthYear = date('F Y', strtotime("{$year}-{$month}-01"));

            $reasons = [];
            foreach ($notFullyPaidStatuses as $item) {
                $typeName = $item['deposit_type_name'] ?? ('Type ' . ($item['deposit_type_id'] ?? '')); 
                $remaining = $item['remaining'] ?? 0;
                $reasons[] = sprintf('%s remaining: %s', $typeName, $remaining);
            }

            $consolidatedReason = 'Deposit not fully paid for ' . $currentMonthYear . '. ' . implode('; ', $reasons);

            // Avoid duplicates - one record per office, do nothing if exists
            Blockage::firstOrCreate([
                'office_id' => $officeId
            ], [
                'reason' => $consolidatedReason
            ]);
        }

        return $status;
    }

    public function unblock_deposits($office_id, $selectedMonth = null)
    {
        $selectedMonth = $selectedMonth ?? date('Y-m');

        $parts = explode('-', $selectedMonth);
        if (count($parts) === 2) {
            $selectedMonth = sprintf('%02d-%s', $parts[1], $parts[0]);
        }

        $officeId = $office_id ?? Sentinel::getUser()?->office_id ?? null;
        if (!$officeId) {
            return false;
        }

        $status = Deposit::depositStatusByMonth($officeId, $selectedMonth);

        // normalize collection
        if ($status instanceof \Illuminate\Support\Collection) {
            $status = $status->toArray();
        }

        // Get the arrays of deposit statuses that are not fully paid
        $notFullyPaidStatuses = array_filter($status, function ($item) {
            $s = strtolower(trim($item['status'] ?? ''));
            return $s !== 'fully paid';
        });

        // if only fully paid deposits exist, remove blockage for the office
        if (empty($notFullyPaidStatuses)) {
            Blockage::where('office_id', $officeId)->delete();
        } else {
            //update the blockage reason if there are still not fully paid deposits
            $year = $parts[0] ?? date('Y');
            $month = $parts[1] ?? date('m');
            $currentMonthYear = date('F Y', strtotime("{$year}-{$month}-01"));

            $reasons = [];
            foreach ($notFullyPaidStatuses as $item) {
                $typeName = $item['deposit_type_name'] ?? ('Type ' . ($item['deposit_type_id'] ?? '')); 
                $remaining = $item['remaining'] ?? 0;
                $reasons[] = sprintf('%s remaining: %s', $typeName, $remaining);
            }

            $consolidatedReason = 'Deposit not fully paid for ' . $currentMonthYear . '. ' . implode('; ', $reasons);

            Blockage::where('office_id', $officeId)->update(['reason' => $consolidatedReason]);
        }

        return true;
    }
}