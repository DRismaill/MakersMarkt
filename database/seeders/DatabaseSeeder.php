<?php

namespace Database\Seeders;

use Database\Seeders\UserSeeder;
use Database\Seeders\ProductTypeSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the ProductTypeSeeder first
        $this->call(ProductTypeSeeder::class);
        
        // Call the UserSeeder
        $this->call(UserSeeder::class);
        
        // Call the ProductSeeder
        $this->call(ProductSeeder::class);
    }
}
