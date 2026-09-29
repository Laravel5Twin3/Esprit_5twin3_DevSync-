<?php

namespace Database\Factories;

use App\Models\CategoriePoint;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriePointFactory extends Factory
{
    protected $model = CategoriePoint::class;

    private static array $categories = [
        ['nom' => 'Parc ombragé',        'icone' => 'bi-tree',           'couleur' => '#198754'],
        ['nom' => 'Salle climatisée',     'icone' => 'bi-building',       'couleur' => '#0d6efd'],
        ['nom' => 'Fontaine publique',    'icone' => 'bi-droplet',        'couleur' => '#0dcaf0'],
        ['nom' => 'Bibliothèque',         'icone' => 'bi-book',           'couleur' => '#6f42c1'],
        ['nom' => 'Centre commercial',    'icone' => 'bi-shop',           'couleur' => '#fd7e14'],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $cat = self::$categories[self::$index % count(self::$categories)];
        self::$index++;

        return [
            'nom'         => $cat['nom'],
            'icone'       => $cat['icone'],
            'couleur'     => $cat['couleur'],
            'description' => $this->faker->sentence(),
        ];
    }
}
