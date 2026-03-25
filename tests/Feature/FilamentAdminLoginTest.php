<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FilamentAdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_to_admin_panel(): void
    {
        // Arrange
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Act
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Assert
        $response->assertRedirect('/admin'); // or to your configured Filament home
        $this->assertAuthenticatedAs($admin);
    }

    public function test_buyer_cannot_login_to_admin_panel_and_gets_validation_error(): void
    {
        // Arrange
        $buyer = User::factory()->create([
            'role' => UserRole::Buyer,
            'username' => 'buyer1',
            'email' => 'buyer@example.com',
            'password' => Hash::make('password'),
        ]);

        // Act
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'buyer@example.com',
            'password' => 'password',
        ]);

        // Assert: stays on login page and shows validation error on "email"
        $response
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_maker_cannot_login_to_admin_panel_and_gets_validation_error(): void
    {
        // Arrange
        $maker = User::factory()->create([
            'role' => UserRole::Maker,
            'username' => 'maker1',
            'email' => 'maker@example.com',
            'password' => Hash::make('password'),
        ]);

        // Act
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'maker@example.com',
            'password' => 'password',
        ]);

        // Assert
        $response
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_wrong_password_fails_with_validation_error(): void
    {
        // Arrange
        User::factory()->create([
            'role' => UserRole::Admin,
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Act
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        // Assert
        $response
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
