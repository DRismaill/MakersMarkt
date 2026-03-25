<?php

namespace App\Enums;

enum CreditAdjustmentDirection: string
{
    case Increase = 'increase';
    case Decrease = 'decrease';

    public function label(): string
    {
        return match ($this) {
            self::Increase => 'Verhogen',
            self::Decrease => 'Verlagen',
        };
    }
}
