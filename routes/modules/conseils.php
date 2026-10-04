<?php

use App\Http\Controllers\Back\CategorieConseilController as BackCategorieConseilController;
use App\Http\Controllers\Back\ConseilController as BackConseilController;
use App\Http\Controllers\Front\ConseilController as FrontConseilController;
use Illuminate\Support\Facades\Route;

// ─── Front Office ──────────────────────────────────────────────────────────
Route::get('/conseils', [FrontConseilController::class, 'index'])
    ->name('conseils.index');

Route::get('/conseils/{conseil}', [FrontConseilController::class, 'show'])
    ->name('conseils.show');

// ─── Back Office (admin) ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::resource('conseils', BackConseilController::class);

    Route::resource('categories-conseil', BackCategorieConseilController::class)
        ->parameters(['categories-conseil' => 'categorieConseil']);
});
