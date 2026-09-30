<?php

namespace App\Services\Coupure;

/**
 * Régression logistique (classification binaire) entraînée par descente de gradient.
 *
 * Le modèle calcule p = sigmoïde(w · z + b), où z est le vecteur de variables
 * standardisées (moyenne 0, écart-type 1). p est la probabilité que l'événement se produise.
 */
class RegressionLogistique
{
    /**
     * @param  list<float>  $poids  un poids par variable (importance apprise)
     * @param  list<float>  $moyennes  moyennes des variables, pour la standardisation
     * @param  list<float>  $ecartsTypes  écarts-types des variables, pour la standardisation
     */
    public function __construct(
        private array $poids = [],
        private float $biais = 0.0,
        private array $moyennes = [],
        private array $ecartsTypes = [],
    ) {}

    /**
     * @param  list<list<float>>  $X  exemples (une ligne = un exemple, une colonne = une variable)
     * @param  list<int>  $y  étiquettes (1 = coupure, 0 = pas de coupure)
     * @param  float  $taux  taux d'apprentissage
     * @param  float  $lambda  régularisation L2 (limite le sur-apprentissage)
     */
    public function entrainer(array $X, array $y, int $iterations = 1500, float $taux = 0.5, float $lambda = 0.001): static
    {
        $n = count($X);
        $d = count($X[0]);

        // 1. Standardisation : met toutes les variables à la même échelle
        $this->moyennes = [];
        $this->ecartsTypes = [];
        for ($j = 0; $j < $d; $j++) {
            $colonne = array_column($X, $j);
            $moyenne = array_sum($colonne) / $n;
            $variance = array_sum(array_map(fn ($v) => ($v - $moyenne) ** 2, $colonne)) / $n;
            $this->moyennes[$j] = $moyenne;
            $this->ecartsTypes[$j] = sqrt($variance) ?: 1.0;
        }
        $Z = array_map(fn (array $x) => $this->standardiser($x), $X);

        // 2. Descente de gradient sur la perte logistique (entropie croisée)
        $this->poids = array_fill(0, $d, 0.0);
        $this->biais = 0.0;

        for ($iteration = 0; $iteration < $iterations; $iteration++) {
            $gradientPoids = array_fill(0, $d, 0.0);
            $gradientBiais = 0.0;

            foreach ($Z as $i => $z) {
                $erreur = $this->sigmoide($this->score($z)) - $y[$i];
                for ($j = 0; $j < $d; $j++) {
                    $gradientPoids[$j] += $erreur * $z[$j];
                }
                $gradientBiais += $erreur;
            }

            for ($j = 0; $j < $d; $j++) {
                $this->poids[$j] -= $taux * ($gradientPoids[$j] / $n + $lambda * $this->poids[$j]);
            }
            $this->biais -= $taux * $gradientBiais / $n;
        }

        return $this;
    }

    /**
     * Probabilité (entre 0 et 1) que l'exemple soit positif.
     *
     * @param  list<float>  $x
     */
    public function probabilite(array $x): float
    {
        return $this->sigmoide($this->score($this->standardiser($x)));
    }

    /**
     * Contribution de chaque variable au score : explique pourquoi le modèle prédit ce résultat.
     * Positive = augmente le risque, négative = le diminue.
     *
     * @param  list<float>  $x
     * @return list<float>
     */
    public function contributions(array $x): array
    {
        $z = $this->standardiser($x);

        return array_map(fn ($poids, $valeur) => $poids * $valeur, $this->poids, $z);
    }

    /** @return list<float> */
    public function poids(): array
    {
        return $this->poids;
    }

    public function toArray(): array
    {
        return [
            'poids' => $this->poids,
            'biais' => $this->biais,
            'moyennes' => $this->moyennes,
            'ecarts_types' => $this->ecartsTypes,
        ];
    }

    public static function fromArray(array $donnees): static
    {
        return new static($donnees['poids'], $donnees['biais'], $donnees['moyennes'], $donnees['ecarts_types']);
    }

    private function standardiser(array $x): array
    {
        return array_map(fn ($v, $j) => ($v - $this->moyennes[$j]) / $this->ecartsTypes[$j], $x, array_keys($x));
    }

    private function score(array $z): float
    {
        $score = $this->biais;
        foreach ($z as $j => $valeur) {
            $score += $this->poids[$j] * $valeur;
        }

        return $score;
    }

    private function sigmoide(float $t): float
    {
        return 1 / (1 + exp(-max(-30, min(30, $t))));
    }
}
