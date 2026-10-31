{{-- Messages de confirmation renvoyés par Fortify (session « status »), traduits en français. --}}
@php
    $messages = [
        'verification-link-sent' => 'Un nouveau lien de vérification vient d\'être envoyé à votre adresse e-mail.',
        'profile-information-updated' => 'Vos informations ont été mises à jour.',
        'password-updated' => 'Votre mot de passe a été modifié.',
        'two-factor-authentication-enabled' => 'Scannez le QR code puis saisissez le code affiché par votre application pour terminer l\'activation.',
        'two-factor-authentication-confirmed' => 'La double authentification est activée. Gardez vos codes de récupération en lieu sûr.',
        'two-factor-authentication-disabled' => 'La double authentification est désactivée.',
        'recovery-codes-generated' => 'De nouveaux codes de récupération ont été générés ; les anciens ne fonctionnent plus.',
    ];
    $statut = session('status');
@endphp

@if ($statut)
    <div class="alert alert-success" role="status">
        <i class="bi bi-check-circle"></i> {{ $messages[$statut] ?? $statut }}
    </div>
@endif
