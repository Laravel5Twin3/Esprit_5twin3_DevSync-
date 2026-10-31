@extends('layouts.front')

@section('title', 'Vérifiez votre e-mail')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <x-card title="Vérifiez votre adresse e-mail">
                <div class="text-center mb-3"><i class="bi bi-envelope-check fs-1 text-warning"></i></div>
                <p>
                    Un lien de confirmation a été envoyé à <strong>{{ auth()->user()->email }}</strong>.
                    Cliquez dessus pour activer votre compte : vous pourrez ensuite signaler des coupures,
                    publier sur l'entraide et accéder à votre profil.
                </p>
                <p class="text-muted small">Pensez à regarder dans les courriers indésirables.</p>

                <x-auth.statut />

                <div class="d-flex flex-wrap gap-2 justify-content-between">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-ha"><i class="bi bi-send"></i> Renvoyer le lien</button>
                    </form>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">Se déconnecter</button>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
@endsection
