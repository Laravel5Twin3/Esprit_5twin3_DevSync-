@extends('layouts.front')

@section('title', 'Inscription')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <x-card title="Créer un compte">
                <form method="POST" action="{{ route('register') }}" novalidate>
                    @csrf
                    <x-form.input name="name" label="Nom complet" required autofocus />
                    <x-form.input name="email" type="email" label="Email" required />
                    <x-form.input name="phone" label="Téléphone" help="Optionnel — pour recevoir les alertes." />
                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="password" type="password" label="Mot de passe" required
                                          help="8 caractères min., lettres et chiffres." />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="password_confirmation" type="password" label="Confirmation" required />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-ha w-100">S'inscrire</button>
                </form>

                <x-slot:footer>
                    <span class="small">Déjà inscrit ? <a href="{{ route('login') }}">Connectez-vous</a></span>
                </x-slot:footer>
            </x-card>
        </div>
    </div>
@endsection
