<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupure\ZoneRequest;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneController extends Controller
{
    public function index(Request $request): View
    {
        $zones = Zone::query()
            ->withCount([
                'coupures',
                'coupures as coupures_actives_count' => fn ($q) => $q->actives(),
            ])
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('nom', 'like', '%'.$request->q.'%')
                    ->orWhere('code_postal', 'like', $request->q.'%');
            }))
            ->when($request->filled('gouvernorat'), fn ($q) => $q->where('gouvernorat', $request->gouvernorat))
            ->when($request->filled('niveau_risque'), fn ($q) => $q->where('niveau_risque', $request->niveau_risque))
            ->orderBy('nom')
            ->paginate(10)
            ->withQueryString();

        return view('back.zones.index', compact('zones'));
    }

    public function create(): View
    {
        return view('back.zones.create', ['zone' => new Zone(['niveau_risque' => 'moyen'])]);
    }

    public function store(ZoneRequest $request): RedirectResponse
    {
        $zone = Zone::create($request->validated());

        return redirect()
            ->route('admin.zones.show', $zone)
            ->with('success', "La zone « {$zone->nom} » a été créée.");
    }

    public function show(Zone $zone): View
    {
        $coupures = $zone->coupures()->latest('date_debut')->paginate(8);

        return view('back.zones.show', compact('zone', 'coupures'));
    }

    public function edit(Zone $zone): View
    {
        return view('back.zones.edit', compact('zone'));
    }

    public function update(ZoneRequest $request, Zone $zone): RedirectResponse
    {
        $zone->update($request->validated());

        return redirect()
            ->route('admin.zones.show', $zone)
            ->with('success', "La zone « {$zone->nom} » a été mise à jour.");
    }

    public function destroy(Zone $zone): RedirectResponse
    {
        if ($zone->coupures()->exists()) {
            return back()->with('error', "Impossible de supprimer « {$zone->nom} » : elle possède encore des coupures. Supprimez-les d'abord.");
        }

        $zone->delete();

        return redirect()
            ->route('admin.zones.index')
            ->with('success', 'Zone supprimée.');
    }
}
