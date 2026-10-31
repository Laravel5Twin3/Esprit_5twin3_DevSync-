<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;

/**
 * Redirection après connexion : l'admin arrive sur le back office, le citoyen sur l'accueil
 * (ou sur la page qu'il voulait ouvrir avant d'être redirigé vers la connexion).
 */
class LoginResponse implements LoginResponseContract, TwoFactorLoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $defaut = $request->user()->isAdmin() ? route('admin.dashboard') : route('home');

        return redirect()->intended($defaut);
    }
}
