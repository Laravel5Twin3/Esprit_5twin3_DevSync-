@extends('layouts.front')

@section('title', 'Points de Fraîcheur – HeatAlert')

@section('hero')
<div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #1e3a5f 0%, #0dcaf0 100%);">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">🧊 Points de Fraîcheur</h1>
        <p class="lead mb-0">Trouvez les endroits les plus frais près de chez vous pendant la canicule</p>
    </div>
</div>
@endsection

@section('content')
    {{-- IA : bouton recommandation --}}
    <div class="alert alert-info d-flex align-items-center gap-3 mb-4" role="alert">
        <i class="bi bi-robot fs-3"></i>
        <div class="flex-grow-1">
            <strong>Recommandation IA</strong> — Activez votre géolocalisation pour obtenir
            les meilleurs points adaptés à la température actuelle.
        </div>
        <button id="btn-reco" class="btn btn-info text-white flex-shrink-0" onclick="recommander()">
            <i class="bi bi-geo-alt"></i> Me localiser &amp; recommander
        </button>
    </div>

    {{-- Résultat IA --}}
    <div id="reco-result" class="mb-4" style="display:none;"></div>

    <div class="row g-4">
        {{-- Filtre catégories --}}
        <div class="col-lg-3">
            <div class="card shadow-sm sticky-top" style="top: 1rem;">
                <div class="card-header fw-semibold"><i class="bi bi-funnel"></i> Filtrer</div>
                <div class="card-body p-2">
                    <a href="{{ route('points-fraicheur.index') }}"
                       class="btn btn-sm w-100 mb-2 {{ !request('categorie') ? 'btn-dark' : 'btn-outline-secondary' }}">
                        Tous ({{ $points->count() }})
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('points-fraicheur.index', ['categorie' => $cat->id]) }}"
                           class="btn btn-sm w-100 mb-2 {{ request('categorie') == $cat->id ? 'text-white' : 'btn-outline-secondary' }}"
                           style="{{ request('categorie') == $cat->id ? 'background-color:' . $cat->couleur . '; border-color:' . $cat->couleur : '' }}">
                            <i class="bi {{ $cat->icone }}"></i>
                            {{ $cat->nom }}
                            <span class="badge bg-white text-dark ms-1">{{ $cat->point_fraicheurs_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Carte + liste --}}
        <div class="col-lg-9">
            {{-- Carte interactive Leaflet --}}
            <div class="card shadow-sm mb-4">
                <div id="main-map" style="height: 420px; border-radius: 8px;"></div>
            </div>

            {{-- Liste des points --}}
            @if($points->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Aucun point de fraîcheur trouvé pour cette catégorie.
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    @foreach($points as $point)
                    <div class="col">
                        <x-card>
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:48px; height:48px; background-color: {{ $point->categoriePoint->couleur ?? '#0d6efd' }};">
                                    <i class="bi {{ $point->categoriePoint->icone ?? 'bi-geo-alt' }} text-white fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">
                                        <a href="{{ route('points-fraicheur.show', $point) }}" class="text-decoration-none text-dark">
                                            {{ $point->nom }}
                                        </a>
                                    </h6>
                                    <p class="small text-muted mb-1">
                                        <i class="bi bi-geo-alt"></i> {{ Str::limit($point->adresse, 45) }}
                                    </p>
                                    @if($point->horaires)
                                    <p class="small text-muted mb-1">
                                        <i class="bi bi-clock"></i> {{ $point->horaires }}
                                    </p>
                                    @endif
                                    @if($point->capacite)
                                    <p class="small text-muted mb-0">
                                        <i class="bi bi-people"></i> {{ number_format($point->capacite) }} personnes
                                    </p>
                                    @endif
                                    <a href="{{ route('points-fraicheur.show', $point) }}"
                                       class="btn btn-sm btn-outline-primary mt-2">
                                        Voir le détail →
                                    </a>
                                </div>
                            </div>
                        </x-card>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #main-map { z-index: 0; }
    .reco-card { border-left: 4px solid #0dcaf0; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ─── Carte principale ───────────────────────────────────────────
    const map = L.map('main-map').setView([36.8190, 10.1657], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const geojson = @json(json_decode($geojson));
    const markers = {};

    L.geoJSON(geojson, {
        pointToLayer: function(feature, latlng) {
            const p = feature.properties;
            const icon = L.divIcon({
                html: `<div style="
                    background:${p.couleur};
                    color:#fff;
                    border-radius:50%;
                    width:36px; height:36px;
                    display:flex; align-items:center; justify-content:center;
                    font-size:16px;
                    box-shadow: 0 2px 6px rgba(0,0,0,.4);">
                    <i class="bi ${p.icone}"></i></div>`,
                className: '',
                iconSize: [36, 36],
                iconAnchor: [18, 36],
            });
            const m = L.marker(latlng, { icon });
            markers[p.id] = m;
            return m;
        },
        onEachFeature: function(feature, layer) {
            const p = feature.properties;
            layer.bindPopup(`
                <b>${p.nom}</b><br>
                <small>${p.adresse}</small><br>
                ${p.horaires ? '<i class="bi bi-clock"></i> ' + p.horaires + '<br>' : ''}
                <a href="${p.url}" class="btn btn-sm btn-primary mt-2">Voir le détail</a>
            `);
        }
    }).addTo(map);

    // ─── Recommandation IA ──────────────────────────────────────────
    function recommander() {
        const btn = document.getElementById('btn-reco');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Analyse en cours…';

        navigator.geolocation.getCurrentPosition(function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            fetch(`/api/points-fraicheur/recommander?lat=${lat}&lng=${lng}`)
                .then(r => r.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-geo-alt"></i> Me localiser & recommander';

                    let html = `
                        <div class="alert alert-warning">
                            <strong>🌡️ ${data.message}</strong>
                        </div>
                        <h6 class="fw-bold mb-3">🤖 Top 5 recommandés pour vous :</h6>
                        <div class="row g-3">
                    `;
                    data.recommandations.forEach((p, i) => {
                        html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="card reco-card h-100 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge rounded-pill" style="background:${p.couleur}">
                                            <i class="bi ${p.icone}"></i> ${p.categorie}
                                        </span>
                                        <span class="badge bg-warning text-dark ms-auto">#${i+1}</span>
                                    </div>
                                    <h6 class="fw-bold mb-1">${p.nom}</h6>
                                    <p class="small text-muted mb-1"><i class="bi bi-geo-alt"></i> ${p.distance_km} km</p>
                                    ${p.horaires ? `<p class="small text-muted mb-1"><i class="bi bi-clock"></i> ${p.horaires}</p>` : ''}
                                    <a href="${p.url}" class="btn btn-sm btn-outline-primary mt-1">Voir →</a>
                                </div>
                            </div>
                        </div>`;
                        // Centrer la carte sur le premier résultat
                        if (i === 0) map.setView([p.latitude, p.longitude], 14);
                        if (markers[p.id]) markers[p.id].openPopup();
                    });
                    html += '</div>';

                    const div = document.getElementById('reco-result');
                    div.innerHTML = html;
                    div.style.display = 'block';
                    div.scrollIntoView({ behavior: 'smooth' });
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-geo-alt"></i> Me localiser & recommander';
                    alert('Erreur lors de la recommandation. Veuillez réessayer.');
                });
        }, function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-geo-alt"></i> Me localiser & recommander';
            alert('Géolocalisation refusée. Veuillez autoriser l\'accès à votre position.');
        });
    }
</script>
@endpush
