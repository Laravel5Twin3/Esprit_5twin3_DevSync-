<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

/**
 * Inscription d'un habitant : il reçoit le rôle « citoyen » et doit ensuite vérifier son e-mail.
 */
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'phone' => ['nullable', 'regex:/^[0-9+\s]{8,20}$/'],
            'password' => $this->passwordRules(),
        ], [
            'phone.regex' => 'Le téléphone ne doit contenir que des chiffres, des espaces et « + » (8 à 20 caractères).',
        ], [
            'name' => 'nom',
            'phone' => 'téléphone',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'password' => Hash::make($input['password']),
        ]);

        $user->assignRole('citoyen');

        return $user;
    }
}
