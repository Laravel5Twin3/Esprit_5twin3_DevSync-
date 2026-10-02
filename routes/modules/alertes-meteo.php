<?php

use App\Http\Controllers\Back\AlerteMeteoController as BackAlerteMeteoController;
use App\Http\Controllers\Back\NiveauVigilanceController;
use Illuminate\Support\Facades\Route;

// ─── Back Office (admin) ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // CRUD des niveaux de vigilance : admin.niveaux-vigilance.*
    Route::resource('niveaux-vigilance', NiveauVigilanceController::class)
        ->parameters(['niveaux-vigilance' => 'niveau']);

    // CRUD des alertes météo : admin.alertes-meteo.*
    Route::resource('alertes-meteo', BackAlerteMeteoController::class)
        ->parameters(['alertes-meteo' => 'alerte']);
});
