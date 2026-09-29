<?php

use App\Http\Controllers\Back\CategoriePointController as BackCategoriePointController;
use App\Http\Controllers\Back\PointFraicheurController as BackPointFraicheurController;
use App\Http\Controllers\Front\PointFraicheurController as FrontPointFraicheurController;
use Illuminate\Support\Facades\Route;

// ─── Front Office ──────────────────────────────────────────────────────────
Route::get('/points-fraicheur', [FrontPointFraicheurController::class, 'index'])
    ->name('points-fraicheur.index');

Route::get('/points-fraicheur/{point}', [FrontPointFraicheurController::class, 'show'])
    ->name('points-fraicheur.show');

// API de recommandation IA (appelée en AJAX depuis le front)
Route::get('/api/points-fraicheur/recommander', [FrontPointFraicheurController::class, 'recommander'])
    ->name('points-fraicheur.recommander');

// ─── Back Office (admin) ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // CRUD complet des points de fraîcheur
    Route::resource('points-fraicheur', BackPointFraicheurController::class);

    // CRUD complet des catégories
    Route::resource('categories-point', BackCategoriePointController::class)
        ->except(['show']);
});
