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
            'Went to the reloan approvals page',
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
            'Went to the loan transactions approvals page',
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
            'Went to the loan transactions top up approvals page',
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
    public function logCreateClientLoan($user, $request, $client)
    {
        $clientId = $request->route('client');
        $loanProductId = $request->route('loan_product');

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            'Created a new loan for client named ' . $client->first_name . ' ' . $client->last_name,
            $user->id,
            $request,
            [],
            [
                'action' => 'created_a_new_client_loan',
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'client_id' => $client->id,
                'client_first_name' => $client->first_name ?? null,
                'client_last_name' => $client->last_name ?? null,
                'client_phone' => $client->phone ?? null,
                'loan_product_id' => $loanProductId,
                'office_id' => $user->office_id ?? null,
                'office_name' => $user->office->name ?? null
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
            'office_id' => $user->office_id ?? null,
            'office_name' => $user->office->name ?? null
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_amount'] = $loan->principal ?? $request->principal;
            $newValues['loan_product_id'] = $loan->loan_product_id ?? $request->route('loan_product');
            $newValues['loan_first_installment_date'] = $loan->first_installment_date ?? null;
        } else {
            $newValues['loan_amount'] = $request->principal;
            $newValues['loan_product_id'] = $request->route('loan_product');
        }

        // Add client information if available
        if ($client) {
            $newValues['client_id'] = $client->id;
            $newValues['client_name'] = $client->first_name . ' ' . $client->last_name;
            $newValues['client_first_name'] = $client->first_name ?? null;
            $newValues['client_last_name'] = $client->last_name ?? null;
            $newValues['client_phone'] = $client->phone ?? null;
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
            'Went to the loan details page',
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
            'office_name' => $user->office->name ?? null,
            'topup_amount' => $request->amount,
            'topup_date' => $request->top_up_date ?? date('Y-m-d')
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_principal'] = $loan->principal;
            $newValues['loan_amount'] = $loan->principal;
            $newValues['loan_first_installment_date'] = $loan->first_installment_date ?? null;
            $newValues['client_id'] = $loan->client_id;
            $newValues['client_first_name'] = $loan->client->first_name ?? null;
            $newValues['client_last_name'] = $loan->client->last_name ?? null;
            $newValues['client_phone'] = $loan->client->phone ?? null;
            $newValues['balance_before'] = $loan->principal; // Original loan amount
            $newValues['balance_after'] = $loan->principal + $request->amount; // New total
        } else {
            $newValues['loan_id'] = $request->route('id');
        }

        $message = 'Added a top up of ' . number_format($request->amount, 2) . ' to loan #' . $loan->id;
        if ($loan && $loan->client) {
            $message .= ' for client ' . $loan->client->first_name . ' ' . $loan->client->last_name;
        }
        if ($user->office) {
            $message .= ' at office ' . $user->office->name;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'office_name' => $user->office->name ?? null,
            'topup_amount' => $request->amount,
            'topup_date' => $request->top_up_date ?? date('Y-m-d')
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_principal'] = $loan->principal;
            $newValues['loan_amount'] = $loan->principal;
            $newValues['loan_first_installment_date'] = $loan->first_installment_date ?? null;
            $newValues['client_id'] = $loan->client_id;
            $newValues['client_first_name'] = $loan->client->first_name ?? null;
            $newValues['client_last_name'] = $loan->client->last_name ?? null;
            $newValues['client_phone'] = $loan->client->phone ?? null;
            $newValues['balance_before'] = $loan->principal;
            $newValues['balance_after'] = $loan->principal + $request->amount;
        } else {
            $newValues['loan_id'] = $request->route('id');
        }

        $message = 'Requested for a top up approval of ' . number_format($request->amount, 2) .' on loan #' .  $loan->id ;
        if ($loan && $loan->client) {
            $message .= ' for client ' . $loan->client->first_name . ' ' . $loan->client->last_name;
        }
        if ($user->office) {
            $message .= ' at office ' . $user->office->name;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'office_id' => $user->office_id ?? null,
            'office_name' => $user->office->name ?? null
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
            $newValues['loan_amount'] = $loan->principal;
            $newValues['loan_first_installment_date'] = $loan->first_installment_date ?? null;
            $newValues['client_id'] = $loan->client_id;
            $newValues['client_first_name'] = $loan->client->first_name ?? null;
            $newValues['client_last_name'] = $loan->client->last_name ?? null;
            $newValues['client_phone'] = $loan->client->phone ?? null;
        } else {
            $newValues['loan_id'] = $request->route('id');
            $newValues['topup_id'] = $request->route('trans_id');
        }

        $amount = $topup ? $topup->amount : ($request->amount ?? 0);
        $message = 'Approved loan top up of ' . number_format($amount, 2) .' on loan #' .  $loan->id ;
        if ($loan && $loan->client) {
            $message .= ' for client ' . $loan->client->first_name . ' ' . $loan->client->last_name;
        }
        if ($user->office) {
            $message .= ' at office ' . $user->office->name;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'office_id' => $user->office_id ?? null,
            'office_name' => $user->office->name ?? null
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id'] = $loan->id;
            $newValues['loan_principal'] = $loan->principal;
            $newValues['loan_amount'] = $loan->principal;
            $newValues['loan_first_installment_date'] = $loan->first_installment_date ?? null;
            $newValues['loan_status'] = $loan->status;
            $newValues['client_id'] = $loan->client_id;
            $newValues['client_first_name'] = $loan->client->first_name ?? null;
            $newValues['client_last_name'] = $loan->client->last_name ?? null;
            $newValues['client_phone'] = $loan->client->phone ?? null;
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

        $message = 'Updated client loan #' . $loan->id;
        $updates = [];
        if ($request->has('principal')) {
            $updates[] = 'principal to ' . number_format($request->principal, 2);
        }
        if ($request->has('loan_term')) {
            $updates[] = 'term to ' . $request->loan_term;
        }
        if ($request->has('interest_rate')) {
            $updates[] = 'interest rate to ' . $request->interest_rate . '%';
        }
        if (!empty($updates)) {
            $message .= ' (' . implode(', ', $updates) . ')';
        }
        if ($loan && $loan->client) {
            $message .= ' for client ' . $loan->client->first_name . ' ' . $loan->client->last_name;
        }
        if ($user->office) {
            $message .= ' at office ' . $user->office->name;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'office_name' => $user->office->name ?? null,
            'reason'      => $request->reason ?? $request->decline_reason ?? null,
            'declined_at' => now()->toDateTimeString(),
        ];

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id']         = $loan->id;
            $newValues['loan_principal']  = $loan->principal;
            $newValues['loan_amount']     = $loan->principal;
            $newValues['loan_first_installment_date'] = $loan->first_installment_date ?? null;
            $newValues['loan_status']     = $loan->status;
            $newValues['client_id']       = $loan->client_id;
            $newValues['client_first_name'] = $loan->client->first_name ?? null;
            $newValues['client_last_name'] = $loan->client->last_name ?? null;
            $newValues['client_phone']    = $loan->client->phone ?? null;
            $newValues['loan_product_id'] = $loan->loan_product_id;
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $message = 'Declined client loan #' . $loan->id ;
        if ($loan && $loan->client) {
            $message .= ' for client ' . $loan->client->first_name . ' ' . $loan->client->last_name;
        }
        if ($user->office) {
            $message .= ' at office ' . $user->office->name;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'office_name'      => $user->office->name ?? null,
            'new_officer_id'   => $request->loan_officer_id ?? $request->officer_id ?? $request->user_id ?? null,
            'new_officer_name' => null, // Will be set below
            'changed_at'       => now()->toDateTimeString(),
        ];

        // Get new officer name
        if ($newValues['new_officer_id']) {
            $newOfficer = \App\Models\User::find($newValues['new_officer_id']);
            $newValues['new_officer_name'] = $newOfficer ? $newOfficer->first_name . ' ' . $newOfficer->last_name : null;
        }

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id']            = $loan->id;
            $newValues['loan_principal']     = $loan->principal;
            $newValues['loan_amount']        = $loan->principal;
            $newValues['loan_first_installment_date'] = $loan->first_installment_date ?? null;
            $newValues['loan_status']        = $loan->status;
            $newValues['client_id']          = $loan->client_id;
            $newValues['client_first_name']  = $loan->client->first_name ?? null;
            $newValues['client_last_name']   = $loan->client->last_name ?? null;
            $newValues['client_phone']       = $loan->client->phone ?? null;
            $newValues['loan_product_id']    = $loan->loan_product_id;
            $newValues['old_officer_id']     = $loan->user_id ?? $loan->loan_officer_id ?? null;
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $message = 'Changed loan officer for loan #' . $loan->id;
        if ($newValues['new_officer_name']) {
            $message .= ' to ' . $newValues['new_officer_name'];
        }
        if ($loan && $loan->client) {
            $message .= ' for client ' . $loan->client->first_name . ' ' . $loan->client->last_name;
        }
        if ($user->office) {
            $message .= ' at office ' . $user->office->name;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'office_name'     => $user->office->name ?? null,
            'new_branch_id'   => $request->branch_id ?? $request->office_id ?? null,
            'new_branch_name' => null, // Will be set below
            'changed_at'      => now()->toDateTimeString(),
        ];

        // Get new branch name
        if ($newValues['new_branch_id']) {
            $newBranch = \App\Models\Office::find($newValues['new_branch_id']);
            $newValues['new_branch_name'] = $newBranch ? $newBranch->name : null;
        }

        // Add loan information if available
        if ($loan) {
            $newValues['loan_id']          = $loan->id;
            $newValues['loan_principal']   = $loan->principal;
            $newValues['loan_amount']      = $loan->principal;
            $newValues['loan_first_installment_date'] = $loan->first_installment_date ?? null;
            $newValues['loan_status']      = $loan->status;
            $newValues['client_id']        = $loan->client_id;
            $newValues['client_first_name'] = $loan->client->first_name ?? null;
            $newValues['client_last_name']  = $loan->client->last_name ?? null;
            $newValues['client_phone']     = $loan->client->phone ?? null;
            $newValues['loan_product_id']  = $loan->loan_product_id;
            $newValues['old_branch_id']    = $loan->office_id ?? $loan->branch_id ?? null;
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $message = 'Changed loan branch for loan #' . $loan->id;
        if ($newValues['new_branch_name']) {
            $message .= ' to ' . $newValues['new_branch_name'];
        }
        if ($loan && $loan->client) {
            $message .= ' for client ' . $loan->client->first_name . ' ' . $loan->client->last_name;
        }
        if ($user->office) {
            $message .= ' at office ' . $user->office->name;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'office_name'   => $user->office->name ?? null,
            'disbursed_at'  => now()->toDateTimeString(),
        ];

        // Add loan and client information if available
        if ($loan) {
            $newValues['loan_id']          = $loan->id;
            $newValues['loan_principal']   = $loan->principal;
            $newValues['loan_amount']      = $loan->principal;
            $newValues['loan_first_installment_date'] = $loan->first_installment_date ?? null;
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
                $newValues['client_first_name'] = $loan->client->first_name;
                $newValues['client_last_name'] = $loan->client->last_name;
                $newValues['client_phone'] = $loan->client->phone ?? null;
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

        $message = 'Disbursed client loan #' . ($loan ? $loan->id : 'unknown');
        if ($loan) {
            $message .= ' of ' . number_format($loan->principal, 2);
            if ($loan->relationLoaded('client') && $loan->client) {
                $message .= ' for client ' . $loan->client->first_name . ' ' . $loan->client->last_name;
            }
        }
        if ($user->office) {
            $message .= ' at office ' . $user->office->name;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'office_name'        => $user->office->name ?? null,
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
                $newValues['client_first_name'] = $loan->client->first_name;
                $newValues['client_last_name'] = $loan->client->last_name;
                $newValues['client_phone'] = $loan->client->phone ?? null;
                $newValues['client_nrc']  = $loan->client->nrc ?? null;
            }
        } else {
            $newValues['loan_id'] = $request->route('id') ?? $request->route('loan');
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'office_name'        => $user->office->name ?? null,
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

        $message = 'Submitted a transaction for approval for loan #' . ($loan ? $loan->id : 'unknown');
        if ($request->amount) {
            $message .= ' of ' . number_format($request->amount, 2);
        }
        if ($request->type ?? $request->transaction_type) {
            $message .= ' (' . ($request->type ?? $request->transaction_type) . ')';
        }
        if ($loan && $loan->relationLoaded('client') && $loan->client) {
            $message .= ' for client ' . $loan->client->first_name . ' ' . $loan->client->last_name;
        }

        $this->logCustomAudit(
            'App\Models\User',
            $user->id,
            $message,
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
            'Approved transaction for loan #' . ($loan ? $loan->id : 'unknown'),
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
            'Entered debt recovery transaction for approval for loan #' . ($loan ? $loan->id : 'unknown'),
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
            'Entered waiver transaction for loan #' . ($loan ? $loan->id : 'unknown'),
            $user->id,
            $request,
            [],
            $newValues,
            'waiver_transaction_entry'
        );
    }
}