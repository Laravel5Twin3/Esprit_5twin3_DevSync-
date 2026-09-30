@extends('layouts.back')

@section('title', $point->nom)
@section('page-title', '🔍 ' . $point->nom)

@section('page-actions')
    <a href="{{ route('admin.points-fraicheur.edit', $point) }}" class="btn btn-warning btn-sm">
        <i class="bi bi-pencil"></i> Modifier
    </a>
    <a href="{{ route('admin.points-fraicheur.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Catégorie</dt>
                    <dd class="col-sm-8">
                        @if($point->categoriePoint)
                            <span class="badge rounded-pill fs-6"
                                  style="background-color: {{ $point->categoriePoint->couleur }}">
                                <i class="bi {{ $point->categoriePoint->icone }}"></i>
                                {{ $point->categoriePoint->nom }}
                            </span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">Adresse</dt>
                    <dd class="col-sm-8">{{ $point->adresse }}</dd>

                    <dt class="col-sm-4">Coordonnées</dt>
                    <dd class="col-sm-8">
                        {{ $point->latitude }}, {{ $point->longitude }}
                        <a href="https://maps.google.com/?q={{ $point->latitude }},{{ $point->longitude }}"
                           target="_blank" class="ms-2 small text-primary">
                            <i class="bi bi-map"></i> Google Maps
                        </a>
                    </dd>

                    <dt class="col-sm-4">Horaires</dt>
                    <dd class="col-sm-8">{{ $point->horaires ?? '—' }}</dd>

                    <dt class="col-sm-4">Capacité</dt>
                    <dd class="col-sm-8">{{ $point->capacite ? number_format($point->capacite) . ' personnes' : '—' }}</dd>

                    <dt class="col-sm-4">Statut</dt>
                    <dd class="col-sm-8">
                        @if($point->actif)
                            <span class="badge bg-success">✓ Actif</span>
                        @else
                            <span class="badge bg-secondary">Inactif</span>
                        @endif
                    </dd>

                    @if($point->description)
                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8">{{ $point->description }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header"><i class="bi bi-map"></i> Localisation</div>
            <div id="detail-map" style="height: 250px;"></div>
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
    const map = L.map('detail-map').setView([{{ $point->latitude }}, {{ $point->longitude }}], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([{{ $point->latitude }}, {{ $point->longitude }}])
        .addTo(map)
        .bindPopup('<b>{{ addslashes($point->nom) }}</b>').openPopup();
</script>
@endpush
