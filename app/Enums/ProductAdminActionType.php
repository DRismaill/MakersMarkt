<?php

namespace App\Enums;

enum ProductAdminActionType: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Deactivated = 'deactivated';
    case FlagsResolved = 'flags_resolved';

    public function label(): string
    {
        return match ($this) {
            self::Approved => 'Goedgekeurd',
            self::Rejected => 'Afgekeurd',
            self::Deactivated => 'Gedeactiveerd',
            self::FlagsResolved => 'Flags afgehandeld',
        };
    }
}
