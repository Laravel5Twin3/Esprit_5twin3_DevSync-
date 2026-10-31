<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

/**
 * Gestion des utilisateurs (tâche commune) : un test par critère de la grille.
 */
class AuthentificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake(fn () => Http::response('Service indisponible', 503));
    }

    /** Session où le mot de passe vient d'être confirmé (exigé pour la page profil). */
    private function motDePasseConfirme(): array
    {
        return ['auth.password_confirmed_at' => time()];
    }

    // ─── 1. Authentification de base ──────────────────────────────────────

    public function test_inscription_cree_un_citoyen_et_envoie_la_verification(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Sana Trabelsi',
            'email' => 'sana@example.tn',
            'phone' => '+216 22 333 444',
            'password' => 'chaleur2026',
            'password_confirmation' => 'chaleur2026',
        ])->assertRedirect();

        $user = User::where('email', 'sana@example.tn')->firstOrFail();
        $this->assertTrue($user->hasRole('citoyen'));
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_inscription_refuse_un_mot_de_passe_faible(): void
    {
        $this->post('/register', [
            'name' => 'Sana', 'email' => 'sana@example.tn',
            'password' => 'abc', 'password_confirmation' => 'abc',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_connexion_redirige_selon_le_role(): void
    {
        $admin = User::factory()->admin()->create();
        $citoyen = User::factory()->create();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect(route('admin.dashboard'));
        $this->post('/logout');
        $this->post('/login', ['email' => $citoyen->email, 'password' => 'password'])->assertRedirect(route('home'));
    }

    public function test_mauvais_mot_de_passe_affiche_une_erreur(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'faux'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ─── 2. Reset du mot de passe et vérification d'e-mail ─────────────────

    public function test_lien_de_reinitialisation_envoye_et_fonctionnel(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'nouveau2026',
                'password_confirmation' => 'nouveau2026',
            ])->assertSessionHasNoErrors();

            return true;
        });

        $this->post('/login', ['email' => $user->email, 'password' => 'nouveau2026'])->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_le_jeton_de_reinitialisation_expire(): void
    {
        $user = User::factory()->create();
        $jeton = Password::createToken($user);

        $this->travel(config('auth.passwords.users.expire') + 1)->minutes();

        $this->post('/reset-password', [
            'token' => $jeton,
            'email' => $user->email,
            'password' => 'nouveau2026',
            'password_confirmation' => 'nouveau2026',
        ])->assertSessionHasErrors('email');
    }

    public function test_email_non_verifie_bloque_les_pages_protegees(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get('/mes-signalements')->assertRedirect(route('verification.notice'));

        $lien = URL::temporarySignedRoute('verification.verify', now()->addHour(), [
            'id' => $user->id, 'hash' => sha1($user->email),
        ]);
        $this->actingAs($user)->get($lien)->assertRedirect();

        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->actingAs($user->fresh())->get('/mes-signalements')->assertOk();
    }

    // ─── 3. Rôles et permissions (Spatie) ─────────────────────────────────

    public function test_le_citoyen_n_accede_pas_au_back_office(): void
    {
        $citoyen = User::factory()->create();

        foreach (['/admin', '/admin/utilisateurs', '/admin/coupures'] as $uri) {
            $this->actingAs($citoyen)->get($uri)->assertForbidden();
        }
    }

    public function test_l_admin_change_le_role_d_un_utilisateur(): void
    {
        $admin = User::factory()->admin()->create();
        $citoyen = User::factory()->create();

        $this->actingAs($admin)->get('/admin/utilisateurs')->assertOk()->assertSee($citoyen->email);

        $this->actingAs($admin)->patch("/admin/utilisateurs/{$citoyen->id}/role", ['role' => 'admin'])->assertRedirect();
        $this->assertTrue($citoyen->fresh()->hasRole('admin'));

        // Un admin ne peut pas se retirer lui-même ses droits
        $this->actingAs($admin)->patch("/admin/utilisateurs/{$admin->id}/role", ['role' => 'citoyen'])->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    public function test_les_permissions_sont_verifiees_cote_serveur(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->roles->first()->revokePermissionTo('gerer coupures');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Toujours admin, mais sans la permission du module : refusé même en tapant l'URL
        $this->actingAs($admin->fresh())->get('/admin/coupures')->assertForbidden();
        $this->actingAs($admin->fresh())->get('/admin/zones')->assertForbidden();
    }

    // ─── 4. Double authentification (2FA) ─────────────────────────────────

    public function test_activation_et_connexion_avec_la_2fa(): void
    {
        $user = User::factory()->create();
        $google2fa = new Google2FA;

        // Activation puis confirmation avec un code valide
        $this->actingAs($user)->withSession($this->motDePasseConfirme())->post('/user/two-factor-authentication')->assertRedirect();
        $secret = decrypt($user->fresh()->two_factor_secret);
        $this->actingAs($user)->withSession($this->motDePasseConfirme())
            ->post('/user/confirmed-two-factor-authentication', ['code' => $google2fa->getCurrentOtp($secret)])
            ->assertSessionHasNoErrors();
        $this->assertTrue($user->fresh()->aActiveLa2fa());
        $this->assertCount(8, $user->fresh()->recoveryCodes());

        // Connexion : le mot de passe ne suffit plus, un code est demandé
        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/two-factor-challenge');
        $this->assertGuest();

        $this->post('/two-factor-challenge', ['code' => '000000'])->assertSessionHasErrors();
        $this->assertGuest();

        $this->post('/two-factor-challenge', ['recovery_code' => $user->fresh()->recoveryCodes()[0]])->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_desactivation_de_la_2fa(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession($this->motDePasseConfirme())->post('/user/two-factor-authentication');

        $this->actingAs($user)->withSession($this->motDePasseConfirme())->delete('/user/two-factor-authentication')->assertRedirect();

        $this->assertNull($user->fresh()->two_factor_secret);
    }

    // ─── 5. Profil utilisateur ────────────────────────────────────────────

    public function test_la_page_profil_redemande_le_mot_de_passe(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profil')->assertRedirect(route('password.confirm'));
        $this->actingAs($user)->withSession($this->motDePasseConfirme())->get('/profil')->assertOk()->assertSee($user->email);
    }

    public function test_modification_des_informations_et_de_la_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Nouveau Nom',
            'email' => $user->email,
            'phone' => '+216 98 765 432',
            'photo' => UploadedFile::fake()->image('moi.jpg', 200, 200),
        ])->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('Nouveau Nom', $user->name);
        Storage::disk('public')->assertExists($user->photo_path);

        // Un fichier qui n'est pas une image est refusé
        $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Nouveau Nom', 'email' => $user->email,
            'photo' => UploadedFile::fake()->create('virus.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrorsIn('updateProfileInformation', 'photo');
    }

    public function test_changer_d_email_impose_une_nouvelle_verification(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->put('/user/profile-information', ['name' => $user->name, 'email' => 'nouvelle@example.tn']);

        $this->assertNull($user->fresh()->email_verified_at);
        Notification::assertSentTo($user->fresh(), VerifyEmail::class);
    }

    public function test_modification_du_mot_de_passe(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/user/password', [
            'current_password' => 'mauvais', 'password' => 'nouveau2026', 'password_confirmation' => 'nouveau2026',
        ])->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->actingAs($user)->put('/user/password', [
            'current_password' => 'password', 'password' => 'nouveau2026', 'password_confirmation' => 'nouveau2026',
        ])->assertSessionHasNoErrors();
    }

    public function test_suppression_du_compte(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete('/profil', ['password' => 'mauvais', 'confirmation' => '1'])
            ->assertSessionHasErrorsIn('suppressionCompte', 'password');
        $this->assertModelExists($user);

        $this->actingAs($user)->delete('/profil', ['password' => 'password', 'confirmation' => '1'])->assertRedirect(route('home'));
        $this->assertModelMissing($user);
        $this->assertGuest();
    }
}
