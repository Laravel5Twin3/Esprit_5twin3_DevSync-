<?php

namespace App\Services\Coupure;

use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Températures maximales journalières (passées et prévues) de chaque zone,
 * via l'API gratuite Open-Meteo. Sans connexion, une simulation saisonnière prend le relais.
 */
class MeteoService
{
    private const URL = 'https://api.open-meteo.com/v1/forecast';

    private const URL_ARCHIVE = 'https://archive-api.open-meteo.com/v1/archive';

    /** Horizon maximal des prévisions Open-Meteo (en jours, aujourd'hui compris). */
    public const JOURS_PREVISION_MAX = 16;

    /** Nombre d'années passées utilisées pour calculer les normales saisonnières. */
    private const ANNEES_NORMALES = 5;

    // Tunis centre : utilisé quand une zone n'a pas de coordonnées
    private const LATITUDE_DEFAUT = 36.8065;
    private const LONGITUDE_DEFAUT = 10.1815;

    /** 'open-meteo', 'normales' ou 'simulation' selon l'origine des dernières données. */
    public string $source = 'open-meteo';

    /**
     * Température maximale de chaque zone pour une date donnée :
     * - jusqu'à 16 jours : vraie prévision Open-Meteo ;
     * - au-delà : normale saisonnière (moyenne du même jour sur les 5 dernières années).
     *
     * @param  Collection<int, Zone>  $zones
     * @return array<int, float|null> [zone_id => température]
     */
    public function temperaturesPourDate(Collection $zones, Carbon $date): array
    {
        if ($date->lt(today()->addDays(self::JOURS_PREVISION_MAX))) {
            $series = $this->temperaturesMax($zones, 0, self::JOURS_PREVISION_MAX);

            return $zones->mapWithKeys(fn (Zone $zone) => [$zone->id => $series[$zone->id][$date->toDateString()] ?? null])->all();
        }

        $coordonnees = $this->coordonnees($zones);
        $cle = 'meteo-normales:'.md5(json_encode($coordonnees)).':'.$date->format('m-d');

        try {
            $normales = Cache::remember($cle, now()->addDays(7), fn () => $this->normalesSaisonnieres($coordonnees, $date));
            $this->source = 'normales';
        } catch (\Throwable $e) {
            Log::warning('Archives Open-Meteo indisponibles, météo simulée utilisée : '.$e->getMessage());
            $normales = array_map(fn (array $c) => $this->temperatureSimulee($date, $c[0]), $coordonnees);
            $this->source = 'simulation';
        }

        return $zones->values()
            ->mapWithKeys(fn (Zone $zone, int $i) => [$zone->id => $normales[$i] ?? $this->temperatureSimulee($date, $coordonnees[$i][0])])
            ->all();
    }

    /**
     * @param  Collection<int, Zone>  $zones
     * @return array<int, array<string, float>> [zone_id => ['2026-08-01' => 38.4, ...]]
     */
    public function temperaturesMax(Collection $zones, int $joursPasses, int $joursFuturs): array
    {
        if ($zones->isEmpty()) {
            return [];
        }

        $coordonnees = $this->coordonnees($zones);

        $cle = 'meteo-coupures:'.md5(json_encode($coordonnees)).":{$joursPasses}:{$joursFuturs}:".today()->toDateString();

        try {
            $series = Cache::remember($cle, now()->addHours(3), fn () => $this->appelerApi($coordonnees, $joursPasses, $joursFuturs));
            $this->source = 'open-meteo';
        } catch (\Throwable $e) {
            Log::warning('Open-Meteo indisponible, météo simulée utilisée : '.$e->getMessage());
            $series = $this->simuler($coordonnees, $joursPasses, $joursFuturs);
            $this->source = 'simulation';
        }

        // L'API renvoie les lieux dans l'ordre demandé : on les réassocie aux zones
        return $zones->values()
            ->mapWithKeys(fn (Zone $zone, int $i) => [$zone->id => $series[$i] ?? []])
            ->all();
    }

    /**
     * @return list<array{0: float, 1: float}> [latitude, longitude] de chaque zone, dans l'ordre
     */
    private function coordonnees(Collection $zones): array
    {
        return $zones->map(fn (Zone $zone) => [
            round($zone->latitude ?? self::LATITUDE_DEFAUT, 4),
            round($zone->longitude ?? self::LONGITUDE_DEFAUT, 4),
        ])->values()->all();
    }

    /**
     * Moyenne de la température maximale du même jour calendaire sur les dernières années.
     *
     * @return list<float|null>
     */
    private function normalesSaisonnieres(array $coordonnees, Carbon $date): array
    {
        $sommes = array_fill(0, count($coordonnees), 0.0);
        $nombres = array_fill(0, count($coordonnees), 0);

        for ($annee = 1; $annee <= self::ANNEES_NORMALES; $annee++) {
            $jour = $date->copy()->subYears($annee);

            // Fenêtre de ±3 jours pour lisser les journées exceptionnelles (canicule record, etc.)
            $reponse = Http::timeout(8)->retry(2, 300)->get(self::URL_ARCHIVE, [
                'latitude' => implode(',', array_column($coordonnees, 0)),
                'longitude' => implode(',', array_column($coordonnees, 1)),
                'start_date' => $jour->copy()->subDays(3)->toDateString(),
                'end_date' => $jour->copy()->addDays(3)->toDateString(),
                'daily' => 'temperature_2m_max',
                'timezone' => 'Africa/Tunis',
            ])->throw()->json();

            $lieux = array_is_list($reponse) ? $reponse : [$reponse];
            foreach ($lieux as $i => $lieu) {
                foreach ($lieu['daily']['temperature_2m_max'] as $temperature) {
                    if ($temperature !== null) {
                        $sommes[$i] += $temperature;
                        $nombres[$i]++;
                    }
                }
            }
        }

        return array_map(fn ($somme, $nombre) => $nombre ? round($somme / $nombre, 1) : null, $sommes, $nombres);
    }

    /**
     * @return list<array<string, float>>
     */
    private function appelerApi(array $coordonnees, int $joursPasses, int $joursFuturs): array
    {
        $reponse = Http::timeout(8)->retry(2, 300)->get(self::URL, [
            'latitude' => implode(',', array_column($coordonnees, 0)),
            'longitude' => implode(',', array_column($coordonnees, 1)),
            'daily' => 'temperature_2m_max',
            'timezone' => 'Africa/Tunis',
            'past_days' => min($joursPasses, 92),
            'forecast_days' => max(1, min($joursFuturs, 16)),
        ])->throw()->json();

        // Un seul lieu => objet ; plusieurs lieux => liste d'objets
        $lieux = array_is_list($reponse) ? $reponse : [$reponse];

        return array_map(function (array $lieu, int $i) use ($coordonnees) {
            $serie = [];
            foreach ($lieu['daily']['time'] as $j => $date) {
                // Certaines valeurs peuvent manquer (null) : on les complète par simulation
                $serie[$date] = (float) ($lieu['daily']['temperature_2m_max'][$j]
                    ?? $this->temperatureSimulee(Carbon::parse($date), $coordonnees[$i][0]));
            }

            return $serie;
        }, $lieux, array_keys($lieux));
    }

    /**
     * @return list<array<string, float>>
     */
    private function simuler(array $coordonnees, int $joursPasses, int $joursFuturs): array
    {
        return array_map(function (array $coordonnee) use ($joursPasses, $joursFuturs) {
            $serie = [];
            for ($k = -$joursPasses; $k < $joursFuturs; $k++) {
                $date = today()->addDays($k);
                $serie[$date->toDateString()] = $this->temperatureSimulee($date, $coordonnee[0]);
            }

            return $serie;
        }, $coordonnees);
    }

    /**
     * Courbe saisonnière (pic début août) + variation déterministe propre à chaque jour.
     */
    private function temperatureSimulee(Carbon $date, float $latitude): float
    {
        $saison = 25.5 + 9 * cos(2 * M_PI * ($date->dayOfYear - 213) / 365);
        $variation = (crc32($date->toDateString().$latitude) % 700) / 100 - 3.5;

        return round($saison + $variation, 1);
    }
}
