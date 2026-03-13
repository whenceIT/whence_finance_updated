<?php

namespace App\Enums;

enum ActivityType: string
{
    case Call             = 'call';
    case Sms              = 'sms';
    case FieldVisit       = 'field_visit';
    case LegalFiling      = 'legal_filing';
    case PaymentReceived  = 'payment_received';
    case GuarantorContact = 'guarantor_contact';
    case SkipTrace        = 'skip_trace';
    case StatusChange     = 'status_change';
    case Note             = 'note';
    case Escalation       = 'escalation';

    public function label(): string
    {
        return match($this) {
            self::Call             => 'Phone Call',
            self::Sms              => 'SMS Sent',
            self::FieldVisit       => 'Field Visit',
            self::LegalFiling      => 'Legal Filing',
            self::PaymentReceived  => 'Payment Received',
            self::GuarantorContact => 'Guarantor Contact',
            self::SkipTrace        => 'Skip Trace Action',
            self::StatusChange     => 'Status Change',
            self::Note             => 'Note Added',
            self::Escalation       => 'Escalation',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Call             => '📞',
            self::Sms              => '💬',
            self::FieldVisit       => '🚗',
            self::LegalFiling      => '⚖️',
            self::PaymentReceived  => '💰',
            self::GuarantorContact => '👤',
            self::SkipTrace        => '🔍',
            self::StatusChange     => '🔄',
            self::Note             => '📝',
            self::Escalation       => '⬆️',
        };
    }
}
