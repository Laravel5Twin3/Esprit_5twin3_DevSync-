@extends('layouts.front')

@section('title', 'Double authentification')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <x-card title="Double authentification">
                {{-- Par défaut : code à 6 chiffres ; « recuperation » dans l'URL ou après erreur : code de récupération --}}
                @php($recuperation = request()->has('recuperation') || $errors->has('recovery_code'))

                @if ($recuperation)
                    <p class="text-muted small">Saisissez l'un de vos codes de récupération (chaque code ne sert qu'une fois).</p>
                    <form method="POST" action="{{ route('two-factor.login') }}" novalidate>
                        @csrf
                        <x-form.input name="recovery_code" label="Code de récupération" required autofocus autocomplete="one-time-code" />
                        <button type="submit" class="btn btn-ha w-100">Valider</button>
                    </form>
                    <a href="{{ route('two-factor.login') }}" class="small d-block mt-3">Utiliser plutôt le code de l'application</a>
                @else
                    <p class="text-muted small">Ouvrez votre application d'authentification (Google Authenticator, Microsoft Authenticator…) et saisissez le code à 6 chiffres.</p>
                    <form method="POST" action="{{ route('two-factor.login') }}" novalidate>
                        @csrf
                        <x-form.input name="code" label="Code" required autofocus inputmode="numeric" autocomplete="one-time-code" maxlength="6" />
                        <button type="submit" class="btn btn-ha w-100">Valider</button>
                    </form>
                    <a href="{{ route('two-factor.login', ['recuperation' => 1]) }}" class="small d-block mt-3">Je n'ai pas accès à mon téléphone : utiliser un code de récupération</a>
                @endif
            </x-card>
        </div>
    </div>
@endsection
