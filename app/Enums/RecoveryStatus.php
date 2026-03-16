<?php

namespace App\Enums;

enum RecoveryStatus: string
{
    case Pending          = 'pending';
    case Active           = 'active';
    case LegalEscalation  = 'legal_escalation';
    case Resolved         = 'resolved';
    case WrittenOff       = 'written_off';

    public function label(): string
    {
        return match($this) {
            self::Pending         => 'Pending',
            self::Active          => 'Active Recovery',
            self::LegalEscalation => 'Legal Escalation',
            self::Resolved        => 'Resolved',
            self::WrittenOff      => 'Written Off',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Pending         => 'badge-muted',
            self::Active          => 'badge-blue',
            self::LegalEscalation => 'badge-red',
            self::Resolved        => 'badge-green',
            self::WrittenOff      => 'badge-dark',
        };
    }
}
