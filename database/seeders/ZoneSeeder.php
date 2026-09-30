<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Zones réelles du Grand Tunis.
     */
    public function run(): void
    {
        $zones = [
            ['nom' => 'La Marsa', 'gouvernorat' => 'Tunis', 'code_postal' => '2070', 'population' => 92987, 'latitude' => 36.8782, 'longitude' => 10.3247, 'niveau_risque' => 'moyen'],
            ['nom' => 'El Menzah', 'gouvernorat' => 'Tunis', 'code_postal' => '1004', 'population' => 38500, 'latitude' => 36.8380, 'longitude' => 10.1650, 'niveau_risque' => 'faible'],
            ['nom' => 'Cité El Khadra', 'gouvernorat' => 'Tunis', 'code_postal' => '1003', 'population' => 27400, 'latitude' => 36.8330, 'longitude' => 10.1940, 'niveau_risque' => 'moyen'],
            ['nom' => 'Bab Souika', 'gouvernorat' => 'Tunis', 'code_postal' => '1006', 'population' => 31200, 'latitude' => 36.8060, 'longitude' => 10.1690, 'niveau_risque' => 'eleve'],
            ['nom' => 'Ettadhamen', 'gouvernorat' => 'Ariana', 'code_postal' => '2041', 'population' => 84312, 'latitude' => 36.8390, 'longitude' => 10.1010, 'niveau_risque' => 'eleve'],
            ['nom' => 'Ariana Ville', 'gouvernorat' => 'Ariana', 'code_postal' => '2080', 'population' => 114486, 'latitude' => 36.8625, 'longitude' => 10.1956, 'niveau_risque' => 'moyen'],
            ['nom' => 'Ezzahra', 'gouvernorat' => 'Ben Arous', 'code_postal' => '2034', 'population' => 34962, 'latitude' => 36.7440, 'longitude' => 10.3080, 'niveau_risque' => 'faible'],
            ['nom' => 'Mégrine', 'gouvernorat' => 'Ben Arous', 'code_postal' => '2033', 'population' => 24510, 'latitude' => 36.7690, 'longitude' => 10.2330, 'niveau_risque' => 'moyen'],
            ['nom' => 'Den Den', 'gouvernorat' => 'La Manouba', 'code_postal' => '2011', 'population' => 29600, 'latitude' => 36.8060, 'longitude' => 10.1100, 'niveau_risque' => 'eleve'],
        ];

        $descriptions = [
            'eleve' => 'Quartier densément peuplé dont le réseau est régulièrement saturé en été. Les coupures y sont fréquentes lors des pics de chaleur.',
            'moyen' => 'Réseau fortement sollicité pendant les vagues de chaleur. Quelques délestages ponctuels sont à prévoir en été.',
            'faible' => 'Réseau de distribution récemment renforcé. Les coupures y sont rares et généralement de courte durée.',
        ];

        foreach ($zones as $zone) {
            Zone::create($zone + [
                'description' => "Secteur électrique de {$zone['nom']} ({$zone['gouvernorat']}). ".$descriptions[$zone['niveau_risque']],
            ]);
        }
    }
}
