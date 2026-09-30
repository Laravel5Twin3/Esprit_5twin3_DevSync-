<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\PointFraicheur\StoreCategoriePointRequest;
use App\Http\Requests\PointFraicheur\UpdateCategoriePointRequest;
use App\Models\CategoriePoint;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoriePointController extends Controller
{
    public function index(): View
    {
        $categories = CategoriePoint::withCount('pointsFraicheur')
            ->orderBy('nom')
            ->paginate(10);

        return view('back.categories-point.index', compact('categories'));
    }

    public function create(): View
    {
        return view('back.categories-point.create');
    }

    public function store(StoreCategoriePointRequest $request): RedirectResponse
    {
        CategoriePoint::create($request->validated());

        return redirect()
            ->route('admin.categories-point.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(CategoriePoint $categoriesPoint): View
    {
        return view('back.categories-point.edit', ['categorie' => $categoriesPoint]);
    }

    public function update(UpdateCategoriePointRequest $request, CategoriePoint $categoriesPoint): RedirectResponse
    {
        $categoriesPoint->update($request->validated());

        return redirect()
            ->route('admin.categories-point.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(CategoriePoint $categoriesPoint): RedirectResponse
    {
        $categoriesPoint->delete();

        return redirect()
            ->route('admin.categories-point.index')
            ->with('success', 'Catégorie supprimée.');
    }
}
