<?php

namespace App\Enums;

enum UserRole: string
{
    case Maker = 'maker';
    case Buyer = 'buyer';
    case Admin = 'admin';

    /**
     * Get the label for the user role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Maker => 'Maker',
            self::Buyer => 'Buyer',
            self::Admin => 'Administrator',
        };
    }
}
