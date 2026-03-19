<?php

namespace App\Enums;

enum NotificationType: string
{
    case NewOrder = 'new_order';
    case ProductReview = 'product_review';
    case MakerReview = 'maker_review';
    case StatusChanged = 'status_changed';
}
