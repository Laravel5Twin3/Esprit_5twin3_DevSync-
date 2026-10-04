<?php

use App\Http\Controllers\Back\EntraideController;
use App\Http\Controllers\Front\HelpOfferController;
use App\Http\Controllers\Front\HelpRequestController;
use App\Http\Controllers\Front\HelpResponseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Front Office - Entraide entre voisins
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Offres d'aide
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'help-offers',
        HelpOfferController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Demandes d'aide
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'help-requests',
        HelpRequestController::class
    );


    /*
    |--------------------------------------------------------------------------
    | Réponses aux demandes
    |--------------------------------------------------------------------------
    */

    Route::post(
        'help-requests/{helpRequest}/responses',
        [HelpResponseController::class, 'store']
    )->name('help-responses.store');

    Route::patch(
        'help-responses/{helpResponse}/accept',
        [HelpResponseController::class, 'accept']
    )->name('help-responses.accept');

    Route::patch(
        'help-responses/{helpResponse}/reject',
        [HelpResponseController::class, 'reject']
    )->name('help-responses.reject');

    Route::delete(
        'help-responses/{helpResponse}',
        [HelpResponseController::class, 'destroy']
    )->name('help-responses.destroy');
});


/*
|--------------------------------------------------------------------------
| Back Office - Entraide entre voisins
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard Entraide
        |--------------------------------------------------------------------------
        */

        Route::get(
            'entraide',
            [EntraideController::class, 'index']
        )->name('entraide.index');


        /*
        |--------------------------------------------------------------------------
        | Demandes d'aide
        |--------------------------------------------------------------------------
        */

        Route::get(
            'entraide/demandes/{helpRequest}',
            [EntraideController::class, 'showRequest']
        )->name('entraide.requests.show');

        Route::delete(
            'entraide/demandes/{helpRequest}',
            [EntraideController::class, 'destroyRequest']
        )->name('entraide.requests.destroy');


        /*
        |--------------------------------------------------------------------------
        | Offres d'aide
        |--------------------------------------------------------------------------
        */

        Route::get(
            'entraide/offres/{helpOffer}',
            [EntraideController::class, 'showOffer']
        )->name('entraide.offers.show');

        Route::delete(
            'entraide/offres/{helpOffer}',
            [EntraideController::class, 'destroyOffer']
        )->name('entraide.offers.destroy');


        /*
        |--------------------------------------------------------------------------
        | Réponses aux demandes
        |--------------------------------------------------------------------------
        */

        Route::delete(
            'entraide/reponses/{helpResponse}',
            [EntraideController::class, 'destroyResponse']
        )->name('entraide.responses.destroy');

    });
