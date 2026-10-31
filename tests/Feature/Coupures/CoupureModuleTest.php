<?php

namespace Tests\Feature\Coupures;

use App\Models\Coupure;
use App\Models\Signalement;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Module Coupures de courant : CRUD Zone / Coupure, validation et signalements.
 */
class CoupureModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(fn () => Http::response('Service indisponible', 503));
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_cree_une_zone(): void
    {
        $this->actingAs($this->admin)->post('/admin/zones', [
            'nom' => 'La Marsa',
            'gouvernorat' => 'Tunis',
            'code_postal' => '2070',
            'niveau_risque' => 'moyen',
        ])->assertRedirect();

        $this->assertDatabaseHas('zones', ['nom' => 'La Marsa', 'code_postal' => '2070']);
    }

    public function test_zone_invalide_est_refusee(): void
    {
        Zone::factory()->create(['nom' => 'La Marsa']);

        $this->actingAs($this->admin)->post('/admin/zones', [
            'nom' => 'La Marsa',          // doublon
            'gouvernorat' => 'Paris',     // hors liste
            'code_postal' => '12a',       // pas 4 chiffres
            'niveau_risque' => 'moyen',
        ])->assertSessionHasErrors(['nom', 'gouvernorat', 'code_postal']);

        $this->assertDatabaseCount('zones', 1);
    }

    public function test_admin_cree_une_coupure_prevue(): void
    {
        $zone = Zone::factory()->create();

        $this->actingAs($this->admin)->post('/admin/coupures', [
            'zone_id' => $zone->id,
            'titre' => 'Délestage programmé',
            'type' => 'delestage',
            'statut' => 'prevue',
            'date_debut' => now()->addDay()->format('Y-m-d\TH:i'),
            'date_fin' => now()->addDay()->addHours(2)->format('Y-m-d\TH:i'),
        ])->assertRedirect();

        $this->assertDatabaseHas('coupures', ['zone_id' => $zone->id, 'statut' => 'prevue']);
    }

    public function test_regles_de_dates_selon_le_statut(): void
    {
        $zone = Zone::factory()->create();

        // Une coupure prévue ne peut pas commencer dans le passé, et la fin doit suivre le début
        $this->actingAs($this->admin)->post('/admin/coupures', [
            'zone_id' => $zone->id,
            'titre' => 'Coupure invalide',
            'type' => 'panne',
            'statut' => 'prevue',
            'date_debut' => now()->subDay()->format('Y-m-d\TH:i'),
            'date_fin' => now()->subDays(2)->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors(['date_debut', 'date_fin']);

        // Une coupure résolue doit avoir une date de fin
        $this->actingAs($this->admin)->post('/admin/coupures', [
            'zone_id' => $zone->id,
            'titre' => 'Panne résolue',
            'type' => 'panne',
            'statut' => 'resolue',
            'date_debut' => now()->subHours(3)->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors('date_fin');

        $this->assertDatabaseCount('coupures', 0);
    }

    public function test_zone_avec_coupures_non_supprimable(): void
    {
        $zone = Zone::factory()->create();
        Coupure::factory()->for($zone)->create();

        $this->actingAs($this->admin)->delete("/admin/zones/{$zone->id}")->assertSessionHas('error');

        $this->assertDatabaseHas('zones', ['id' => $zone->id]);
    }

    public function test_habitant_signale_et_admin_valide(): void
    {
        $zone = Zone::factory()->create();
        $habitant = User::factory()->create();

        $this->actingAs($habitant)->post('/coupures/signaler', [
            'zone_id' => $zone->id,
            'date_constat' => now()->subMinutes(10)->format('Y-m-d\TH:i'),
            'description' => 'Plus de courant dans tout l\'immeuble.',
        ])->assertRedirect('/mes-signalements');

        $signalement = Signalement::firstOrFail();
        $this->assertSame('en_attente', $signalement->statut);

        // Doublon dans l'heure refusé
        $this->actingAs($habitant)->post('/coupures/signaler', [
            'zone_id' => $zone->id,
            'date_constat' => now()->subMinutes(5)->format('Y-m-d\TH:i'),
            'description' => 'Toujours pas de courant ici.',
        ])->assertSessionHasErrors('zone_id');

        // Validation par l'admin : une coupure en cours est créée et rattachée
        $this->actingAs($this->admin)->post("/admin/signalements/{$signalement->id}/valider")->assertRedirect();

        $signalement->refresh();
        $this->assertSame('valide', $signalement->statut);
        $this->assertSame('en_cours', $signalement->coupure->statut);
    }
}
