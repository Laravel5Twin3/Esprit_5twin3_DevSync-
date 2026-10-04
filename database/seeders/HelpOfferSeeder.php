<?php

namespace Database\Seeders;

use App\Models\HelpOffer;
use App\Models\User;
use Illuminate\Database\Seeder;

class HelpOfferSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 3) {
            $this->command->warn(
                'Il faut au moins 3 utilisateurs dans la table users.'
            );

            return;
        }

        HelpOffer::create([
            'user_id' => $users[0]->id,
            'title' => 'Je peux aider pour les courses',
            'description' => 'Je fais régulièrement mes courses au supermarché du quartier. Je peux aider une personne âgée ou une personne qui ne peut pas se déplacer.',
            'category' => 'courses',
            'location' => 'Centre-ville',
            'available_from' => now()->addDays(1)->setTime(9, 0),
            'available_until' => now()->addDays(1)->setTime(12, 0),
            'status' => 'active',
        ]);

        HelpOffer::create([
            'user_id' => $users[1]->id,
            'title' => 'Aide informatique',
            'description' => 'Je peux aider les voisins avec leurs ordinateurs, téléphones, imprimantes ou petits problèmes informatiques.',
            'category' => 'informatique',
            'location' => 'Centre-ville',
            'available_from' => now()->addDays(2)->setTime(14, 0),
            'available_until' => now()->addDays(2)->setTime(18, 0),
            'status' => 'active',
        ]);

        HelpOffer::create([
            'user_id' => $users[2]->id,
            'title' => 'Aide pour le jardinage',
            'description' => 'Je peux donner un coup de main pour entretenir un petit jardin ou aider à planter des fleurs et des plantes.',
            'category' => 'jardinage',
            'location' => 'El Habib',
            'available_from' => now()->addDays(3)->setTime(8, 0),
            'available_until' => now()->addDays(3)->setTime(11, 0),
            'status' => 'active',
        ]);

        HelpOffer::create([
            'user_id' => $users[0]->id,
            'title' => 'Aide pour déménagement',
            'description' => 'Je peux aider à porter des cartons et des meubles pour un petit déménagement.',
            'category' => 'demenagement',
            'location' => 'Sakiet Ezzit',
            'available_from' => now()->addDays(5)->setTime(9, 0),
            'available_until' => now()->addDays(5)->setTime(15, 0),
            'status' => 'active',
        ]);
    }
}
