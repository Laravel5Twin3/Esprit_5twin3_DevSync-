<?php

namespace App\Services\Coupure;

use App\Models\Coupure;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Prédiction IA du risque de coupure par zone et par jour.
 *
 * Apprentissage : pour chaque zone et chacun des 90 derniers jours, on construit un exemple
 * (température max, niveau de risque, coupures récentes, population) et on note s'il y a eu
 * une coupure ce jour-là. Une régression logistique apprend le lien entre ces variables et
 * les coupures, puis l'applique aux prévisions météo des prochains jours.
 */
class PredictionCoupureService
{
    public const FICHIER_MODELE = 'ia/modele-coupures.json';

    public const JOURS_HISTORIQUE = 90;

    public const JOURS_PREVISION = 4;

    /** Coupures liées à la charge du réseau (la maintenance programmée n'est pas prévisible par la météo). */
    public const TYPES_ETUDIES = ['delestage', 'surcharge', 'panne'];

    /** Variables d'entrée du modèle (dans cet ordre). */
    public const VARIABLES = [
        'temperature_max' => 'Température maximale',
        'niveau_risque' => 'Niveau de risque de la zone',
        'coupures_30j' => 'Coupures des 30 derniers jours',
        'population' => 'Population de la zone',
    ];

    public function __construct(private MeteoService $meteo) {}

    /**
     * Entraîne le modèle sur l'historique, l'évalue et l'enregistre.
     *
     * @throws RuntimeException s'il n'y a pas assez de données
     */
    public function entrainer(): array
    {
        [$X, $y] = $this->jeuDeDonnees();

        $positifs = array_sum($y);
        if (count($X) < 50 || $positifs < 5 || $positifs === count($y)) {
            throw new RuntimeException('Pas assez de données pour entraîner le modèle (il faut des jours avec et sans coupure).');
        }

        // Évaluation honnête : 80 % des exemples pour apprendre, 20 % jamais vus pour tester
        mt_srand(42);
        $indices = range(0, count($X) - 1);
        shuffle($indices);
        mt_srand();
        $separation = (int) round(count($indices) * 0.8);
        $pick = fn (array $source, array $ids) => array_map(fn ($i) => $source[$i], $ids);

        $modeleTest = (new RegressionLogistique)->entrainer(
            $pick($X, array_slice($indices, 0, $separation)),
            $pick($y, array_slice($indices, 0, $separation)),
        );
        $metriques = $this->evaluer(
            $modeleTest,
            $pick($X, array_slice($indices, $separation)),
            $pick($y, array_slice($indices, $separation)),
        );

        // Modèle final : entraîné sur toutes les données disponibles
        $modele = (new RegressionLogistique)->entrainer($X, $y);

        $donnees = [
            'modele' => $modele->toArray(),
            'variables' => array_keys(self::VARIABLES),
            'metriques' => $metriques + [
                'exemples' => count($X),
                'positifs' => $positifs,
                'taux_coupure' => $positifs / count($X),
            ],
            'source_meteo' => $this->meteo->source,
            'entraine_le' => now()->toIso8601String(),
        ];

        Storage::disk('local')->put(self::FICHIER_MODELE, json_encode($donnees, JSON_PRETTY_PRINT));

        return $donnees;
    }

    /**
     * Informations du modèle enregistré (l'entraîne s'il n'existe pas encore), ou null si impossible.
     */
    public function informations(): ?array
    {
        if (Storage::disk('local')->exists(self::FICHIER_MODELE)) {
            return json_decode(Storage::disk('local')->get(self::FICHIER_MODELE), true);
        }

        try {
            return $this->entrainer();
        } catch (RuntimeException) {
            return null;
        }
    }

    /**
     * Prévision du risque pour chaque zone sur les prochains jours.
     *
     * @return Collection<int, array{zone: Zone, jours: list<array>}>
     */
    public function previsions(): Collection
    {
        $informations = $this->informations();
        if (! $informations) {
            return collect();
        }

        $modele = RegressionLogistique::fromArray($informations['modele']);
        $zones = Zone::orderBy('nom')->get();
        $temperatures = $this->meteo->temperaturesMax($zones, 0, self::JOURS_PREVISION);
        $coupures = $this->joursAvecCoupure();
        $aujourdhui = today()->toDateString();

        return $zones->map(function (Zone $zone) use ($modele, $temperatures, $coupures, $aujourdhui) {
            $recentes = $this->coupuresRecentes($coupures[$zone->id] ?? [], $aujourdhui);

            $jours = [];
            for ($k = 0; $k < self::JOURS_PREVISION; $k++) {
                $date = today()->addDays($k);
                $temperature = $temperatures[$zone->id][$date->toDateString()] ?? null;
                if ($temperature === null) {
                    continue;
                }

                $x = $this->variables($zone, $temperature, $recentes);
                $probabilite = $modele->probabilite($x);

                $jours[] = [
                    'date' => $date,
                    'temperature' => $temperature,
                    'probabilite' => $probabilite,
                    'niveau' => self::niveau($probabilite),
                    'facteurs' => $this->facteurs($modele->contributions($x)),
                ];
            }

            return ['zone' => $zone, 'coupures_30j' => $recentes, 'jours' => $jours];
        });
    }

    /**
     * Simulation : risque de chaque zone si la température maximale atteignait $temperature.
     *
     * @return Collection<int, array{zone: Zone, temperature: float, probabilite: float, niveau: array, facteurs: list<array>}>
     */
    public function simuler(float $temperature): Collection
    {
        return $this->evaluerZones(Zone::orderBy('nom')->get(), fn () => $temperature);
    }

    /**
     * Prédiction pour une date choisie (jusqu'à 16 jours : météo prévue ; au-delà : normales saisonnières).
     *
     * @return array{source: string, resultats: Collection}
     */
    public function previsionPourDate(Carbon $date): array
    {
        $zones = Zone::orderBy('nom')->get();
        $temperatures = $this->meteo->temperaturesPourDate($zones, $date);

        return [
            'source' => $this->meteo->source,
            'resultats' => $this->evaluerZones($zones, fn (Zone $zone) => $temperatures[$zone->id] ?? null),
        ];
    }

    /**
     * Applique le modèle à chaque zone, avec la température donnée par $temperaturePour($zone).
     * Les zones sont triées de la plus à la moins exposée.
     */
    private function evaluerZones(Collection $zones, callable $temperaturePour): Collection
    {
        $informations = $this->informations();
        if (! $informations) {
            return collect();
        }

        $modele = RegressionLogistique::fromArray($informations['modele']);
        $coupures = $this->joursAvecCoupure();
        $aujourdhui = today()->toDateString();

        return $zones
            ->map(function (Zone $zone) use ($modele, $coupures, $aujourdhui, $temperaturePour) {
                $temperature = $temperaturePour($zone);
                if ($temperature === null) {
                    return null;
                }

                $x = $this->variables($zone, $temperature, $this->coupuresRecentes($coupures[$zone->id] ?? [], $aujourdhui));
                $probabilite = $modele->probabilite($x);

                return [
                    'zone' => $zone,
                    'temperature' => $temperature,
                    'probabilite' => $probabilite,
                    'niveau' => self::niveau($probabilite),
                    'facteurs' => $this->facteurs($modele->contributions($x)),
                ];
            })
            ->filter()
            ->sortByDesc('probabilite')
            ->values();
    }

    /**
     * Traduit une probabilité en niveau lisible.
     *
     * @return array{label: string, couleur: string}
     */
    public static function niveau(float $probabilite): array
    {
        return match (true) {
            $probabilite >= 0.45 => ['label' => 'Élevé', 'couleur' => 'danger'],
            $probabilite >= 0.2 => ['label' => 'Modéré', 'couleur' => 'warning'],
            default => ['label' => 'Faible', 'couleur' => 'success'],
        };
    }

    public function sourceMeteo(): string
    {
        return $this->meteo->source;
    }

    /**
     * @return array{0: list<list<float>>, 1: list<int>}
     */
    private function jeuDeDonnees(): array
    {
        $zones = Zone::all();
        $temperatures = $this->meteo->temperaturesMax($zones, self::JOURS_HISTORIQUE, 1);
        $coupures = $this->joursAvecCoupure();

        $X = [];
        $y = [];
        foreach ($zones as $zone) {
            for ($k = self::JOURS_HISTORIQUE; $k >= 1; $k--) {
                $date = today()->subDays($k)->toDateString();
                $temperature = $temperatures[$zone->id][$date] ?? null;
                if ($temperature === null) {
                    continue;
                }

                $X[] = $this->variables($zone, $temperature, $this->coupuresRecentes($coupures[$zone->id] ?? [], $date));
                $y[] = isset($coupures[$zone->id][$date]) ? 1 : 0;
            }
        }

        return [$X, $y];
    }

    /**
     * @return list<float> dans l'ordre de self::VARIABLES
     */
    private function variables(Zone $zone, float $temperature, int $coupuresRecentes): array
    {
        return [
            $temperature,
            (float) array_search($zone->niveau_risque, ['faible', 'moyen', 'eleve'], true),
            (float) $coupuresRecentes,
            ($zone->population ?? 50000) / 10000,
        ];
    }

    /**
     * Jours où chaque zone a subi une coupure liée au réseau.
     *
     * @return array<int, array<string, int>> [zone_id => ['2026-08-01' => nombre]]
     */
    private function joursAvecCoupure(): array
    {
        $jours = [];
        Coupure::query()
            ->whereIn('type', self::TYPES_ETUDIES)
            ->whereIn('statut', ['en_cours', 'resolue'])
            ->get(['zone_id', 'date_debut'])
            ->each(function (Coupure $coupure) use (&$jours) {
                $date = $coupure->date_debut->toDateString();
                $jours[$coupure->zone_id][$date] = ($jours[$coupure->zone_id][$date] ?? 0) + 1;
            });

        return $jours;
    }

    /**
     * Nombre de coupures sur les 30 jours précédant $date (exclue).
     */
    private function coupuresRecentes(array $joursDeLaZone, string $date): int
    {
        $debut = Carbon::parse($date)->subDays(30)->toDateString();
        $total = 0;
        foreach ($joursDeLaZone as $jour => $nombre) {
            if ($jour >= $debut && $jour < $date) {
                $total += $nombre;
            }
        }

        return $total;
    }

    /**
     * Facteurs expliquant la prédiction, du plus influent au moins influent.
     *
     * @return list<array{label: string, contribution: float}>
     */
    private function facteurs(array $contributions): array
    {
        $facteurs = array_map(
            fn ($label, $contribution) => ['label' => $label, 'contribution' => $contribution],
            array_values(self::VARIABLES),
            $contributions,
        );
        usort($facteurs, fn ($a, $b) => abs($b['contribution']) <=> abs($a['contribution']));

        return $facteurs;
    }

    /**
     * Métriques sur le jeu de test.
     */
    private function evaluer(RegressionLogistique $modele, array $X, array $y): array
    {
        $vp = $fp = $vn = $fn = 0;
        $scores = [];
        foreach ($X as $i => $x) {
            $p = $modele->probabilite($x);
            $scores[] = [$p, $y[$i]];
            $predit = $p >= 0.5 ? 1 : 0;
            match (true) {
                $predit === 1 && $y[$i] === 1 => $vp++,
                $predit === 1 && $y[$i] === 0 => $fp++,
                $predit === 0 && $y[$i] === 0 => $vn++,
                default => $fn++,
            };
        }

        return [
            'exactitude' => ($vp + $vn) / max(1, count($y)),
            'precision' => $vp + $fp ? $vp / ($vp + $fp) : null,
            'rappel' => $vp + $fn ? $vp / ($vp + $fn) : null,
            'auc' => $this->auc($scores),
            'exemples_test' => count($y),
        ];
    }

    /**
     * Aire sous la courbe ROC : probabilité qu'un jour avec coupure reçoive un score plus élevé
     * qu'un jour sans coupure. 0,5 = hasard, 1 = parfait.
     */
    private function auc(array $scores): ?float
    {
        $positifs = array_filter($scores, fn ($s) => $s[1] === 1);
        $negatifs = array_filter($scores, fn ($s) => $s[1] === 0);
        if (! $positifs || ! $negatifs) {
            return null;
        }

        $gagnes = 0.0;
        foreach ($positifs as [$p]) {
            foreach ($negatifs as [$n]) {
                $gagnes += $p > $n ? 1 : ($p == $n ? 0.5 : 0);
            }
        }

        return $gagnes / (count($positifs) * count($negatifs));
    }
}
