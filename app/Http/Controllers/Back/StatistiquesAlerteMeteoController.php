<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\AlerteMeteo;
use App\Models\NiveauVigilance;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Statistiques avancées des alertes météo, zone par zone.
 */
class StatistiquesAlerteMeteoController extends Controller
{
    /** Périodes proposées dans le filtre : clé => [libellé, nombre de mois (null = tout)]. */
    public const PERIODES = [
        '3' => ['3 derniers mois', 3],
        '12' => ['12 derniers mois', 12],
        'tout' => ['Toute la période', null],
    ];

    public function index(Request $request): View
    {
        // Période choisie : on n'accepte qu'une des clés prévues (sinon 12 mois par défaut)
        $periode = $request->query('periode');
        if (! is_string($periode) || ! array_key_exists($periode, self::PERIODES)) {
            $periode = '12';
        }
        $mois = self::PERIODES[$periode][1];
        $depuis = $mois ? now()->subMonths($mois)->startOfMonth() : null;

        $niveaux = NiveauVigilance::orderBy('ordre')->get();

        // Toutes les alertes de la période, avec leur niveau (eager loading)
        $alertes = AlerteMeteo::with('niveauVigilance')
            ->when($depuis, fn ($q) => $q->where('date_fin', '>=', $depuis))
            ->get();

        $zones = Zone::orderBy('nom')->get()->map(fn (Zone $zone) => $this->statistiquesZone(
            $zone, $alertes->where('zone_id', $zone->id)
        ));

        // Classement : les zones les plus exposées d'abord
        $classement = $zones->sortByDesc(fn ($z) => [$z['score'], $z['total']])->values();

        return view('back.alertes-meteo.statistiques', [
            'periode' => $periode,
            'niveaux' => $niveaux,
            'classement' => $classement,
            'totaux' => [
                'alertes' => $alertes->count(),
                'jours' => $classement->sum('jours'),
                'record' => $alertes->sortByDesc('temperature_max')->first(),
                'zones' => $classement->where('total', '>', 0)->count(),
            ],
            'parNiveau' => $niveaux->map(fn ($n) => [
                'nom' => $n->nom,
                'couleur' => $n->couleur,
                'total' => $alertes->where('niveau_vigilance_id', $n->id)->count(),
            ]),
            'parMois' => $this->alertesParMois($alertes, $mois ?? 12),
        ]);
    }

    /**
     * Chiffres d'une zone : nombre d'alertes, jours de vigilance, température record,
     * niveau le plus grave atteint, répartition par niveau et score d'exposition.
     */
    private function statistiquesZone(Zone $zone, Collection $alertes): array
    {
        $parNiveau = $alertes->groupBy('niveau_vigilance_id')->map->count();

        // Jours de vigilance : chaque alerte compte pour le nombre de jours qu'elle couvre (au moins 1)
        $jours = $alertes->sum(fn (AlerteMeteo $a) => max(1, (int) ceil($a->date_debut->diffInHours($a->date_fin) / 24)));

        $plusGrave = $alertes->sortByDesc(fn ($a) => $a->niveauVigilance?->ordre ?? 0)->first()?->niveauVigilance;

        // Score d'exposition : chaque alerte pèse selon la gravité de son niveau (Vert = 1 … Rouge = 4)
        $score = $alertes->sum(fn ($a) => $a->niveauVigilance?->ordre ?? 1);

        return [
            'zone' => $zone,
            'total' => $alertes->count(),
            'en_cours' => $alertes->filter(fn ($a) => $a->statut === 'en_cours')->count(),
            'jours' => $jours,
            'record' => $alertes->max('temperature_max'),
            'moyenne' => $alertes->isNotEmpty() ? round($alertes->avg('temperature_max'), 1) : null,
            'plus_grave' => $plusGrave,
            'par_niveau' => $parNiveau,
            'score' => $score,
        ];
    }

    /** Nombre d'alertes par mois (selon la date de début) sur les N derniers mois. */
    private function alertesParMois(Collection $alertes, int $mois): array
    {
        $resultat = [];
        for ($i = $mois - 1; $i >= 0; $i--) {
            $debut = now()->startOfMonth()->subMonths($i);
            $resultat[] = [
                'mois' => ucfirst($debut->translatedFormat('M Y')),
                'total' => $alertes->filter(fn ($a) => $a->date_debut->isSameMonth($debut))->count(),
            ];
        }

        return $resultat;
    }
}
