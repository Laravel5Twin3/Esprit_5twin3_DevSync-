<?php

use App\Http\Controllers\Back\DashboardController;
use App\Http\Controllers\Back\UtilisateurController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\ProfilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes communes
|--------------------------------------------------------------------------
| Les routes propres à chaque module se trouvent dans routes/modules/*.php
| (un fichier par module) et sont chargées automatiquement en bas de ce fichier.
| => Ne modifiez pas ce fichier pour votre module : cela évite les conflits.
|
| Authentification : les routes (connexion, inscription, déconnexion, mot de passe oublié,
| vérification d'e-mail, 2FA…) sont fournies par Laravel Fortify (config/fortify.php).
| Pour une page réservée aux connectés : middleware ['auth', 'verified'].
*/

// Front Office
Route::get('/', [HomeController::class, 'index'])->name('home');

// Profil de l'utilisateur connecté
Route::middleware(['auth', 'verified'])->group(function () {
    // password.confirm : le mot de passe est redemandé (valable 3 h) avant d'afficher la sécurité du compte
    Route::get('/profil', [ProfilController::class, 'show'])->middleware('password.confirm')->name('profil.show');
    Route::delete('/profil', [ProfilController::class, 'destroy'])->name('profil.destroy');
});

// Back Office
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Gestion des utilisateurs et de leurs rôles
    Route::middleware('permission:gerer utilisateurs')->group(function () {
        Route::get('utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index');
        Route::patch('utilisateurs/{utilisateur}/role', [UtilisateurController::class, 'changerRole'])->name('utilisateurs.role');
        Route::delete('utilisateurs/{utilisateur}', [UtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');
    });
});

// Chargement automatique des routes des modules
foreach (glob(__DIR__.'/modules/*.php') as $moduleRoutes) {
    require $moduleRoutes;
}
