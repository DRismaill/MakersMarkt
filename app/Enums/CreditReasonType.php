<?php

namespace App\Enums;

enum CreditReasonType: string
{
    case Topup = 'topup';
    case Adjustment = 'adjustment';
    case Purchase = 'purchase';
    case Refund = 'refund';
}
