<?php

namespace App\Services\AlerteMeteo;

use App\Models\NiveauVigilance;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

/**
 * Récupère les prévisions de température (API gratuite Open-Meteo, sans clé)
 * et prépare une alerte à partir du jour le plus chaud des 7 prochains jours.
 */
class PrevisionChaleurService
{
    private const URL = 'https://api.open-meteo.com/v1/forecast';

    /** Coordonnées de Tunis centre, utilisées si la zone n'en a pas. */
    private const TUNIS = [36.8065, 10.1815];

    /**
     * Prévisions journalières : collection de ['date' => Carbon, 'temperature' => float, 'niveau' => NiveauVigilance].
     *
     * @throws \RuntimeException si l'API est injoignable
     */
    public function previsions(Zone $zone, int $jours = 7): Collection
    {
        $reponse = Http::timeout(8)->retry(2, 300)->get(self::URL, [
            'latitude' => $zone->latitude ?? self::TUNIS[0],
            'longitude' => $zone->longitude ?? self::TUNIS[1],
            'daily' => 'temperature_2m_max',
            'timezone' => 'Africa/Tunis',
            'forecast_days' => $jours,
        ]);

        if ($reponse->failed() || ! $reponse->json('daily.time')) {
            throw new \RuntimeException('Le service météo Open-Meteo est indisponible pour le moment.');
        }

        return collect($reponse->json('daily.time'))
            ->zip($reponse->json('daily.temperature_2m_max'))
            ->map(fn ($jour) => [
                'date' => Carbon::parse($jour[0]),
                'temperature' => (float) $jour[1],
                'niveau' => NiveauVigilance::pourTemperature((float) $jour[1]),
            ]);
    }

    /**
     * Valeurs pré-remplies d'une alerte pour le jour le plus chaud.
     */
    public function suggestion(Zone $zone, Collection $previsions): array
    {
        $pic = $previsions->sortByDesc('temperature')->first();
        $niveau = $pic['niveau'];

        return [
            'titre' => ($pic['temperature'] >= 39 ? 'Canicule' : 'Fortes chaleurs').' prévue à '.$zone->nom,
            'zone_id' => $zone->id,
            'temperature_max' => $pic['temperature'],
            'niveau_vigilance_id' => $niveau?->id,
            'date_debut' => $pic['date']->copy()->setTime(10, 0),
            'date_fin' => $pic['date']->copy()->setTime(20, 0),
            'message' => "Les prévisions Open-Meteo annoncent jusqu'à {$pic['temperature']} °C le "
                .$pic['date']->translatedFormat('l j F').' à '.$zone->nom.'. '
                .($niveau?->consigne ?? ''),
        ];
    }
}
