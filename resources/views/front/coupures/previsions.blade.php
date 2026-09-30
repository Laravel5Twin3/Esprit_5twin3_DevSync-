@extends('layouts.front')

@section('title', 'Prédiction coupures')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">
            <h1 class="fw-bold"><i class="bi bi-cpu"></i> Prédiction coupures</h1>
            <p class="lead mb-0">Notre modèle croise la météo prévue et l'historique de chaque quartier pour estimer le risque de coupure des prochains jours.</p>
        </div>
    </section>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <a href="{{ route('coupures.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Coupures en cours</a>
        <span class="small text-muted">
            Météo : {{ $sourceMeteo === 'open-meteo' ? 'Open-Meteo (prévisions réelles)' : 'simulation (API météo indisponible)' }}
        </span>
    </div>

    @if ($previsions->isEmpty())
        <div class="alert alert-info">Pas encore assez de données pour établir des prévisions.</div>
    @else
        <x-card title="Risque de coupure par zone — {{ \App\Services\Coupure\PredictionCoupureService::JOURS_PREVISION }} prochains jours" class="mb-4">
            <x-coupure.tableau-previsions :previsions="$previsions" />
        </x-card>

        {{-- Prédiction pour une date choisie --}}
        <x-card title="Prédiction pour une date précise" class="mb-4" id="date">
            <form method="GET" action="{{ route('coupures.previsions') }}#date" class="row g-2 align-items-end mb-3" novalidate>
                <div class="col-sm-6 col-md-4">
                    <x-form.input name="date" type="date" label="Date" :value="$date?->toDateString()"
                                  :min="today()->toDateString()" :max="today()->addYear()->toDateString()" />
                </div>
                <div class="col-auto mb-3">
                    <button type="submit" class="btn btn-ha"><i class="bi bi-calendar-check"></i> Prédire</button>
                </div>
            </form>

            @if ($predictionDate)
                <p class="mb-2">
                    Risque de coupure le <strong>{{ $date->translatedFormat('l d F Y') }}</strong> :
                </p>
                @if ($predictionDate['source'] === 'open-meteo')
                    <p class="small text-success mb-3"><i class="bi bi-check-circle"></i> Basé sur les prévisions météo réelles d'Open-Meteo.</p>
                @elseif ($predictionDate['source'] === 'normales')
                    <p class="small text-warning-emphasis mb-3">
                        <i class="bi bi-info-circle"></i> Au-delà de {{ \App\Services\Coupure\MeteoService::JOURS_PREVISION_MAX }} jours, aucune prévision météo n'est disponible :
                        la température utilisée est la <strong>normale saisonnière</strong> (moyenne de ce jour sur les 5 dernières années).
                        Estimation indicative, moins fiable.
                    </p>
                @else
                    <p class="small text-muted mb-3"><i class="bi bi-wifi-off"></i> Service météo indisponible : température simulée.</p>
                @endif

                <x-coupure.resultats-zones :resultats="$predictionDate['resultats']" />
            @else
                <p class="text-muted mb-0 small">
                    Choisissez une date jusqu'à un an à l'avance. Jusqu'à {{ \App\Services\Coupure\MeteoService::JOURS_PREVISION_MAX }} jours, la prédiction
                    utilise la vraie prévision météo ; au-delà, les normales saisonnières.
                </p>
            @endif
        </x-card>

        {{-- Simulateur --}}
        <x-card title="Simulateur : et s'il faisait très chaud ?" class="mb-4" id="simulateur">
            <form method="GET" action="{{ route('coupures.previsions') }}#simulateur" class="row g-2 align-items-end mb-3" novalidate>
                <div class="col-sm-6 col-md-4">
                    <x-form.input name="temperature" type="number" label="Température maximale (°C)" :value="$temperature"
                                  min="15" max="50" step="0.5" placeholder="Ex : 42" class="mb-0" />
                </div>
                <div class="col-auto mb-3">
                    <button type="submit" class="btn btn-ha"><i class="bi bi-cpu"></i> Simuler</button>
                </div>
            </form>

            @if ($simulation)
                <p class="mb-2">À <strong>{{ $temperature }} °C</strong>, le modèle estime :</p>
                <x-coupure.resultats-zones :resultats="$simulation" />
            @else
                <p class="text-muted mb-0 small">Saisissez une température pour voir comment le risque évoluerait dans chaque quartier lors d'une canicule.</p>
            @endif
        </x-card>
    @endif

    <x-card title="Comment ça marche ?">
        <ol class="mb-0">
            <li>Pour chaque quartier et chacun des {{ \App\Services\Coupure\PredictionCoupureService::JOURS_HISTORIQUE }} derniers jours, nous comparons
                la <strong>température maximale</strong> relevée avec les <strong>coupures</strong> qui ont eu lieu.</li>
            <li>Un modèle d'apprentissage automatique (<em>régression logistique</em>) apprend quels facteurs
                augmentent le risque : chaleur, fragilité du réseau de la zone, coupures récentes, population.</li>
            <li>Le modèle applique ce qu'il a appris aux <strong>prévisions météo</strong> des prochains jours pour chaque quartier.</li>
        </ol>
        <p class="small text-muted mt-2 mb-0">Il s'agit d'une estimation statistique : elle ne remplace pas les avis officiels de la STEG.</p>
    </x-card>
@endsection
