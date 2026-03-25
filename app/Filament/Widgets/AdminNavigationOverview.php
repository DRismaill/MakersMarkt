<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\FlaggedProducts;
use App\Filament\Pages\ManageUserCredits;
use App\Filament\Pages\ManageUsers;
use App\Filament\Pages\PlatformStatistics;
use App\Filament\Pages\ProductModeration;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminNavigationOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Beheeronderdelen';

    protected ?string $description = 'Klik op een widget om direct naar het juiste adminscherm te gaan.';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $moderationCount = Product::query()
            ->whereIn('approval_status', ['pending', 'rejected'])
            ->count();

        $flaggedCount = Product::query()
            ->where(function ($query): void {
                $query
                    ->where('has_external_link', true)
                    ->orWhereHas('productReports');
            })
            ->count();

        return [
            Stat::make('Gebruikersbeheer', User::query()->count())
                ->description('Open gebruikersoverzicht')
                ->icon('heroicon-o-users')
                ->color('gray')
                ->url(ManageUsers::getUrl(isAbsolute: false, panel: 'admin')),
            Stat::make('Productmoderatie', $moderationCount)
                ->description('Nieuw of afgekeurd')
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->url(ProductModeration::getUrl(isAbsolute: false, panel: 'admin')),
            Stat::make('Kredietbeheer', User::query()->where('credit_balance', '>', 0)->count())
                ->description('Accounts met positief saldo')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->url(ManageUserCredits::getUrl(isAbsolute: false, panel: 'admin')),
            Stat::make('Statistieken', Product::query()->visibleInCatalog()->count())
                ->description('Zichtbare producten in catalogus')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->url(PlatformStatistics::getUrl(isAbsolute: false, panel: 'admin')),
            Stat::make('Gemarkeerde Producten', $flaggedCount)
                ->description('Links of rapporten')
                ->icon('heroicon-o-flag')
                ->color('danger')
                ->url(FlaggedProducts::getUrl(isAbsolute: false, panel: 'admin')),
        ];
    }
}
