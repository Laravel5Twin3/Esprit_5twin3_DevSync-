<?php

namespace Database\Factories;

use App\Models\CategoriePoint;
use App\Models\PointFraicheur;
use Illuminate\Database\Eloquent\Factories\Factory;

class PointFraicheurFactory extends Factory
{
    protected $model = PointFraicheur::class;

    // Coordonnées centrées sur Tunis (zone réaliste pour HeatAlert.tn)
    private const LAT_CENTER = 36.8190;
    private const LNG_CENTER = 10.1657;

    public function definition(): array
    {
        return [
            'categorie_point_id' => CategoriePoint::inRandomOrder()->first()?->id
                                    ?? CategoriePoint::factory(),
            'nom'         => $this->faker->company() . ' ' . $this->faker->randomElement(['Park', 'Center', 'Plaza', 'Square']),
            'adresse'     => $this->faker->streetAddress() . ', Tunis',
            'latitude'    => self::LAT_CENTER + $this->faker->randomFloat(4, -0.05, 0.05),
            'longitude'   => self::LNG_CENTER + $this->faker->randomFloat(4, -0.05, 0.05),
            'horaires'    => $this->faker->randomElement([
                '08h00 - 20h00',
                '09h00 - 18h00',
                '07h00 - 22h00',
                '24h/24',
                null,
            ]),
            'capacite'    => $this->faker->optional(0.7)->numberBetween(20, 500),
            'actif'       => $this->faker->boolean(85), // 85% actifs
            'description' => $this->faker->paragraph(),
        ];
    }

    /**
     * État : point actif.
     */
    public function actif(): static
    {
        return $this->state(['actif' => true]);
    }
}
