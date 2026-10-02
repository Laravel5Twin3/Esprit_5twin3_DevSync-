<?php

namespace Database\Factories;

use App\Models\NiveauVigilance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NiveauVigilance>
 */
class NiveauVigilanceFactory extends Factory
{
    protected $model = NiveauVigilance::class;

    public function definition(): array
    {
        $min = fake()->numberBetween(30, 44);

        return [
            'nom' => 'Niveau '.fake()->unique()->colorName(),
            'couleur' => fake()->hexColor(),
            'temperature_min' => $min,
            'temperature_max' => $min + 3.9,
            'consigne' => fake()->randomElement([
                'Buvez régulièrement et évitez les efforts entre 12h et 16h.',
                'Restez au frais, fermez les volets en journée et aérez la nuit.',
                'Limitez les appareils énergivores aux heures de pointe pour éviter les coupures.',
            ]),
            'ordre' => fake()->numberBetween(1, 4),
        ];
    }
}
