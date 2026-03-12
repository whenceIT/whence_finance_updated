<?php

namespace App\Enums;

enum RecoveryCategory: string
{
    case CrossBranch = 'cross_branch';
    case Escalated   = 'escalated';
    case Dormant     = 'dormant';
    case Legal       = 'legal';
    case SkipTrace   = 'skip_trace';

    public function label(): string
    {
        return match($this) {
            self::CrossBranch => 'Cross-Branch',
            self::Escalated   => 'Escalated Accounts',
            self::Dormant     => 'Dormant Revival',
            self::Legal       => 'Legal Recovery',
            self::SkipTrace   => 'Skip Tracing',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CrossBranch => '#3b82f6',
            self::Escalated   => '#f59e0b',
            self::Dormant     => '#a855f7',
            self::Legal       => '#ef4444',
            self::SkipTrace   => '#00d4a0',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::CrossBranch => 'badge-blue',
            self::Escalated   => 'badge-amber',
            self::Dormant     => 'badge-purple',
            self::Legal       => 'badge-red',
            self::SkipTrace   => 'badge-green',
        };
    }

    /** Default attribution % going to Recoveries dept */
    public function recoveriesDefaultPct(): float
    {
        return match($this) {
            self::CrossBranch => 50.0,
            self::Escalated   => 100.0,
            self::Dormant     => 100.0,
            self::Legal       => 100.0,
            self::SkipTrace   => 100.0,
        };
    }
}
