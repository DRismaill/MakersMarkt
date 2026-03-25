<?php

namespace App\Filament\Widgets;

use App\Enums\ReportStatus;
use App\Filament\Pages\FlaggedProducts;
use App\Filament\Pages\ProductModeration;
use App\Models\Product;
use App\Models\ProductReport;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ModerationQueueOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Moderatie Overzicht';

    protected ?string $description = 'Snelle signalen voor producten die admin-aandacht vragen.';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $pendingProducts = Product::query()->where('approval_status', 'pending')->count();
        $rejectedProducts = Product::query()->where('approval_status', 'rejected')->count();
        $externalLinks = Product::query()->where('has_external_link', true)->count();
        $openReports = ProductReport::query()
            ->whereIn('status', [ReportStatus::Open->value, ReportStatus::InReview->value])
            ->count();

        return [
            Stat::make('Nieuwe Producten', $pendingProducts)
                ->description('Wachten op beoordeling')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->url(ProductModeration::getUrl(isAbsolute: false, panel: 'admin')),
            Stat::make('Afgekeurde Producten', $rejectedProducts)
                ->description('Opnieuw te bekijken')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->url(ProductModeration::getUrl(isAbsolute: false, panel: 'admin')),
            Stat::make('Externe Links', $externalLinks)
                ->description('Gemarkeerd door links')
                ->icon('heroicon-o-link')
                ->color('info')
                ->url(FlaggedProducts::getUrl(isAbsolute: false, panel: 'admin')),
            Stat::make('Open Rapporten', $openReports)
                ->description('Meldingen van gebruikers')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->url(FlaggedProducts::getUrl(isAbsolute: false, panel: 'admin')),
        ];
    }
}
