@extends('layouts.front')

@section('title', $alerte->titre)

@section('content')
    <a href="{{ route('alertes-meteo.index') }}" class="btn btn-link px-0 mb-3"><i class="bi bi-arrow-left"></i> Toutes les alertes</a>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="p-4" style="background: {{ $alerte->niveauVigilance->couleur }}; color: {{ $alerte->niveauVigilance->couleur_texte }};">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-uppercase small fw-bold">Vigilance {{ $alerte->niveauVigilance->nom }}</div>
                            <h1 class="h3 fw-bold mb-1">{{ $alerte->titre }}</h1>
                            <div><i class="bi bi-geo-alt"></i> {{ $alerte->zone->nom }} ({{ $alerte->zone->gouvernorat }})</div>
                        </div>
                        <div class="display-5 fw-bold">{{ rtrim(rtrim(number_format($alerte->temperature_max, 1, ',', ''), '0'), ',') }}°</div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-3 mb-3 small">
                        <span class="badge bg-{{ $alerte->statut_couleur }}">{{ $alerte->statut_label }}</span>
                        <span><i class="bi bi-calendar-event"></i> Du {{ $alerte->date_debut->format('d/m/Y à H:i') }}</span>
                        <span><i class="bi bi-calendar-check"></i> au {{ $alerte->date_fin->format('d/m/Y à H:i') }}</span>
                    </div>
                    <p>{{ $alerte->message }}</p>

                    <div class="alert alert-warning mb-0">
                        <h6 class="fw-bold"><i class="bi bi-shield-check"></i> Les bons réflexes</h6>
                        {{ $alerte->niveauVigilance->consigne }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if (Route::has('points-fraicheur.index'))
                <x-card class="mb-3">
                    <h6 class="fw-bold"><i class="bi bi-snow"></i> Besoin de fraîcheur ?</h6>
                    <p class="small mb-2">Parcs, salles climatisées et fontaines accessibles près de chez vous.</p>
                    <a href="{{ route('points-fraicheur.index') }}" class="btn btn-sm btn-ha">Trouver un point de fraîcheur</a>
                </x-card>
            @endif

            <x-card title="Autres alertes dans la zone">
                @forelse ($autresAlertes as $autre)
                    <a href="{{ route('alertes-meteo.show', $autre) }}"
                       class="d-flex justify-content-between align-items-center border rounded p-2 mb-2 text-decoration-none text-body">
                        <div>
                            <strong class="small">{{ $autre->titre }}</strong>
                            <div class="small text-muted">{{ $autre->date_debut->format('d/m H:i') }}</div>
                        </div>
                        <x-alerte-meteo.badge-niveau :niveau="$autre->niveauVigilance" />
                    </a>
                @empty
                    <p class="text-muted small mb-0">Aucune autre alerte active dans cette zone.</p>
                @endforelse
            </x-card>
        </div>
    </div>
@endsection
