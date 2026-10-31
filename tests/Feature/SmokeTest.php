<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Test de fumée : chaque page de l'application (tous modules) doit s'afficher sans erreur
 * pour le bon profil, et les pages protégées doivent refuser les autres.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Pas d'appel réseau pendant les tests : les API externes (Open-Meteo…) sont simulées hors service
        Http::fake(fn () => Http::response('Service indisponible', 503));
    }

    public static function pagesPubliques(): array
    {
        return array_map(fn ($uri) => [$uri], [
            '/', '/login', '/register',
            '/alertes-meteo', '/alertes-meteo/previsions',
            '/coupures', '/coupures/previsions',
            '/points-fraicheur',
            '/conseils',
        ]);
    }

    public static function pagesConnectees(): array
    {
        return array_map(fn ($uri) => [$uri], [
            '/coupures/signaler', '/mes-signalements',
            '/help-requests', '/help-requests/create',
            '/help-offers', '/help-offers/create',
        ]);
    }

    public static function pagesAdmin(): array
    {
        return array_map(fn ($uri) => [$uri], [
            '/admin',
            '/admin/alertes-meteo', '/admin/alertes-meteo/create', '/admin/alertes-meteo/statistiques',
            '/admin/niveaux-vigilance', '/admin/niveaux-vigilance/create',
            '/admin/coupures', '/admin/coupures/create', '/admin/zones', '/admin/zones/create',
            '/admin/signalements', '/admin/coupures-ia',
            '/admin/points-fraicheur', '/admin/points-fraicheur/create',
            '/admin/categories-point', '/admin/categories-point/create',
            '/admin/conseils', '/admin/conseils/create',
            '/admin/categories-conseil', '/admin/categories-conseil/create',
            '/admin/entraide',
        ]);
    }

    #[DataProvider('pagesPubliques')]
    public function test_page_publique_accessible(string $uri): void
    {
        $this->get($uri)->assertOk();
    }

    #[DataProvider('pagesConnectees')]
    public function test_page_reservee_aux_connectes(string $uri): void
    {
        $this->get($uri)->assertRedirect('/login');

        $this->actingAs(User::factory()->create())->get($uri)->assertOk();
    }

    #[DataProvider('pagesAdmin')]
    public function test_page_admin_protegee(string $uri): void
    {
        $this->get($uri)->assertRedirect('/login');

        $this->actingAs(User::factory()->create())->get($uri)->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())->get($uri)->assertOk();
    }
}
