@extends('layouts.back')

@section('title', 'Niveau '.$niveau->nom)
@section('page-title', 'Niveau de vigilance « '.$niveau->nom.' »')

@section('page-actions')
    <a href="{{ route('admin.niveaux-vigilance.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Niveaux</a>
    <a href="{{ route('admin.niveaux-vigilance.edit', $niveau) }}" class="btn btn-warning btn-sm ms-2"><i class="bi bi-pencil"></i> Modifier</a>
    <x-delete-button :action="route('admin.niveaux-vigilance.destroy', $niveau)" class="btn btn-sm btn-danger ms-2"
                     :confirm="'Supprimer le niveau '.$niveau->nom.' ?'" />
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <x-card title="Informations">
                <div class="mb-3"><x-alerte-meteo.badge-niveau :niveau="$niveau" class="fs-6" /></div>
                <dl class="mb-0">
                    <dt>Plage de température</dt>
                    <dd>{{ $niveau->plage }}</dd>
                    <dt>Ordre de gravité</dt>
                    <dd>{{ $niveau->ordre }}</dd>
                    <dt>Couleur</dt>
                    <dd><span class="d-inline-block rounded border align-middle" style="width:1.2rem;height:1.2rem;background:{{ $niveau->couleur }}"></span> {{ $niveau->couleur }}</dd>
                    <dt>Consigne</dt>
                    <dd class="mb-0">{{ $niveau->consigne }}</dd>
                </dl>
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Alertes de ce niveau <span class="badge bg-secondary">{{ $alertes->total() }}</span></h5>
                    @if (Route::has('admin.alertes-meteo.create'))
                        <a href="{{ route('admin.alertes-meteo.create', ['niveau_vigilance_id' => $niveau->id]) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg"></i> Ajouter une alerte
                        </a>
                    @endif
                </div>

                @forelse ($alertes as $alerte)
                    <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                        <div>
                            <strong>{{ $alerte->titre }}</strong>
                            <div class="small text-muted">
                                <i class="bi bi-geo-alt"></i> {{ $alerte->zone->nom }} ·
                                {{ $alerte->date_debut->format('d/m/Y H:i') }} → {{ $alerte->date_fin->format('d/m/Y H:i') }} ·
                                {{ $alerte->temperature_max }} °C
                            </div>
                        </div>
                        <span class="badge bg-{{ $alerte->statut_couleur }}">{{ $alerte->statut_label }}</span>
                    </div>
                @empty
                    <p class="text-muted mb-0">Aucune alerte n'utilise ce niveau.</p>
                @endforelse

                @if ($alertes->hasPages())
                    <div class="mt-3">{{ $alertes->links() }}</div>
                @endif
            </x-card>
        </div>
    </div>
@endsection
