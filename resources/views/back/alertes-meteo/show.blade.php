@extends('layouts.back')

@section('title', $alerte->titre)
@section('page-title', $alerte->titre)

@section('page-actions')
    <a href="{{ route('admin.alertes-meteo.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Alertes</a>
    <a href="{{ route('admin.alertes-meteo.edit', $alerte) }}" class="btn btn-warning btn-sm ms-2"><i class="bi bi-pencil"></i> Modifier</a>
    <x-delete-button :action="route('admin.alertes-meteo.destroy', $alerte)" class="btn btn-sm btn-danger ms-2"
                     :confirm="'Supprimer l\'alerte '.$alerte->titre.' ?'" />
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <x-card>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <x-alerte-meteo.badge-niveau :niveau="$alerte->niveauVigilance" class="fs-6" />
                    <span class="badge bg-{{ $alerte->statut_couleur }} fs-6">{{ $alerte->statut_label }}</span>
                </div>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Zone</dt>
                    <dd class="col-sm-8">
                        <a href="{{ route('admin.zones.show', $alerte->zone) }}">{{ $alerte->zone->nom }}</a>
                        <span class="text-muted">({{ $alerte->zone->gouvernorat }})</span>
                    </dd>
                    <dt class="col-sm-4">Température maximale</dt>
                    <dd class="col-sm-8">{{ $alerte->temperature_max }} °C</dd>
                    <dt class="col-sm-4">Début</dt>
                    <dd class="col-sm-8">{{ $alerte->date_debut->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-4">Fin</dt>
                    <dd class="col-sm-8">{{ $alerte->date_fin->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-4">Message</dt>
                    <dd class="col-sm-8">{{ $alerte->message }}</dd>
                    <dt class="col-sm-4">Consigne du niveau</dt>
                    <dd class="col-sm-8 mb-0">
                        <a href="{{ route('admin.niveaux-vigilance.show', $alerte->niveauVigilance) }}">{{ $alerte->niveauVigilance->nom }}</a> :
                        {{ $alerte->niveauVigilance->consigne }}
                    </dd>
                </dl>
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card title="Autres alertes de la zone">
                @forelse ($autresAlertes as $autre)
                    <a href="{{ route('admin.alertes-meteo.show', $autre) }}"
                       class="d-flex justify-content-between align-items-center border rounded p-2 mb-2 text-decoration-none text-body">
                        <div>
                            <strong class="small">{{ $autre->titre }}</strong>
                            <div class="small text-muted">{{ $autre->date_debut->format('d/m/Y') }}</div>
                        </div>
                        <x-alerte-meteo.badge-niveau :niveau="$autre->niveauVigilance" />
                    </a>
                @empty
                    <p class="text-muted mb-0">Aucune autre alerte.</p>
                @endforelse
            </x-card>
        </div>
    </div>
@endsection
