<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesAdminAccess;
use App\Filament\Widgets\AdminNavigationOverview;
use App\Filament\Widgets\ModerationQueueOverview;
use Filament\Actions\Action;
use Filament\Pages\Dashboard;
use Filament\Widgets\Widget;

class AdminDashboard extends Dashboard
{
    use AuthorizesAdminAccess;

    protected static bool $isDiscovered = false;

    protected static ?string $title = 'Admin Dashboard';

    protected ?string $subheading = 'Snelkoppelingen en widgets voor het beheergedeelte.';

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageUsers')
                ->label('Gebruikersbeheer')
                ->icon('heroicon-o-users')
                ->color('gray')
                ->url(ManageUsers::getUrl(isAbsolute: false, panel: 'admin')),
            Action::make('productModeration')
                ->label('Productmoderatie')
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->url(ProductModeration::getUrl(isAbsolute: false, panel: 'admin')),
            Action::make('creditManagement')
                ->label('Kredietbeheer')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->url(ManageUserCredits::getUrl(isAbsolute: false, panel: 'admin')),
            Action::make('statistics')
                ->label('Statistieken')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->url(PlatformStatistics::getUrl(isAbsolute: false, panel: 'admin')),
            Action::make('flaggedProducts')
                ->label('Gemarkeerde Producten')
                ->icon('heroicon-o-flag')
                ->color('danger')
                ->url(FlaggedProducts::getUrl(isAbsolute: false, panel: 'admin')),
        ];
    }

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return [
            AdminNavigationOverview::class,
            ModerationQueueOverview::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}
