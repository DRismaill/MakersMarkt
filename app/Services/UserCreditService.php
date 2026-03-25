<?php

namespace App\Services;

use App\Enums\CreditAdjustmentDirection;
use App\Enums\CreditReasonType;
use App\Exceptions\NegativeCreditBalanceNotAllowedException;
use App\Models\CreditTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UserCreditService
{
    public function adjustBalance(
        User $user,
        string|int|float $amount,
        CreditAdjustmentDirection|string $direction,
        ?string $note = null,
        ?User $performedBy = null,
    ): User {
        $direction = is_string($direction)
            ? CreditAdjustmentDirection::from($direction)
            : $direction;

        if (($performedBy !== null) && (! $performedBy->isAdmin())) {
            throw new InvalidArgumentException('Alleen admins kunnen een saldo-aanpassing uitvoeren.');
        }

        $amountInCents = $this->normalizeAmountToCents($amount);

        return DB::transaction(function () use ($amountInCents, $direction, $note, $performedBy, $user): User {
            /** @var User $lockedUser */
            $lockedUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->getKey());

            $currentBalanceInCents = $this->decimalToCents($lockedUser->credit_balance);
            $deltaInCents = $direction === CreditAdjustmentDirection::Increase
                ? $amountInCents
                : -$amountInCents;
            $newBalanceInCents = $currentBalanceInCents + $deltaInCents;

            if (($newBalanceInCents < 0) && (! config('credit.allow_negative_balances', false))) {
                throw new NegativeCreditBalanceNotAllowedException('Negatief saldo is niet toegestaan.');
            }

            $lockedUser->forceFill([
                'credit_balance' => $this->formatCents($newBalanceInCents),
            ])->save();

            CreditTransaction::query()->create([
                'from_user_id' => $direction === CreditAdjustmentDirection::Decrease ? $lockedUser->getKey() : null,
                'to_user_id' => $direction === CreditAdjustmentDirection::Increase ? $lockedUser->getKey() : null,
                'amount' => $this->formatCents($amountInCents),
                'reason_type' => CreditReasonType::Adjustment,
                'created_by_admin_id' => $performedBy?->getKey(),
                'note' => blank($note) ? null : trim($note),
            ]);

            return $lockedUser->refresh();
        });
    }

    private function normalizeAmountToCents(string|int|float $amount): int
    {
        $amount = is_string($amount)
            ? str_replace(',', '.', trim($amount))
            : $amount;

        if (! is_numeric($amount)) {
            throw new InvalidArgumentException('Voer een geldig bedrag in.');
        }

        $amountInCents = (int) round(((float) $amount) * 100);

        if ($amountInCents <= 0) {
            throw new InvalidArgumentException('Het bedrag moet groter zijn dan 0.');
        }

        return $amountInCents;
    }

    private function decimalToCents(string|int|float|null $amount): int
    {
        if (($amount === null) || ($amount === '')) {
            return 0;
        }

        return (int) round(((float) $amount) * 100);
    }

    private function formatCents(int $amountInCents): string
    {
        return number_format($amountInCents / 100, 2, '.', '');
    }
}
