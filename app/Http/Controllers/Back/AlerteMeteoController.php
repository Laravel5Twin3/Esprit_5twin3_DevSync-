<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlerteMeteo\AlerteMeteoRequest;
use App\Models\AlerteMeteo;
use App\Models\NiveauVigilance;
use App\Models\Zone;
use App\Services\AlerteMeteo\PrevisionChaleurService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlerteMeteoController extends Controller
{
    public function index(Request $request): View
    {
        $alertes = AlerteMeteo::query()
            ->with(['niveauVigilance', 'zone']) // eager loading : évite le problème N+1
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('titre', 'like', '%'.$request->q.'%')
                    ->orWhere('message', 'like', '%'.$request->q.'%');
            }))
            ->when($request->filled('zone_id'), fn ($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->filled('niveau_id'), fn ($q) => $q->where('niveau_vigilance_id', $request->niveau_id))
            ->statut($request->input('statut'))
            ->latest('date_debut')
            ->paginate(10)
            ->withQueryString();

        return view('back.alertes-meteo.index', [
            'alertes' => $alertes,
            'zones' => Zone::orderBy('nom')->pluck('nom', 'id'),
            'niveaux' => NiveauVigilance::orderBy('ordre')->pluck('nom', 'id'),
        ]);
    }

    public function create(Request $request): View
    {
        $alerte = new AlerteMeteo([
            'zone_id' => $request->integer('zone_id') ?: null,
            'niveau_vigilance_id' => $request->integer('niveau_vigilance_id') ?: null,
            'date_debut' => now()->startOfHour(),
            'date_fin' => now()->startOfHour()->addDays(2),
        ]);

        return view('back.alertes-meteo.create', $this->donneesFormulaire($alerte));
    }

    /**
     * Valeur ajoutée : pré-remplit le formulaire de création à partir des
     * prévisions météo réelles de la zone (jour le plus chaud des 7 prochains jours).
     */
    public function generer(Request $request, PrevisionChaleurService $meteo): View|RedirectResponse
    {
        $request->validate(
            ['zone_id' => ['required', 'exists:zones,id']],
            ['zone_id.required' => 'Choisissez une zone pour générer l\'alerte.', 'zone_id.exists' => 'Zone invalide.'],
        );
        $zone = Zone::findOrFail($request->zone_id);

        try {
            $previsions = $meteo->previsions($zone);
        } catch (\Throwable) {
            return back()->with('error', 'Impossible de récupérer les prévisions météo pour le moment. Réessayez plus tard.');
        }

        $alerte = new AlerteMeteo($meteo->suggestion($zone, $previsions));

        return view('back.alertes-meteo.create', $this->donneesFormulaire($alerte) + [
            'previsions' => $previsions,
            'zoneGeneree' => $zone,
        ]);
    }

    public function store(AlerteMeteoRequest $request): RedirectResponse
    {
        $alerte = AlerteMeteo::create($this->avecNiveau($request->validated()));

        return redirect()
            ->route('admin.alertes-meteo.show', $alerte)
            ->with('success', "L'alerte « {$alerte->titre} » a été publiée (niveau {$alerte->niveauVigilance->nom}).");
    }

    public function show(AlerteMeteo $alerte): View
    {
        $alerte->load(['niveauVigilance', 'zone']);

        // Autres alertes de la même zone
        $autresAlertes = AlerteMeteo::with('niveauVigilance')
            ->where('zone_id', $alerte->zone_id)
            ->whereKeyNot($alerte->id)
            ->latest('date_debut')
            ->limit(5)
            ->get();

        return view('back.alertes-meteo.show', compact('alerte', 'autresAlertes'));
    }

    public function edit(AlerteMeteo $alerte): View
    {
        return view('back.alertes-meteo.edit', $this->donneesFormulaire($alerte));
    }

    public function update(AlerteMeteoRequest $request, AlerteMeteo $alerte): RedirectResponse
    {
        $alerte->update($this->avecNiveau($request->validated()));

        return redirect()
            ->route('admin.alertes-meteo.show', $alerte)
            ->with('success', "L'alerte « {$alerte->titre} » a été mise à jour.");
    }

    public function destroy(AlerteMeteo $alerte): RedirectResponse
    {
        $alerte->delete();

        return redirect()
            ->route('admin.alertes-meteo.index')
            ->with('success', 'Alerte supprimée.');
    }

    /** Listes déroulantes du formulaire (relations). */
    private function donneesFormulaire(AlerteMeteo $alerte): array
    {
        return [
            'alerte' => $alerte,
            'zones' => Zone::orderBy('nom')->pluck('nom', 'id'),
            'niveaux' => NiveauVigilance::orderBy('ordre')->get(),
        ];
    }

    /** Niveau automatique : si aucun niveau n'est choisi, on le déduit de la température. */
    private function avecNiveau(array $donnees): array
    {
        if (empty($donnees['niveau_vigilance_id'])) {
            $donnees['niveau_vigilance_id'] = NiveauVigilance::pourTemperature($donnees['temperature_max'])?->id;
        }

        return $donnees;
    }
}
