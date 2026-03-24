<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the maker user "gert"
        $gert = User::where('username', 'gert')->first();

        if (!$gert) {
            return; // Skip if gert doesn't exist
        }

        $products = [
            [
                'maker_id' => $gert->id,
                'product_type_id' => 1,
                'name' => 'Handgemaakte Houten Tafel',
                'slug' => 'handgemaakte-houten-tafel',
                'description' => 'Een prachtige houten tafel gemaakt van eikenhout. Perfect voor uw eetkamer.',
                'material' => 'Eikenhout',
                'production_time_days' => 14,
                'complexity' => 'high',
                'durability' => 'high',
                'unique_feature' => 'Handgesneden details',
                'price_credit' => 250.00,
                'approval_status' => 'approved',
                'is_active' => true,
            ],
            [
                'maker_id' => $gert->id,
                'product_type_id' => 2,
                'name' => 'Zilveren Ketting',
                'slug' => 'zilveren-ketting',
                'description' => 'Delicate zilveren ketting met handgemaakte hanger.',
                'material' => 'Sterling zilver',
                'production_time_days' => 5,
                'complexity' => 'medium',
                'durability' => 'high',
                'unique_feature' => 'Gegraveerde initialen beschikbaar',
                'price_credit' => 45.00,
                'approval_status' => 'approved',
                'is_active' => true,
            ],
            [
                'maker_id' => $gert->id,
                'product_type_id' => 3,
                'name' => 'Abstract Schilderij',
                'slug' => 'abstract-schilderij',
                'description' => 'Modern abstract schilderij op canvas. Unieke kleurcombinatie.',
                'material' => 'Acryl op canvas',
                'production_time_days' => 7,
                'complexity' => 'medium',
                'durability' => 'medium',
                'unique_feature' => 'Handgesigneerd door kunstenaar',
                'price_credit' => 120.00,
                'approval_status' => 'approved',
                'is_active' => true,
            ],
            [
                'maker_id' => $gert->id,
                'product_type_id' => 4,
                'name' => 'Keramische Koffiemok',
                'slug' => 'keramische-koffiemok',
                'description' => 'Handgedraaide keramische mok met glazuur. Gemak en stijl.',
                'material' => 'Keramiek',
                'production_time_days' => 10,
                'complexity' => 'low',
                'durability' => 'high',
                'unique_feature' => 'Elk stuk is uniek',
                'price_credit' => 25.00,
                'approval_status' => 'approved',
                'is_active' => true,
            ],
            [
                'maker_id' => $gert->id,
                'product_type_id' => 5,
                'name' => 'Wollen Sjaal',
                'slug' => 'wollen-sjaal',
                'description' => 'Handgeweven wollen sjaal in warme kleuren.',
                'material' => 'Merino wol',
                'production_time_days' => 21,
                'complexity' => 'high',
                'durability' => 'high',
                'unique_feature' => 'Handgeweven op traditionele weefgetouw',
                'price_credit' => 85.00,
                'approval_status' => 'approved',
                'is_active' => true,
            ],
            [
                'maker_id' => $gert->id,
                'product_type_id' => 6,
                'name' => 'Houten Snijdplank',
                'slug' => 'houten-snijdplank',
                'description' => 'Duurzame houten snijdplank voor de keuken. Mooi en praktisch.',
                'material' => 'Bamboe',
                'production_time_days' => 6,
                'complexity' => 'low',
                'durability' => 'high',
                'unique_feature' => 'Ergonomisch handvat',
                'price_credit' => 35.00,
                'approval_status' => 'pending',
                'is_active' => true,
            ],
            [
                'maker_id' => $gert->id,
                'product_type_id' => 7,
                'name' => 'Koperen Lamp',
                'slug' => 'koperen-lamp',
                'description' => 'Handgemaakte koperen hanglamp. Vintage stijl met modern ontwerp.',
                'material' => 'Koper',
                'production_time_days' => 12,
                'complexity' => 'high',
                'durability' => 'high',
                'unique_feature' => 'Verstelbare hoogte',
                'price_credit' => 180.00,
                'approval_status' => 'approved',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
