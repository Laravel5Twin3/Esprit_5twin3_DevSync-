@extends('layouts.back')

@section('title', $coupure->titre)
@section('page-title', 'Coupure #'.$coupure->id)

@section('page-actions')
    <a href="{{ route('admin.coupures.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Coupures</a>
    <a href="{{ route('admin.coupures.edit', $coupure) }}" class="btn btn-warning btn-sm ms-2"><i class="bi bi-pencil"></i> Modifier</a>
    <x-delete-button :action="route('admin.coupures.destroy', $coupure)" class="btn btn-sm btn-danger ms-2" />
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <x-card>
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h4 class="mb-1">{{ $coupure->titre }}</h4>
                        <span class="text-muted"><i class="bi {{ $coupure->type_icone }}"></i> {{ $coupure->type_label }}</span>
                    </div>
                    <x-coupure.statut-badge :coupure="$coupure" class="fs-6" />
                </div>

                <div class="row text-center g-3 mb-3">
                    <div class="col-sm-4">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Début</div>
                            <strong>{{ $coupure->date_debut->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Fin</div>
                            <strong>{{ $coupure->date_fin?->format('d/m/Y H:i') ?? 'Indéterminée' }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="border rounded p-2">
                            <div class="small text-muted">Durée</div>
                            <strong>{{ $coupure->duree ?? '—' }}</strong>
                        </div>
                    </div>
                </div>

                <h6>Description</h6>
                <p class="mb-0">{!! nl2br(e($coupure->description ?: 'Aucune description.')) !!}</p>
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card title="Zone concernée">
                <h5><a href="{{ route('admin.zones.show', $coupure->zone) }}">{{ $coupure->zone->nom }}</a></h5>
                <p class="mb-1">{{ $coupure->zone->gouvernorat }} — {{ $coupure->zone->code_postal }}</p>
                <p class="mb-1">Risque : <span class="badge bg-{{ $coupure->zone->niveau_risque_couleur }}">{{ $coupure->zone->niveau_risque_label }}</span></p>
                <p class="mb-0">
                    Foyers touchés :
                    <strong>{{ $coupure->foyers_touches ? number_format($coupure->foyers_touches, 0, ',', ' ') : 'non renseigné' }}</strong>
                </p>
            </x-card>
            @if ($coupure->signalements->isNotEmpty())
                <x-card title="Signalements des habitants ({{ $coupure->signalements->count() }})" class="mt-3">
                    @foreach ($coupure->signalements as $signalement)
                        <div class="small border-bottom pb-2 mb-2">
                            <strong>{{ $signalement->user->name }}</strong>
                            <span class="text-muted">· {{ $signalement->date_constat->format('d/m H:i') }}</span>
                            <div>{{ $signalement->description }}</div>
                        </div>
                    @endforeach
                </x-card>
            @endif

            <p class="small text-muted mt-3 mb-0">
                Créée le {{ $coupure->created_at->format('d/m/Y H:i') }} · Modifiée le {{ $coupure->updated_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
@endsection
