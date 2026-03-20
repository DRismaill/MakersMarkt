<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 admin user
        User::create([
            'role' => UserRole::Admin->value,
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'credit_balance' => 100.00,
            'is_blocked' => false,
            'is_deleted' => false,
        ]);

        // Create 10 buyer users
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'role' => UserRole::Buyer->value,
                'username' => 'buyer'.$i,
                'email' => 'buyer'.$i.'@example.com',
                'password' => Hash::make('password'),
                'credit_balance' => 100.00,
                'is_blocked' => false,
                'is_deleted' => false,
            ]);
        }

        // Create 10 maker users
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'role' => UserRole::Maker->value,
                'username' => 'maker'.$i,
                'email' => 'maker'.$i.'@example.com',
                'password' => Hash::make('password'),
                'credit_balance' => 100.00,
                'is_blocked' => false,
                'is_deleted' => false,
            ]);
        }
    }
}
