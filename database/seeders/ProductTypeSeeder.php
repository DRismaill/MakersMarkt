<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productTypes = [
            ['name' => 'Furniture', 'description' => 'Handcrafted furniture pieces'],
            ['name' => 'Jewelry', 'description' => 'Handmade jewelry and accessories'],
            ['name' => 'Art', 'description' => 'Original artwork and paintings'],
            ['name' => 'Ceramics', 'description' => 'Handmade pottery and ceramic items'],
            ['name' => 'Textiles', 'description' => 'Woven and textile products'],
            ['name' => 'Woodcraft', 'description' => 'Wood carving and woodworking'],
            ['name' => 'Metalwork', 'description' => 'Metal crafted items'],
            ['name' => 'Other', 'description' => 'Other handmade products'],
        ];

        foreach ($productTypes as $type) {
            ProductType::create($type);
        }
    }
}
