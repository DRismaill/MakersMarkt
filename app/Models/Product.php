<?php

namespace App\Models;

use App\Enums\ComplexityLevel;
use App\Enums\DurabilityLevel;
use App\Enums\ProductApprovalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'maker_id',
        'product_type_id',
        'name',
        'slug',
        'description',
        'material',
        'production_time_days',
        'complexity',
        'durability',
        'unique_feature',
        'price_credit',
        'approval_status',
        'approved_by_admin_id',
        'approved_at',
        'rejection_reason',
        'has_external_link',
        'needs_moderation',
        'is_active',
        'is_deleted',
        'average_rating',
        'review_count',
    ];

    protected $casts = [
        'complexity' => ComplexityLevel::class,
        'durability' => DurabilityLevel::class,
        'approval_status' => ProductApprovalStatus::class,
        'approved_at' => 'datetime',
        'has_external_link' => 'boolean',
        'needs_moderation' => 'boolean',
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function maker()
    {
        return $this->belongsTo(User::class, 'maker_id', 'id');
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id', 'id');
    }

    public function approvedByAdmin()
    {
        return $this->belongsTo(User::class, 'approved_by_admin_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'product_id', 'id');
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'related_product_id', 'id');
    }

    public function productReports()
    {
        return $this->hasMany(ProductReport::class, 'product_id', 'id');
    }

    public function adminActionLogs()
    {
        return $this->hasMany(ProductAdminActionLog::class, 'product_id', 'id');
    }

    public function scopeVisibleInCatalog(Builder $query): Builder
    {
        return $query
            ->where('approval_status', ProductApprovalStatus::Approved->value)
            ->where('is_active', true)
            ->where('is_deleted', false);
    }
}
