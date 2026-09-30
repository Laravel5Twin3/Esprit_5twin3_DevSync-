<?php

namespace Database\Seeders;

use App\Models\Coupure;
use App\Models\Zone;
use App\Services\Coupure\MeteoService;
use App\Services\Coupure\PredictionCoupureService;
use Illuminate\Database\Seeder;

class CoupureSeeder extends Seeder
{
    /**
     * Génère un historique de coupures réaliste (ZoneSeeder doit passer avant).
     *
     * Les coupures passées suivent la météo RÉELLE des 90 derniers jours (Open-Meteo) :
     * plus il fait chaud et plus la zone est fragile, plus une coupure est probable.
     * C'est cette relation que le modèle IA doit ensuite retrouver par lui-même.
     */
    public function run(MeteoService $meteo, PredictionCoupureService $prediction): void
    {
        $zones = Zone::all();
        $temperatures = $meteo->temperaturesMax($zones, PredictionCoupureService::JOURS_HISTORIQUE, 1);
        $this->command?->info("Météo de l'historique : {$meteo->source}");

        foreach ($zones as $zone) {
            $this->historique($zone, $temperatures[$zone->id] ?? []);

            $parZone = Coupure::factory()->for($zone);
            $parZone->count(fake()->numberBetween(1, 2))->resolue()->state(['type' => 'maintenance'])->create();
            $parZone->count(fake()->numberBetween(0, 1))->annulee()->create();
            $parZone->count(fake()->numberBetween(0, 2))->prevue()->create();

            if ($zone->niveau_risque === 'eleve') {
                $parZone->enCours()->state(['type' => 'surcharge'])->create();
            }
        }

        // Entraîne le modèle IA sur les données qui viennent d'être créées
        $infos = $prediction->entrainer();
        $this->command?->info(sprintf(
            'Modèle IA entraîné : %d exemples, AUC = %.2f',
            $infos['metriques']['exemples'],
            $infos['metriques']['auc'] ?? 0,
        ));
    }

    /**
     * @param  array<string, float>  $temperatures  ['2026-08-01' => 38.4, ...]
     */
    private function historique(Zone $zone, array $temperatures): void
    {
        $base = ['faible' => -4.2, 'moyen' => -3.3, 'eleve' => -2.4][$zone->niveau_risque];

        foreach ($temperatures as $date => $temperatureMax) {
            if ($date >= today()->toDateString()) {
                continue;
            }

            // Probabilité de coupure ce jour-là (fonction logistique)
            $score = $base + 0.45 * ($temperatureMax - 32) + 0.08 * (($zone->population ?? 50000) / 10000 - 5);
            $probabilite = 1 / (1 + exp(-$score));

            if (fake()->randomFloat(4, 0, 1) >= $probabilite) {
                continue;
            }

            // Forte chaleur => délestage ou surcharge l'après-midi ; sinon panne à toute heure
            $type = $temperatureMax >= 37
                ? fake()->randomElement(['delestage', 'delestage', 'surcharge'])
                : fake()->randomElement(['panne', 'surcharge', 'delestage']);

            $debut = \Carbon\Carbon::parse($date)->setTime(
                $type === 'panne' ? fake()->numberBetween(0, 23) : fake()->numberBetween(12, 17),
                fake()->randomElement([0, 15, 30, 45]),
            );

            Coupure::factory()->for($zone)->create([
                'type' => $type,
                'statut' => 'resolue',
                'date_debut' => $debut,
                'date_fin' => $debut->copy()->addMinutes(fake()->numberBetween(45, 300)),
                'foyers_touches' => (int) (($zone->population ?? 50000) / 4 * fake()->randomFloat(2, 0.05, 0.4)),
            ]);
        }
    }
}
