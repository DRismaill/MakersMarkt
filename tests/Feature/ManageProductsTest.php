<?php

use App\Enums\ComplexityLevel;
use App\Enums\DurabilityLevel;
use App\Enums\OrderStatus;
use App\Enums\ProductApprovalStatus;
use App\Enums\UserRole;
use App\Filament\Pages\ManageProducts;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::bootCurrentPanel();
});

function createProductRecord(array $overrides = []): Product
{
    $maker = $overrides['maker'] ?? User::factory()->create([
        'role' => UserRole::Maker,
    ]);

    $productType = $overrides['product_type'] ?? ProductType::query()->create([
        'name' => 'Type '.Str::random(6),
        'description' => 'Test producttype',
    ]);

    $name = $overrides['name'] ?? 'Product '.Str::random(6);

    return Product::query()->create([
        'maker_id' => $maker->id,
        'product_type_id' => $productType->id,
        'name' => $name,
        'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
        'description' => 'Beschrijving voor testproduct.',
        'material' => 'Hout',
        'production_time_days' => 5,
        'complexity' => $overrides['complexity'] ?? ComplexityLevel::Medium,
        'durability' => $overrides['durability'] ?? DurabilityLevel::High,
        'unique_feature' => 'Handgemaakt',
        'price_credit' => $overrides['price_credit'] ?? 39.95,
        'approval_status' => $overrides['approval_status'] ?? ProductApprovalStatus::Approved,
        'approved_by_admin_id' => $overrides['approved_by_admin_id'] ?? null,
        'approved_at' => $overrides['approved_at'] ?? now(),
        'rejection_reason' => $overrides['rejection_reason'] ?? null,
        'has_external_link' => $overrides['has_external_link'] ?? false,
        'needs_moderation' => $overrides['needs_moderation'] ?? false,
        'is_active' => $overrides['is_active'] ?? true,
        'is_deleted' => $overrides['is_deleted'] ?? false,
        'average_rating' => $overrides['average_rating'] ?? null,
        'review_count' => $overrides['review_count'] ?? 0,
    ]);
}

it('allows admins to open the product management screen', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->get(ManageProducts::getUrl(panel: 'admin'))
        ->assertOk();
});

it('forbids non admins from opening the product management screen', function (): void {
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
    ]);

    $this->actingAs($buyer)
        ->get(ManageProducts::getUrl(panel: 'admin'))
        ->assertForbidden();
});

it('shows the deactivate action only for active products', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $activeProduct = createProductRecord();
    $deactivatedProduct = createProductRecord([
        'is_active' => false,
        'is_deleted' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test(ManageProducts::class)
        ->assertTableActionVisible('deactivateProduct', $activeProduct->getKey())
        ->assertTableActionHidden('deactivateProduct', $deactivatedProduct->getKey());
});

it('lets an admin deactivate a product without breaking existing orders or reviews and logs the action', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
    ]);
    $product = createProductRecord();

    $order = Order::query()->create([
        'buyer_id' => $buyer->id,
        'product_id' => $product->id,
        'maker_id' => $product->maker_id,
        'status' => OrderStatus::PendingPayment,
        'status_note' => null,
        'price_credit' => $product->price_credit,
    ]);

    DB::table('product_reviews')->insert([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'buyer_id' => $buyer->id,
        'rating' => 5,
        'comment' => 'Sterk product',
        'created_at' => now(),
    ]);

    $this->actingAs($admin);

    Livewire::test(ManageProducts::class)
        ->callTableAction('deactivateProduct', $product->getKey());

    $product->refresh();
    $order->refresh();
    $review = DB::table('product_reviews')->where('product_id', $product->id)->first();

    expect($product->is_active)->toBeFalse();
    expect($product->is_deleted)->toBeTrue();
    expect($order->product)->not->toBeNull();
    expect($order->product->id)->toBe($product->id);
    expect($review)->not->toBeNull();
    expect($review->product_id)->toBe($product->id);

    $this->assertDatabaseHas('product_admin_action_logs', [
        'product_id' => $product->id,
        'admin_user_id' => $admin->id,
        'product_name' => $product->name,
        'action_type' => 'deactivated',
    ]);
});

it('excludes inactive or deleted products from the public catalog scope', function (): void {
    $visibleProduct = createProductRecord([
        'approval_status' => ProductApprovalStatus::Approved,
        'is_active' => true,
        'is_deleted' => false,
    ]);
    createProductRecord([
        'approval_status' => ProductApprovalStatus::Approved,
        'is_active' => false,
        'is_deleted' => false,
    ]);
    createProductRecord([
        'approval_status' => ProductApprovalStatus::Approved,
        'is_active' => true,
        'is_deleted' => true,
    ]);
    createProductRecord([
        'approval_status' => ProductApprovalStatus::Pending,
        'is_active' => true,
        'is_deleted' => false,
    ]);

    $visibleProductIds = Product::query()
        ->visibleInCatalog()
        ->pluck('id');

    expect($visibleProductIds)->toHaveCount(1);
    expect($visibleProductIds->all())->toBe([$visibleProduct->id]);
});
