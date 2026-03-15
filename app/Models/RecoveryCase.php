<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class RecoveryCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'case_number','client_id','origin_branch_id','supporting_branch_id',
        'assigned_specialist_id','loan_id','category','status',
        'loan_outstanding_amount','amount_recovered','recovery_costs','settlement_amount',
        'escalated_by_user_id','escalation_date','days_past_due_at_escalation','lc_contact_attempts',
        'recoveries_dept_attribution_pct','origin_branch_attribution_pct','supporting_branch_attribution_pct',
        'legal_reference_number','lawyer_firm','legal_filed_date','court_date',
        'legal_costs_incurred','enforcement_type',
        'skip_trace_tracking_code','client_located','located_date','skip_trace_costs',
        'last_payment_date','dormant_days','revival_method',
        'client_last_known_location','client_new_location','joint_field_visit_done',
        'notes','target_resolution_date','resolved_date',
    ];

    protected $casts = [
        'escalation_date'        => 'date',
        'legal_filed_date'       => 'date',
        'court_date'             => 'date',
        'located_date'           => 'date',
        'last_payment_date'      => 'date',
        'target_resolution_date' => 'date',
        'resolved_date'          => 'date',
        'loan_outstanding_amount'=> 'decimal:2',
        'amount_recovered'       => 'decimal:2',
        'recovery_costs'         => 'decimal:2',
        'settlement_amount'      => 'decimal:2',
        'legal_costs_incurred'   => 'decimal:2',
        'skip_trace_costs'       => 'decimal:2',
        'client_located'         => 'boolean',
        'joint_field_visit_done' => 'boolean',
    ];

    const CATEGORIES = [
        'cross_branch' => 'Cross-Branch (Runaway)',
        'escalated'    => 'Escalated Accounts',
        'dormant'      => 'Dormant Revival',
        'legal'        => 'Legal Recovery',
        'skip_trace'   => 'Skip Tracing',
    ];

    const RESOLVED_STATUSES = [
        'recovered_runaway', 'recovered_post_escalation', 'recovery_revived',
        'recovered_legal', 'closed', 'written_off',
    ];

    // Active = everything that is not resolved
    const ACTIVE_STATUSES = [
        'runaway_pending_confirmation', 'runaway_active_recovery',
        'escalated_handover', 'escalated_in_review', 'escalated_active_recovery',
        'dormant_for_revival',
        'pre_litigation_review', 'legal_filed', 'legal_active', 'legal_judgment_won',
        'skip_trace_required', 'skip_trace_digital_review',
        'skip_trace_contact_reengagement', 'skip_trace_field_intel_active', 'located_for_recovery',
    ];

    // Relationships
    public function client(): BelongsTo         { return $this->belongsTo(Client::class); }
    public function loan(): BelongsTo           { return $this->belongsTo(Loan::class); }
    public function originBranch(): BelongsTo   { return $this->belongsTo(\App\Models\Office::class, 'origin_branch_id'); }
    public function supportingBranch(): BelongsTo { return $this->belongsTo(\App\Models\Office::class, 'supporting_branch_id'); }
    public function assignedSpecialist(): BelongsTo { return $this->belongsTo(User::class, 'assigned_specialist_id'); }
    public function escalatedBy(): BelongsTo    { return $this->belongsTo(User::class, 'escalated_by_user_id'); }
    public function activities(): HasMany        { return $this->hasMany(RecoveryActivity::class)->latest(); }
    public function payments(): HasMany          { return $this->hasMany(RecoveryPayment::class)->latest('payment_date'); }
    public function documents(): HasMany         { return $this->hasMany(RecoveryDocument::class)->latest(); }

    // Computed attributes
    public function getNetRecoveryAttribute(): float
    {
        return (float)$this->amount_recovered - (float)$this->recovery_costs
            - (float)$this->legal_costs_incurred - (float)$this->skip_trace_costs;
    }

    public function getRecoveryRateAttribute(): float
    {
        if ($this->loan_outstanding_amount <= 0) return 0;
        return round(($this->amount_recovered / $this->loan_outstanding_amount) * 100, 1);
    }

    public function getRecoveriesDeptAmountAttribute(): float
    {
        return round($this->amount_recovered * ($this->recoveries_dept_attribution_pct / 100), 2);
    }

    public function getIsResolvedAttribute(): bool
    {
        return in_array($this->status, self::RESOLVED_STATUSES)
            || ($this->loan?->status === 'closed');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    public function getStatusColorAttribute(): string
    {
        return match(true) {
            in_array($this->status, self::RESOLVED_STATUSES) => 'success',
            str_contains($this->status, 'legal')             => 'danger',
            str_contains($this->status, 'skip_trace')        => 'info',
            str_contains($this->status, 'runaway')           => 'primary',
            default                                          => 'warning',
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('recovery_cases.status', self::RESOLVED_STATUSES)
                     ->whereHas('loan', fn($q) => $q->where('status', '!=', 'closed'));
    }

    public function scopeResolved($query)
    {
        return $query->where(function($q) {
            $q->whereIn('recovery_cases.status', self::RESOLVED_STATUSES)
              ->orWhereHas('loan', fn($q2) => $q2->where('status', 'closed'));
        });
    }
    public function scopeByCategory($query, string $cat)   { return $query->where('category', $cat); }
    public function scopeAssignedTo($query, int $userId)   { return $query->where('assigned_specialist_id', $userId); }

    public function scopeForPeriod($query, string $period, ?string $dateFrom = null, ?string $dateTo = null)
    {
        if ($period === 'custom' && $dateFrom && $dateTo) {
            return $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($dateFrom)->startOfDay(),
                \Carbon\Carbon::parse($dateTo)->endOfDay(),
            ]);
        }
        return match($period) {
            'week'    => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month'   => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'quarter' => $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]),
            'year'    => $query->whereYear('created_at', now()->year),
            default   => $query,
        };
    }

    // Attribution helpers
    public function applyCrossBranchAttribution(): void
    {
        $this->update(['recoveries_dept_attribution_pct'=>50,'origin_branch_attribution_pct'=>25,'supporting_branch_attribution_pct'=>25]);
    }

    public function applyFullRecoveriesAttribution(): void
    {
        $this->update(['recoveries_dept_attribution_pct'=>100,'origin_branch_attribution_pct'=>0,'supporting_branch_attribution_pct'=>0]);
    }

    public function applyCooperativeAttribution(): void
    {
        $this->update(['recoveries_dept_attribution_pct'=>50,'origin_branch_attribution_pct'=>50,'supporting_branch_attribution_pct'=>0]);
    }

    public static function generateCaseNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->lockForUpdate()->count();
        return sprintf('RC-%d-%05d', $year, $last + 1);
    }
}