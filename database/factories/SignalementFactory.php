<?php

namespace Database\Factories;

use App\Models\Signalement;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Signalement>
 */
class SignalementFactory extends Factory
{
    protected $model = Signalement::class;

    private const DESCRIPTIONS = [
        'Plus de courant dans tout l\'immeuble depuis quelques minutes.',
        'Coupure totale dans ma rue, les feux de circulation sont aussi éteints.',
        'Le courant a sauté après une forte baisse de tension, les lumières clignotaient avant.',
        'Toute la cité est dans le noir, les climatiseurs se sont arrêtés d\'un coup.',
        'Coupure chez moi et chez les voisins du bâtiment B, l\'ascenseur est bloqué.',
        'Bruit d\'explosion du côté du transformateur puis plus d\'électricité.',
        'Le réfrigérateur et la climatisation sont coupés, les voisins confirment la panne.',
    ];

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'zone_id' => Zone::factory(),
            'date_constat' => fake()->dateTimeBetween('-20 hours', '-10 minutes'),
            'description' => fake()->randomElement(self::DESCRIPTIONS),
            'statut' => 'en_attente',
        ];
    }
}
