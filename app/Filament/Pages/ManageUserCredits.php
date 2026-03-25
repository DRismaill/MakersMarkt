<?php

namespace App\Filament\Pages;

use App\Enums\CreditAdjustmentDirection;
use App\Enums\UserRole;
use App\Exceptions\NegativeCreditBalanceNotAllowedException;
use App\Filament\Pages\Concerns\AuthorizesAdminAccess;
use App\Models\User;
use App\Services\UserCreditService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use UnitEnum;

class ManageUserCredits extends Page implements HasTable
{
    use AuthorizesAdminAccess;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|UnitEnum|null $navigationGroup = 'Gebruikers';

    protected static ?string $navigationLabel = 'Kredietbeheer';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'kredietbeheer';

    protected static ?string $title = 'Kredietbeheer';

    protected ?string $subheading = 'Beheer het winkelkrediet per gebruiker. Elke wijziging wordt als adjustment-transactie gelogd.';

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
            ->description(
                config('credit.allow_negative_balances', false)
                    ? 'Alle gebruikers worden getoond met hun actuele saldo. Negatieve saldi zijn via configuratie toegestaan.'
                    : 'Alle gebruikers worden getoond met hun actuele saldo. Negatieve saldi zijn niet toegestaan.'
            )
            ->columns([
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
                    ->formatStateUsing(function (UserRole|string|null $state): string {
                        if ($state instanceof UserRole) {
                            return $state->label();
                        }

                        return filled($state)
                            ? UserRole::from($state)->label()
                            : '-';
                    })
                    ->color(function (UserRole|string|null $state): string {
                        $state = $state instanceof UserRole
                            ? $state
                            : (filled($state) ? UserRole::from($state) : null);

                        return match ($state) {
                            UserRole::Admin => 'warning',
                            UserRole::Maker => 'success',
                            UserRole::Buyer => 'gray',
                            default => 'gray',
                        };
                    })
                    ->sortable(),
                TextColumn::make('credit_balance')
                    ->label('Saldo')
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn (?string $state): string => $this->formatCurrency($state)),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Rol')
                    ->options($this->getRoleFilterOptions()),
            ])
            ->actions([
                Action::make('adjustCredit')
                    ->label('Saldo aanpassen')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Select::make('direction')
                            ->label('Wijziging')
                            ->options($this->getDirectionOptions())
                            ->default(CreditAdjustmentDirection::Increase->value)
                            ->required()
                            ->native(false),
                        TextInput::make('amount')
                            ->label('Bedrag')
                            ->prefix('EUR')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->rule('decimal:0,2')
                            ->inputMode('decimal')
                            ->helperText('Voer een positief bedrag in. Het type wijziging bepaalt of het saldo stijgt of daalt.'),
                        Textarea::make('note')
                            ->label('Reden')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Optioneel. Deze tekst wordt mee opgeslagen in de transactielog.'),
                    ])
                    ->modalHeading(fn (User $record): string => "Saldo aanpassen voor {$record->username}")
                    ->modalDescription(function (User $record): string {
                        $negativeBalanceRule = config('credit.allow_negative_balances', false)
                            ? 'Negatieve saldi zijn toegestaan via configuratie.'
                            : 'Negatieve saldi zijn niet toegestaan.';

                        return 'Huidig saldo: '.$this->formatCurrency($record->credit_balance).'. '.$negativeBalanceRule;
                    })
                    ->modalSubmitActionLabel('Saldo bijwerken')
                    ->action(function (array $data, User $record): void {
                        $admin = Filament::auth()->user();

                        if (! $admin instanceof User) {
                            abort(403);
                        }

                        try {
                            $updatedUser = app(UserCreditService::class)->adjustBalance(
                                user: $record,
                                amount: $data['amount'],
                                direction: $data['direction'],
                                note: $data['note'] ?? null,
                                performedBy: $admin,
                            );
                        } catch (NegativeCreditBalanceNotAllowedException $exception) {
                            throw ValidationException::withMessages([
                                'amount' => $exception->getMessage(),
                            ]);
                        }

                        Notification::make()
                            ->success()
                            ->title('Saldo bijgewerkt')
                            ->body("Nieuw saldo van {$updatedUser->username}: {$this->formatCurrency($updatedUser->credit_balance)}")
                            ->send();
                    }),
            ]);
    }

    /**
     * @return array<string, string>
     */
    private function getDirectionOptions(): array
    {
        return collect(CreditAdjustmentDirection::cases())
            ->mapWithKeys(fn (CreditAdjustmentDirection $direction): array => [
                $direction->value => $direction->label(),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function getRoleFilterOptions(): array
    {
        return collect(UserRole::cases())
            ->mapWithKeys(fn (UserRole $role): array => [
                $role->value => $role->label(),
            ])
            ->all();
    }

    private function formatCurrency(string|int|float|null $amount): string
    {
        return 'EUR '.number_format((float) ($amount ?? 0), 2, ',', '.');
    }
}
