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
            'message' => self::message($temperature),
            // Niveau cohérent avec la température si les niveaux existent déjà
            'niveau_vigilance_id' => fn () => NiveauVigilance::pourTemperature($temperature)?->id
                ?? NiveauVigilance::factory(),
            'zone_id' => fn () => Zone::inRandomOrder()->value('id') ?? Zone::factory(),
        ];
    }

    /**
     * Message réaliste en français selon la température.
     */
    public static function message(float $temperature): string
    {
        $t = number_format($temperature, 1, ',', '');

        $messages = match (true) {
            $temperature >= 44 => [
                "Chaleur exceptionnelle : jusqu'à {$t} °C attendus. Évitez toute sortie entre 11h et 17h et rejoignez un point de fraîcheur si votre logement n'est pas climatisé.",
                "Épisode caniculaire sévère avec {$t} °C sous abri. Risque élevé de délestage électrique : chargez vos téléphones et gardez une lampe à portée de main.",
                "Sirocco et températures extrêmes ({$t} °C). Prenez des nouvelles des personnes âgées et isolées de votre quartier plusieurs fois par jour.",
            ],
            $temperature >= 39 => [
                "Canicule en cours : jusqu'à {$t} °C cet après-midi. Fermez volets et fenêtres en journée et aérez la nuit.",
                "Fortes chaleurs ({$t} °C). Limitez l'usage simultané de la climatisation, du four et du chauffe-eau entre 13h et 17h pour éviter la surcharge du réseau.",
                "Températures élevées attendues ({$t} °C). Buvez au moins 1,5 L d'eau par jour, même sans soif, et évitez l'alcool.",
            ],
            $temperature >= 35 => [
                "Chaleur marquée : {$t} °C prévus. Évitez les efforts physiques aux heures les plus chaudes et portez des vêtements légers.",
                "Journée chaude ({$t} °C). Pensez à vous hydrater régulièrement et à ne jamais laisser un enfant ou un animal dans une voiture.",
            ],
            default => [
                "Températures en hausse ({$t} °C). Restez attentifs aux prochains bulletins de vigilance.",
                "Temps chaud sans danger particulier ({$t} °C). Hydratez-vous et protégez-vous du soleil.",
            ],
        };

        return fake()->randomElement($messages);
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
