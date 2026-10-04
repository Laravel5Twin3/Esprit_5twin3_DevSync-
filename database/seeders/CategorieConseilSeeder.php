<?php

namespace Database\Seeders;

use App\Models\CategorieConseil;
use Illuminate\Database\Seeder;

class CategorieConseilSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nom'         => 'Hydratation et santé',
                'icone'       => 'bi-droplet',
                'couleur'     => '#0dcaf0',
                'description' => 'Boire, se rafraîchir et reconnaître les signes de coup de chaleur.',
            ],
            [
                'nom'         => 'Économie d\'énergie',
                'icone'       => 'bi-lightning-charge',
                'couleur'     => '#fd7e14',
                'description' => 'Réduire la consommation électrique pendant les pics de chaleur.',
            ],
            [
                'nom'         => 'Coupures de courant',
                'icone'       => 'bi-plug',
                'couleur'     => '#6f42c1',
                'description' => 'Se préparer aux délestages et protéger les équipements sensibles.',
            ],
            [
                'nom'         => 'Personnes vulnérables',
                'icone'       => 'bi-heart-pulse',
                'couleur'     => '#dc3545',
                'description' => 'Gestes de prévention pour enfants, personnes âgées et malades chroniques.',
            ],
            [
                'nom'         => 'Habitat et équipements',
                'icone'       => 'bi-house-gear',
                'couleur'     => '#198754',
                'description' => 'Rafraîchir le logement et utiliser climatisation et ventilateurs sans danger.',
            ],
        ];

        foreach ($categories as $cat) {
            CategorieConseil::firstOrCreate(['nom' => $cat['nom']], $cat);
        }
    }
}
