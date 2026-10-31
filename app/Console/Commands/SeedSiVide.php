<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Utilisée au démarrage du conteneur Docker : remplit la base avec les données
 * de démonstration uniquement si elle est vide (premier déploiement).
 */
class SeedSiVide extends Command
{
    protected $signature = 'app:seed-si-vide';

    protected $description = 'Lance les seeders seulement si la base ne contient encore aucun utilisateur';

    public function handle(): int
    {
        if (User::exists()) {
            $this->info('Base déjà remplie : seeders ignorés.');

            return self::SUCCESS;
        }

        $this->info('Base vide : chargement des données de démonstration…');

        return $this->call('db:seed', ['--force' => true]);
    }
}
