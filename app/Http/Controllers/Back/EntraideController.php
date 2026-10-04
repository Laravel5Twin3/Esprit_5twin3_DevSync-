<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\HelpOffer;
use App\Models\HelpRequest;
use App\Models\HelpResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EntraideController extends Controller
{
    /**
     * Dashboard du module entraide.
     */
    public function index(): View
    {
        /*
        |--------------------------------------------------------------------------
        | Statistiques
        |--------------------------------------------------------------------------
        */

        $offersCount = HelpOffer::count();

        $activeOffersCount = HelpOffer::where(
            'status',
            'active'
        )->count();

        $requestsCount = HelpRequest::count();

        $openRequestsCount = HelpRequest::where(
            'status',
            'open'
        )->count();

        $responsesCount = HelpResponse::count();

        $pendingResponsesCount = HelpResponse::where(
            'status',
            'pending'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Dernières offres
        |--------------------------------------------------------------------------
        */

        $latestOffers = HelpOffer::with('user')
            ->latest()
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Dernières demandes
        |--------------------------------------------------------------------------
        */

        $latestRequests = HelpRequest::with('user')
            ->withCount('responses')
            ->latest()
            ->take(5)
            ->get();


        return view(
            'back.entraide.index',
            compact(
                'offersCount',
                'activeOffersCount',
                'requestsCount',
                'openRequestsCount',
                'responsesCount',
                'pendingResponsesCount',
                'latestOffers',
                'latestRequests'
            )
        );
    }


    /**
     * Afficher le détail d'une demande.
     */
    public function showRequest(
        HelpRequest $helpRequest
    ): View {

        $helpRequest->load([
            'user',
            'responses.user',
        ]);

        return view(
            'back.entraide.demandes.show',
            compact('helpRequest')
        );
    }


    /**
     * Afficher le détail d'une offre.
     */
    public function showOffer(
        HelpOffer $helpOffer
    ): View {

        $helpOffer->load('user');

        return view(
            'back.entraide.offres.show',
            compact('helpOffer')
        );
    }


    /**
     * Supprimer une demande.
     */
    public function destroyRequest(
        HelpRequest $helpRequest
    ): RedirectResponse {

        $helpRequest->delete();

        return redirect()
            ->route('admin.entraide.index')
            ->with(
                'success',
                'La demande d’aide a été supprimée avec succès.'
            );
    }


    /**
     * Supprimer une offre.
     */
    public function destroyOffer(
        HelpOffer $helpOffer
    ): RedirectResponse {

        $helpOffer->delete();

        return redirect()
            ->route('admin.entraide.index')
            ->with(
                'success',
                'L’offre d’aide a été supprimée avec succès.'
            );
    }


    /**
     * Supprimer une réponse.
     */
    public function destroyResponse(
        HelpResponse $helpResponse
    ): RedirectResponse {

        $helpResponse->delete();

        return redirect()
            ->back()
            ->with(
                'success',
                'La réponse a été supprimée avec succès.'
            );
    }
}
