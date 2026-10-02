<?php

namespace Database\Seeders;

use App\Models\NiveauVigilance;
use Illuminate\Database\Seeder;

class NiveauVigilanceSeeder extends Seeder
{
    /**
     * Les 4 niveaux de vigilance canicule (inspirés de l'INM / Météo-France).
     */
    public function run(): void
    {
        $niveaux = [
            ['nom' => 'Vert', 'couleur' => '#198754', 'temperature_min' => 0, 'temperature_max' => 34.9, 'ordre' => 1,
                'consigne' => 'Pas de vigilance particulière. Restez hydraté et suivez les prévisions.'],
            ['nom' => 'Jaune', 'couleur' => '#ffc107', 'temperature_min' => 35, 'temperature_max' => 38.9, 'ordre' => 2,
                'consigne' => 'Chaleur marquée. Buvez régulièrement, évitez les efforts entre 12h et 16h et fermez les volets en journée.'],
            ['nom' => 'Orange', 'couleur' => '#fd7e14', 'temperature_min' => 39, 'temperature_max' => 43.9, 'ordre' => 3,
                'consigne' => 'Canicule. Restez au frais, prenez des nouvelles des personnes âgées et limitez l\'usage des appareils énergivores aux heures de pointe.'],
            ['nom' => 'Rouge', 'couleur' => '#dc3545', 'temperature_min' => 44, 'temperature_max' => null, 'ordre' => 4,
                'consigne' => 'Canicule extrême. Évitez toute sortie entre 11h et 17h, rejoignez un point de fraîcheur et préparez-vous à d\'éventuelles coupures de courant.'],
        ];

        foreach ($niveaux as $niveau) {
            NiveauVigilance::create($niveau);
        }
    }
}
