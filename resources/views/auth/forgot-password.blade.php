@extends('layouts.front')

@section('title', 'Mot de passe oublié')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <x-card title="Mot de passe oublié">
                <p class="text-muted small">
                    Indiquez l'adresse e-mail de votre compte : nous vous envoyons un lien pour choisir un nouveau mot de passe.
                    Le lien est valable {{ config('auth.passwords.users.expire') }} minutes.
                </p>

                <x-auth.statut />

                <form method="POST" action="{{ route('password.email') }}" novalidate>
                    @csrf
                    <x-form.input name="email" type="email" label="Email" required autofocus autocomplete="username" />
                    <button type="submit" class="btn btn-ha w-100">Envoyer le lien</button>
                </form>

                <x-slot:footer>
                    <a href="{{ route('login') }}" class="small"><i class="bi bi-arrow-left"></i> Retour à la connexion</a>
                </x-slot:footer>
            </x-card>
        </div>
    </div>
@endsection
