<?php

namespace Database\Seeders;

use App\Models\HelpRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class HelpRequestSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        // Les demandes viennent d'autres voisins que ceux qui proposent les offres (HelpOfferSeeder),
        // sinon la mise en relation n'aurait personne à suggérer.

        if ($users->count() < 7) {
            $this->command->warn(
                'Il faut au moins 7 utilisateurs dans la table users.'
            );

            return;
        }

        HelpRequest::create([
            'user_id' => $users[3]->id,
            'title' => 'Besoin d’aide pour déplacer un meuble',
            'description' => 'J’ai besoin d’aide pour déplacer une petite armoire de mon appartement vers une autre pièce. Deux personnes seraient idéales.',
            'category' => 'demenagement',
            'location' => 'Centre-ville',
            'needed_from' => now()->addDays(2)->setTime(10, 0),
            'needed_until' => now()->addDays(2)->setTime(12, 0),
            'status' => 'open',
        ]);

        HelpRequest::create([
            'user_id' => $users[4]->id,
            'title' => 'Besoin d’aide pour faire des courses',
            'description' => 'Je ne peux pas me déplacer cette semaine et j’aurais besoin de quelqu’un pour acheter quelques produits alimentaires.',
            'category' => 'courses',
            'location' => 'Centre-ville',
            'needed_from' => now()->addDays(1)->setTime(9, 0),
            'needed_until' => now()->addDays(1)->setTime(13, 0),
            'status' => 'open',
        ]);

        HelpRequest::create([
            'user_id' => $users[5]->id,
            'title' => 'Besoin d’aide avec mon ordinateur',
            'description' => 'Mon ordinateur ne démarre plus correctement. J’aurais besoin de quelqu’un pour m’aider à identifier le problème.',
            'category' => 'informatique',
            'location' => 'El Habib',
            'needed_from' => now()->addDays(3)->setTime(15, 0),
            'needed_until' => now()->addDays(3)->setTime(18, 0),
            'status' => 'open',
        ]);

        HelpRequest::create([
            'user_id' => $users[6]->id,
            'title' => 'Besoin d’aide pour le jardin',
            'description' => 'Je cherche quelqu’un pour m’aider à nettoyer et préparer mon petit jardin.',
            'category' => 'jardinage',
            'location' => 'Sakiet Ezzit',
            'needed_from' => now()->addDays(4)->setTime(8, 0),
            'needed_until' => now()->addDays(4)->setTime(11, 0),
            'status' => 'open',
        ]);
    }
}
