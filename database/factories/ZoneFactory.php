<?php

namespace Database\Factories;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Zone>
 */
class ZoneFactory extends Factory
{
    protected $model = Zone::class;

    public function definition(): array
    {
        $nom = 'Quartier '.fake()->unique()->lastName();
        $gouvernorat = fake()->randomElement(Zone::GOUVERNORATS);

        return [
            'nom' => $nom,
            'gouvernorat' => $gouvernorat,
            'code_postal' => (string) fake()->numberBetween(1000, 9999),
            'population' => fake()->numberBetween(2000, 80000),
            'niveau_risque' => fake()->randomElement(array_keys(Zone::NIVEAUX_RISQUE)),
            'description' => "Secteur de distribution électrique de {$nom} ({$gouvernorat}).",
        ];
    }
}
