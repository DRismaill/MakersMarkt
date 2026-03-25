<?php

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Filament\Pages\Concerns\AuthorizesAdminAccess;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use UnitEnum;

class ManageUsers extends Page implements HasTable
{
    use AuthorizesAdminAccess;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Gebruikers';

    protected static ?string $navigationLabel = 'Gebruikersbeheer';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'gebruikersbeheer';

    protected static ?string $title = 'Gebruikersbeheer';

    protected ?string $subheading = 'Overzicht van alle gebruikers met rol, status en een bewerkactie per account.';

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedTable::make(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->defaultSort('username')
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('username')
                    ->label('Gebruiker')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (UserRole|string|null $state): string => $this->formatRole($state))
                    ->color(fn (UserRole|string|null $state): string => $this->roleColor($state))
                    ->sortable(),
                TextColumn::make('credit_balance')
                    ->label('Saldo')
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn (string|int|float|null $state): string => $this->formatCurrency($state)),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (User $record): string => $this->formatStatus($record))
                    ->color(fn (User $record): string => $this->statusColor($record)),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rol')
                    ->options($this->getRoleOptions()),
            ])
            ->actions([
                Action::make('editUser')
                    ->label('Bewerken')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Select::make('role')
                            ->label('Rol')
                            ->options($this->getRoleOptions())
                            ->required()
                            ->native(false)
                            ->rules([
                                Rule::in(array_keys($this->getRoleOptions())),
                            ]),
                        Toggle::make('is_blocked')
                            ->label('Geblokkeerd')
                            ->helperText('Geblokkeerde accounts kunnen het adminpanel niet openen en kunnen later weer gedeblokkeerd worden.'),
                    ])
                    ->fillForm(fn (User $record): array => [
                        'role' => $record->role->value,
                        'is_blocked' => $record->is_blocked,
                    ])
                    ->modalHeading(fn (User $record): string => "Gebruiker bewerken: {$record->username}")
                    ->modalSubmitActionLabel('Opslaan')
                    ->action(function (array $data, User $record): void {
                        $record->update([
                            'role' => $data['role'],
                            'is_blocked' => (bool) ($data['is_blocked'] ?? false),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Gebruiker bijgewerkt')
                            ->body("{$record->username} is bijgewerkt.")
                            ->send();
                    }),
            ]);
    }

    /**
     * @return array<string, string>
     */
    private function getRoleOptions(): array
    {
        return collect(UserRole::cases())
            ->mapWithKeys(fn (UserRole $role): array => [
                $role->value => $role->label(),
            ])
            ->all();
    }

    private function formatRole(UserRole|string|null $state): string
    {
        $state = $state instanceof UserRole
            ? $state
            : (filled($state) ? UserRole::from($state) : null);

        return $state?->label() ?? '-';
    }

    private function roleColor(UserRole|string|null $state): string
    {
        $state = $state instanceof UserRole
            ? $state
            : (filled($state) ? UserRole::from($state) : null);

        return match ($state) {
            UserRole::Admin => 'warning',
            UserRole::Maker => 'success',
            UserRole::Buyer => 'gray',
            default => 'gray',
        };
    }

    private function formatCurrency(string|int|float|null $amount): string
    {
        return 'EUR '.number_format((float) ($amount ?? 0), 2, ',', '.');
    }

    private function formatStatus(User $record): string
    {
        return match (true) {
            $record->is_deleted => 'Verwijderd',
            $record->is_blocked => 'Geblokkeerd',
            default => 'Actief',
        };
    }

    private function statusColor(User $record): string
    {
        return match (true) {
            $record->is_deleted => 'danger',
            $record->is_blocked => 'warning',
            default => 'success',
        };
    }
}
