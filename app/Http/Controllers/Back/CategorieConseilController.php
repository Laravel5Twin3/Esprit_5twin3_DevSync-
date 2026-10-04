<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conseil\StoreCategorieConseilRequest;
use App\Http\Requests\Conseil\UpdateCategorieConseilRequest;
use App\Models\CategorieConseil;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategorieConseilController extends Controller
{
    public function index(): View
    {
        $categories = CategorieConseil::withCount('conseils')
            ->orderBy('nom')
            ->paginate(10);

        return view('back.categories-conseil.index', compact('categories'));
    }

    public function create(): View
    {
        return view('back.categories-conseil.create');
    }

    public function store(StoreCategorieConseilRequest $request): RedirectResponse
    {
        CategorieConseil::create($request->validated());

        return redirect()
            ->route('admin.categories-conseil.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function show(CategorieConseil $categorieConseil): View
    {
        $categorieConseil->load(['conseils' => fn ($q) => $q->orderBy('titre')]);

        return view('back.categories-conseil.show', ['categorie' => $categorieConseil]);
    }

    public function edit(CategorieConseil $categorieConseil): View
    {
        return view('back.categories-conseil.edit', ['categorie' => $categorieConseil]);
    }

    public function update(UpdateCategorieConseilRequest $request, CategorieConseil $categorieConseil): RedirectResponse
    {
        $categorieConseil->update($request->validated());

        return redirect()
            ->route('admin.categories-conseil.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(CategorieConseil $categorieConseil): RedirectResponse
    {
        $categorieConseil->delete();

        return redirect()
            ->route('admin.categories-conseil.index')
            ->with('success', 'Catégorie supprimée.');
    }
}
