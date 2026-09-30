@extends('layouts.front')

@section('title', 'Coupures de courant')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">
            <h1 class="fw-bold"><i class="bi bi-lightning-charge"></i> Coupures de courant</h1>
            <p class="lead">Délestages, pannes et travaux en cours ou prévus dans votre quartier.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('signalements.create') }}" class="btn btn-dark">
                    <i class="bi bi-megaphone"></i> Signaler une coupure
                </a>
                <a href="{{ route('coupures.previsions') }}" class="btn btn-light">
                    <i class="bi bi-cpu"></i> Prédiction coupures
                </a>
                @auth
                    <a href="{{ route('signalements.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-list-check"></i> Mes signalements
                    </a>
                @endauth
            </div>
        </div>
    </section>
@endsection

@section('content')
    @if ($enCoursCount > 0)
        <div class="alert alert-danger d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
            <div><strong>{{ $enCoursCount }} coupure(s) en cours</strong> en ce moment. Protégez vos appareils sensibles et gardez vos aliments au frais.</div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Colonne filtres --}}
        <aside class="col-lg-3">
            <x-card title="Période">
                <div class="list-group list-group-flush">
                    <a href="{{ route('coupures.index', ['periode' => 'actives', 'zone_id' => request('zone_id')]) }}"
                       class="list-group-item list-group-item-action @if ($periode !== 'historique') active @endif">
                        <i class="bi bi-broadcast"></i> En cours et prévues
                    </a>
                    <a href="{{ route('coupures.index', ['periode' => 'historique', 'zone_id' => request('zone_id')]) }}"
                       class="list-group-item list-group-item-action @if ($periode === 'historique') active @endif">
                        <i class="bi bi-clock-history"></i> Historique
                    </a>
                </div>
            </x-card>

            <x-card title="Zones" class="mt-3">
                <div class="list-group list-group-flush">
                    <a href="{{ route('coupures.index', ['periode' => $periode]) }}"
                       class="list-group-item list-group-item-action @unless (request('zone_id')) active @endunless">
                        Toutes les zones
                    </a>
                    @foreach ($zones as $zone)
                        <a href="{{ route('coupures.index', ['periode' => $periode, 'zone_id' => $zone->id]) }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center @if (request('zone_id') == $zone->id) active @endif">
                            {{ $zone->nom }}
                            @if ($zone->coupures_actives_count)
                                <span class="badge bg-danger rounded-pill">{{ $zone->coupures_actives_count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </x-card>
        </aside>

        {{-- Liste --}}
        <div class="col-lg-9">
            @if ($coupures->isEmpty())
                <x-card class="text-center py-4">
                    <i class="bi bi-lightbulb fs-1 text-success"></i>
                    <p class="mt-2 mb-0">
                        {{ $periode === 'historique' ? 'Aucune coupure passée pour cette sélection.' : 'Aucune coupure en cours ni prévue. Bonne nouvelle !' }}
                    </p>
                </x-card>
            @else
                <div class="row g-3">
                    @foreach ($coupures as $coupure)
                        <div class="col-md-6 col-xl-4">
                            <x-coupure.card :coupure="$coupure" />
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">{{ $coupures->links() }}</div>
            @endif
        </div>
    </div>
@endsection
