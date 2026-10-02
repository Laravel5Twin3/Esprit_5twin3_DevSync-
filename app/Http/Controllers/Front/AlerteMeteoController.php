<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\AlerteMeteo;
use App\Models\NiveauVigilance;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlerteMeteoController extends Controller
{
    /**
     * Alertes en cours et à venir (ou historique), filtrables par zone et par niveau.
     */
    public function index(Request $request): View
    {
        $periode = $request->input('periode', 'actives'); // actives | historique

        $alertes = AlerteMeteo::query()
            ->with(['niveauVigilance', 'zone'])
            ->when($request->filled('zone_id'), fn ($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->filled('niveau_id'), fn ($q) => $q->where('niveau_vigilance_id', $request->niveau_id))
            ->when(
                $periode === 'historique',
                fn ($q) => $q->terminees()->latest('date_fin'),
                fn ($q) => $q->actives()->orderBy('date_debut'),
            )
            ->paginate(9)
            ->withQueryString();

        // Alerte la plus grave en cours : affichée en bandeau
        $alertePrincipale = AlerteMeteo::enCours()
            ->with(['niveauVigilance', 'zone'])
            ->join('niveau_vigilances', 'niveau_vigilances.id', '=', 'alerte_meteos.niveau_vigilance_id')
            ->orderByDesc('niveau_vigilances.ordre')
            ->orderByDesc('alerte_meteos.temperature_max')
            ->select('alerte_meteos.*')
            ->first();

        return view('front.alertes-meteo.index', [
            'alertes' => $alertes,
            'periode' => $periode,
            'alertePrincipale' => $alertePrincipale,
            'zones' => Zone::orderBy('nom')->get(['id', 'nom']),
            // Nombre d'alertes actives par zone : [zone_id => nombre]
            'actifsParZone' => AlerteMeteo::actives()->selectRaw('zone_id, count(*) as total')
                ->groupBy('zone_id')->pluck('total', 'zone_id'),
            'niveaux' => NiveauVigilance::orderBy('ordre')->get(),
        ]);
    }

    public function show(AlerteMeteo $alerte): View
    {
        $alerte->load(['niveauVigilance', 'zone']);

        $autresAlertes = AlerteMeteo::with('niveauVigilance')
            ->actives()
            ->where('zone_id', $alerte->zone_id)
            ->whereKeyNot($alerte->id)
            ->orderBy('date_debut')
            ->limit(3)
            ->get();

        return view('front.alertes-meteo.show', compact('alerte', 'autresAlertes'));
    }
}
