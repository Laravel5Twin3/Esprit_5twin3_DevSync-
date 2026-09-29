<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Back\DashboardController;
use App\Http\Controllers\Front\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes communes
|--------------------------------------------------------------------------
| Les routes propres à chaque module se trouvent dans routes/modules/*.php
| (un fichier par module) et sont chargées automatiquement en bas de ce fichier.
| => Ne modifiez pas ce fichier pour votre module : cela évite les conflits.
*/

// Front Office
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Back Office
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

// Chargement automatique des routes des modules
foreach (glob(__DIR__.'/modules/*.php') as $moduleRoutes) {
    require $moduleRoutes;
}
