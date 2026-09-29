<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\PointFraicheur\StorePointFraicheurRequest;
use App\Http\Requests\PointFraicheur\UpdatePointFraicheurRequest;
use App\Models\CategoriePoint;
use App\Models\PointFraicheur;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PointFraicheurController extends Controller
{
    /**
     * Liste paginée de tous les points de fraîcheur.
     */
    public function index(): View
    {
        $points = PointFraicheur::with('categoriePoint')
            ->orderByDesc('created_at')
            ->paginate(10);

        $stats = [
            'total'  => PointFraicheur::count(),
            'actifs' => PointFraicheur::where('actif', true)->count(),
        ];

        return view('back.points-fraicheur.index', compact('points', 'stats'));
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        $categories = CategoriePoint::orderBy('nom')->get();
        return view('back.points-fraicheur.create', compact('categories'));
    }

    /**
     * Enregistrement d'un nouveau point.
     */
    public function store(StorePointFraicheurRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['actif'] = $request->boolean('actif', true);

        PointFraicheur::create($data);

        return redirect()
            ->route('admin.points-fraicheur.index')
            ->with('success', 'Point de fraîcheur créé avec succès.');
    }

    /**
     * Détail d'un point.
     */
    public function show(PointFraicheur $pointsFraicheur): View
    {
        $pointsFraicheur->load('categoriePoint');
        return view('back.points-fraicheur.show', ['point' => $pointsFraicheur]);
    }

    /**
     * Formulaire de modification (pré-rempli).
     */
    public function edit(PointFraicheur $pointsFraicheur): View
    {
        $categories = CategoriePoint::orderBy('nom')->get();
        return view('back.points-fraicheur.edit', [
            'point'      => $pointsFraicheur,
            'categories' => $categories,
        ]);
    }

    /**
     * Mise à jour d'un point.
     */
    public function update(UpdatePointFraicheurRequest $request, PointFraicheur $pointsFraicheur): RedirectResponse
    {
        $data = $request->validated();
        $data['actif'] = $request->boolean('actif', false);

        $pointsFraicheur->update($data);

        return redirect()
            ->route('admin.points-fraicheur.index')
            ->with('success', 'Point de fraîcheur mis à jour avec succès.');
    }

    /**
     * Suppression avec confirmation.
     */
    public function destroy(PointFraicheur $pointsFraicheur): RedirectResponse
    {
        $pointsFraicheur->delete();

        return redirect()
            ->route('admin.points-fraicheur.index')
            ->with('success', 'Point de fraîcheur supprimé.');
    }
}
