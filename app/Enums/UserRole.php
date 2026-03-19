<?php

namespace App\Enums;

enum UserRole: string
{
    case Maker = 'maker';
    case Buyer = 'buyer';
    case Admin = 'admin';
}
