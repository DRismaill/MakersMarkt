<?php

namespace App\Models;

use App\Enums\ProductAdminActionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAdminActionLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'admin_user_id',
        'product_name',
        'action_type',
    ];

    protected $casts = [
        'action_type' => ProductAdminActionType::class,
        'created_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id', 'id');
    }
}
