<?php

namespace App\Filament\Pages;

use App\Enums\ProductAdminActionType;
use App\Enums\ProductApprovalStatus;
use App\Filament\Pages\Concerns\AuthorizesAdminAccess;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use App\Services\ProductAdminService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ManageProducts extends Page implements HasTable
{
    use AuthorizesAdminAccess;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box-x-mark';

    protected static string|UnitEnum|null $navigationGroup = 'Producten';

    protected static ?string $navigationLabel = 'Productbeheer';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'productbeheer';

    protected static ?string $title = 'Productbeheer';

    protected ?string $subheading = 'Admins kunnen producten deactiveren. Gedeactiveerde producten blijven in de database, maar verdwijnen uit de openbare catalogus.';

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->with(['maker', 'productType']))
            ->defaultSort('created_at', 'desc')
            ->deferFilters(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(1)
            ->description('Deactiveren zet een product uit de publieke catalogus zonder bestaande orders of reviews te verbreken.')
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Beschrijving')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('maker.username')
                    ->label('Maker')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('productType.name')
                    ->label('Type')
                    ->toggleable(),
                TextColumn::make('material')
                    ->label('Materiaal')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('production_time_days')
                    ->label('Productietijd')
                    ->suffix(' dagen')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('approval_status')
                    ->label('Goedkeuring')
                    ->badge()
                    ->formatStateUsing(fn (ProductApprovalStatus|string|null $state): string => $this->formatApprovalStatus($state))
                    ->color(fn (ProductApprovalStatus|string|null $state): string => $this->approvalStatusColor($state))
                    ->sortable(),
                TextColumn::make('price_credit')
                    ->label('Prijs')
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn (string|int|float|null $state): string => $this->formatCurrency($state)),
                TextColumn::make('is_active')
                    ->label('Actief')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ja' : 'Nee')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextColumn::make('is_deleted')
                    ->label('Verwijderd')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ja' : 'Nee')
                    ->color(fn (bool $state): string => $state ? 'danger' : 'gray'),
            ])
            ->filters([
                Filter::make('product_filters')
                    ->label('Snel filteren')
                    ->columns([
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->label('Naam')
                            ->placeholder('Bijv. vaas of lamp')
                            ->prefixIcon('heroicon-o-magnifying-glass'),
                        TextInput::make('description')
                            ->label('Beschrijving')
                            ->placeholder('Bijv. handgemaakt of glazuur')
                            ->prefixIcon('heroicon-o-document-text'),
                        Select::make('product_type_id')
                            ->label('Type')
                            ->placeholder('Alle types')
                            ->options($this->getProductTypeOptions())
                            ->native(false)
                            ->searchable(),
                        TextInput::make('material')
                            ->label('Materiaal')
                            ->placeholder('Bijv. hout of keramiek')
                            ->prefixIcon('heroicon-o-swatch'),
                        TextInput::make('production_time_days')
                            ->label('Productietijd (dagen)')
                            ->numeric()
                            ->placeholder('Bijv. 7'),
                        Select::make('approval_status')
                            ->label('Goedkeuring')
                            ->placeholder('Alle statussen')
                            ->options($this->getApprovalStatusOptions())
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $this->applyProductFilters($query, $data))
                    ->indicateUsing(fn (array $data): array => $this->getProductFilterIndicators($data)),
            ])
            ->actions([
                Action::make('deactivateProduct')
                    ->label('Deactiveren')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Product $record): string => "Product deactiveren: {$record->name}")
                    ->modalDescription('Het product blijft in de database voor bestaande orders en reviews, maar is niet langer zichtbaar in de openbare catalogus.')
                    ->modalSubmitActionLabel('Deactiveren')
                    ->hidden(fn (Product $record): bool => (! $record->is_active) && $record->is_deleted)
                    ->action(function (Product $record): void {
                        $admin = Filament::auth()->user();

                        if (! $admin instanceof User) {
                            abort(403);
                        }

                        $updatedProduct = app(ProductAdminService::class)->deactivate($record, $admin);

                        Notification::make()
                            ->success()
                            ->title(ProductAdminActionType::Deactivated->label())
                            ->body("{$updatedProduct->name} is gedeactiveerd en verdwijnt uit de openbare catalogus.")
                            ->send();
                    }),
            ]);
    }

    private function applyProductFilters(Builder $query, array $data): Builder
    {
        return $query
            ->when(
                filled($data['name'] ?? null),
                fn (Builder $query): Builder => $query->where('name', 'like', '%'.trim($data['name']).'%'),
            )
            ->when(
                filled($data['description'] ?? null),
                fn (Builder $query): Builder => $query->where('description', 'like', '%'.trim($data['description']).'%'),
            )
            ->when(
                filled($data['product_type_id'] ?? null),
                fn (Builder $query): Builder => $query->where('product_type_id', $data['product_type_id']),
            )
            ->when(
                filled($data['material'] ?? null),
                fn (Builder $query): Builder => $query->where('material', 'like', '%'.trim($data['material']).'%'),
            )
            ->when(
                filled($data['production_time_days'] ?? null),
                fn (Builder $query): Builder => $query->where('production_time_days', (int) $data['production_time_days']),
            )
            ->when(
                filled($data['approval_status'] ?? null),
                fn (Builder $query): Builder => $query->where('approval_status', $data['approval_status']),
            );
    }

    /**
     * @return array<int, Indicator>
     */
    private function getProductFilterIndicators(array $data): array
    {
        $indicators = [];

        if (filled($data['name'] ?? null)) {
            $indicators[] = Indicator::make('Naam: '.trim($data['name']))
                ->removeField('name');
        }

        if (filled($data['description'] ?? null)) {
            $indicators[] = Indicator::make('Beschrijving: '.trim($data['description']))
                ->removeField('description');
        }

        if (filled($data['product_type_id'] ?? null)) {
            $productTypeLabel = $this->getProductTypeOptions()[(string) $data['product_type_id']] ?? $data['product_type_id'];

            $indicators[] = Indicator::make('Type: '.$productTypeLabel)
                ->removeField('product_type_id');
        }

        if (filled($data['material'] ?? null)) {
            $indicators[] = Indicator::make('Materiaal: '.trim($data['material']))
                ->removeField('material');
        }

        if (filled($data['production_time_days'] ?? null)) {
            $indicators[] = Indicator::make('Productietijd: '.(int) $data['production_time_days'].' dagen')
                ->removeField('production_time_days');
        }

        if (filled($data['approval_status'] ?? null)) {
            $indicators[] = Indicator::make('Goedkeuring: '.$this->formatApprovalStatus($data['approval_status']))
                ->removeField('approval_status');
        }

        return $indicators;
    }

    /**
     * @return array<string, string>
     */
    private function getProductTypeOptions(): array
    {
        return ProductType::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function getApprovalStatusOptions(): array
    {
        return collect(ProductApprovalStatus::cases())
            ->mapWithKeys(fn (ProductApprovalStatus $status): array => [
                $status->value => $this->formatApprovalStatus($status),
            ])
            ->all();
    }

    private function formatApprovalStatus(ProductApprovalStatus|string|null $state): string
    {
        $state = $state instanceof ProductApprovalStatus
            ? $state
            : (filled($state) ? ProductApprovalStatus::from($state) : null);

        return match ($state) {
            ProductApprovalStatus::Approved => 'Goedgekeurd',
            ProductApprovalStatus::Pending => 'In afwachting',
            ProductApprovalStatus::Rejected => 'Afgekeurd',
            default => '-',
        };
    }

    private function approvalStatusColor(ProductApprovalStatus|string|null $state): string
    {
        $state = $state instanceof ProductApprovalStatus
            ? $state
            : (filled($state) ? ProductApprovalStatus::from($state) : null);

        return match ($state) {
            ProductApprovalStatus::Approved => 'success',
            ProductApprovalStatus::Pending => 'warning',
            ProductApprovalStatus::Rejected => 'danger',
            default => 'gray',
        };
    }

    private function formatCurrency(string|int|float|null $amount): string
    {
        return 'EUR '.number_format((float) ($amount ?? 0), 2, ',', '.');
    }
}
