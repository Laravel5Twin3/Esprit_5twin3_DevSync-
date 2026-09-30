@extends('layouts.back')

@section('title', $zone->nom)
@section('page-title', $zone->nom)

@section('page-actions')
    <a href="{{ route('admin.zones.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Zones</a>
    <a href="{{ route('admin.zones.edit', $zone) }}" class="btn btn-warning btn-sm ms-2"><i class="bi bi-pencil"></i> Modifier</a>
    <x-delete-button :action="route('admin.zones.destroy', $zone)" class="btn btn-sm btn-danger ms-2"
                     :confirm="'Supprimer la zone '.$zone->nom.' ?'" />
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <x-card title="Informations">
                <dl class="mb-0">
                    <dt>Gouvernorat</dt>
                    <dd>{{ $zone->gouvernorat }}</dd>
                    <dt>Code postal</dt>
                    <dd>{{ $zone->code_postal }}</dd>
                    <dt>Population</dt>
                    <dd>{{ $zone->population ? number_format($zone->population, 0, ',', ' ').' habitants' : '—' }}</dd>
                    <dt>Coordonnées</dt>
                    <dd>{{ $zone->latitude ? $zone->latitude.', '.$zone->longitude : 'Non renseignées (météo de Tunis centre)' }}</dd>
                    <dt>Niveau de risque</dt>
                    <dd><span class="badge bg-{{ $zone->niveau_risque_couleur }}">{{ $zone->niveau_risque_label }}</span></dd>
                    <dt>Description</dt>
                    <dd class="mb-0">{{ $zone->description ?: '—' }}</dd>
                </dl>
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Coupures de la zone <span class="badge bg-secondary">{{ $coupures->total() }}</span></h5>
                    <a href="{{ route('admin.coupures.create', ['zone_id' => $zone->id]) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Ajouter une coupure
                    </a>
                </div>

                @forelse ($coupures as $coupure)
                    <a href="{{ route('admin.coupures.show', $coupure) }}"
                       class="d-flex justify-content-between align-items-center border rounded p-2 mb-2 text-decoration-none text-body">
                        <div>
                            <i class="bi {{ $coupure->type_icone }} text-muted"></i>
                            <strong>{{ $coupure->titre }}</strong>
                            <div class="small text-muted">{{ $coupure->date_debut->format('d/m/Y H:i') }} · {{ $coupure->type_label }}</div>
                        </div>
                        <x-coupure.statut-badge :coupure="$coupure" />
                    </a>
                @empty
                    <p class="text-muted mb-0">Aucune coupure enregistrée pour cette zone.</p>
                @endforelse

                @if ($coupures->hasPages())
                    <div class="mt-3">{{ $coupures->links() }}</div>
                @endif
            </x-card>
        </div>
    </div>
@endsection
