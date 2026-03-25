<?php

namespace App\Models;

use App\Enums\CreditReasonType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditTransaction extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'amount',
        'reason_type',
        'order_id',
        'created_by_admin_id',
    ];

    protected $casts = [
        'reason_type' => CreditReasonType::class,
        'created_at' => 'datetime',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id', 'id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(User::class, 'created_by_admin_id', 'id');
    }
}
