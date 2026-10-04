<?php

namespace Database\Factories;

use App\Models\CategorieConseil;
use App\Models\Conseil;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConseilFactory extends Factory
{
    protected $model = Conseil::class;

    public function definition(): array
    {
        $titre = $this->faker->sentence(6);

        return [
            'categorie_conseil_id' => CategorieConseil::inRandomOrder()->first()?->id
                                    ?? CategorieConseil::factory(),
            'titre'         => rtrim($titre, '.'),
            'resume'        => $this->faker->sentence(12),
            'contenu'       => $this->faker->paragraphs(3, true),
            'public_cible'  => $this->faker->randomElement(array_keys(Conseil::PUBLICS)),
            'priorite'      => $this->faker->randomElement(array_keys(Conseil::PRIORITES)),
            'actif'         => $this->faker->boolean(90),
        ];
    }

    public function actif(): static
    {
        return $this->state(['actif' => true]);
    }
}
