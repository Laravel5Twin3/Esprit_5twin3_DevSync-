<?php

namespace Database\Seeders;

use App\Models\CategoriePoint;
use Illuminate\Database\Seeder;

class CategoriePointSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nom'         => 'Parc ombragé',
                'icone'       => 'bi-tree',
                'couleur'     => '#198754',
                'description' => 'Espaces verts avec arbres et zones d\'ombre naturelle.',
            ],
            [
                'nom'         => 'Salle climatisée',
                'icone'       => 'bi-building',
                'couleur'     => '#0d6efd',
                'description' => 'Bâtiments publics équipés de climatisation (mairies, bibliothèques, centres culturels…).',
            ],
            [
                'nom'         => 'Fontaine publique',
                'icone'       => 'bi-droplet',
                'couleur'     => '#0dcaf0',
                'description' => 'Points d\'eau publics accessibles à tous.',
            ],
            [
                'nom'         => 'Bibliothèque',
                'icone'       => 'bi-book',
                'couleur'     => '#6f42c1',
                'description' => 'Bibliothèques et médiathèques climatisées.',
            ],
            [
                'nom'         => 'Centre commercial',
                'icone'       => 'bi-shop',
                'couleur'     => '#fd7e14',
                'description' => 'Centres commerciaux ouverts au public offrant de la fraîcheur.',
            ],
        ];

        foreach ($categories as $cat) {
            CategoriePoint::firstOrCreate(['nom' => $cat['nom']], $cat);
        }
    }
}
