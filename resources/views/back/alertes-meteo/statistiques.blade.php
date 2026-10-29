@extends('layouts.back')

@section('title', 'Statistiques des alertes météo')
@section('page-title', 'Statistiques par zone')

@section('page-actions')
    <a href="{{ route('admin.alertes-meteo.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Alertes</a>
@endsection

@section('content')
    {{-- Filtre de période --}}
    <form method="GET" class="d-flex flex-wrap align-items-center gap-2 mb-4">
        <span class="small text-muted">Période :</span>
        @foreach (App\Http\Controllers\Back\StatistiquesAlerteMeteoController::PERIODES as $cle => [$libelle])
            <button type="submit" name="periode" value="{{ $cle }}"
                    class="btn btn-sm {{ $periode === (string) $cle ? 'btn-dark' : 'btn-outline-secondary' }}">{{ $libelle }}</button>
        @endforeach
    </form>

    {{-- Chiffres clés --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-back.stat-card icon="bi-bell" label="Alertes sur la période" :value="$totaux['alertes']" color="primary" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-back.stat-card icon="bi-calendar-week" label="Jours de vigilance" :value="$totaux['jours']" color="warning" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-back.stat-card icon="bi-thermometer-high" label="Température record"
                              :value="$totaux['record'] ? $totaux['record']->temperature_max.' °C' : '—'" color="danger" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-back.stat-card icon="bi-geo-alt" label="Zones touchées" :value="$totaux['zones'].' / '.$classement->count()" color="info" />
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <x-card title="Alertes par zone et par niveau" class="h-100">
                <div style="position: relative; height: 320px"><canvas id="graphique-zones"></canvas></div>
            </x-card>
        </div>
        <div class="col-lg-4">
            <x-card title="Répartition par niveau" class="h-100">
                <div style="position: relative; height: 320px"><canvas id="graphique-niveaux"></canvas></div>
            </x-card>
        </div>
        <div class="col-12">
            <x-card title="Évolution du nombre d’alertes par mois">
                <div style="position: relative; height: 260px"><canvas id="graphique-mois"></canvas></div>
            </x-card>
        </div>
    </div>

    {{-- Classement des zones --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-semibold">
            Classement des zones les plus exposées
            <span class="small text-muted fw-normal">(score = somme des niveaux de gravité des alertes)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Zone</th>
                        <th class="text-center">Alertes</th>
                        <th class="text-center">En cours</th>
                        <th class="text-center">Jours de vigilance</th>
                        <th class="text-center">Record</th>
                        <th class="text-center">Moyenne</th>
                        <th>Niveau le plus grave</th>
                        <th class="text-center">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($classement as $i => $ligne)
                        <tr>
                            <td class="fw-bold">{{ $i + 1 }}</td>
                            <td>
                                <a href="{{ route('admin.alertes-meteo.index', ['zone_id' => $ligne['zone']->id]) }}" class="fw-semibold text-decoration-none">{{ $ligne['zone']->nom }}</a>
                                <div class="small text-muted">{{ $ligne['zone']->gouvernorat }}</div>
                            </td>
                            <td class="text-center">{{ $ligne['total'] }}</td>
                            <td class="text-center">
                                <span class="badge {{ $ligne['en_cours'] ? 'bg-danger' : 'bg-light text-dark' }}">{{ $ligne['en_cours'] }}</span>
                            </td>
                            <td class="text-center">{{ $ligne['jours'] }}</td>
                            <td class="text-center">{{ $ligne['record'] !== null ? $ligne['record'].' °C' : '—' }}</td>
                            <td class="text-center">{{ $ligne['moyenne'] !== null ? $ligne['moyenne'].' °C' : '—' }}</td>
                            <td><x-alerte-meteo.badge-niveau :niveau="$ligne['plus_grave']" /></td>
                            <td class="text-center">
                                <div class="progress" style="height: .6rem; min-width: 70px" title="{{ $ligne['score'] }}">
                                    <div class="progress-bar bg-danger"
                                         style="width: {{ $classement->max('score') ? $ligne['score'] / $classement->max('score') * 100 : 0 }}%"></div>
                                </div>
                                <span class="small">{{ $ligne['score'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const zones = @js($classement->map(fn ($l) => $l['zone']->nom)->values());
            const niveaux = @js($niveaux->map(fn ($n) => ['id' => $n->id, 'nom' => $n->nom, 'couleur' => $n->couleur])->values());
            const parZone = @js($classement->map(fn ($l) => $l['par_niveau'])->values());
            const parNiveau = @js($parNiveau->values());
            const parMois = @js($parMois);

            // Barres empilées : une couleur par niveau de vigilance
            new Chart(document.getElementById('graphique-zones'), {
                type: 'bar',
                data: {
                    labels: zones,
                    datasets: niveaux.map(n => ({
                        label: n.nom, backgroundColor: n.couleur,
                        data: parZone.map(z => z[n.id] ?? 0),
                    })),
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } },
                },
            });

            new Chart(document.getElementById('graphique-niveaux'), {
                type: 'doughnut',
                data: {
                    labels: parNiveau.map(n => n.nom),
                    datasets: [{ data: parNiveau.map(n => n.total), backgroundColor: parNiveau.map(n => n.couleur) }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
            });

            new Chart(document.getElementById('graphique-mois'), {
                type: 'line',
                data: {
                    labels: parMois.map(m => m.mois),
                    datasets: [{
                        label: 'Alertes', data: parMois.map(m => m.total), borderColor: '#c2410c',
                        backgroundColor: 'rgba(232, 89, 12, .15)', fill: true, tension: .3,
                    }],
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                },
            });
        })();
    </script>
@endpush
