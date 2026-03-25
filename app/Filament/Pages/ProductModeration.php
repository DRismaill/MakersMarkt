<?php

namespace App\Filament\Pages;

use App\Enums\ProductApprovalStatus;
use App\Filament\Pages\Concerns\AuthorizesAdminAccess;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductAdminService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ProductModeration extends Page implements HasTable
{
    use AuthorizesAdminAccess;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|UnitEnum|null $navigationGroup = 'Moderatie';

    protected static ?string $navigationLabel = 'Productmoderatie';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'productmoderatie';

    protected static ?string $title = 'Productmoderatie';

    protected ?string $subheading = 'Nieuwe en afgekeurde producten die admin-aandacht nodig hebben.';

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
                    ->with(['maker', 'productType'])
                    ->whereIn('approval_status', [
                        ProductApprovalStatus::Pending->value,
                        ProductApprovalStatus::Rejected->value,
                    ])
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('maker.username')
                    ->label('Maker')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('productType.name')
                    ->label('Categorie')
                    ->toggleable(),
                TextColumn::make('approval_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (ProductApprovalStatus|string|null $state): string => $this->formatStatus($state))
                    ->color(fn (ProductApprovalStatus|string|null $state): string => $this->statusColor($state))
                    ->sortable(),
                TextColumn::make('needs_moderation')
                    ->label('Moderatie Nodig')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ja' : 'Nee')
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
                TextColumn::make('rejection_reason')
                    ->label('Afwijsreden')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('approval_status')
                    ->label('Status')
                    ->options($this->getStatusOptions()),
            ])
            ->actions([
                Action::make('approveProduct')
                    ->label('Goedkeuren')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Product $record): string => "Product goedkeuren: {$record->name}")
                    ->modalDescription('Het product wordt goedgekeurd voor het platform. Open flags blijven wel bestaan als er nog rapporten of een externe link aanwezig zijn.')
                    ->action(function (Product $record): void {
                        $admin = Filament::auth()->user();

                        if (! $admin instanceof User) {
                            abort(403);
                        }

                        $updatedProduct = app(ProductAdminService::class)->approve($record, $admin);

                        Notification::make()
                            ->success()
                            ->title('Product goedgekeurd')
                            ->body("{$updatedProduct->name} is bijgewerkt.")
                            ->send();
                    }),
                Action::make('rejectProduct')
                    ->label('Afkeuren')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Afwijsreden')
                            ->rows(4)
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->modalHeading(fn (Product $record): string => "Product afkeuren: {$record->name}")
                    ->modalDescription('De maker kan de afwijsreden gebruiken om het product te verbeteren.')
                    ->modalSubmitActionLabel('Afkeuren')
                    ->action(function (array $data, Product $record): void {
                        $admin = Filament::auth()->user();

                        if (! $admin instanceof User) {
                            abort(403);
                        }

                        $updatedProduct = app(ProductAdminService::class)->reject(
                            product: $record,
                            admin: $admin,
                            reason: $data['rejection_reason'],
                        );

                        Notification::make()
                            ->success()
                            ->title('Product afgekeurd')
                            ->body("{$updatedProduct->name} is afgekeurd.")
                            ->send();
                    }),
            ]);
    }

    /**
     * @return array<string, string>
     */
    private function getStatusOptions(): array
    {
        return [
            ProductApprovalStatus::Pending->value => $this->formatStatus(ProductApprovalStatus::Pending),
            ProductApprovalStatus::Rejected->value => $this->formatStatus(ProductApprovalStatus::Rejected),
        ];
    }

    private function formatStatus(ProductApprovalStatus|string|null $state): string
    {
        $state = $state instanceof ProductApprovalStatus
            ? $state
            : (filled($state) ? ProductApprovalStatus::from($state) : null);

        return match ($state) {
            ProductApprovalStatus::Pending => 'Nieuw',
            ProductApprovalStatus::Rejected => 'Afgekeurd',
            ProductApprovalStatus::Approved => 'Goedgekeurd',
            default => '-',
        };
    }

    private function statusColor(ProductApprovalStatus|string|null $state): string
    {
        $state = $state instanceof ProductApprovalStatus
            ? $state
            : (filled($state) ? ProductApprovalStatus::from($state) : null);

        return match ($state) {
            ProductApprovalStatus::Pending => 'warning',
            ProductApprovalStatus::Rejected => 'danger',
            ProductApprovalStatus::Approved => 'success',
            default => 'gray',
        };
    }
}
