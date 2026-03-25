<?php

namespace App\Enums;

enum ProductAdminActionType: string
{
    case Deactivated = 'deactivated';

    public function label(): string
    {
        return match ($this) {
            self::Deactivated => 'Gedeactiveerd',
        };
    }
}
