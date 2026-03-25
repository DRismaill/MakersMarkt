<?php

namespace App\Filament\Pages\Concerns;

use App\Models\User;
use Filament\Facades\Filament;

trait AuthorizesAdminAccess
{
    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->isAdmin()
            && (! $user->is_blocked)
            && (! $user->is_deleted);
    }
}
