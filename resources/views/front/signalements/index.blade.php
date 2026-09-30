@extends('layouts.front')

@section('title', 'Mes signalements')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h3 mb-0">Mes signalements</h1>
        <a href="{{ route('signalements.create') }}" class="btn btn-ha"><i class="bi bi-megaphone"></i> Signaler une coupure</a>
    </div>

    @forelse ($signalements as $signalement)
        <x-card class="mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <div>
                    <strong><i class="bi bi-geo-alt"></i> {{ $signalement->zone->nom }}</strong>
                    <span class="text-muted small ms-2">constaté le {{ $signalement->date_constat->format('d/m/Y à H\hi') }}</span>
                    <p class="mb-0 mt-1">{{ $signalement->description }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $signalement->statut_couleur }}">{{ $signalement->statut_label }}</span>
                    @if ($signalement->coupure)
                        <div class="small mt-1">
                            <a href="{{ route('coupures.show', $signalement->coupure) }}">Voir la coupure <i class="bi bi-arrow-right"></i></a>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>
    @empty
        <x-card class="text-center py-4">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="mt-2 mb-0">Vous n'avez encore fait aucun signalement.</p>
        </x-card>
    @endforelse

    <div class="d-flex justify-content-center">{{ $signalements->links() }}</div>
@endsection
