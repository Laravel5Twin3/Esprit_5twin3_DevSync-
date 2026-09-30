<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupure\CoupureRequest;
use App\Models\Coupure;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoupureController extends Controller
{
    public function index(Request $request): View
    {
        $coupures = Coupure::query()
            ->with('zone')
            ->when($request->filled('q'), fn ($q) => $q->where('titre', 'like', '%'.$request->q.'%'))
            ->when($request->filled('zone_id'), fn ($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->latest('date_debut')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Coupure::count(),
            'en_cours' => Coupure::where('statut', 'en_cours')->count(),
            'prevues' => Coupure::where('statut', 'prevue')->count(),
            'foyers' => (int) Coupure::where('statut', 'en_cours')->sum('foyers_touches'),
        ];

        return view('back.coupures.index', [
            'coupures' => $coupures,
            'stats' => $stats,
            'zones' => Zone::orderBy('nom')->pluck('nom', 'id'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('back.coupures.create', [
            // ?zone_id=X permet de pré-remplir la zone depuis la fiche d'une zone
            'coupure' => new Coupure(['statut' => 'prevue', 'zone_id' => $request->integer('zone_id') ?: null]),
            'zones' => Zone::orderBy('nom')->pluck('nom', 'id'),
        ]);
    }

    public function store(CoupureRequest $request): RedirectResponse
    {
        $coupure = Coupure::create($request->validated());

        return redirect()
            ->route('admin.coupures.show', $coupure)
            ->with('success', 'La coupure a été enregistrée.');
    }

    public function show(Coupure $coupure): View
    {
        $coupure->load(['zone', 'signalements.user']);

        return view('back.coupures.show', compact('coupure'));
    }

    public function edit(Coupure $coupure): View
    {
        return view('back.coupures.edit', [
            'coupure' => $coupure,
            'zones' => Zone::orderBy('nom')->pluck('nom', 'id'),
        ]);
    }

    public function update(CoupureRequest $request, Coupure $coupure): RedirectResponse
    {
        $coupure->update($request->validated());

        return redirect()
            ->route('admin.coupures.show', $coupure)
            ->with('success', 'La coupure a été mise à jour.');
    }

    public function destroy(Coupure $coupure): RedirectResponse
    {
        $coupure->delete();

        return redirect()
            ->route('admin.coupures.index')
            ->with('success', 'Coupure supprimée.');
    }
}
