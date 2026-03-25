<?php

namespace App\Filament\Pages;

use App\Enums\ProductAdminActionType;
use App\Enums\ProductApprovalStatus;
use App\Filament\Pages\Concerns\AuthorizesAdminAccess;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductAdminService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
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
            ->description('Deactiveren zet een product uit de publieke catalogus zonder bestaande orders of reviews te verbreken.')
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
                SelectFilter::make('approval_status')
                    ->label('Goedkeuring')
                    ->options($this->getApprovalStatusOptions()),
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
