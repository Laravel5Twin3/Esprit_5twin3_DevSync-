<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\HelpRequest;
use App\Models\HelpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpResponseController extends Controller
{
    /**
     * Répondre à une demande d'aide.
     */
    public function store(
        Request $request,
        HelpRequest $helpRequest
    ) {
        /*
        |--------------------------------------------------------------------------
        | Vérifier que la demande est encore ouverte
        |--------------------------------------------------------------------------
        */

        if ($helpRequest->status !== 'open') {
            return back()->with(
                'error',
                'Cette demande n’est plus ouverte.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Le propriétaire ne peut pas répondre à sa propre demande
        |--------------------------------------------------------------------------
        */

        if ($helpRequest->user_id === Auth::id()) {
            return back()->with(
                'error',
                'Vous ne pouvez pas répondre à votre propre demande.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Vérifier que l'utilisateur n'a pas déjà répondu
        |--------------------------------------------------------------------------
        */

        $alreadyResponded = HelpResponse::where(
            'help_request_id',
            $helpRequest->id
        )
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyResponded) {
            return back()->with(
                'error',
                'Vous avez déjà proposé votre aide pour cette demande.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'message' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Création de la réponse
        |--------------------------------------------------------------------------
        */

        HelpResponse::create([
            'help_request_id' => $helpRequest->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with(
            'success',
            'Votre proposition d’aide a été envoyée.'
        );
    }

    /**
     * Accepter une réponse.
     */
    public function accept(HelpResponse $helpResponse)
    {
        /*
        |--------------------------------------------------------------------------
        | Seul le propriétaire de la demande peut accepter
        |--------------------------------------------------------------------------
        */

        $this->authorizeRequestOwner($helpResponse);

        /*
        |--------------------------------------------------------------------------
        | Vérifier que la demande est toujours ouverte/en cours
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $helpResponse->request->status,
                ['open', 'in_progress']
            )
        ) {
            return back()->with(
                'error',
                'Cette demande n’est plus disponible.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Accepter la réponse
        |--------------------------------------------------------------------------
        */

        $helpResponse->update([
            'status' => 'accepted',
        ]);

        /*
        |--------------------------------------------------------------------------
        | La demande passe en cours
        |--------------------------------------------------------------------------
        */

        $helpResponse->request->update([
            'status' => 'in_progress',
        ]);

        return back()->with(
            'success',
            'La proposition d’aide a été acceptée.'
        );
    }

    /**
     * Refuser une réponse.
     */
    public function reject(HelpResponse $helpResponse)
    {
        $this->authorizeRequestOwner($helpResponse);

        /*
        |--------------------------------------------------------------------------
        | Une réponse déjà terminée ne peut pas être refusée
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $helpResponse->status,
                ['pending']
            )
        ) {
            return back()->with(
                'error',
                'Cette réponse ne peut plus être refusée.'
            );
        }

        $helpResponse->update([
            'status' => 'rejected',
        ]);

        return back()->with(
            'success',
            'La proposition d’aide a été refusée.'
        );
    }

    /**
     * Supprimer sa propre réponse.
     */
    public function destroy(HelpResponse $helpResponse)
    {
        abort_unless(
            $helpResponse->user_id === Auth::id(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | On peut supprimer uniquement une réponse en attente
        |--------------------------------------------------------------------------
        */

        if ($helpResponse->status !== 'pending') {
            return back()->with(
                'error',
                'Cette réponse ne peut plus être supprimée.'
            );
        }

        $helpResponse->delete();

        return back()->with(
            'success',
            'Votre proposition d’aide a été supprimée.'
        );
    }

    /**
     * Vérifier que l'utilisateur connecté
     * est propriétaire de la demande.
     */
    private function authorizeRequestOwner(
        HelpResponse $helpResponse
    ): void {
        $helpResponse->loadMissing('request');

        abort_unless(
            $helpResponse->request->user_id === Auth::id(),
            403
        );
    }
}
