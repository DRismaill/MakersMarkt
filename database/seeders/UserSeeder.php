<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $users = [
            [
                'role' => UserRole::Admin->value,
                'username' => 'adminuser',
                'email' => 'admin@example.com',
                'credit_balance' => 450.00,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Buyer->value,
                'username' => 'buyer1',
                'email' => 'buyer1@example.com',
                'credit_balance' => 220.00,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Buyer->value,
                'username' => 'buyer2',
                'email' => 'buyer2@example.com',
                'credit_balance' => 145.50,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Buyer->value,
                'username' => 'buyer3',
                'email' => 'buyer3@example.com',
                'credit_balance' => 68.00,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Buyer->value,
                'username' => 'buyer4',
                'email' => 'buyer4@example.com',
                'credit_balance' => 98.25,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Buyer->value,
                'username' => 'buyer5',
                'email' => 'buyer5@example.com',
                'credit_balance' => 310.00,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Buyer->value,
                'username' => 'buyer6',
                'email' => 'buyer6@example.com',
                'credit_balance' => 12.75,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Buyer->value,
                'username' => 'buyer7',
                'email' => 'buyer7@example.com',
                'credit_balance' => 55.00,
                'is_blocked' => true,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Buyer->value,
                'username' => 'buyer8',
                'email' => 'buyer8@example.com',
                'credit_balance' => 0.00,
                'is_blocked' => false,
                'is_deleted' => true,
            ],
            [
                'role' => UserRole::Maker->value,
                'username' => 'maker1',
                'email' => 'maker1@example.com',
                'credit_balance' => 520.00,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Maker->value,
                'username' => 'maker2',
                'email' => 'maker2@example.com',
                'credit_balance' => 430.25,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Maker->value,
                'username' => 'maker3',
                'email' => 'maker3@example.com',
                'credit_balance' => 388.40,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Maker->value,
                'username' => 'maker4',
                'email' => 'maker4@example.com',
                'credit_balance' => 610.00,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Maker->value,
                'username' => 'maker5',
                'email' => 'maker5@example.com',
                'credit_balance' => 275.90,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Maker->value,
                'username' => 'maker6',
                'email' => 'maker6@example.com',
                'credit_balance' => 190.00,
                'is_blocked' => false,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Maker->value,
                'username' => 'maker7',
                'email' => 'maker7@example.com',
                'credit_balance' => 75.00,
                'is_blocked' => true,
                'is_deleted' => false,
            ],
            [
                'role' => UserRole::Maker->value,
                'username' => 'maker8',
                'email' => 'maker8@example.com',
                'credit_balance' => 0.00,
                'is_blocked' => false,
                'is_deleted' => true,
            ],
        ];

        foreach ($users as $attributes) {
            User::query()->updateOrCreate(
                ['email' => $attributes['email']],
                [
                    ...$attributes,
                    'password' => $password,
                ],
            );
        }
    }
}
