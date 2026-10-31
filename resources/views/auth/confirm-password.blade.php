@extends('layouts.front')

@section('title', 'Confirmer le mot de passe')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <x-card title="Confirmez votre mot de passe">
                <p class="text-muted small">Cette action touche à la sécurité de votre compte : merci de saisir à nouveau votre mot de passe.</p>
                <form method="POST" action="{{ route('password.confirm.store') }}" novalidate>
                    @csrf
                    <x-form.input name="password" type="password" label="Mot de passe" required autofocus autocomplete="current-password" />
                    <button type="submit" class="btn btn-ha w-100">Confirmer</button>
                </form>
            </x-card>
        </div>
    </div>
@endsection
