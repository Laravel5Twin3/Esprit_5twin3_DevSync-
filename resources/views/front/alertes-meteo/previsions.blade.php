@extends('layouts.front')

@section('title', 'Prévisions de chaleur')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">
            <h1 class="fw-bold"><i class="bi bi-graph-up-arrow"></i> Prévisions de chaleur</h1>
            <p class="lead mb-0">Températures des 7 prochains jours dans votre zone, comparées aux seuils de vigilance.</p>
        </div>
    </section>
@endsection

@section('content')
    {{-- Choix de la zone --}}
    <form method="GET" class="card card-body shadow-sm border-0 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="zone_id" class="form-label small">Zone</label>
                <select id="zone_id" name="zone_id" class="form-select" onchange="this.form.submit()">
                    @foreach ($zones as $z)
                        <option value="{{ $z->id }}" @selected($zone && $zone->id === $z->id)>{{ $z->nom }} ({{ $z->gouvernorat }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('alertes-meteo.index', ['zone_id' => $zone?->id]) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-bell"></i> Alertes de cette zone
                </a>
            </div>
        </div>
    </form>

    @if ($erreur)
        <div class="alert alert-warning"><i class="bi bi-cloud-slash"></i> {{ $erreur }}</div>
    @elseif ($previsions->isEmpty())
        <x-card class="text-center py-4">Aucune zone disponible.</x-card>
    @else
        {{-- Résumé --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <x-card class="h-100 text-center">
                    <div class="small text-muted">Jour le plus chaud</div>
                    <div class="fs-5 fw-bold">{{ ucfirst($pic['date']->translatedFormat('l j F')) }}</div>
                    <div class="display-6 fw-bold" style="color: {{ $pic['niveau']?->couleur }}">{{ $pic['temperature'] }} °C</div>
                    <x-alerte-meteo.badge-niveau :niveau="$pic['niveau']" />
                </x-card>
            </div>
            <div class="col-md-4">
                <x-card class="h-100 text-center">
                    <div class="small text-muted">Ressentie maximale</div>
                    @php $maxRessentie = $previsions->max('ressentie'); @endphp
                    <div class="display-6 fw-bold text-danger">{{ $maxRessentie !== null ? $maxRessentie.' °C' : '—' }}</div>
                    <div class="small text-muted">Chaleur ressentie (température + humidité + vent)</div>
                </x-card>
            </div>
            <div class="col-md-4">
                <x-card class="h-100 text-center">
                    <div class="small text-muted">Jours en vigilance orange ou rouge</div>
                    <div class="display-6 fw-bold">{{ $previsions->filter(fn ($p) => ($p['niveau']?->ordre ?? 0) >= 3)->count() }} / {{ $previsions->count() }}</div>
                    <div class="small text-muted">sur les 7 prochains jours</div>
                </x-card>
            </div>
        </div>

        {{-- Graphique --}}
        <x-card title="Températures prévues à {{ $zone->nom }}">
            <div style="position: relative; height: 360px">
                <canvas id="graphique-previsions" aria-label="Graphique des températures prévues">Graphique des températures maximales et ressenties des 7 prochains jours.</canvas>
            </div>
            <p class="small text-muted mt-2 mb-0">
                <i class="bi bi-info-circle"></i> Les lignes pointillées sont les seuils des niveaux de vigilance.
                Source : Open-Meteo.
            </p>
        </x-card>

        {{-- Détail jour par jour --}}
        <div class="row g-2 mt-3">
            @foreach ($previsions as $jour)
                <div class="col-6 col-md">
                    <div class="border rounded text-center p-2 bg-white h-100">
                        <div class="small text-muted">{{ ucfirst($jour['date']->translatedFormat('D d/m')) }}</div>
                        <div class="fw-bold">{{ $jour['temperature'] }} °C</div>
                        @if ($jour['ressentie'] !== null)
                            <div class="small text-muted">ressentie {{ $jour['ressentie'] }} °C</div>
                        @endif
                        <x-alerte-meteo.badge-niveau :niveau="$jour['niveau']" class="mt-1" />
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Alertes publiées pour la zone --}}
    @if ($alertes->isNotEmpty())
        <h2 class="h5 mt-4">Alertes publiées pour {{ $zone->nom }}</h2>
        <div class="row g-3">
            @foreach ($alertes as $alerte)
                <div class="col-md-6 col-xl-4"><x-alerte-meteo.card :alerte="$alerte" /></div>
            @endforeach
        </div>
    @endif


    {{-- Assistant canicule (chatbot) --}}
    <x-alerte-meteo.assistant />
@endsection

@if ($previsions->isNotEmpty())
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.js"
                integrity="sha384-G436+Z2nlA8+PNoeRvWdxKbvOf8E/y+lYxqht2iBwNHTQDV5CJr3+AGVj8fGZi5t" crossorigin="anonymous"></script>
        <script>
            (() => {
                const jours = @js($previsions->map(fn ($p) => ucfirst($p['date']->translatedFormat('D d/m')))->values());
                const temperatures = @js($previsions->pluck('temperature')->values());
                const ressenties = @js($previsions->pluck('ressentie')->values());
                const couleursPoints = @js($previsions->map(fn ($p) => $p['niveau']?->couleur ?? '#6c757d')->values());
                // Un seuil par niveau (sauf le premier, qui commence à 0 °C)
                const seuils = @js($niveaux->filter(fn ($n) => $n->temperature_min > 0)->map(fn ($n) => ['nom' => $n->nom, 'valeur' => $n->temperature_min, 'couleur' => $n->couleur])->values());

                const datasets = [
                    {
                        label: 'Température maximale (°C)', data: temperatures, borderColor: '#c2410c',
                        backgroundColor: 'rgba(232, 89, 12, .15)', fill: true, tension: .35, borderWidth: 3,
                        pointBackgroundColor: couleursPoints, pointBorderColor: '#fff', pointRadius: 7, pointHoverRadius: 9,
                    },
                    {
                        label: 'Température ressentie (°C)', data: ressenties, borderColor: '#7c3aed',
                        borderWidth: 2, tension: .35, pointRadius: 3, fill: false,
                    },
                    ...seuils.map(s => ({
                        label: 'Seuil ' + s.nom + ' (' + s.valeur + ' °C)', data: jours.map(() => s.valeur),
                        borderColor: s.couleur, borderDash: [6, 6], borderWidth: 1.5, pointRadius: 0, fill: false,
                    })),
                ];

                new Chart(document.getElementById('graphique-previsions'), {
                    type: 'line',
                    data: { labels: jours, datasets },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true } },
                            tooltip: { callbacks: { label: c => c.dataset.label.split(' (')[0] + ' : ' + c.parsed.y + ' °C' } },
                        },
                        scales: { y: { title: { display: true, text: '°C' }, suggestedMin: 25, suggestedMax: 48 } },
                    },
                });
            })();
        </script>
    @endpush
@endif
