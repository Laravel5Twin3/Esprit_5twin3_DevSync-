<?php

namespace Database\Seeders;

use App\Models\Coupure;
use App\Models\Signalement;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class SignalementSeeder extends Seeder
{
    /**
     * Signalements d'habitants (CoupureSeeder doit passer avant).
     */
    public function run(): void
    {
        $habitants = User::where('role', 'citoyen')->get();

        // Coupures en cours : plusieurs habitants les ont signalées, l'admin a validé
        Coupure::where('statut', 'en_cours')->get()->each(function (Coupure $coupure) use ($habitants) {
            foreach ($habitants->random(min(3, $habitants->count())) as $habitant) {
                Signalement::factory()->for($habitant)->for($coupure->zone)->create([
                    'coupure_id' => $coupure->id,
                    'statut' => 'valide',
                    'date_constat' => $coupure->date_debut->copy()->addMinutes(fake()->numberBetween(2, 40)),
                ]);
            }
        });

        // Nouveaux signalements en attente de vérification dans des zones sans coupure en cours
        Zone::whereDoesntHave('coupures', fn ($q) => $q->where('statut', 'en_cours'))
            ->inRandomOrder()
            ->limit(2)
            ->get()
            ->each(function (Zone $zone) use ($habitants) {
                $constat = now()->subMinutes(fake()->numberBetween(20, 90));
                foreach ($habitants->random(min(2, $habitants->count())) as $habitant) {
                    Signalement::factory()->for($habitant)->for($zone)->create([
                        'date_constat' => $constat->copy()->addMinutes(fake()->numberBetween(0, 25)),
                    ]);
                }
            });

        // Un signalement rejeté (fausse alerte)
        Signalement::factory()
            ->for($habitants->random())
            ->for(Zone::inRandomOrder()->first())
            ->create([
                'statut' => 'rejete',
                'date_constat' => now()->subHours(fake()->numberBetween(5, 15)),
                'description' => 'Plus de courant chez moi.',
            ]);
    }
}
