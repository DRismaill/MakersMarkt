<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
            $this->command->warn('User "gert" not found. Skipping ProductSeeder.');
            return;
        }

        $products = [
            [
                'product_type_id'      => 1,
                'name'                 => 'Houten Eettafel',
                'description'          => 'Een prachtige tafel gemaakt van eikenhout. Perfect voor uw eetkamer.',
                'material'             => 'Eikenhout',
                'production_time_days' => 14,
                'complexity'           => 'high',
                'durability'           => 'high',
                'unique_feature'       => 'Handgesneden details in het blad',
                'price_credit'         => 250.00,
                'approval_status'      => 'approved',
                'is_active'            => true,
            ],
            [
                'product_type_id'      => 2,
                'name'                 => 'Zilveren Ketting',
                'description'          => 'Delicate zilveren ketting met handgemaakte hanger.',
                'material'             => 'Sterling zilver',
                'production_time_days' => 5,
                'complexity'           => 'medium',
                'durability'           => 'high',
                'unique_feature'       => 'Gegraveerde initialen beschikbaar',
                'price_credit'         => 45.00,
                'approval_status'      => 'approved',
                'is_active'            => true,
            ],
            [
                'product_type_id'      => 3,
                'name'                 => 'Abstract Schilderij',
                'description'          => 'Modern abstract schilderij op canvas. Unieke kleurcombinatie.',
                'material'             => 'Acryl op canvas',
                'production_time_days' => 7,
                'complexity'           => 'medium',
                'durability'           => 'medium',
                'unique_feature'       => 'Handgesigneerd door kunstenaar',
                'price_credit'         => 120.00,
                'approval_status'      => 'approved',
                'is_active'            => true,
            ],
            [
                'product_type_id'      => 4,
                'name'                 => 'Keramische Koffiemok',
                'description'          => 'Handgedraaide keramische mok met glazuur.',
                'material'             => 'Keramiek',
                'production_time_days' => 10,
                'complexity'           => 'low',
                'durability'           => 'high',
                'unique_feature'       => 'Elk stuk is uniek van vorm',
                'price_credit'         => 25.00,
                'approval_status'      => 'approved',
                'is_active'            => true,
            ],
            [
                'product_type_id'      => 5,
                'name'                 => 'Wollen Sjaal',
                'description'          => 'Handgeweven wollen sjaal in warme kleuren.',
                'material'             => 'Merino wol',
                'production_time_days' => 21,
                'complexity'           => 'high',
                'durability'           => 'high',
                'unique_feature'       => 'Geweven op traditioneel weefgetouw',
                'price_credit'         => 85.00,
                'approval_status'      => 'approved',
                'is_active'            => true,
            ],
            [
                'product_type_id'      => 6,
                'name'                 => 'Bamboe Snijdplank',
                'description'          => 'Duurzame snijdplank voor de keuken.',
                'material'             => 'Bamboe',
                'production_time_days' => 6,
                'complexity'           => 'low',
                'durability'           => 'high',
                'unique_feature'       => 'Ergonomisch handvat',
                'price_credit'         => 35.00,
                'approval_status'      => 'pending',
                'is_active'            => true,
            ],
            [
                'product_type_id'      => 7,
                'name'                 => 'Koperen Hanglamp',
                'description'          => 'Handgemaakte koperen hanglamp. Vintage stijl met modern ontwerp.',
                'material'             => 'Koper',
                'production_time_days' => 12,
                'complexity'           => 'high',
                'durability'           => 'high',
                'unique_feature'       => 'Verstelbare hoogte',
                'price_credit'         => 180.00,
                'approval_status'      => 'approved',
                'is_active'            => true,
            ],
            [
                'product_type_id'      => 3,
                'name'                 => 'Aquarel Landschap',
                'description'          => 'Handgeschilderd aquarel landschap. Rustig en sfeervol.',
                'material'             => 'Aquarelverf op papier',
                'production_time_days' => 4,
                'complexity'           => 'medium',
                'durability'           => 'medium',
                'unique_feature'       => 'Op maat geschilderd op aanvraag',
                'price_credit'         => 65.00,
                'approval_status'      => 'approved',
                'is_active'            => true,
            ],
            [
                'product_type_id'      => 4,
                'name'                 => 'Aardewerk Vaas',
                'description'          => 'Handgedraaide vaas van steengoed klei.',
                'material'             => 'Steengoed klei',
                'production_time_days' => 8,
                'complexity'           => 'medium',
                'durability'           => 'high',
                'unique_feature'       => 'Uniek glazuurpatroon per stuk',
                'price_credit'         => 55.00,
                'approval_status'      => 'pending',
                'is_active'            => true,
            ],
            [
                'product_type_id'      => 1,
                'name'                 => 'Boekenplank Massief',
                'description'          => 'Robuuste boekenplank van massief grenenhout.',
                'material'             => 'Grenenhout',
                'production_time_days' => 10,
                'complexity'           => 'medium',
                'durability'           => 'high',
                'unique_feature'       => 'Verstelbare legplanken',
                'price_credit'         => 95.00,
                'approval_status'      => 'rejected',
                'is_active'            => false,
            ],
        ];

        foreach ($products as $product) {
            Product::create(array_merge($product, [
                'maker_id' => $gert->id,
                'slug'     => Str::slug($product['name']),
            ]));
        }

        $this->command->info('Seeded 10 products for maker "gert".');
    }
}
