<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlerteMeteo\NiveauVigilanceRequest;
use App\Models\NiveauVigilance;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NiveauVigilanceController extends Controller
{
    public function index(): View
    {
        $niveaux = NiveauVigilance::query()
            ->withCount([
                'alertes',
                'alertes as alertes_actives_count' => fn ($q) => $q->actives(),
            ])
            ->orderBy('ordre')
            ->get();

        return view('back.niveaux-vigilance.index', compact('niveaux'));
    }

    public function create(): View
    {
        return view('back.niveaux-vigilance.create', [
            'niveau' => new NiveauVigilance([
                'couleur' => '#fd7e14',
                'ordre' => NiveauVigilance::max('ordre') + 1,
            ]),
        ]);
    }

    public function store(NiveauVigilanceRequest $request): RedirectResponse
    {
        $niveau = NiveauVigilance::create($request->validated());

        return redirect()
            ->route('admin.niveaux-vigilance.show', $niveau)
            ->with('success', "Le niveau « {$niveau->nom} » a été créé.");
    }

    public function show(NiveauVigilance $niveau): View
    {
        // Les alertes de ce niveau (exploitation de la relation hasMany)
        $alertes = $niveau->alertes()->with('zone')->latest('date_debut')->paginate(8);

        return view('back.niveaux-vigilance.show', compact('niveau', 'alertes'));
    }

    public function edit(NiveauVigilance $niveau): View
    {
        return view('back.niveaux-vigilance.edit', compact('niveau'));
    }

    public function update(NiveauVigilanceRequest $request, NiveauVigilance $niveau): RedirectResponse
    {
        $niveau->update($request->validated());

        return redirect()
            ->route('admin.niveaux-vigilance.show', $niveau)
            ->with('success', "Le niveau « {$niveau->nom} » a été mis à jour.");
    }

    public function destroy(NiveauVigilance $niveau): RedirectResponse
    {
        if ($niveau->alertes()->exists()) {
            return back()->with('error', "Impossible de supprimer « {$niveau->nom} » : des alertes l'utilisent encore.");
        }

        $niveau->delete();

        return redirect()
            ->route('admin.niveaux-vigilance.index')
            ->with('success', 'Niveau de vigilance supprimé.');
    }
}
