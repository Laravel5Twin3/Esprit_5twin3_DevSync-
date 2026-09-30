<?php

namespace App\Console\Commands;

use App\Services\Coupure\PredictionCoupureService;
use Illuminate\Console\Command;
use RuntimeException;

class EntrainerModeleCoupures extends Command
{
    protected $signature = 'coupures:entrainer-ia';

    protected $description = 'Entraîne le modèle IA de prédiction du risque de coupure de courant';

    public function handle(PredictionCoupureService $prediction): int
    {
        try {
            $infos = $prediction->entrainer();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $m = $infos['metriques'];
        $this->info('Modèle entraîné et enregistré dans storage/app/private/'.PredictionCoupureService::FICHIER_MODELE);
        $this->table(['Mesure', 'Valeur'], [
            ['Exemples (zone × jour)', $m['exemples']],
            ['Jours avec coupure', $m['positifs']],
            ['Source météo', $infos['source_meteo']],
            ['Exactitude (test)', round($m['exactitude'] * 100, 1).' %'],
            ['AUC (test)', $m['auc'] !== null ? round($m['auc'], 3) : '—'],
        ]);

        $this->table(['Variable', 'Poids appris'], array_map(
            fn ($label, $poids) => [$label, round($poids, 3)],
            array_values(PredictionCoupureService::VARIABLES),
            $infos['modele']['poids'],
        ));

        return self::SUCCESS;
    }
}
