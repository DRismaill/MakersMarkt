<?php

use App\Enums\UserRole;
use App\Filament\Pages\ManageUserCredits;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::bootCurrentPanel();
});

it('allows admins to open the credit management screen', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->get(ManageUserCredits::getUrl(panel: 'admin'))
        ->assertOk();
});

it('forbids non admins from opening the credit management screen', function (): void {
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
    ]);

    $this->actingAs($buyer)
        ->get(ManageUserCredits::getUrl(panel: 'admin'))
        ->assertForbidden();
});

it('shows all users and their balances in the admin table', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
        'credit_balance' => 80.00,
    ]);
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
        'credit_balance' => 12.50,
    ]);
    $maker = User::factory()->create([
        'role' => UserRole::Maker,
        'credit_balance' => 48.75,
    ]);

    $this->actingAs($admin);

    Livewire::test(ManageUserCredits::class)
        ->assertCanSeeTableRecords([$admin, $buyer, $maker]);

    $this->get(ManageUserCredits::getUrl(panel: 'admin'))
        ->assertSeeText($admin->username)
        ->assertSeeText($buyer->username)
        ->assertSeeText($maker->username)
        ->assertSeeText('EUR 12,50')
        ->assertSeeText('EUR 48,75');
});

it('lets an admin increase a users balance and logs the adjustment', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
        'credit_balance' => 10.00,
    ]);

    $this->actingAs($admin);

    Livewire::test(ManageUserCredits::class)
        ->callTableAction('adjustCredit', $buyer->getKey(), [
            'direction' => 'increase',
            'amount' => '25.50',
            'note' => 'Handmatige correctie',
        ])
        ->assertHasNoTableActionErrors();

    expect($buyer->refresh()->credit_balance)->toBe('35.50');

    $this->assertDatabaseHas('credit_transactions', [
        'from_user_id' => null,
        'to_user_id' => $buyer->id,
        'amount' => '25.50',
        'reason_type' => 'adjustment',
        'created_by_admin_id' => $admin->id,
        'note' => 'Handmatige correctie',
    ]);
});

it('prevents a negative balance by default', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
        'credit_balance' => 10.00,
    ]);

    $this->actingAs($admin);

    Livewire::test(ManageUserCredits::class)
        ->callTableAction('adjustCredit', $buyer->getKey(), [
            'direction' => 'decrease',
            'amount' => '15.00',
        ]);

    expect($buyer->refresh()->credit_balance)->toBe('10.00');

    $this->assertDatabaseCount('credit_transactions', 0);
});

it('allows a negative balance when the rule is explicitly enabled', function (): void {
    config()->set('credit.allow_negative_balances', true);

    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
        'credit_balance' => 10.00,
    ]);

    $this->actingAs($admin);

    Livewire::test(ManageUserCredits::class)
        ->callTableAction('adjustCredit', $buyer->getKey(), [
            'direction' => 'decrease',
            'amount' => '15.00',
            'note' => 'Tijdelijke roodstand',
        ])
        ->assertHasNoTableActionErrors();

    expect($buyer->refresh()->credit_balance)->toBe('-5.00');

    $this->assertDatabaseHas('credit_transactions', [
        'from_user_id' => $buyer->id,
        'to_user_id' => null,
        'amount' => '15.00',
        'reason_type' => 'adjustment',
        'created_by_admin_id' => $admin->id,
        'note' => 'Tijdelijke roodstand',
    ]);
});
