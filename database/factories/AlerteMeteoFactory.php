<?php

namespace Database\Factories;

use App\Models\AlerteMeteo;
use App\Models\NiveauVigilance;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AlerteMeteo>
 */
class AlerteMeteoFactory extends Factory
{
    protected $model = AlerteMeteo::class;

    public function definition(): array
    {
        $debut = fake()->dateTimeBetween('-10 days', '+7 days');
        $temperature = fake()->randomFloat(1, 33, 47);

        return [
            'titre' => fake()->randomElement([
                'Vague de chaleur', 'Épisode caniculaire', 'Coup de sirocco',
                'Fortes chaleurs', 'Nuits tropicales', 'Pic de chaleur',
            ]),
            'temperature_max' => $temperature,
            'date_debut' => $debut,
            'date_fin' => (clone $debut)->modify('+'.fake()->numberBetween(1, 4).' days'),
            'message' => fake()->paragraph(),
            // Niveau cohérent avec la température si les niveaux existent déjà
            'niveau_vigilance_id' => fn () => NiveauVigilance::pourTemperature($temperature)?->id
                ?? NiveauVigilance::factory(),
            'zone_id' => fn () => Zone::inRandomOrder()->value('id') ?? Zone::factory(),
        ];
    }

    /** Alerte en cours en ce moment. */
    public function enCours(): static
    {
        return $this->state(fn () => [
            'date_debut' => now()->subHours(fake()->numberBetween(2, 30)),
            'date_fin' => now()->addDays(fake()->numberBetween(1, 3)),
        ]);
    }

    /** Alerte terminée. */
    public function terminee(): static
    {
        return $this->state(function () {
            $debut = now()->subDays(fake()->numberBetween(5, 30));

            return ['date_debut' => $debut, 'date_fin' => (clone $debut)->addDays(2)];
        });
    }
}
