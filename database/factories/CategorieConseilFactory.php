<?php

namespace Database\Factories;

use App\Models\CategorieConseil;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategorieConseilFactory extends Factory
{
    protected $model = CategorieConseil::class;

    private static array $categories = [
        ['nom' => 'Hydratation et santé',     'icone' => 'bi-droplet',           'couleur' => '#0dcaf0'],
        ['nom' => 'Économie d\'énergie',      'icone' => 'bi-lightning-charge',  'couleur' => '#fd7e14'],
        ['nom' => 'Coupures de courant',      'icone' => 'bi-plug',              'couleur' => '#6f42c1'],
        ['nom' => 'Personnes vulnérables',    'icone' => 'bi-heart-pulse',       'couleur' => '#dc3545'],
        ['nom' => 'Habitat et équipements',   'icone' => 'bi-house-gear',        'couleur' => '#198754'],
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
