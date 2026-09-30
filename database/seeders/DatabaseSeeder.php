<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Comptes communs à l'équipe (mot de passe : "password")
        User::factory()->admin()->create([
            'name'  => 'Admin HeatAlert',
            'email' => 'admin@heatalert.tn',
        ]);

        User::factory()->create([
            'name'  => 'Citoyen Test',
            'email' => 'citoyen@heatalert.tn',
        ]);

        User::factory(10)->create();

        // Seeders des modules : chaque membre décommente/modifie UNIQUEMENT sa ligne.
        $this->call([
            // AlerteMeteoSeeder::class,      // 1. Alertes météo / canicule
            ZoneSeeder::class,              // 2. Coupures de courant — zones d'abord !
            CoupureSeeder::class,           // 2. Coupures de courant
            SignalementSeeder::class,       // 2. Coupures de courant — signalements des habitants
            CategoriePointSeeder::class,    // 3. Points de fraîcheur — catégories d'abord !
            PointFraicheurSeeder::class,    // 3. Points de fraîcheur
            // ConseilSeeder::class,           // 4. Conseils et prévention
            // EntraideSEEDER::class,          // 5. Entraide entre voisins
        ]);
    }
}
