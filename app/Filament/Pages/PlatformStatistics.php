<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\AuthorizesAdminAccess;
use App\Filament\Widgets\ModerationQueueOverview;
use App\Filament\Widgets\PlatformStatisticsOverview;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Widgets\Widget;
use UnitEnum;

class PlatformStatistics extends Page
{
    use AuthorizesAdminAccess;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Statistieken';

    protected static ?string $navigationLabel = 'Statistieken';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'statistieken';

    protected static ?string $title = 'Statistieken';

    protected ?string $subheading = 'Belangrijkste platformcijfers en moderatie-aandachtspunten.';

    /**
     * @return array<class-string<Widget>>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            PlatformStatisticsOverview::class,
            ModerationQueueOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
