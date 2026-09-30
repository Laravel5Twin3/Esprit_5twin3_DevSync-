<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Coupure;
use App\Models\Zone;
use App\Services\Coupure\PredictionCoupureService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoupureController extends Controller
{
    public function index(Request $request): View
    {
        $periode = $request->input('periode', 'actives'); // actives | historique

        $coupures = Coupure::query()
            ->with('zone')
            ->when($request->filled('zone_id'), fn ($q) => $q->where('zone_id', $request->zone_id))
            ->when(
                $periode === 'historique',
                fn ($q) => $q->where('statut', 'resolue')->latest('date_debut'),
                // en cours d'abord, puis les prévues par date
                fn ($q) => $q->actives()->orderByRaw("statut = 'en_cours' desc")->orderBy('date_debut'),
            )
            ->paginate(9)
            ->withQueryString();

        $zones = Zone::withCount(['coupures as coupures_actives_count' => fn ($q) => $q->actives()])
            ->orderBy('nom')
            ->get();

        return view('front.coupures.index', [
            'coupures' => $coupures,
            'zones' => $zones,
            'periode' => $periode,
            'enCoursCount' => Coupure::where('statut', 'en_cours')->count(),
        ]);
    }

    /**
     * Prévision IA du risque de coupure par zone pour les prochains jours.
     */
    public function previsions(Request $request, PredictionCoupureService $prediction): View
    {
        // Paramètres GET optionnels : une date précise et/ou une température simulée
        $donnees = $request->validate([
            'date' => ['nullable', 'date', 'after_or_equal:today', 'before_or_equal:'.today()->addYear()->toDateString()],
            'temperature' => ['nullable', 'numeric', 'between:15,50'],
        ], [
            'date.after_or_equal' => 'Choisissez une date à partir d\'aujourd\'hui.',
            'date.before_or_equal' => 'La prédiction est possible jusqu\'à un an à l\'avance.',
            'temperature.between' => 'La température simulée doit être comprise entre 15 et 50 °C.',
        ]);
        $date = isset($donnees['date']) ? Carbon::parse($donnees['date']) : null;
        $temperature = isset($donnees['temperature']) ? (float) $donnees['temperature'] : null;

        return view('front.coupures.previsions', [
            'previsions' => $prediction->previsions(),
            'sourceMeteo' => $prediction->sourceMeteo(),
            'date' => $date,
            'predictionDate' => $date ? $prediction->previsionPourDate($date) : null,
            'temperature' => $temperature,
            'simulation' => $temperature !== null ? $prediction->simuler($temperature) : null,
        ]);
    }

    public function show(Coupure $coupure): View
    {
        $coupure->load('zone');

        $autresCoupures = $coupure->zone->coupures()
            ->actives()
            ->whereKeyNot($coupure->id)
            ->orderBy('date_debut')
            ->limit(3)
            ->get();

        return view('front.coupures.show', compact('coupure', 'autresCoupures'));
    }
}
