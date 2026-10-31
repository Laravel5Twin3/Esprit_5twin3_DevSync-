<?php

namespace Tests\Unit\IA;

use App\Services\Coupure\RegressionLogistique;
use PHPUnit\Framework\TestCase;

/**
 * Modèle de prédiction des coupures (module Coupures).
 */
class RegressionLogistiqueTest extends TestCase
{
    /** Jeu simple : coupure (1) quand il fait plus de 35 °C. */
    private function donnees(): array
    {
        $X = [];
        $y = [];
        for ($t = 20; $t <= 45; $t++) {
            $X[] = [(float) $t];
            $y[] = $t > 35 ? 1 : 0;
        }

        return [$X, $y];
    }

    public function test_le_modele_apprend_la_relation(): void
    {
        [$X, $y] = $this->donnees();
        $modele = (new RegressionLogistique)->entrainer($X, $y);

        $this->assertGreaterThan(0.8, $modele->probabilite([43.0]));
        $this->assertLessThan(0.2, $modele->probabilite([24.0]));
        $this->assertGreaterThan(0, $modele->poids()[0], 'La chaleur doit augmenter le risque.');
    }

    public function test_le_modele_se_sauvegarde_et_se_recharge(): void
    {
        [$X, $y] = $this->donnees();
        $modele = (new RegressionLogistique)->entrainer($X, $y);

        $recharge = RegressionLogistique::fromArray(json_decode(json_encode($modele->toArray()), true));

        $this->assertEqualsWithDelta($modele->probabilite([38.0]), $recharge->probabilite([38.0]), 1e-9);
    }
}
