@extends('layouts.front')

@section('title', 'Nouveau mot de passe')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <x-card title="Choisir un nouveau mot de passe">
                <form method="POST" action="{{ route('password.update') }}" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <x-form.input name="email" type="email" label="Email" :value="$request->email" required autocomplete="username" />
                    <x-form.input name="password" type="password" label="Nouveau mot de passe" required autofocus
                                  autocomplete="new-password" help="8 caractères min., lettres et chiffres." />
                    <x-form.input name="password_confirmation" type="password" label="Confirmation" required autocomplete="new-password" />

                    <button type="submit" class="btn btn-ha w-100">Enregistrer le mot de passe</button>
                </form>
            </x-card>
        </div>
    </div>
@endsection
