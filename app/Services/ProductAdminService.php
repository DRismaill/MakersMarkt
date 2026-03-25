<?php

namespace App\Services;

use App\Enums\ProductAdminActionType;
use App\Enums\ProductApprovalStatus;
use App\Enums\ReportStatus;
use App\Models\Product;
use App\Models\ProductAdminActionLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductAdminService
{
    public function approve(Product $product, User $admin): Product
    {
        $this->ensureAdmin($admin, 'goedkeuren');

        return DB::transaction(function () use ($admin, $product): Product {
            /** @var Product $lockedProduct */
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->getKey());

            $lockedProduct->forceFill([
                'approval_status' => ProductApprovalStatus::Approved,
                'approved_by_admin_id' => $admin->getKey(),
                'approved_at' => now(),
                'rejection_reason' => null,
                'needs_moderation' => $this->requiresModeration(
                    approvalStatus: ProductApprovalStatus::Approved,
                    hasExternalLink: (bool) $lockedProduct->has_external_link,
                    hasOpenReports: $this->hasOpenReports($lockedProduct),
                ),
            ])->save();

            $this->logAction($lockedProduct, $admin, ProductAdminActionType::Approved);

            return $lockedProduct->refresh();
        });
    }

    public function reject(Product $product, User $admin, string $reason): Product
    {
        $this->ensureAdmin($admin, 'afkeuren');

        $reason = trim($reason);

        if ($reason === '') {
            throw new InvalidArgumentException('Geef een afwijsreden op.');
        }

        return DB::transaction(function () use ($admin, $product, $reason): Product {
            /** @var Product $lockedProduct */
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->getKey());

            $lockedProduct->forceFill([
                'approval_status' => ProductApprovalStatus::Rejected,
                'approved_by_admin_id' => $admin->getKey(),
                'approved_at' => now(),
                'rejection_reason' => $reason,
                'needs_moderation' => true,
            ])->save();

            $this->logAction($lockedProduct, $admin, ProductAdminActionType::Rejected);

            return $lockedProduct->refresh();
        });
    }

    public function deactivate(Product $product, User $admin): Product
    {
        $this->ensureAdmin($admin, 'deactiveren');

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

            $this->logAction($lockedProduct, $admin, ProductAdminActionType::Deactivated);

            return $lockedProduct->refresh();
        });
    }

    public function resolveFlags(Product $product, User $admin): Product
    {
        $this->ensureAdmin($admin, 'modereren');

        return DB::transaction(function () use ($admin, $product): Product {
            /** @var Product $lockedProduct */
            $lockedProduct = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->getKey());

            $lockedProduct->productReports()
                ->whereIn('status', [
                    ReportStatus::Open->value,
                    ReportStatus::InReview->value,
                ])
                ->update([
                    'status' => ReportStatus::Resolved->value,
                ]);

            $lockedProduct->forceFill([
                'has_external_link' => false,
                'needs_moderation' => $this->requiresModeration(
                    approvalStatus: $lockedProduct->approval_status,
                    hasExternalLink: false,
                    hasOpenReports: false,
                ),
            ])->save();

            $this->logAction($lockedProduct, $admin, ProductAdminActionType::FlagsResolved);

            return $lockedProduct->refresh();
        });
    }

    private function ensureAdmin(User $admin, string $action): void
    {
        if (! $admin->isAdmin()) {
            throw new InvalidArgumentException("Alleen admins kunnen producten {$action}.");
        }
    }

    private function hasOpenReports(Product $product): bool
    {
        return $product->productReports()
            ->whereIn('status', [
                ReportStatus::Open->value,
                ReportStatus::InReview->value,
            ])
            ->exists();
    }

    private function requiresModeration(
        ProductApprovalStatus|string $approvalStatus,
        bool $hasExternalLink,
        bool $hasOpenReports,
    ): bool {
        $approvalStatus = is_string($approvalStatus)
            ? ProductApprovalStatus::from($approvalStatus)
            : $approvalStatus;

        if ($approvalStatus !== ProductApprovalStatus::Approved) {
            return true;
        }

        return $hasExternalLink || $hasOpenReports;
    }

    private function logAction(Product $product, User $admin, ProductAdminActionType $actionType): void
    {
        ProductAdminActionLog::query()->create([
            'product_id' => $product->getKey(),
            'admin_user_id' => $admin->getKey(),
            'product_name' => $product->name,
            'action_type' => $actionType,
        ]);
    }
}
