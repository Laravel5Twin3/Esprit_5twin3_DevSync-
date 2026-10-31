<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

/**
 * Mise à jour du profil : nom, e-mail, téléphone et photo.
 * Un changement d'e-mail oblige à vérifier la nouvelle adresse.
 */
class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'regex:/^[0-9+\s]{8,20}$/'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=64,min_height=64'],
            'supprimer_photo' => ['nullable', 'boolean'],
        ], [
            'phone.regex' => 'Le téléphone ne doit contenir que des chiffres, des espaces et « + » (8 à 20 caractères).',
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
        ], [
            'name' => 'nom',
            'phone' => 'téléphone',
        ])->validateWithBag('updateProfileInformation');

        if (($input['photo'] ?? null) instanceof UploadedFile) {
            $this->remplacerPhoto($user, $input['photo']->store('photos-profil', 'public'));
        } elseif (! empty($input['supprimer_photo'])) {
            $this->remplacerPhoto($user, null);
        }

        $nouvelEmail = $input['email'] !== $user->email;

        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'email_verified_at' => $nouvelEmail && $user instanceof MustVerifyEmail ? null : $user->email_verified_at,
        ])->save();

        if ($nouvelEmail && $user instanceof MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
        }
    }

    private function remplacerPhoto(User $user, ?string $nouveauChemin): void
    {
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }

        $user->forceFill(['photo_path' => $nouveauChemin])->save();
    }
}
