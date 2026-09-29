<?php

namespace Database\Seeders;

use App\Models\CategoriePoint;
use App\Models\PointFraicheur;
use Illuminate\Database\Seeder;

class PointFraicheurSeeder extends Seeder
{
    public function run(): void
    {
        // Points réels de Tunis pour la démo (données réalistes)
        $pointsReels = [
            // Parcs
            [
                'categorie' => 'Parc ombragé',
                'nom'       => 'Parc du Belvédère',
                'adresse'   => 'Avenue Farhat Hached, Tunis',
                'latitude'  => 36.8287, 'longitude' => 10.1733,
                'horaires'  => '07h00 - 21h00',
                'capacite'  => 2000,
                'actif'     => true,
                'description' => 'Le plus grand parc de Tunis avec lac et zoo. Arbres centenaires offrant une ombre dense.',
            ],
            [
                'categorie' => 'Parc ombragé',
                'nom'       => 'Jardin de la Marsa',
                'adresse'   => 'Avenue Habib Bourguiba, La Marsa',
                'latitude'  => 36.8884, 'longitude' => 10.3236,
                'horaires'  => '08h00 - 20h00',
                'capacite'  => 500,
                'actif'     => true,
                'description' => 'Jardin public ombragé en bord de mer.',
            ],
            // Salles climatisées
            [
                'categorie' => 'Salle climatisée',
                'nom'       => 'Bibliothèque Nationale de Tunis',
                'adresse'   => '20 Souk El Attarine, Médina, Tunis',
                'latitude'  => 36.7981, 'longitude' => 10.1714,
                'horaires'  => '08h30 - 17h30',
                'capacite'  => 300,
                'actif'     => true,
                'description' => 'Salle de lecture climatisée ouverte au public.',
            ],
            [
                'categorie' => 'Salle climatisée',
                'nom'       => 'Centre Culturel Ibn Rachiq',
                'adresse'   => 'Rue Ibn Rachiq, Tunis',
                'latitude'  => 36.8122, 'longitude' => 10.1789,
                'horaires'  => '09h00 - 18h00',
                'capacite'  => 150,
                'actif'     => true,
                'description' => 'Centre culturel climatisé avec salles d\'exposition.',
            ],
            // Fontaines
            [
                'categorie' => 'Fontaine publique',
                'nom'       => 'Fontaine Avenue Habib Bourguiba',
                'adresse'   => 'Avenue Habib Bourguiba, Tunis Centre',
                'latitude'  => 36.8024, 'longitude' => 10.1812,
                'horaires'  => '24h/24',
                'capacite'  => null,
                'actif'     => true,
                'description' => 'Fontaine décorative avec eau potable accessible toute la journée.',
            ],
            [
                'categorie' => 'Fontaine publique',
                'nom'       => 'Fontaine Place de la Kasbah',
                'adresse'   => 'Place de la Kasbah, Tunis',
                'latitude'  => 36.7985, 'longitude' => 10.1688,
                'horaires'  => '24h/24',
                'capacite'  => null,
                'actif'     => true,
                'description' => 'Fontaine publique historique au cœur de la Médina.',
            ],
            // Bibliothèques
            [
                'categorie' => 'Bibliothèque',
                'nom'       => 'Bibliothèque Municipale de Tunis',
                'adresse'   => 'Rue de Rome, Tunis',
                'latitude'  => 36.8044, 'longitude' => 10.1799,
                'horaires'  => '08h00 - 17h00',
                'capacite'  => 200,
                'actif'     => true,
                'description' => 'Bibliothèque municipale climatisée avec espace de lecture.',
            ],
            // Centres commerciaux
            [
                'categorie' => 'Centre commercial',
                'nom'       => 'City Centre Tunis',
                'adresse'   => 'Route de la Marsa, Les Berges du Lac, Tunis',
                'latitude'  => 36.8420, 'longitude' => 10.2344,
                'horaires'  => '10h00 - 22h00',
                'capacite'  => 5000,
                'actif'     => true,
                'description' => 'Grand centre commercial entièrement climatisé avec espace de repos.',
            ],
        ];

        foreach ($pointsReels as $data) {
            $categorie = CategoriePoint::where('nom', $data['categorie'])->first();
            if (!$categorie) continue;

            PointFraicheur::firstOrCreate(
                ['nom' => $data['nom']],
                [
                    'categorie_point_id' => $categorie->id,
                    'adresse'            => $data['adresse'],
                    'latitude'           => $data['latitude'],
                    'longitude'          => $data['longitude'],
                    'horaires'           => $data['horaires'],
                    'capacite'           => $data['capacite'],
                    'actif'              => $data['actif'],
                    'description'        => $data['description'],
                ]
            );
        }

        // Points aléatoires supplémentaires via Factory (avec relations)
        CategoriePoint::all()->each(function (CategoriePoint $cat) {
            PointFraicheur::factory()
                ->count(3)
                ->create(['categorie_point_id' => $cat->id, 'actif' => true]);
        });
    }
}
