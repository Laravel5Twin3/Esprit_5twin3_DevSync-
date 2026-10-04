<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\HelpOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpOfferController extends Controller
{
    /**
     * Afficher toutes les offres d'aide.
     */
    public function index()
    {
        $offers = HelpOffer::with('user')
            ->latest()
            ->paginate(12);

        return view(
            'front.entraide.offres.index',
            compact('offers')
        );
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('front.entraide.offres.create');
    }

    /**
     * Enregistrer une nouvelle offre.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'available_from' => [
                'nullable',
                'date',
            ],

            'available_until' => [
                'nullable',
                'date',
                'after_or_equal:available_from',
            ],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';

        HelpOffer::create($validated);

        return redirect()
            ->route('help-offers.index')
            ->with(
                'success',
                'Votre offre d’aide a été publiée avec succès.'
            );
    }

    /**
     * Afficher une offre.
     */
    public function show(HelpOffer $helpOffer)
    {
        $helpOffer->load('user');

        return view(
            'front.entraide.offres.show',
            compact('helpOffer')
        );
    }

    /**
     * Formulaire de modification.
     */
    public function edit(HelpOffer $helpOffer)
    {
        $this->authorizeOwner($helpOffer);

        return view(
            'front.entraide.offres.edit',
            compact('helpOffer')
        );
    }

    /**
     * Modifier une offre.
     */
    public function update(
        Request $request,
        HelpOffer $helpOffer
    ) {
        $this->authorizeOwner($helpOffer);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'available_from' => [
                'nullable',
                'date',
            ],

            'available_until' => [
                'nullable',
                'date',
                'after_or_equal:available_from',
            ],

            'status' => [
                'required',
                'in:active,completed,cancelled',
            ],
        ]);

        $helpOffer->update($validated);

        return redirect()
            ->route('help-offers.show', $helpOffer)
            ->with(
                'success',
                'Votre offre d’aide a été modifiée.'
            );
    }

    /**
     * Supprimer une offre.
     */
    public function destroy(HelpOffer $helpOffer)
    {
        $this->authorizeOwner($helpOffer);

        $helpOffer->delete();

        return redirect()
            ->route('help-offers.index')
            ->with(
                'success',
                'Votre offre d’aide a été supprimée.'
            );
    }

    /**
     * Vérifier que l'utilisateur connecté
     * est propriétaire de l'offre.
     */
    private function authorizeOwner(HelpOffer $helpOffer): void
    {
        abort_unless(
            $helpOffer->user_id === Auth::id(),
            403
        );
    }
}
