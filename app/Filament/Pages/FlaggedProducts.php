<?php

namespace App\Filament\Pages;

use App\Enums\ProductApprovalStatus;
use App\Enums\ReportStatus;
use App\Filament\Pages\Concerns\AuthorizesAdminAccess;
use App\Models\Product;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use UnitEnum;

class FlaggedProducts extends Page implements HasTable
{
    use AuthorizesAdminAccess;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static string|UnitEnum|null $navigationGroup = 'Moderatie';

    protected static ?string $navigationLabel = 'Gemarkeerde Producten';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'gemarkeerde-producten';

    protected static ?string $title = 'Gemarkeerde Producten';

    protected ?string $subheading = 'Producten met externe links of meldingen via rapportages.';

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->with(['maker'])
                    ->withCount([
                        'productReports',
                        'productReports as open_reports_count' => fn ($query) => $query->whereIn('status', [
                            ReportStatus::Open->value,
                            ReportStatus::InReview->value,
                        ]),
                    ])
                    ->where(function ($query): void {
                        $query
                            ->where('has_external_link', true)
                            ->orWhereHas('productReports');
                    })
            )
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('maker.username')
                    ->label('Maker')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('approval_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (ProductApprovalStatus|string|null $state): string => $this->formatStatus($state))
                    ->sortable(),
                TextColumn::make('has_external_link')
                    ->label('Externe Link')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ja' : 'Nee')
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
                TextColumn::make('open_reports_count')
                    ->label('Open Rapporten')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->sortable(),
                TextColumn::make('product_reports_count')
                    ->label('Alle Rapporten')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Laatst Gewijzigd')
                    ->since()
                    ->sortable(),
            ]);
    }

    private function formatStatus(ProductApprovalStatus|string|null $state): string
    {
        $state = $state instanceof ProductApprovalStatus
            ? $state
            : (filled($state) ? ProductApprovalStatus::from($state) : null);

        return match ($state) {
            ProductApprovalStatus::Approved => 'Goedgekeurd',
            ProductApprovalStatus::Pending => 'Nieuw',
            ProductApprovalStatus::Rejected => 'Afgekeurd',
            default => '-',
        };
    }
}
