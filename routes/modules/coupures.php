<?php

use App\Http\Controllers\Back\CoupureController as BackCoupureController;
use App\Http\Controllers\Back\PredictionCoupureController;
use App\Http\Controllers\Back\SignalementController as BackSignalementController;
use App\Http\Controllers\Back\ZoneController as BackZoneController;
use App\Http\Controllers\Front\CoupureController as FrontCoupureController;
use App\Http\Controllers\Front\SignalementController as FrontSignalementController;
use Illuminate\Support\Facades\Route;

// ─── Front Office ──────────────────────────────────────────────────────────
Route::get('/coupures', [FrontCoupureController::class, 'index'])->name('coupures.index');
Route::get('/coupures/previsions', [FrontCoupureController::class, 'previsions'])->name('coupures.previsions');
Route::get('/coupures/{coupure}', [FrontCoupureController::class, 'show'])->whereNumber('coupure')->name('coupures.show');

// Signalements des habitants (connexion obligatoire)
Route::middleware('auth')->group(function () {
    Route::get('/coupures/signaler', [FrontSignalementController::class, 'create'])->name('signalements.create');
    Route::post('/coupures/signaler', [FrontSignalementController::class, 'store'])->name('signalements.store');
    Route::get('/mes-signalements', [FrontSignalementController::class, 'index'])->name('signalements.index');
});

// ─── Back Office (admin) ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Prédiction des coupures
    Route::get('coupures-ia', [PredictionCoupureController::class, 'index'])->name('coupures-ia.index');
    Route::post('coupures-ia/entrainer', [PredictionCoupureController::class, 'entrainer'])->name('coupures-ia.entrainer');

    // Signalements des habitants : admin.signalements.*
    Route::get('signalements', [BackSignalementController::class, 'index'])->name('signalements.index');
    Route::post('signalements/{signalement}/valider', [BackSignalementController::class, 'valider'])->name('signalements.valider');
    Route::post('signalements/{signalement}/rejeter', [BackSignalementController::class, 'rejeter'])->name('signalements.rejeter');
    Route::delete('signalements/{signalement}', [BackSignalementController::class, 'destroy'])->name('signalements.destroy');

    // CRUD complet des coupures : admin.coupures.*
    Route::resource('coupures', BackCoupureController::class);

    // CRUD complet des zones : admin.zones.*
    Route::resource('zones', BackZoneController::class);
});
