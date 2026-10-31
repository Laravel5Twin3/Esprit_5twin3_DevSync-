<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Page « Mon profil » : informations, photo, mot de passe, 2FA et suppression du compte.
 * Les formulaires d'informations, de mot de passe et de 2FA sont traités par Fortify.
 */
class ProfilController extends Controller
{
    public function show(Request $request): View
    {
        return view('profil.show', ['user' => $request->user()]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('suppressionCompte', [
            'password' => ['required', 'current_password'],
            'confirmation' => ['accepted'],
        ], [
            'password.current_password' => 'Le mot de passe est incorrect.',
            'confirmation.accepted' => 'Cochez la case pour confirmer la suppression définitive.',
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
        }
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Votre compte a été supprimé.');
    }
}
