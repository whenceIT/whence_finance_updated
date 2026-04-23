<?php

namespace App\Services;


use Illuminate\Database\Eloquent\Model;

class AuditorService
{
    /**
     * Get all audits with optional filters
     */
    public function getAudits(array $filters = [])
    {
        $query = \OwenIt\Auditing\Models\Audit::query();

        if (!empty($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        if (!empty($filters['auditable_id'])) {
            $query->where('auditable_id', $filters['auditable_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (!empty($filters['created_at_from'])) {
            $query->where('created_at', '>=', $filters['created_at_from']);
        }

        if (!empty($filters['created_at_to'])) {
            $query->where('created_at', '<=', $filters['created_at_to']);
        }

        if (!empty($filters['time_period'])) {
            if ($filters['time_period'] === 'work_hours') {
                $query->whereRaw("TIME(created_at) BETWEEN '06:00' AND '17:00'");
            } elseif ($filters['time_period'] === 'after_hours') {
                $query->where(function($q) {
                    $q->whereRaw("TIME(created_at) BETWEEN '19:00' AND '23:59'")
                      ->orWhereRaw("TIME(created_at) BETWEEN '00:00' AND '05:00'");
                });
            }
        }

        if (!empty($filters['user_name'])) {
            $query->whereHas('user', function($q) use ($filters) {
                $q->where('first_name', 'like', "%{$filters['user_name']}%")
                  ->orWhere('last_name', 'like', "%{$filters['user_name']}%");
            });
        }

        return $query->with('user.roles', 'auditable')->orderBy('created_at', 'desc')->paginate(50);
    }

    /**
     * Get audits for a specific model
     */
    public function getAuditsForModel(Model $model)
    {
        return $model->audits()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get audit by ID
     */
    public function getAuditById($id)
    {
        return \OwenIt\Auditing\Models\Audit::findOrFail($id);
    }

    /**
     * Delete an audit (soft delete or hard, depending on need)
     */
    public function deleteAudit($id)
    {
        $audit = \OwenIt\Auditing\Models\Audit::findOrFail($id);
        return $audit->delete();
    }

    /**
     * Get audit events
     */
    public function getAuditEvents()
    {
        return \OwenIt\Auditing\Models\Audit::distinct('event')->pluck('event');
    }

    /**
     * Get auditable types
     */
    public function getAuditableTypes()
    {
        return \OwenIt\Auditing\Models\Audit::distinct('auditable_type')->pluck('auditable_type');
    }

    /**
     * Log a custom audit event
     */
    public function logCustomAudit($auditableType, $auditableId, $event, $userId, $request, $oldValues = [], $newValues = [], $tags = null)
    {
        \OwenIt\Auditing\Models\Audit::create([
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'event' => $event,
            'user_id' => $userId,
            'url' => $request->url(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'tags' => $tags
        ]);
    }

    /**
     * Log login event
     */
    public function logLogin($userId, $request)
    {
        $this->logCustomAudit(
            'App\Models\User',
            $userId,
            'logged in',
            $userId,
            $request,
            [],
            ['action' => 'logged in'],
            'authentication'
        );
    }

    /**
     * Log loan access event
     */
    public function logLoanAccess($user, $request)
    {
        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'accessed active loans view',
            $user->id,
            $request,
            [],
            [
                'action' => 'viewed_active_loans',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'query' => $request->input('query', '')
            ],
            'loan_access'
        );
    }

    /**
     * Log branch loan access event
     */
    public function logBranchLoanAccess($user, $request)
    {
        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'accessed branch active loans view',
            $user->id,
            $request,
            [],
            [
                'action' => 'viewed_branch_active_loans',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'query' => $request->input('query', '')
            ],
            'loan_access'
        );
    }

    /**
     * Log reloan approvals access event
     */
    public function logReloanApprovalsAccess($user, $request)
    {
        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'accessed reloan approvals view',
            $user->id,
            $request,
            [],
            [
                'action' => 'viewed_reloan_approvals',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'office_id' => $user->office_id ?? null
            ],
            'loan_approvals'
        );
    }

    /**
     * Log loan transaction approvals page access event
     */
    public function logTransactionApprovalsPage($user, $request)
    {
        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'accessed loan transactions approvals page',
            $user->id,
            $request,
            [],
            [
                'action' => 'viewed_transaction_approvals_page',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'office_id' => $user->office_id ?? null
            ],
            'transaction_approvals'
        );
    }

    /**
     * Log loan transaction top up approvals page access event
     */
    public function logTransactionTopUpApprovalsPage($user, $request)
    {
        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'accessed loan transactions top up approvals page',
            $user->id,
            $request,
            [],
            [
                'action' => 'viewed_transaction_topup_approvals_page',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'office_id' => $user->office_id ?? null
            ],
            'transaction_approvals'
        );
    }

    /**
     * Log creating a new client loan event
     */
    public function logCreateClientLoan($user, $request)
    {
        $clientId = $request->route('client');
        $loanProductId = $request->route('loan_product');

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'initiated creation of new client loan',
            $user->id,
            $request,
            [],
            [
                'action' => 'create_client_loan_initiated',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'client_id' => $clientId,
                'loan_product_id' => $loanProductId,
                'office_id' => $user->office_id ?? null
            ],
            'loan_creation'
        );
    }

    /**
     * Log storing a new client loan event (after successful creation)
     */
    public function logStoreClientLoan($user, $request, $loan = null, $client = null)
    {
        $newValues = [
            'action' => 'create_client_loan_completed',
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'office_id' => $user->office_id ?? null
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_amount'] = $loan->principal ?? $request->principal;
            $newValues['loan_product_id'] = $loan->loan_product_id ?? $request->route('loan_product');
        } else {
            $newValues['loan_amount'] = $request->principal;
            $newValues['loan_product_id'] = $request->route('loan_product');
        }

        // Add client information if available
        if ($client) {
            $newValues['client_id'] = $client->id;
            $newValues['client_name'] = $client->first_name . ' ' . $client->last_name;
        } else {
            $newValues['client_id'] = $request->route('client');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'successfully created new client loan',
            $user->id,
            $request,
            [],
            $newValues,
            'loan_creation'
        );
    }

    /**
     * Log accessing loan detail page event
     */
    public function logAccessedLoanDetail($user, $request, $loan = null)
    {
        $newValues = [
            'action' => 'viewed_loan_details',
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'office_id' => $user->office_id ?? null
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_amount'] = $loan->principal;
            $newValues['loan_status'] = $loan->status;
            $newValues['client_id'] = $loan->client_id;
            $newValues['loan_product_id'] = $loan->loan_product_id;
        } else {
            $newValues['loan_id'] = $request->route('loan');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'accessed loan details page',
            $user->id,
            $request,
            [],
            $newValues,
            'loan_access'
        );
    }

    /**
     * Log adding loan top up event
     */
    public function logAddedTopUp($user, $request, $loan = null)
    {
        $newValues = [
            'action' => 'added_loan_topup',
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'office_id' => $user->office_id ?? null,
            'topup_amount' => $request->amount,
            'topup_date' => $request->top_up_date ?? date('Y-m-d')
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_principal'] = $loan->principal;
            $newValues['client_id'] = $loan->client_id;
            $newValues['balance_before'] = $loan->principal; // Original loan amount
            $newValues['balance_after'] = $loan->principal + $request->amount; // New total
        } else {
            $newValues['loan_id'] = $request->route('id');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'added top up to loan',
            $user->id,
            $request,
            [],
            $newValues,
            'loan_topup'
        );
    }

    /**
     * Log top up approval request submission event
     */
    public function logTopUpApproval($user, $request, $loan = null)
    {
        $newValues = [
            'action' => 'submitted_topup_approval_request',
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'office_id' => $user->office_id ?? null,
            'topup_amount' => $request->amount,
            'topup_date' => $request->top_up_date ?? date('Y-m-d')
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_principal'] = $loan->principal;
            $newValues['client_id'] = $loan->client_id;
            $newValues['balance_before'] = $loan->principal;
            $newValues['balance_after'] = $loan->principal + $request->amount;
        } else {
            $newValues['loan_id'] = $request->route('id');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'submitted top up approval request',
            $user->id,
            $request,
            [],
            $newValues,
            'topup_approval'
        );
    }

    /**
     * Log top up approval event
     */
    public function logTopUpApproved($user, $request, $loan = null, $topup = null)
    {
        $newValues = [
            'action' => 'approved_loan_topup',
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'office_id' => $user->office_id ?? null
        ];

        // Add topup information if available
        if ($topup) {
            $newValues['topup_id'] = $topup->id;
            $newValues['topup_amount'] = $topup->amount;
            $newValues['topup_date'] = $topup->date;
            $newValues['balance_before'] = $topup->balance_bf;
            $newValues['balance_after'] = $topup->balance_new;
        }

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_principal'] = $loan->principal;
            $newValues['client_id'] = $loan->client_id;
        } else {
            $newValues['loan_id'] = $request->route('id');
            $newValues['topup_id'] = $request->route('trans_id');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'approved loan top up',
            $user->id,
            $request,
            [],
            $newValues,
            'topup_approval'
        );
    }

    /**
     * Log loan update event
     */
    public function logLoanUpdated($user, $request, $loan = null)
    {
        $newValues = [
            'action' => 'updated_client_loan',
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'office_id' => $user->office_id ?? null
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_principal'] = $loan->principal;
            $newValues['loan_status'] = $loan->status;
            $newValues['client_id'] = $loan->client_id;
            $newValues['loan_product_id'] = $loan->loan_product_id;
        } else {
            $newValues['loan_id'] = $request->route('id');
        }

        // Add update information from request
        if ($request->has('principal')) {
            $newValues['updated_principal'] = $request->principal;
        }
        if ($request->has('loan_term')) {
            $newValues['updated_term'] = $request->loan_term;
        }
        if ($request->has('interest_rate')) {
            $newValues['updated_interest_rate'] = $request->interest_rate;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'updated client loan',
            $user->id,
            $request,
            [],
            $newValues,
            'loan_update'
        );
    }

    /**
     * Log loan decline event
     */
    public function logDeclinedLoan($user, $request, $loan = null)
    {
        $newValues = [
            'action'      => 'declined_client_loan',
            'user_name'   => $user->first_name . ' ' . $user->last_name,
            'office_id'   => $user->office_id ?? null,
            'reason'      => $request->reason ?? $request->decline_reason ?? null,
            'declined_at' => now()->toDateTimeString(),
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id']         = $loan->id;
            $newValues['loan_principal']  = $loan->principal;
            $newValues['loan_status']     = $loan->status;
            $newValues['client_id']       = $loan->client_id;
            $newValues['loan_product_id'] = $loan->loan_product_id;
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'declined client loan',
            $user->id,
            $request,
            [],
            $newValues,
            'loan_decline'
        );
    }

    /**
     * Log loan officer change event
     */
    public function logChangedLoanOfficer($user, $request, $loan = null)
    {
        $newValues = [
            'action'           => 'changed_loan_officer',
            'user_name'        => $user->first_name . ' ' . $user->last_name,
            'office_id'        => $user->office_id ?? null,
            'new_officer_id'   => $request->loan_officer_id ?? $request->officer_id ?? $request->user_id ?? null,
            'changed_at'       => now()->toDateTimeString(),
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id']            = $loan->id;
            $newValues['loan_principal']     = $loan->principal;
            $newValues['loan_status']        = $loan->status;
            $newValues['client_id']          = $loan->client_id;
            $newValues['loan_product_id']    = $loan->loan_product_id;
            $newValues['old_officer_id']     = $loan->user_id ?? $loan->loan_officer_id ?? null;
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'changed loan officer',
            $user->id,
            $request,
            [],
            $newValues,
            'loan_officer_change'
        );
    }

    /**
     * Log loan branch change event
     */
    public function logChangedBranch($user, $request, $loan = null)
    {
        $newValues = [
            'action'          => 'changed_loan_branch',
            'user_name'       => $user->first_name . ' ' . $user->last_name,
            'office_id'       => $user->office_id ?? null,
            'new_branch_id'   => $request->branch_id ?? $request->office_id ?? null,
            'changed_at'      => now()->toDateTimeString(),
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id']          = $loan->id;
            $newValues['loan_principal']   = $loan->principal;
            $newValues['loan_status']      = $loan->status;
            $newValues['client_id']        = $loan->client_id;
            $newValues['loan_product_id']  = $loan->loan_product_id;
            $newValues['old_branch_id']    = $loan->office_id ?? $loan->branch_id ?? null;
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'changed loan branch',
            $user->id,
            $request,
            [],
            $newValues,
            'loan_branch_change'
        );
    }

    /**
     * Log loan disbursement event (includes client/loan details)
     */
    public function logDisbursedLoan($user, $request, $loan = null)
    {
        $newValues = [
            'action'        => 'disbursed_client_loan',
            'user_name'     => $user->first_name . ' ' . $user->last_name,
            'office_id'     => $user->office_id ?? null,
            'disbursed_at'  => now()->toDateTimeString(),
        ];

        // Add loan and client information if available
        if ($loan) {
            $newValues['loan_id']          = $loan->id;
            $newValues['loan_principal']   = $loan->principal;
            $newValues['loan_status']      = $loan->status;
            $newValues['loan_product_id']  = $loan->loan_product_id;
            $newValues['interest_rate']    = $loan->interest_rate ?? null;
            $newValues['loan_term']        = $loan->loan_term ?? null;
            $newValues['release_date']     = $loan->release_date ?? $request->release_date ?? null;
            $newValues['maturity_date']    = $loan->maturity_date ?? null;
            $newValues['client_id']        = $loan->client_id;

            // Include client details if relationship is loaded
            if ($loan->relationLoaded('client') && $loan->client) {
                $newValues['client_name'] = $loan->client->first_name . ' ' . $loan->client->last_name;
                $newValues['client_nrc']  = $loan->client->nrc ?? null;
            }

            // Include loan officer
            $newValues['loan_officer_id'] = $loan->user_id ?? $loan->loan_officer_id ?? null;
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        // Add disbursement fields from request if present
        if ($request->has('disbursement_date')) {
            $newValues['disbursement_date'] = $request->disbursement_date;
        }
        if ($request->has('first_payment_date')) {
            $newValues['first_payment_date'] = $request->first_payment_date;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'disbursed client loan',
            $user->id,
            $request,
            [],
            $newValues,
            'loan_disbursement'
        );
    }

    /**
     * Log entering a transaction for approval event (includes loan/client details)
     */
    public function logTransactionForApproval($user, $request, $loan = null)
    {
        $newValues = [
            'action'             => 'submitted_transaction_for_approval',
            'user_name'          => $user->first_name . ' ' . $user->last_name,
            'office_id'          => $user->office_id ?? null,
            'transaction_amount' => $request->amount ?? null,
            'transaction_type'   => $request->type ?? $request->transaction_type ?? null,
            'transaction_date'   => $request->date ?? $request->transaction_date ?? date('Y-m-d'),
            'submitted_at'       => now()->toDateTimeString(),
        ];

        // Add loan and client information if available
        if ($loan) {
            $newValues['loan_id']          = $loan->id;
            $newValues['loan_principal']   = $loan->principal;
            $newValues['loan_status']      = $loan->status;
            $newValues['loan_product_id']  = $loan->loan_product_id;
            $newValues['client_id']        = $loan->client_id;

            // Include client details if relationship is loaded
            if ($loan->relationLoaded('client') && $loan->client) {
                $newValues['client_name'] = $loan->client->first_name . ' ' . $loan->client->last_name;
                $newValues['client_nrc']  = $loan->client->nrc ?? null;
            }
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'submitted transaction for approval',
            $user->id,
            $request,
            [],
            $newValues,
            'transaction_approval'
        );
    }

    /**
     * Log entering a transaction for approval (includes loan/client details)
     */
    public function logEnteredTransaction($user, $request, $loan = null)
    {
        $newValues = [
            'action'             => 'entered_transaction_for_approval',
            'user_name'          => $user->first_name . ' ' . $user->last_name,
            'office_id'          => $user->office_id ?? null,
            'transaction_amount' => $request->amount ?? null,
            'transaction_type'   => $request->type ?? $request->transaction_type ?? null,
            'transaction_date'   => $request->date ?? $request->transaction_date ?? date('Y-m-d'),
            'submitted_at'       => now()->toDateTimeString(),
        ];

        // Add loan and client information if available
        if ($loan) {
            $newValues['loan_id']          = $loan->id;
            $newValues['loan_principal']   = $loan->principal;
            $newValues['loan_status']      = $loan->status;
            $newValues['loan_product_id']  = $loan->loan_product_id;
            $newValues['client_id']        = $loan->client_id;

            // Include client details if relationship is loaded
            if ($loan->relationLoaded('client') && $loan->client) {
                $newValues['client_name'] = $loan->client->first_name . ' ' . $loan->client->last_name;
                $newValues['client_nrc']  = $loan->client->nrc ?? null;
            }
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'entered transaction for approval',
            $user->id,
            $request,
            [],
            $newValues,
            'transaction_entry'
        );
    }

    /**
     * Log approving a transaction (includes loan/client details)
     */
    public function logApprovedTransaction($user, $request, $loan = null)
    {
        $newValues = [
            'action'          => 'approved_transaction',
            'user_name'       => $user->first_name . ' ' . $user->last_name,
            'office_id'       => $user->office_id ?? null,
            'transaction_id'  => $request->route('trans_id') ?? null,
            'approved_at'     => now()->toDateTimeString(),
        ];

        // Add loan and client information if available
        if ($loan) {
            $newValues['loan_id']          = $loan->id;
            $newValues['loan_principal']   = $loan->principal;
            $newValues['loan_status']      = $loan->status;
            $newValues['loan_product_id']  = $loan->loan_product_id;
            $newValues['client_id']        = $loan->client_id;

            // Include client details if relationship is loaded
            if ($loan->relationLoaded('client') && $loan->client) {
                $newValues['client_name'] = $loan->client->first_name . ' ' . $loan->client->last_name;
                $newValues['client_nrc']  = $loan->client->nrc ?? null;
            }
        } else {
            $newValues['loan_id']         = $request->route('id') ?? $request->route('loan');
            $newValues['transaction_id']  = $request->route('trans_id');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'approved transaction',
            $user->id,
            $request,
            [],
            $newValues,
            'transaction_approval'
        );
    }

    /**
     * Log entering a debt recovery transaction for approval (includes loan/client details)
     */
    public function logEntereedRecoveryTransactionForApproval($user, $request, $loan = null)
    {
        $newValues = [
            'action'             => 'entered_recovery_transaction_for_approval',
            'user_name'          => $user->first_name . ' ' . $user->last_name,
            'office_id'          => $user->office_id ?? null,
            'transaction_amount' => $request->amount ?? null,
            'transaction_type'   => $request->type ?? $request->transaction_type ?? 'debt_recovery',
            'transaction_date'   => $request->date ?? $request->transaction_date ?? date('Y-m-d'),
            'submitted_at'       => now()->toDateTimeString(),
        ];

        // Add loan and client information if available
        if ($loan) {
            $newValues['loan_id']          = $loan->id;
            $newValues['loan_principal']   = $loan->principal;
            $newValues['loan_status']      = $loan->status;
            $newValues['loan_product_id']  = $loan->loan_product_id;
            $newValues['client_id']        = $loan->client_id;

            // Include client details if relationship is loaded
            if ($loan->relationLoaded('client') && $loan->client) {
                $newValues['client_name'] = $loan->client->first_name . ' ' . $loan->client->last_name;
                $newValues['client_nrc']  = $loan->client->nrc ?? null;
            }
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'entered debt recovery transaction for approval',
            $user->id,
            $request,
            [],
            $newValues,
            'recovery_transaction_entry'
        );
    }

    /**
     * Log entering a waiver transaction (includes loan/client details)
     */
    public function logEnteredWaiverTransaction($user, $request, $loan = null)
    {
        $newValues = [
            'action'          => 'entered_waiver_transaction',
            'user_name'       => $user->first_name . ' ' . $user->last_name,
            'office_id'       => $user->office_id ?? null,
            'waiver_amount'   => $request->amount ?? null,
            'waiver_reason'   => $request->reason ?? $request->waiver_reason ?? null,
            'transaction_date'=> $request->date ?? $request->transaction_date ?? date('Y-m-d'),
            'submitted_at'    => now()->toDateTimeString(),
        ];

        // Add loan and client information if available
        if ($loan) {
            $newValues['loan_id']          = $loan->id;
            $newValues['loan_principal']   = $loan->principal;
            $newValues['loan_status']      = $loan->status;
            $newValues['loan_product_id']  = $loan->loan_product_id;
            $newValues['client_id']        = $loan->client_id;

            // Include client details if relationship is loaded
            if ($loan->relationLoaded('client') && $loan->client) {
                $newValues['client_name'] = $loan->client->first_name . ' ' . $loan->client->last_name;
                $newValues['client_nrc']  = $loan->client->nrc ?? null;
            }
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'entered waiver transaction',
            $user->id,
            $request,
            [],
            $newValues,
            'waiver_transaction_entry'
        );
    }
}