@extends('layouts.front')

@section('title', 'Connexion')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <x-card title="Connexion">
                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf
                    <x-form.input name="email" type="email" label="Email" required autofocus />
                    <x-form.input name="password" type="password" label="Mot de passe" required />

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Se souvenir de moi</label>
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
