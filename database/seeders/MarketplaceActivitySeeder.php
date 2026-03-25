<?php

namespace Database\Seeders;

use App\Enums\CreditReasonType;
use App\Enums\OrderStatus;
use App\Enums\ProductAdminActionType;
use App\Enums\ReportStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketplaceActivitySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $buyers = User::query()
            ->whereIn('username', ['buyer1', 'buyer2', 'buyer3', 'buyer4', 'buyer5', 'buyer6', 'buyer7'])
            ->get()
            ->keyBy('username');

        $products = Product::query()
            ->whereIn('slug', [
                'keramiek-vaas-zand',
                'eiken-wandplank-straal',
                'linnen-shopper-groen',
                'poster-kustlijn-print',
                'stalen-plantstandaard-arc',
                'keramiek-espresso-set',
                'archief-vaas-slate',
            ])
            ->get()
            ->keyBy('slug');

        $orders = [];

        foreach ([
            [
                'buyer_username' => 'buyer1',
                'product_slug' => 'keramiek-vaas-zand',
                'status' => OrderStatus::Completed,
                'status_note' => 'Afgerond en beoordeeld.',
            ],
            [
                'buyer_username' => 'buyer2',
                'product_slug' => 'eiken-wandplank-straal',
                'status' => OrderStatus::Paid,
                'status_note' => 'Betaald, wacht op productie.',
            ],
            [
                'buyer_username' => 'buyer3',
                'product_slug' => 'linnen-shopper-groen',
                'status' => OrderStatus::Completed,
                'status_note' => 'Op tijd geleverd.',
            ],
            [
                'buyer_username' => 'buyer4',
                'product_slug' => 'keramiek-espresso-set',
                'status' => OrderStatus::InProduction,
                'status_note' => 'Glazuur wordt deze week afgewerkt.',
            ],
            [
                'buyer_username' => 'buyer5',
                'product_slug' => 'stalen-plantstandaard-arc',
                'status' => OrderStatus::Completed,
                'status_note' => 'Afgeleverd en klaar voor review.',
            ],
        ] as $attributes) {
            $product = $products[$attributes['product_slug']];
            $buyer = $buyers[$attributes['buyer_username']];

            $orders[$attributes['product_slug']] = Order::query()->updateOrCreate(
                [
                    'buyer_id' => $buyer->id,
                    'product_id' => $product->id,
                ],
                [
                    'maker_id' => $product->maker_id,
                    'status' => $attributes['status'],
                    'status_note' => $attributes['status_note'],
                    'price_credit' => $product->price_credit,
                ],
            );
        }

        foreach ([
            [
                'product_slug' => 'keramiek-vaas-zand',
                'buyer_username' => 'buyer1',
                'rating' => 5,
                'comment' => 'Zeer nette afwerking en precies zoals op de foto.',
            ],
            [
                'product_slug' => 'linnen-shopper-groen',
                'buyer_username' => 'buyer3',
                'rating' => 4,
                'comment' => 'Stevig en mooi materiaal, de kleur wijkt licht af maar blijft fraai.',
            ],
            [
                'product_slug' => 'stalen-plantstandaard-arc',
                'buyer_username' => 'buyer5',
                'rating' => 5,
                'comment' => 'Stabiel, strak ontwerp en direct goed inzetbaar in huis.',
            ],
        ] as $attributes) {
            $product = $products[$attributes['product_slug']];
            $buyer = $buyers[$attributes['buyer_username']];
            $order = $orders[$attributes['product_slug']];

            DB::table('product_reviews')->updateOrInsert(
                [
                    'order_id' => $order->id,
                    'buyer_id' => $buyer->id,
                ],
                [
                    'product_id' => $product->id,
                    'rating' => $attributes['rating'],
                    'comment' => $attributes['comment'],
                    'created_at' => now()->subDays(2),
                ],
            );
        }

        foreach ([
            [
                'product_slug' => 'poster-kustlijn-print',
                'buyer_username' => 'buyer2',
                'reason' => 'Beschrijving verwijst naar een externe portfolio-link in plaats van alleen productinformatie.',
                'status' => ReportStatus::Open,
            ],
            [
                'product_slug' => 'stalen-plantstandaard-arc',
                'buyer_username' => 'buyer4',
                'reason' => 'Afmetingen lijken niet helemaal overeen te komen met de productfoto.',
                'status' => ReportStatus::InReview,
            ],
            [
                'product_slug' => 'stalen-plantstandaard-arc',
                'buyer_username' => 'buyer5',
                'reason' => 'Melding over duplicaat is gecontroleerd en afgehandeld.',
                'status' => ReportStatus::Resolved,
            ],
        ] as $attributes) {
            $product = $products[$attributes['product_slug']];
            $buyer = $buyers[$attributes['buyer_username']];

            DB::table('product_reports')->updateOrInsert(
                [
                    'product_id' => $product->id,
                    'reported_by_user_id' => $buyer->id,
                    'reason' => $attributes['reason'],
                ],
                [
                    'status' => $attributes['status']->value,
                    'created_at' => now()->subDays(1),
                ],
            );
        }

        foreach ([
            [
                'from_user_id' => null,
                'to_user_id' => $buyers['buyer1']->id,
                'amount' => '150.00',
                'reason_type' => CreditReasonType::Topup->value,
                'order_id' => null,
                'created_by_admin_id' => $admin->id,
                'note' => 'Welkomstegoed voorjaar',
            ],
            [
                'from_user_id' => null,
                'to_user_id' => $buyers['buyer2']->id,
                'amount' => '100.00',
                'reason_type' => CreditReasonType::Topup->value,
                'order_id' => null,
                'created_by_admin_id' => $admin->id,
                'note' => 'Opwaardering voor nieuwe collectie',
            ],
            [
                'from_user_id' => $buyers['buyer1']->id,
                'to_user_id' => $products['keramiek-vaas-zand']->maker_id,
                'amount' => '79.95',
                'reason_type' => CreditReasonType::Purchase->value,
                'order_id' => $orders['keramiek-vaas-zand']->id,
                'created_by_admin_id' => null,
                'note' => 'Bestelling: Zandkleurige Vaas',
            ],
            [
                'from_user_id' => $buyers['buyer3']->id,
                'to_user_id' => $products['linnen-shopper-groen']->maker_id,
                'amount' => '39.95',
                'reason_type' => CreditReasonType::Purchase->value,
                'order_id' => $orders['linnen-shopper-groen']->id,
                'created_by_admin_id' => null,
                'note' => 'Bestelling: Linnen Shopper Groen',
            ],
            [
                'from_user_id' => $buyers['buyer5']->id,
                'to_user_id' => $products['stalen-plantstandaard-arc']->maker_id,
                'amount' => '71.00',
                'reason_type' => CreditReasonType::Purchase->value,
                'order_id' => $orders['stalen-plantstandaard-arc']->id,
                'created_by_admin_id' => null,
                'note' => 'Bestelling: Stalen Plantstandaard Arc',
            ],
            [
                'from_user_id' => null,
                'to_user_id' => $buyers['buyer6']->id,
                'amount' => '15.00',
                'reason_type' => CreditReasonType::Adjustment->value,
                'order_id' => null,
                'created_by_admin_id' => $admin->id,
                'note' => 'Compensatie na vertraagde levering',
            ],
            [
                'from_user_id' => $buyers['buyer7']->id,
                'to_user_id' => null,
                'amount' => '10.00',
                'reason_type' => CreditReasonType::Adjustment->value,
                'order_id' => null,
                'created_by_admin_id' => $admin->id,
                'note' => 'Handmatige correctie op geblokkeerd account',
            ],
        ] as $attributes) {
            DB::table('credit_transactions')->updateOrInsert(
                [
                    'from_user_id' => $attributes['from_user_id'],
                    'to_user_id' => $attributes['to_user_id'],
                    'reason_type' => $attributes['reason_type'],
                    'order_id' => $attributes['order_id'],
                    'note' => $attributes['note'],
                ],
                [
                    'amount' => $attributes['amount'],
                    'created_by_admin_id' => $attributes['created_by_admin_id'],
                    'created_at' => now()->subDays(3),
                ],
            );
        }

        DB::table('product_admin_action_logs')->updateOrInsert(
            [
                'product_id' => $products['archief-vaas-slate']->id,
                'action_type' => ProductAdminActionType::Deactivated->value,
            ],
            [
                'admin_user_id' => $admin->id,
                'product_name' => $products['archief-vaas-slate']->name,
                'created_at' => now()->subDays(5),
            ],
        );

        Product::query()->each(function (Product $product): void {
            $reviewQuery = DB::table('product_reviews')->where('product_id', $product->id);
            $reviewCount = $reviewQuery->count();

            $product->forceFill([
                'review_count' => $reviewCount,
                'average_rating' => $reviewCount > 0
                    ? number_format((float) $reviewQuery->avg('rating'), 2, '.', '')
                    : null,
            ])->save();
        });
    }
}
