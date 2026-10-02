<?php

namespace Database\Seeders;

use App\Models\AlerteMeteo;
use App\Models\NiveauVigilance;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class AlerteMeteoSeeder extends Seeder
{
    /**
     * Crée les niveaux de vigilance puis des alertes rattachées aux zones existantes.
     * À lancer APRÈS ZoneSeeder (module Coupures).
     */
    public function run(): void
    {
        if (NiveauVigilance::count() === 0) {
            $this->call(NiveauVigilanceSeeder::class);
        }

        if (Zone::count() === 0) {
            Zone::factory(5)->create();
        }

        // Pour chaque zone : 1 alerte en cours, 1 à venir et 1 terminée
        Zone::all()->each(function (Zone $zone) {
            AlerteMeteo::factory()->enCours()->create(['zone_id' => $zone->id]);
            AlerteMeteo::factory()->create([
                'zone_id' => $zone->id,
                'date_debut' => now()->addDays(rand(1, 5)),
                'date_fin' => now()->addDays(rand(6, 9)),
            ]);
            AlerteMeteo::factory()->terminee()->create(['zone_id' => $zone->id]);
        });

        // Quelques alertes réalistes pour la démo
        $laMarsa = Zone::where('nom', 'La Marsa')->first();
        if ($laMarsa) {
            $temp = 45.5;
            AlerteMeteo::create([
                'titre' => 'Canicule extrême sur le littoral nord',
                'temperature_max' => $temp,
                'date_debut' => now()->subHours(6),
                'date_fin' => now()->addDays(2),
                'message' => 'Un épisode de sirocco provoque des températures exceptionnelles. Évitez toute sortie aux heures chaudes et rejoignez un point de fraîcheur si votre logement n\'est pas climatisé.',
                'niveau_vigilance_id' => NiveauVigilance::pourTemperature($temp)->id,
                'zone_id' => $laMarsa->id,
            ]);
        }
    }
}
