<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'maker_id',
        'buyer_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function maker()
    {
        return $this->belongsTo(User::class, 'maker_id', 'id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'id');
    }
}
