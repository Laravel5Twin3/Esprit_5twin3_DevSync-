<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Conseil\StoreConseilRequest;
use App\Http\Requests\Conseil\UpdateConseilRequest;
use App\Models\CategorieConseil;
use App\Models\Conseil;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConseilController extends Controller
{
    public function index(): View
    {
        $conseils = Conseil::with('categorieConseil')
            ->orderByDesc('created_at')
            ->paginate(10);

        $stats = [
            'total'  => Conseil::count(),
            'actifs' => Conseil::where('actif', true)->count(),
        ];

        return view('back.conseils.index', compact('conseils', 'stats'));
    }

    public function create(): View
    {
        $categories = CategorieConseil::orderBy('nom')->get();

        return view('back.conseils.create', [
            'categories' => $categories,
            'conseil'    => new Conseil(),
        ]);
    }

    public function store(StoreConseilRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['actif'] = $request->boolean('actif', true);

        Conseil::create($data);

        return redirect()
            ->route('admin.conseils.index')
            ->with('success', 'Conseil créé avec succès.');
    }

    public function show(Conseil $conseil): View
    {
        $conseil->load('categorieConseil');

        return view('back.conseils.show', compact('conseil'));
    }

    public function edit(Conseil $conseil): View
    {
        $categories = CategorieConseil::orderBy('nom')->get();

        return view('back.conseils.edit', compact('conseil', 'categories'));
    }

    public function update(UpdateConseilRequest $request, Conseil $conseil): RedirectResponse
    {
        $data = $request->validated();
        $data['actif'] = $request->boolean('actif', false);

        $conseil->update($data);

        return redirect()
            ->route('admin.conseils.index')
            ->with('success', 'Conseil mis à jour avec succès.');
    }

    public function destroy(Conseil $conseil): RedirectResponse
    {
        $conseil->delete();

        return redirect()
            ->route('admin.conseils.index')
            ->with('success', 'Conseil supprimé.');
    }
}
