<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryActivity extends Model
{
    protected $fillable = [
        'recovery_case_id','performed_by','activity_type',
        'description','status_before','status_after','amount_collected','metadata',
    ];

    protected $casts = [
        'metadata'        => 'array',
        'amount_collected' => 'decimal:2',
    ];

    const TYPE_LABELS = [
        'status_change'           => 'Status Changed',
        'payment_received'        => 'Payment Received',
        'field_visit'             => 'Field Visit',
        'phone_call'              => 'Phone Call',
        'sms_sent'                => 'SMS Sent',
        'legal_filing'            => 'Legal Filing',
        'court_hearing'           => 'Court Hearing',
        'asset_seizure'           => 'Asset Seizure',
        'skip_trace_attempt'      => 'Skip Trace Attempt',
        'guarantor_contact'       => 'Guarantor Contacted',
        'payment_plan_negotiated' => 'Payment Plan Agreed',
        'case_handover'           => 'Case Handover',
        'note_added'              => 'Note Added',
        'document_uploaded'       => 'Document Uploaded',
    ];

    public function recoveryCase(): BelongsTo { return $this->belongsTo(RecoveryCase::class); }
    public function performedBy(): BelongsTo  { return $this->belongsTo(User::class, 'performed_by'); }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->activity_type] ?? ucfirst($this->activity_type);
    }
}
