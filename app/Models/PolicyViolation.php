<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'policy_id',
        'reported_by',
        'description',
        'status',
        'evidence',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'evidence' => 'array',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user who committed the violation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the policy that was violated
     */
    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'policy_id');
    }

    /**
     * Get the user who reported the violation
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Check if violation is escalated
     */
    public function isEscalated(): bool
    {
        return $this->status === 'escalated';
    }

    /**
     * Check if violation is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Get evidence file URLs
     */
    public function getEvidenceUrls(): array
    {
        if (!$this->evidence) {
            return [];
        }

        return array_map(function ($file) {
            return asset('storage/violations/' . $file);
        }, $this->evidence);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by policy
     */
    public function scopeByPolicy($query, $policyId)
    {
        return $query->where('policy_id', $policyId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }
}
