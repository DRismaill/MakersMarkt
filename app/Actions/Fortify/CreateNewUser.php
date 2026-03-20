<?php

namespace App\Actions\Fortify;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'username' => ['required', 'string', 'min:3', 'max:100', Rule::unique(User::class, 'username')],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in([UserRole::Maker->value, UserRole::Buyer->value])],
        ], [
            'username.unique' => 'Deze gebruikersnaam is al in gebruik.',
            'username.min' => 'Gebruikersnaam moet minimaal 3 karakters zijn.',
            'email.unique' => 'Dit e-mailadres is al in gebruik.',
            'password.min' => 'Wachtwoord moet minimaal 6 karakters zijn.',
            'password.confirmed' => 'De wachtwoorden komen niet overeen.',
        ])->validate();

        return User::create([
            'username' => $input['username'],
            'email' => $input['email'] ?? null,
            'password' => $input['password'],
            'role' => $input['role'],
        ]);
    }
}
