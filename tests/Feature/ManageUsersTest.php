<?php

use App\Enums\UserRole;
use App\Filament\Pages\ManageUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::bootCurrentPanel();
});

it('allows admins to open the user management screen', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->get(ManageUsers::getUrl(panel: 'admin'))
        ->assertOk();
});

it('forbids non admins from opening the user management screen', function (): void {
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
    ]);

    $this->actingAs($buyer)
        ->get(ManageUsers::getUrl(panel: 'admin'))
        ->assertForbidden();
});

it('shows user id username role and status in the overview', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
        'is_blocked' => false,
    ]);
    $blockedBuyer = User::factory()->create([
        'role' => UserRole::Buyer,
        'is_blocked' => true,
    ]);

    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->assertCanSeeTableRecords([$admin, $blockedBuyer]);

    $this->get(ManageUsers::getUrl(panel: 'admin'))
        ->assertSeeText((string) $admin->id)
        ->assertSeeText($admin->username)
        ->assertSeeText('Administrator')
        ->assertSeeText($blockedBuyer->username)
        ->assertSeeText('Geblokkeerd');
});

it('lets an admin edit a user role and blocked status', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
        'is_blocked' => false,
    ]);

    $this->actingAs($admin);

    Livewire::test(ManageUsers::class)
        ->callTableAction('editUser', $buyer->getKey(), [
            'role' => UserRole::Maker->value,
            'is_blocked' => true,
        ])
        ->assertHasNoTableActionErrors();

    expect($buyer->refresh()->role)->toBe(UserRole::Maker);
    expect($buyer->is_blocked)->toBeTrue();
});

it('has a table edit action and paginates the user list', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    User::factory()->count(12)->create();

    $this->actingAs($admin);

    $component = Livewire::test(ManageUsers::class)
        ->assertTableActionVisible('editUser', $admin->getKey());

    expect($component->instance()->getTable()->isPaginated())->toBeTrue();
});
