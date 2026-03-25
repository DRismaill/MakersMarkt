<?php

namespace App\Filament\Widgets;

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\CreditTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\ProductReview;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatisticsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Platformcijfers';

    protected ?string $description = 'Kernstatistieken van gebruikers, producten en transacties.';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Gebruikers', User::query()->count())
                ->description('Totaal aantal accounts')
                ->icon('heroicon-o-users')
                ->color('gray'),
            Stat::make('Makers', User::query()->where('role', UserRole::Maker->value)->count())
                ->description('Actieve verkopers')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('success'),
            Stat::make('Kopers', User::query()->where('role', UserRole::Buyer->value)->count())
                ->description('Actieve kopers')
                ->icon('heroicon-o-shopping-bag')
                ->color('info'),
            Stat::make('Catalogusproducten', Product::query()->visibleInCatalog()->count())
                ->description('Openbaar zichtbare producten')
                ->icon('heroicon-o-squares-2x2')
                ->color('primary'),
            Stat::make('Bestellingen', Order::query()->count())
                ->description('Totaal aantal orders')
                ->icon('heroicon-o-shopping-cart')
                ->color('warning'),
            Stat::make('Reviews', ProductReview::query()->count())
                ->description('Productreviews geplaatst')
                ->icon('heroicon-o-star')
                ->color('success'),
            Stat::make('Open Rapporten', ProductReport::query()->whereIn('status', [ReportStatus::Open->value, ReportStatus::InReview->value])->count())
                ->description('Nog niet afgehandeld')
                ->icon('heroicon-o-flag')
                ->color('danger'),
            Stat::make('Krediettransacties', CreditTransaction::query()->count())
                ->description('Alle geregistreerde bewegingen')
                ->icon('heroicon-o-banknotes')
                ->color('gray'),
        ];
    }
}
