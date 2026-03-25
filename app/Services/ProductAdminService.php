<?php

namespace App\Services;

use App\Enums\ProductAdminActionType;
use App\Models\Product;
use App\Models\ProductAdminActionLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductAdminService
{
    public function deactivate(Product $product, User $admin): Product
    {
        if (! $admin->isAdmin()) {
            throw new InvalidArgumentException('Alleen admins kunnen producten deactiveren.');
        }

        return DB::transaction(function () use ($admin, $product): Product {
            /** @var Product $lockedProduct */
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->getKey());

            if (! $lockedProduct->is_active && $lockedProduct->is_deleted) {
                return $lockedProduct;
            }

            $lockedProduct->forceFill([
                'is_active' => false,
                'is_deleted' => true,
            ])->save();

            ProductAdminActionLog::query()->create([
                'product_id' => $lockedProduct->getKey(),
                'admin_user_id' => $admin->getKey(),
                'product_name' => $lockedProduct->name,
                'action_type' => ProductAdminActionType::Deactivated,
            ]);

            return $lockedProduct->refresh();
        });
    }
}
