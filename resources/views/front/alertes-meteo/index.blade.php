@extends('layouts.front')

@section('title', 'Alertes météo')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">
            <h1 class="fw-bold"><i class="bi bi-thermometer-sun"></i> Alertes météo & canicule</h1>
            <p class="lead mb-0">Vigilances chaleur en cours et à venir dans votre quartier, avec les bons réflexes à adopter.</p>
        </div>
    </section>
@endsection

@section('content')
    {{-- Bandeau : alerte la plus grave en cours --}}
    @if ($alertePrincipale)
        <div class="alert d-flex align-items-center gap-3 shadow-sm"
             style="background: {{ $alertePrincipale->niveauVigilance->couleur }}; color: {{ $alertePrincipale->niveauVigilance->couleur_texte }};">
            <i class="bi bi-exclamation-triangle-fill fs-2"></i>
            <div>
                <strong>Vigilance {{ $alertePrincipale->niveauVigilance->nom }} en cours : {{ $alertePrincipale->titre }}</strong>
                ({{ $alertePrincipale->zone->nom }}, {{ $alertePrincipale->temperature_max }} °C)<br>
                <span class="small">{{ $alertePrincipale->niveauVigilance->consigne }}</span>
            </div>
            <a href="{{ route('alertes-meteo.show', $alertePrincipale) }}" class="btn btn-light btn-sm ms-auto">Voir</a>
        </div>
    @endif

    <div class="row g-4">
        {{-- Colonne filtres --}}
        <aside class="col-lg-3">
            <x-card title="Période">
                <div class="list-group list-group-flush">
                    <a href="{{ route('alertes-meteo.index', ['periode' => 'actives'] + request()->only('zone_id', 'niveau_id')) }}"
                       class="list-group-item list-group-item-action @if ($periode !== 'historique') active @endif">
                        <i class="bi bi-broadcast"></i> En cours et à venir
                    </a>
                    <a href="{{ route('alertes-meteo.index', ['periode' => 'historique'] + request()->only('zone_id', 'niveau_id')) }}"
                       class="list-group-item list-group-item-action @if ($periode === 'historique') active @endif">
                        <i class="bi bi-clock-history"></i> Historique
                    </a>
                </div>
            </x-card>

            <x-card title="Niveau" class="mt-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('alertes-meteo.index', ['periode' => $periode] + request()->only('zone_id')) }}"
                       class="btn btn-sm {{ request('niveau_id') ? 'btn-outline-secondary' : 'btn-dark' }}">Tous</a>
                    @foreach ($niveaux as $niveau)
                        <a href="{{ route('alertes-meteo.index', ['periode' => $periode, 'niveau_id' => $niveau->id] + request()->only('zone_id')) }}"
                           class="btn btn-sm" title="{{ $niveau->plage }}"
                           style="background: {{ $niveau->couleur }}; color: {{ $niveau->couleur_texte }}; {{ request('niveau_id') == $niveau->id ? 'outline: 3px solid #212529;' : 'opacity:.85' }}">
                            {{ $niveau->nom }}
                        </a>
                    @endforeach
                </div>
            </x-card>

            <x-card title="Zones" class="mt-3">
                <div class="list-group list-group-flush">
                    <a href="{{ route('alertes-meteo.index', ['periode' => $periode] + request()->only('niveau_id')) }}"
                       class="list-group-item list-group-item-action @unless (request('zone_id')) active @endunless">
                        Toutes les zones
                    </a>
                    @foreach ($zones as $zone)
                        <a href="{{ route('alertes-meteo.index', ['periode' => $periode, 'zone_id' => $zone->id] + request()->only('niveau_id')) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center @if (request('zone_id') == $zone->id) active @endif">
                            {{ $zone->nom }}
                            @if ($actifsParZone[$zone->id] ?? 0)
                                <span class="badge bg-danger rounded-pill">{{ $actifsParZone[$zone->id] }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </x-card>
        </aside>

        {{-- Liste --}}
        <div class="col-lg-9">
            @if ($alertes->isEmpty())
                <x-card class="text-center py-4">
                    <i class="bi bi-sun fs-1 text-success"></i>
                    <p class="mt-2 mb-0">
                        {{ $periode === 'historique' ? 'Aucune alerte passée pour cette sélection.' : 'Aucune vigilance chaleur en cours. Profitez-en !' }}
                    </p>
                </x-card>
            @else
                <div class="row g-3">
                    @foreach ($alertes as $alerte)
                        <div class="col-md-6 col-xl-4">
                            <x-alerte-meteo.card :alerte="$alerte" />
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">{{ $alertes->links() }}</div>
            @endif
        </div>
    </div>
@endsection
