<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'product_id',
        'maker_id',
        'status',
        'status_note',
        'price_credit',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function maker()
    {
        return $this->belongsTo(User::class, 'maker_id', 'id');
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'order_id', 'id');
    }

    public function makerReviews()
    {
        return $this->hasMany(MakerReview::class, 'order_id', 'id');
    }

    public function creditTransactions()
    {
        return $this->hasMany(CreditTransaction::class, 'order_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'related_order_id', 'id');
    }
}
