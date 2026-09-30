@extends('layouts.front')

@section('title', $point->nom . ' – Points de Fraîcheur')

@section('content')
<div class="row g-4">
    {{-- Infos principales --}}
    <div class="col-lg-7">
        {{-- En-tête --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:60px; height:60px; background-color: {{ $point->categoriePoint->couleur ?? '#0d6efd' }}; flex-shrink:0;">
                <i class="bi {{ $point->categoriePoint->icone ?? 'bi-geo-alt' }} text-white fs-3"></i>
            </div>
            <div>
                <h1 class="h3 mb-1">{{ $point->nom }}</h1>
                <span class="badge rounded-pill" style="background-color: {{ $point->categoriePoint->couleur ?? '#0d6efd' }}">
                    {{ $point->categoriePoint->nom ?? '—' }}
                </span>
                @if($point->actif)
                    <span class="badge bg-success ms-1">✓ Ouvert</span>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4"><i class="bi bi-geo-alt text-muted"></i> Adresse</dt>
                    <dd class="col-sm-8">{{ $point->adresse }}</dd>

                    @if($point->horaires)
                    <dt class="col-sm-4"><i class="bi bi-clock text-muted"></i> Horaires</dt>
                    <dd class="col-sm-8">{{ $point->horaires }}</dd>
                    @endif

                    @if($point->capacite)
                    <dt class="col-sm-4"><i class="bi bi-people text-muted"></i> Capacité</dt>
                    <dd class="col-sm-8">{{ number_format($point->capacite) }} personnes</dd>
                    @endif

                    @if($point->description)
                    <dt class="col-sm-4"><i class="bi bi-info-circle text-muted"></i> Info</dt>
                    <dd class="col-sm-8">{{ $point->description }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Bouton itinéraire --}}
        <a href="https://maps.google.com/?q={{ $point->latitude }},{{ $point->longitude }}"
           target="_blank" class="btn btn-outline-primary mb-4">
            <i class="bi bi-map"></i> Obtenir l'itinéraire
        </a>

        {{-- Points proches --}}
        @if($proches->isNotEmpty())
        <h5 class="fw-bold mb-3">Points proches (< 5 km)</h5>
        <div class="row g-3">
            @foreach($proches as $proche)
            <div class="col-sm-6">
                <a href="{{ route('points-fraicheur.show', $proche) }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 hover-shadow">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:40px; height:40px; background-color: {{ $proche->categoriePoint->couleur ?? '#0d6efd' }};">
                                <i class="bi {{ $proche->categoriePoint->icone ?? 'bi-geo-alt' }} text-white"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $proche->nom }}</div>
                                <div class="small text-muted">
                                    {{ round($proche->distanceTo($point->latitude, $point->longitude), 1) }} km
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Carte --}}
    <div class="col-lg-5">
        <div class="card shadow-sm sticky-top" style="top: 1rem;">
            <div class="card-header fw-semibold">
                <i class="bi bi-map"></i> Localisation
            </div>
            <div id="point-map" style="height: 350px;"></div>
            <div class="card-footer text-muted small">
                {{ number_format($point->latitude, 5) }}, {{ number_format($point->longitude, 5) }}
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('points-fraicheur.index') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-left"></i> Voir tous les points
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('point-map').setView([{{ $point->latitude }}, {{ $point->longitude }}], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const icon = L.divIcon({
        html: `<div style="
            background:{{ $point->categoriePoint->couleur ?? '#0d6efd' }};
            color:#fff; border-radius:50%;
            width:44px; height:44px;
            display:flex; align-items:center; justify-content:center;
            font-size:20px; box-shadow:0 3px 8px rgba(0,0,0,.4);">
            <i class="bi {{ $point->categoriePoint->icone ?? 'bi-geo-alt' }}"></i></div>`,
        className: '',
        iconSize: [44, 44],
        iconAnchor: [22, 44],
    });

    L.marker([{{ $point->latitude }}, {{ $point->longitude }}], { icon })
        .addTo(map)
        .bindPopup('<b>{{ addslashes($point->nom) }}</b>').openPopup();
</script>
@endpush
