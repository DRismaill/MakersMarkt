<?php

use App\Enums\ComplexityLevel;
use App\Enums\DurabilityLevel;
use App\Enums\ProductApprovalStatus;
use App\Enums\UserRole;
use App\Filament\Pages\FlaggedProducts;
use App\Filament\Pages\ProductModeration;
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

function createModerationProductRecord(array $overrides = []): Product
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
        'description' => $overrides['description'] ?? 'Beschrijving voor moderatietest.',
        'material' => $overrides['material'] ?? 'Hout',
        'production_time_days' => $overrides['production_time_days'] ?? 5,
        'complexity' => $overrides['complexity'] ?? ComplexityLevel::Medium,
        'durability' => $overrides['durability'] ?? DurabilityLevel::High,
        'unique_feature' => $overrides['unique_feature'] ?? 'Handgemaakt',
        'price_credit' => $overrides['price_credit'] ?? 39.95,
        'approval_status' => $overrides['approval_status'] ?? ProductApprovalStatus::Pending,
        'approved_by_admin_id' => $overrides['approved_by_admin_id'] ?? null,
        'approved_at' => $overrides['approved_at'] ?? null,
        'rejection_reason' => $overrides['rejection_reason'] ?? null,
        'has_external_link' => $overrides['has_external_link'] ?? false,
        'needs_moderation' => $overrides['needs_moderation'] ?? true,
        'is_active' => $overrides['is_active'] ?? true,
        'is_deleted' => $overrides['is_deleted'] ?? false,
        'average_rating' => $overrides['average_rating'] ?? null,
        'review_count' => $overrides['review_count'] ?? 0,
    ]);
}

it('allows admins to open the moderation screens', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->get(ProductModeration::getUrl(panel: 'admin'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(FlaggedProducts::getUrl(panel: 'admin'))
        ->assertOk();
});

it('forbids non admins from the moderation screens', function (): void {
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
    ]);

    $this->actingAs($buyer)
        ->get(ProductModeration::getUrl(panel: 'admin'))
        ->assertForbidden();

    $this->actingAs($buyer)
        ->get(FlaggedProducts::getUrl(panel: 'admin'))
        ->assertForbidden();
});

it('lets an admin approve a product from the moderation queue', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $product = createModerationProductRecord([
        'approval_status' => ProductApprovalStatus::Pending,
        'approved_by_admin_id' => null,
        'approved_at' => null,
        'rejection_reason' => 'Oude reden',
        'needs_moderation' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test(ProductModeration::class)
        ->assertCanSeeTableRecords([$product])
        ->callTableAction('approveProduct', $product->getKey())
        ->assertHasNoTableActionErrors()
        ->assertCanNotSeeTableRecords([$product]);

    $product->refresh();

    expect($product->approval_status)->toBe(ProductApprovalStatus::Approved);
    expect($product->needs_moderation)->toBeFalse();
    expect($product->rejection_reason)->toBeNull();
    expect($product->approved_by_admin_id)->toBe($admin->id);
    expect($product->approved_at)->not->toBeNull();

    $this->assertDatabaseHas('product_admin_action_logs', [
        'product_id' => $product->id,
        'admin_user_id' => $admin->id,
        'action_type' => 'approved',
    ]);
});

it('lets an admin reject a product with a moderation reason', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $product = createModerationProductRecord([
        'approval_status' => ProductApprovalStatus::Pending,
        'approved_by_admin_id' => null,
        'approved_at' => null,
        'needs_moderation' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test(ProductModeration::class)
        ->callTableAction('rejectProduct', $product->getKey(), [
            'rejection_reason' => 'Productinformatie is onvolledig en de foto toont niet het volledige product.',
        ])
        ->assertHasNoTableActionErrors()
        ->assertCanSeeTableRecords([$product]);

    $product->refresh();

    expect($product->approval_status)->toBe(ProductApprovalStatus::Rejected);
    expect($product->needs_moderation)->toBeTrue();
    expect($product->rejection_reason)->toBe('Productinformatie is onvolledig en de foto toont niet het volledige product.');
    expect($product->approved_by_admin_id)->toBe($admin->id);
    expect($product->approved_at)->not->toBeNull();

    $this->assertDatabaseHas('product_admin_action_logs', [
        'product_id' => $product->id,
        'admin_user_id' => $admin->id,
        'action_type' => 'rejected',
    ]);
});

it('lets an admin resolve flagged products and removes them from the unresolved queue', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
    ]);
    $product = createModerationProductRecord([
        'approval_status' => ProductApprovalStatus::Approved,
        'approved_at' => now()->subDay(),
        'has_external_link' => true,
        'needs_moderation' => true,
    ]);

    DB::table('product_reports')->insert([
        [
            'product_id' => $product->id,
            'reported_by_user_id' => $buyer->id,
            'reason' => 'Linkt door naar een externe shop.',
            'status' => 'open',
            'created_at' => now()->subHours(2),
        ],
        [
            'product_id' => $product->id,
            'reported_by_user_id' => $admin->id,
            'reason' => 'Nog in review bij moderatie.',
            'status' => 'in_review',
            'created_at' => now()->subHour(),
        ],
    ]);

    $this->actingAs($admin);

    Livewire::test(FlaggedProducts::class)
        ->assertCanSeeTableRecords([$product])
        ->callTableAction('resolveFlags', $product->getKey())
        ->assertHasNoTableActionErrors()
        ->assertCanNotSeeTableRecords([$product]);

    $product->refresh();

    expect($product->has_external_link)->toBeFalse();
    expect($product->needs_moderation)->toBeFalse();

    expect(DB::table('product_reports')
        ->where('product_id', $product->id)
        ->whereIn('status', ['open', 'in_review'])
        ->count())->toBe(0);

    expect(DB::table('product_reports')
        ->where('product_id', $product->id)
        ->where('status', 'resolved')
        ->count())->toBe(2);

    $this->assertDatabaseHas('product_admin_action_logs', [
        'product_id' => $product->id,
        'admin_user_id' => $admin->id,
        'action_type' => 'flags_resolved',
    ]);
});
