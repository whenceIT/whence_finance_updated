<?php

namespace App\Services;

use App\Models\{RecoveryCase, RecoveryActivity, RecoveryPayment};
use Illuminate\Support\Facades\DB;

class RecoveryCaseService
{
    /**
     * Open a new recovery case from a loan escalation or manual intake.
     */
    public function openCase(array $data): RecoveryCase
    {
        return DB::transaction(function () use ($data) {
            $data['case_number'] = RecoveryCase::generateCaseNumber();

            // Set default attribution based on category
            $data = $this->applyDefaultAttribution($data);

            $case = RecoveryCase::create($data);

            // If category is dormant, update the loan
            if (isset($data['category']) && $data['category'] === 'dormant') {
                \App\Models\Loan::where('id', $data['loan_id'])->update([
                    'dormant_recovery' => 1,
                    'shared' => 0,
                ]);
            }

            // Log the opening activity
            $this->logActivity($case, [
                'activity_type' => 'status_change',
                'description'   => 'Case opened and assigned to Recoveries Unit.',
                'status_after'  => $case->status,
            ]);

            return $case;
        });
    }

    /**
     * Transition a case to a new status and log the change.
     */
    public function updateStatus(RecoveryCase $case, string $newStatus, string $note = '', int $userId = null): RecoveryCase
    {
        DB::transaction(function () use ($case, $newStatus, $note, $userId) {
            $oldStatus = $case->status;

            $case->update([
                'status'        => $newStatus,
                'resolved_date' => in_array($newStatus, RecoveryCase::RESOLVED_STATUSES) ? now() : null,
            ]);

            $this->logActivity($case, [
                'activity_type' => 'status_change',
                'description'   => $note ?: "Status changed from {$oldStatus} to {$newStatus}.",
                'status_before' => $oldStatus,
                'status_after'  => $newStatus,
                'performed_by'  => $userId ?? optional(\Sentinel::getUser())->id,
            ]);
        });

        return $case->fresh();
    }

/**
      * Record a payment against a recovery case.
      */
     public function recordPayment(RecoveryCase $case, array $data): RecoveryPayment
     {
         return DB::transaction(function () use ($case, $data) {
             $data['recovery_case_id'] = $case->id;
             $data['recorded_by']      = $data['recorded_by'] ?? optional(\Sentinel::getUser())->id;
             $data['receipt_number']   = RecoveryPayment::generateReceiptNumber();

             $payment = RecoveryPayment::createWithAttribution($case, $data);

             // Handle dept_share_amount
             if (isset($data['dept_share_amount']) && $data['dept_share_amount'] > 0) {
                 \App\Models\RecoveriesDeptExcalatedShare::create([
                     'recovery_case_id' => $case->id,
                     'recovery_payment_id' => $payment->id,
                     'dept_share_amount' => $data['dept_share_amount'],
                     'notes' => $data['notes'] ?? null,
                     'created_by' => optional(\Sentinel::getUser())->id,
                 ]);
             }

             // Update the case's running total
             $newTotal = (float)$case->amount_recovered + (float)$data['amount'];
             $case->update(['amount_recovered' => $newTotal]);

             // Auto-close if fully paid
             if ($newTotal >= (float)$case->loan_outstanding_amount) {
                 $this->updateStatus($case, 'closed', 'Loan fully recovered. Case closed.');
             }

             $this->logActivity($case, [
                 'activity_type'  => 'payment_received',
                 'description'    => "Payment of K" . number_format($data['amount'], 2) . " received.",
                 'amount_collected' => $data['amount'],
             ]);

             return $payment;
         });
     }

    /**
     * Assign a case to a specialist and log the handover.
     */
    public function assignSpecialist(RecoveryCase $case, int $specialistId): RecoveryCase
    {
        $case->update(['assigned_specialist_id' => $specialistId]);

        $this->logActivity($case, [
            'activity_type' => 'case_handover',
            'description'   => 'Case assigned to specialist.',
        ]);

        return $case->fresh(['assignedSpecialist']);
    }

    /**
     * Log any activity against a case.
     */
    /**
     * Record a cost against a recovery case.
     * cost_type must match a column on recovery_cases:
     *   recovery_costs | legal_costs_incurred | skip_trace_costs
     */
    public function recordCost(RecoveryCase $case, array $data): void
    {
        DB::transaction(function () use ($case, $data) {
            $costType = $data['cost_type'];  // recovery_costs | legal_costs_incurred | skip_trace_costs
            $amount   = (float) $data['amount'];

            // Increment the relevant cost column on the case
            $case->increment($costType, $amount);

            // Map cost_type to a human label for the description prefix
            $costLabels = [
                'recovery_costs'       => 'General Recovery',
                'legal_costs_incurred' => 'Legal',
                'skip_trace_costs'     => 'Skip Trace',
            ];
            $typeLabel = $costLabels[$costType] ?? ucwords(str_replace('_', ' ', $costType));

            // Log the activity — description prefixed with type label so show view can identify it
            $this->logActivity($case, [
                'activity_type'  => 'cost_incurred',
                'description'    => $typeLabel . ': ' . ($data['description'] ?? $typeLabel . ' cost')
                                    . ' — K' . number_format($amount, 2),
                'cost_incurred'  => $amount,
                'status_before'  => $case->status,
                'status_after'   => $case->status,
                'performed_by'   => optional(\Sentinel::getUser())->id,
            ]);
        });
    }

    public function logActivity(RecoveryCase $case, array $data): RecoveryActivity
    {
        $data['recovery_case_id'] = $case->id;
        $data['performed_by']     = $data['performed_by'] ?? optional(\Sentinel::getUser())->id;

        return RecoveryActivity::create($data);
    }

    private function applyDefaultAttribution(array $data): array
    {
        return match($data['category']) {
            'cross_branch' => array_merge($data, [
                'recoveries_dept_attribution_pct'   => 50,
                'origin_branch_attribution_pct'     => 25,
                'supporting_branch_attribution_pct' => 25,
            ]),
            default => array_merge($data, [
                'recoveries_dept_attribution_pct'   => 100,
                'origin_branch_attribution_pct'     => 0,
                'supporting_branch_attribution_pct' => 0,
            ]),
        };
    }
}
