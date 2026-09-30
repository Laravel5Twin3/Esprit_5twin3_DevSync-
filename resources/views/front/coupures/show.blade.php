@extends('layouts.front')

@section('title', $coupure->titre)

@section('content')
    <nav class="mb-3">
        <a href="{{ route('coupures.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Toutes les coupures</a>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <x-card class="border-top border-4 border-{{ $coupure->statut_couleur }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted"><i class="bi {{ $coupure->type_icone }}"></i> {{ $coupure->type_label }}</span>
                        <h2 class="h3 mt-1 mb-0">{{ $coupure->titre }}</h2>
                    </div>
                    <x-coupure.statut-badge :coupure="$coupure" class="fs-6" />
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="bg-light rounded p-3 h-100">
                            <div class="small text-muted">Début</div>
                            <strong>{{ $coupure->date_debut->translatedFormat('l d F Y à H\hi') }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bg-light rounded p-3 h-100">
                            <div class="small text-muted">Fin</div>
                            <strong>{{ $coupure->date_fin?->translatedFormat('l d F Y à H\hi') ?? 'Indéterminée' }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="bg-light rounded p-3 h-100">
                            <div class="small text-muted">Durée estimée</div>
                            <strong>{{ $coupure->duree ?? '—' }}</strong>
                        </div>
                    </div>
                </div>

                @if ($coupure->description)
                    <h5>Détails</h5>
                    <p>{!! nl2br(e($coupure->description)) !!}</p>
                @endif

                @if (in_array($coupure->statut, ['prevue', 'en_cours']))
                    <div class="alert alert-warning mb-0">
                        <h6 class="alert-heading"><i class="bi bi-shield-check"></i> Que faire ?</h6>
                        <ul class="mb-0 small">
                            <li>Débranchez les appareils sensibles (ordinateur, TV) pour éviter les surtensions au retour du courant.</li>
                            <li>Gardez le réfrigérateur fermé : il conserve le froid environ 4 heures.</li>
                            <li>Chargez téléphones et batteries externes avant une coupure prévue.</li>
                            <li>En cas de forte chaleur, rejoignez un <a href="{{ Route::has('points-fraicheur.index') ? route('points-fraicheur.index') : '#' }}">point de fraîcheur</a>.</li>
                        </ul>
                    </div>
                @endif
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card title="Zone concernée">
                <h5 class="mb-1">{{ $coupure->zone->nom }}</h5>
                <p class="text-muted mb-2">{{ $coupure->zone->gouvernorat }} — {{ $coupure->zone->code_postal }}</p>
                <p class="mb-2">Risque de coupure : <span class="badge bg-{{ $coupure->zone->niveau_risque_couleur }}">{{ $coupure->zone->niveau_risque_label }}</span></p>
                @if ($coupure->foyers_touches)
                    <p class="mb-0"><i class="bi bi-house"></i> {{ number_format($coupure->foyers_touches, 0, ',', ' ') }} foyers touchés</p>
                @endif
                @if ($nbSignalements = $coupure->signalements()->where('statut', 'valide')->count())
                    <p class="mb-0 mt-2"><i class="bi bi-megaphone"></i> Signalée par {{ $nbSignalements }} habitant(s)</p>
                @endif
            </x-card>

            @if ($autresCoupures->isNotEmpty())
                <x-card title="Autres coupures dans la zone" class="mt-3">
                    @foreach ($autresCoupures as $autre)
                        <a href="{{ route('coupures.show', $autre) }}" class="d-block text-decoration-none mb-2">
                            <x-coupure.statut-badge :coupure="$autre" /> {{ $autre->titre }}
                            <div class="small text-muted">{{ $autre->date_debut->format('d/m à H\hi') }}</div>
                        </a>
                    @endforeach
                </x-card>
            @endif
        </div>
    </div>
@endsection
