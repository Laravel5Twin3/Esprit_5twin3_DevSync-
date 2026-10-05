<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CategorieConseil;
use App\Models\Conseil;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConseilController extends Controller
{
    public function index(Request $request): View
    {
        $categories = CategorieConseil::withCount(['conseils' => fn ($q) => $q->where('actif', true)])
            ->orderBy('nom')
            ->get();

        $query = Conseil::with('categorieConseil')->actifs();

        if ($request->filled('categorie')) {
            $query->where('categorie_conseil_id', $request->integer('categorie'));
        }

        if ($request->filled('public')) {
            $query->where('public_cible', $request->input('public'));
        }

        $conseils = $query
            ->orderByRaw("CASE priorite WHEN 'urgent' THEN 1 WHEN 'important' THEN 2 ELSE 3 END")
            ->orderBy('titre')
            ->paginate(9)
            ->withQueryString();

        return view('front.conseils.index', compact('categories', 'conseils'));
    }

    public function show(Conseil $conseil): View
    {
        abort_unless($conseil->actif, 404);
        $conseil->load('categorieConseil');

        $similaires = Conseil::with('categorieConseil')
            ->actifs()
            ->where('categorie_conseil_id', $conseil->categorie_conseil_id)
            ->where('id', '!=', $conseil->id)
            ->orderBy('titre')
            ->take(4)
            ->get();

        return view('front.conseils.show', compact('conseil', 'similaires'));
    }
}
