<?php

use App\Http\Controllers\Back\AlerteMeteoController as BackAlerteMeteoController;
use App\Http\Controllers\Back\NiveauVigilanceController;
use App\Http\Controllers\Back\StatistiquesAlerteMeteoController;
use App\Http\Controllers\Front\AlerteMeteoController as FrontAlerteMeteoController;
use App\Http\Controllers\Front\AssistantCaniculeController;
use Illuminate\Support\Facades\Route;

// ─── Front Office ──────────────────────────────────────────────────────────
Route::get('/alertes-meteo', [FrontAlerteMeteoController::class, 'index'])->name('alertes-meteo.index');
Route::get('/alertes-meteo/previsions', [FrontAlerteMeteoController::class, 'previsions'])->name('alertes-meteo.previsions');

// Assistant canicule (chatbot) : 20 questions par minute maximum
Route::post('/alertes-meteo/assistant', [AssistantCaniculeController::class, 'repondre'])
    ->middleware('throttle:20,1')->name('alertes-meteo.assistant');
Route::get('/alertes-meteo/{alerte}', [FrontAlerteMeteoController::class, 'show'])->whereNumber('alerte')->name('alertes-meteo.show');
Route::get('/alertes-meteo/{alerte}/fiche', [FrontAlerteMeteoController::class, 'fiche'])->whereNumber('alerte')->name('alertes-meteo.fiche');

// ─── Back Office (admin) ───────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {
    // CRUD des niveaux de vigilance : admin.niveaux-vigilance.*
    Route::resource('niveaux-vigilance', NiveauVigilanceController::class)
        ->parameters(['niveaux-vigilance' => 'niveau']);

    // Statistiques avancées par zone (avant le resource !)
    Route::get('alertes-meteo/statistiques', [StatistiquesAlerteMeteoController::class, 'index'])->name('alertes-meteo.statistiques');

    // Génération d'une alerte depuis les prévisions Open-Meteo (avant le resource !)
    Route::get('alertes-meteo/generer', [BackAlerteMeteoController::class, 'generer'])->name('alertes-meteo.generer');

    // CRUD des alertes météo : admin.alertes-meteo.*
    Route::resource('alertes-meteo', BackAlerteMeteoController::class)
        ->parameters(['alertes-meteo' => 'alerte']);
});
