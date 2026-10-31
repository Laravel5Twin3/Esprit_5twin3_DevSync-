@extends('layouts.front')

@section('title', 'Mon profil')

@section('content')
    @php
        $erreursProfil = $errors->getBag('updateProfileInformation');
        $erreursMdp = $errors->getBag('updatePassword');
        $erreurs2fa = $errors->getBag('confirmTwoFactorAuthentication');
        $erreursSuppression = $errors->getBag('suppressionCompte');
    @endphp

    <div class="d-flex align-items-center gap-3 mb-4">
        <x-avatar :user="$user" size="64" />
        <div>
            <h1 class="h3 mb-0">{{ $user->name }}</h1>
            <span class="text-muted">{{ $user->email }}</span>
            @foreach ($user->getRoleNames() as $role)
                <span class="badge {{ $role === 'admin' ? 'bg-danger' : 'bg-secondary' }} ms-1">{{ ucfirst($role) }}</span>
            @endforeach
        </div>
    </div>

    <x-auth.statut />

    <div class="row g-4">
        <div class="col-lg-7 d-flex flex-column gap-4">

            {{-- 1. Informations et photo --}}
            <x-card title="Informations personnelles">
                <form method="POST" action="{{ route('user-profile-information.update') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="d-flex align-items-center gap-3 mb-3">
                        <x-avatar :user="$user" size="72" />
                        <div class="flex-grow-1">
                            <label for="photo" class="form-label">Photo de profil</label>
                            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp"
                                   class="form-control @if ($erreursProfil->has('photo')) is-invalid @endif">
                            @if ($erreursProfil->has('photo'))
                                <div class="invalid-feedback">{{ $erreursProfil->first('photo') }}</div>
                            @else
                                <div class="form-text">JPG, PNG ou WebP, 2 Mo maximum.</div>
                            @endif
                            @if ($user->photo_path)
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="supprimer_photo" value="1" id="supprimer_photo">
                                    <label class="form-check-label small" for="supprimer_photo">Supprimer la photo actuelle</label>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nom complet</label>
                        <input type="text" id="name" name="name" autocomplete="name" value="{{ old('name', $user->name) }}"
                               class="form-control @if ($erreursProfil->has('name')) is-invalid @endif">
                        @if ($erreursProfil->has('name'))
                            <div class="invalid-feedback">{{ $erreursProfil->first('name') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" autocomplete="email" value="{{ old('email', $user->email) }}"
                               class="form-control @if ($erreursProfil->has('email')) is-invalid @endif">
                        @if ($erreursProfil->has('email'))
                            <div class="invalid-feedback">{{ $erreursProfil->first('email') }}</div>
                        @endif
                        <div class="form-text">Si vous changez d'adresse, vous devrez la confirmer par e-mail.</div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="tel" id="phone" name="phone" autocomplete="tel" value="{{ old('phone', $user->phone) }}"
                               class="form-control @if ($erreursProfil->has('phone')) is-invalid @endif">
                        @if ($erreursProfil->has('phone'))
                            <div class="invalid-feedback">{{ $erreursProfil->first('phone') }}</div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-ha"><i class="bi bi-check-lg"></i> Enregistrer</button>
                </form>
            </x-card>

            {{-- 2. Mot de passe --}}
            <x-card title="Mot de passe">
                <form method="POST" action="{{ route('user-password.update') }}" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                               class="form-control @if ($erreursMdp->has('current_password')) is-invalid @endif">
                        @if ($erreursMdp->has('current_password'))
                            <div class="invalid-feedback">{{ $erreursMdp->first('current_password') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" id="password" name="password" autocomplete="new-password"
                               class="form-control @if ($erreursMdp->has('password')) is-invalid @endif">
                        @if ($erreursMdp->has('password'))
                            <div class="invalid-feedback">{{ $erreursMdp->first('password') }}</div>
                        @endif
                        <div class="form-text">8 caractères minimum, avec des lettres et des chiffres.</div>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmation</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                               class="form-control @if ($erreursMdp->has('password_confirmation')) is-invalid @endif">
                        @if ($erreursMdp->has('password_confirmation'))
                            <div class="invalid-feedback">{{ $erreursMdp->first('password_confirmation') }}</div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-ha"><i class="bi bi-key"></i> Changer le mot de passe</button>
                </form>
            </x-card>
        </div>

        <div class="col-lg-5 d-flex flex-column gap-4">

            {{-- 3. Double authentification --}}
            <x-card title="Double authentification (2FA)">
                @if (! $user->two_factor_secret)
                    <p class="text-muted small">
                        Protégez votre compte avec un code à 6 chiffres généré par une application
                        (Google Authenticator, Microsoft Authenticator…), demandé à chaque connexion.
                    </p>
                    <span class="badge bg-secondary mb-3">Désactivée</span>
                    <form method="POST" action="{{ route('two-factor.enable') }}">
                        @csrf
                        <button type="submit" class="btn btn-ha w-100"><i class="bi bi-shield-lock"></i> Activer la 2FA</button>
                    </form>

                @elseif (! $user->two_factor_confirmed_at)
                    <p class="small"><strong>1.</strong> Scannez ce QR code avec votre application d'authentification :</p>
                    <div class="text-center bg-white p-3 rounded border mb-2">{!! $user->twoFactorQrCodeSvg() !!}</div>
                    <p class="small text-muted text-break">Ou saisissez la clé : <code>{{ decrypt($user->two_factor_secret) }}</code></p>

                    <p class="small"><strong>2.</strong> Saisissez le code affiché pour confirmer :</p>
                    <form method="POST" action="{{ route('two-factor.confirm') }}" novalidate>
                        @csrf
                        <input type="text" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6"
                               class="form-control mb-2 @if ($erreurs2fa->has('code')) is-invalid @endif" placeholder="123456" aria-label="Code à 6 chiffres">
                        @if ($erreurs2fa->has('code'))
                            <div class="invalid-feedback d-block mb-2">Code incorrect, réessayez avec le code actuellement affiché.</div>
                        @endif
                        <button type="submit" class="btn btn-ha w-100">Confirmer l'activation</button>
                    </form>
                    <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link btn-sm w-100">Annuler</button>
                    </form>

                @else
                    <span class="badge bg-success mb-3"><i class="bi bi-shield-check"></i> Activée</span>
                    <p class="small text-muted">
                        Codes de récupération : chacun permet de se connecter une fois si vous perdez votre téléphone.
                        Conservez-les en lieu sûr.
                    </p>
                    <ul class="list-unstyled font-monospace small bg-body-tertiary rounded p-3 mb-3 row row-cols-2 g-1 mx-0">
                        @foreach ($user->recoveryCodes() as $code)
                            <li class="col">{{ $code }}</li>
                        @endforeach
                    </ul>
                    <div class="d-flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-repeat"></i> Nouveaux codes</button>
                        </form>
                        <form method="POST" action="{{ route('two-factor.disable') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-shield-x"></i> Désactiver la 2FA</button>
                        </form>
                    </div>
                @endif
            </x-card>

            {{-- 4. Suppression du compte --}}
            <x-card title="Supprimer mon compte" class="border border-danger-subtle">
                <p class="small text-muted">
                    Votre compte, votre photo et vos publications seront définitivement supprimés.
                    Cette action est irréversible.
                </p>
                <form method="POST" action="{{ route('profil.destroy') }}" novalidate>
                    @csrf
                    @method('DELETE')
                    <div class="mb-2">
                        <label for="password_suppression" class="form-label small">Mot de passe</label>
                        <input type="password" id="password_suppression" name="password" autocomplete="current-password"
                               class="form-control @if ($erreursSuppression->has('password')) is-invalid @endif">
                        @if ($erreursSuppression->has('password'))
                            <div class="invalid-feedback">{{ $erreursSuppression->first('password') }}</div>
                        @endif
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input @if ($erreursSuppression->has('confirmation')) is-invalid @endif"
                               type="checkbox" name="confirmation" value="1" id="confirmation_suppression">
                        <label class="form-check-label small" for="confirmation_suppression">Je comprends que la suppression est définitive</label>
                        @if ($erreursSuppression->has('confirmation'))
                            <div class="invalid-feedback">{{ $erreursSuppression->first('confirmation') }}</div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-danger w-100"><i class="bi bi-trash"></i> Supprimer définitivement</button>
                </form>
            </x-card>
        </div>
    </div>
@endsection
