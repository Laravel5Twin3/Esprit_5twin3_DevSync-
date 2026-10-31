@extends('layouts.front')

@section('title', 'Connexion')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <x-card title="Connexion">
                <x-auth.statut />

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf
                    <x-form.input name="email" type="email" label="Email" required autofocus autocomplete="username" />
                    <x-form.input name="password" type="password" label="Mot de passe" required autocomplete="current-password" />

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Se souvenir de moi</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="small">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn btn-ha w-100">Se connecter</button>
                </form>

                <x-slot:footer>
                    <span class="small">Pas encore de compte ? <a href="{{ route('register') }}">Inscrivez-vous</a></span>
                </x-slot:footer>
            </x-card>
        </div>
    </div>
@endsection
