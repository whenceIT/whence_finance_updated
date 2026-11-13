<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Office;
use App\Models\WageBill;
use Carbon\Carbon;

class SyncWageBills extends Command
{
    protected $signature = 'payroll:sync-wage-bills';

    protected $description = 'Synchronize branch wage bills from the salary predictor API';

    // Salary predictor endpoint
    private const SALARY_PREDICTOR_URL =
        'https://lms2backend.whencefinancesystem.com/salary-predictor';

    public function handle()
    {
        $offices = Office::where('active', 1)->get();

        $this->info("Starting wage bill synchronization...");

        $successfulBranches = 0;
        $failedBranches = 0;
        $processedEmployees = 0;

        foreach ($offices as $office) {
            try {
                $this->info("Processing branch ID: {$office->id}");

                $response = Http::timeout(30)
                    ->retry(2, 1000)
                    ->get(self::SALARY_PREDICTOR_URL . '/' . $office->id);

                if (!$response->successful()) {
                    throw new \Exception(
                        "API returned HTTP status {$response->status()}"
                    );
                }

                $data = $response->json();

                if (!is_array($data) ||
                    !isset($data['office_id']) ||
                    !isset($data['cycle_start']) ||
                    !isset($data['consultants']) ||
                    !is_array($data['consultants'])) {

                    throw new \Exception(
                        "Invalid or incomplete API response."
                    );
                }

                // Confirm that the API returned data for the requested branch.
                if ((int) $data['office_id'] !== (int) $office->id) {
                    throw new \Exception(
                        "API office_id does not match requested branch."
                    );
                }

                // Validate the payroll cycle start date.
                $cycleStart = Carbon::createFromFormat(
                    '!Y-m-d',
                    $data['cycle_start']
                );

                if ($cycleStart->format('Y-m-d') !== $data['cycle_start'] ||
                    $cycleStart->day !== 25) {

                    throw new \Exception(
                        "Invalid cycle_start. Expected a valid date on the 25th."
                    );
                }

                foreach ($data['consultants'] as $consultant) {

                    if (!isset($consultant['user_id'])) {
                        Log::warning('Payroll sync: consultant missing user_id', [
                            'branch_id' => $office->id,
                            'consultant' => $consultant,
                        ]);

                        continue;
                    }

                    $userId = (int) $consultant['user_id'];

                    DB::transaction(function () use (
                        $office,
                        $cycleStart,
                        $consultant,
                        $userId
                    ) {
                        $wageBill = WageBill::where('branch_id', $office->id)
                            ->where('user_id', $userId)
                            ->where('cycle_start', $cycleStart->format('Y-m-d'))
                            ->lockForUpdate()
                            ->first();

                        // Create a new record if one does not exist.
                        if (!$wageBill) {
                            $wageBill = new WageBill();

                            $wageBill->branch_id = $office->id;
                            $wageBill->user_id = $userId;
                            $wageBill->cycle_start = $cycleStart->format('Y-m-d');

                            $wageBill->allowances = 0;
                            $wageBill->advance_deductions = 0;
                            $wageBill->manual_overrides = [];
                            $wageBill->status = 'draft';
                        }

                        // Never modify submitted or approved payroll records.
                        if (!in_array($wageBill->status, ['draft', 'returned'])) {
                            return;
                        }

                        $overrides = $wageBill->manual_overrides ?? [];

                        // Always update the system-generated values.
                        $wageBill->predicted_salary = $this->amount(
                            $consultant['predicted_salary'] ?? 0
                        );

                        $wageBill->system_gross_pay = $this->amount(
                            $consultant['gross_salary'] ?? 0
                        );

                        $wageBill->system_paye = $this->amount(
                            $consultant['paye'] ?? 0
                        );

                        $wageBill->system_napsa = $this->amount(
                            $consultant['napsa'] ?? 0
                        );

                        $wageBill->system_nhima = $this->amount(
                            $consultant['nhima'] ?? 0
                        );

                        $wageBill->system_net_pay = $this->amount(
                            $consultant['net_salary'] ?? 0
                        );

                        // Update effective payroll values only when
                        // they have not been manually overridden.
                        if (empty($overrides['gross_pay'])) {
                            $wageBill->gross_pay = $wageBill->system_gross_pay;
                        }

                        if (empty($overrides['paye'])) {
                            $wageBill->paye = $wageBill->system_paye;
                        }

                        if (empty($overrides['napsa'])) {
                            $wageBill->napsa = $wageBill->system_napsa;
                        }

                        if (empty($overrides['nhima'])) {
                            $wageBill->nhima = $wageBill->system_nhima;
                        }

                        // Predicted salary is always system-controlled.
                        // Allowances are not taxed.
                        if (empty($overrides['net_pay'])) {
                            $wageBill->net_pay =
                                (float) $wageBill->predicted_salary
                                + (float) $wageBill->allowances
                                - (float) $wageBill->advance_deductions;
                        }

                        $wageBill->save();
                    });

                    $processedEmployees++;
                }

                $successfulBranches++;

                $this->info(
                    "Successfully processed branch ID: {$office->id}"
                );

            } catch (\Throwable $e) {

                $failedBranches++;

                Log::error('Wage bill synchronization failed', [
                    'branch_id' => $office->id,
                    'error' => $e->getMessage(),
                ]);

                $this->error(
                    "Failed branch {$office->id}: {$e->getMessage()}"
                );
            }
        }

        $this->info("Wage bill synchronization completed.");
        $this->info("Successful branches: {$successfulBranches}");
        $this->info("Failed branches: {$failedBranches}");
        $this->info("Consultants processed: {$processedEmployees}");

        return Command::SUCCESS;
    }

    private function amount($value): float
    {
        if (!is_numeric($value)) {
            return 0;
        }

        return round((float) $value, 2);
    }
}
