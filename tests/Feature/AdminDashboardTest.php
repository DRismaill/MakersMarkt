<?php

use App\Enums\UserRole;
use App\Filament\Pages\AdminDashboard;
use App\Filament\Pages\FlaggedProducts;
use App\Filament\Pages\ManageUserCredits;
use App\Filament\Pages\ManageUsers;
use App\Filament\Pages\PlatformStatistics;
use App\Filament\Pages\ProductModeration;
use App\Models\User;
use Filament\Facades\Filament;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::bootCurrentPanel();
});

it('redirects guests to the admin login when opening the admin dashboard', function (): void {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});

it('forbids non admins from opening the admin dashboard', function (): void {
    $buyer = User::factory()->create([
        'role' => UserRole::Buyer,
    ]);

    $this->actingAs($buyer)
        ->get('/admin')
        ->assertForbidden();
});

it('shows dashboard buttons and links to the admin sections for admins', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $response = $this->actingAs($admin)
        ->get(AdminDashboard::getUrl(isAbsolute: false, panel: 'admin'));

    $response
        ->assertOk()
        ->assertSeeText('Gebruikersbeheer')
        ->assertSeeText('Productmoderatie')
        ->assertSeeText('Kredietbeheer')
        ->assertSeeText('Statistieken')
        ->assertSeeText('Gemarkeerde Producten')
        ->assertSee(ManageUsers::getUrl(isAbsolute: false, panel: 'admin'), false)
        ->assertSee(ProductModeration::getUrl(isAbsolute: false, panel: 'admin'), false)
        ->assertSee(ManageUserCredits::getUrl(isAbsolute: false, panel: 'admin'), false)
        ->assertSee(PlatformStatistics::getUrl(isAbsolute: false, panel: 'admin'), false)
        ->assertSee(FlaggedProducts::getUrl(isAbsolute: false, panel: 'admin'), false);
});

it('allows admins to open all dashboard-linked admin pages', function (): void {
    $admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);

    $this->actingAs($admin)
        ->get(ManageUsers::getUrl(isAbsolute: false, panel: 'admin'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(ProductModeration::getUrl(isAbsolute: false, panel: 'admin'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(ManageUserCredits::getUrl(isAbsolute: false, panel: 'admin'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(PlatformStatistics::getUrl(isAbsolute: false, panel: 'admin'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(FlaggedProducts::getUrl(isAbsolute: false, panel: 'admin'))
        ->assertOk();
});
