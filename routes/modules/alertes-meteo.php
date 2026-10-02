<?php

use App\Http\Controllers\Back\AlerteMeteoController as BackAlerteMeteoController;
use App\Http\Controllers\Back\NiveauVigilanceController;
use App\Http\Controllers\Front\AlerteMeteoController as FrontAlerteMeteoController;
use Illuminate\Support\Facades\Route;

// ─── Front Office ──────────────────────────────────────────────────────────
Route::get('/alertes-meteo', [FrontAlerteMeteoController::class, 'index'])->name('alertes-meteo.index');
Route::get('/alertes-meteo/{alerte}', [FrontAlerteMeteoController::class, 'show'])->whereNumber('alerte')->name('alertes-meteo.show');

// ─── Back Office (admin) ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // CRUD des niveaux de vigilance : admin.niveaux-vigilance.*
    Route::resource('niveaux-vigilance', NiveauVigilanceController::class)
        ->parameters(['niveaux-vigilance' => 'niveau']);

    // CRUD des alertes météo : admin.alertes-meteo.*
    Route::resource('alertes-meteo', BackAlerteMeteoController::class)
        ->parameters(['alertes-meteo' => 'alerte']);
});
