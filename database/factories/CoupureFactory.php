<?php

namespace Database\Factories;

use App\Models\Coupure;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupure>
 */
class CoupureFactory extends Factory
{
    protected $model = Coupure::class;

    private const TITRES = [
        'delestage' => ['Délestage programmé', 'Délestage tournant pic de chaleur', 'Délestage préventif STEG'],
        'panne' => ['Panne transformateur', 'Câble endommagé', 'Panne poste de distribution'],
        'maintenance' => ['Travaux de maintenance réseau', 'Remplacement de compteurs', 'Entretien ligne moyenne tension'],
        'surcharge' => ['Surcharge du réseau (climatisation)', 'Surtension signalée', 'Saturation poste source'],
    ];

    /** Cause de la coupure, selon son type. */
    private const CAUSES = [
        'delestage' => [
            'Face à la forte demande liée à la vague de chaleur, la STEG procède à un délestage tournant pour éviter une panne générale du réseau.',
            'La consommation électrique a dépassé la capacité de production disponible aux heures de pointe (14h-17h). Un délestage par secteur est appliqué.',
            'Délestage préventif décidé suite aux températures supérieures à 42 °C annoncées dans la région.',
        ],
        'panne' => [
            'Un transformateur du poste de distribution du quartier est tombé en panne suite à une surchauffe.',
            'Un câble souterrain moyenne tension a été endommagé lors de travaux de voirie.',
            'Un défaut sur la ligne aérienne a provoqué le déclenchement des protections du réseau.',
        ],
        'maintenance' => [
            'Travaux programmés de renforcement du réseau de distribution afin de mieux supporter les pics de consommation estivaux.',
            'Remplacement d\'équipements vétustes dans le poste de transformation du secteur.',
            'Entretien préventif de la ligne moyenne tension alimentant le quartier.',
        ],
        'surcharge' => [
            'L\'utilisation simultanée des climatiseurs a provoqué une surcharge du poste de transformation du quartier.',
            'Le réseau local a atteint sa capacité maximale : les protections ont coupé l\'alimentation pour éviter tout dommage.',
            'Une surtension a été détectée sur le réseau suite à un pic de consommation en fin de journée.',
        ],
    ];

    /** Conseil donné aux habitants, selon le type. */
    private const CONSEILS = [
        'delestage' => 'Pensez à recharger vos téléphones à l\'avance et à limiter l\'ouverture du réfrigérateur.',
        'panne' => 'Les équipes techniques sont sur place. Débranchez vos appareils sensibles pour éviter les dégâts au retour du courant.',
        'maintenance' => 'Nous vous prions de nous excuser pour la gêne occasionnée. Prévoyez vos besoins en eau fraîche et en éclairage.',
        'surcharge' => 'Pour éviter une nouvelle coupure, réglez vos climatiseurs à 26 °C et évitez d\'utiliser plusieurs appareils énergivores en même temps.',
    ];

    public function definition(): array
    {
        $type = fake()->randomElement(array_keys(Coupure::TYPES));
        $debut = fake()->dateTimeBetween('-20 days', '-1 day');

        return [
            'zone_id' => Zone::factory(),
            'type' => $type,
            // Titre et description suivent le type, même s'il est imposé via ->state(['type' => ...])
            'titre' => fn (array $attributs) => fake()->randomElement(self::TITRES[$attributs['type']]),
            'statut' => 'resolue',
            'date_debut' => $debut,
            'date_fin' => (clone $debut)->modify('+'.fake()->numberBetween(30, 360).' minutes'),
            'foyers_touches' => fake()->numberBetween(50, 5000),
            'description' => fn (array $attributs) => fake()->randomElement(self::CAUSES[$attributs['type']]).' '.self::CONSEILS[$attributs['type']],
        ];
    }

    public function prevue(): static
    {
        return $this->state(function () {
            $debut = fake()->dateTimeBetween('+2 hours', '+7 days');

            return [
                'statut' => 'prevue',
                'date_debut' => $debut,
                'date_fin' => (clone $debut)->modify('+'.fake()->numberBetween(60, 240).' minutes'),
            ];
        });
    }

    public function enCours(): static
    {
        return $this->state(function () {
            $debut = fake()->dateTimeBetween('-5 hours', '-10 minutes');

            return [
                'statut' => 'en_cours',
                'date_debut' => $debut,
                'date_fin' => fake()->boolean(60) ? (clone $debut)->modify('+'.fake()->numberBetween(120, 480).' minutes') : null,
            ];
        });
    }

    public function resolue(): static
    {
        return $this->state(fn () => ['statut' => 'resolue']);
    }

    public function annulee(): static
    {
        return $this->state(fn () => ['statut' => 'annulee']);
    }
}
