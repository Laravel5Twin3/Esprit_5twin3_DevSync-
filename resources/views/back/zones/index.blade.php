@extends('layouts.back')

@section('title', 'Zones')
@section('page-title', 'Zones du réseau')

@section('page-actions')
    <a href="{{ route('admin.zones.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nouvelle zone</a>
    <a href="{{ route('admin.coupures.index') }}" class="btn btn-outline-secondary btn-sm ms-2"><i class="bi bi-lightning-charge"></i> Coupures</a>
@endsection

@section('content')
    {{-- Filtres --}}
    <form method="GET" class="card card-body shadow-sm border-0 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">Recherche</label>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nom ou code postal">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Gouvernorat</label>
                <select name="gouvernorat" class="form-select">
                    <option value="">Tous</option>
                    @foreach (App\Models\Zone::GOUVERNORATS as $gouvernorat)
                        <option value="{{ $gouvernorat }}" @selected(request('gouvernorat') === $gouvernorat)>{{ $gouvernorat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Niveau de risque</label>
                <select name="niveau_risque" class="form-select">
                    <option value="">Tous</option>
                    @foreach (App\Models\Zone::NIVEAUX_RISQUE as $value => $label)
                        <option value="{{ $value }}" @selected(request('niveau_risque') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-dark flex-grow-1" type="submit"><i class="bi bi-funnel"></i> Filtrer</button>
                <a href="{{ route('admin.zones.index') }}" class="btn btn-outline-secondary" title="Réinitialiser"><i class="bi bi-x-lg"></i></a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Liste des zones</span>
            <span class="badge bg-secondary">{{ $zones->total() }} résultat(s)</span>
        </div>
        <div class="card-body p-0">
            @if ($zones->isEmpty())
                <p class="text-center text-muted py-4 mb-0">Aucune zone trouvée.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Gouvernorat</th>
                                <th>Code postal</th>
                                <th>Population</th>
                                <th>Risque</th>
                                <th class="text-center">Coupures actives / total</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($zones as $zone)
                                <tr>
                                    <td class="fw-semibold">{{ $zone->nom }}</td>
                                    <td>{{ $zone->gouvernorat }}</td>
                                    <td>{{ $zone->code_postal }}</td>
                                    <td>{{ $zone->population ? number_format($zone->population, 0, ',', ' ') : '—' }}</td>
                                    <td><span class="badge bg-{{ $zone->niveau_risque_couleur }}">{{ $zone->niveau_risque_label }}</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ $zone->coupures_actives_count ? 'bg-danger' : 'bg-light text-dark' }}">{{ $zone->coupures_actives_count }}</span>
                                        / {{ $zone->coupures_count }}
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.zones.show', $zone) }}" class="btn btn-sm btn-outline-info" title="Voir"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.zones.edit', $zone) }}" class="btn btn-sm btn-outline-warning" title="Modifier"><i class="bi bi-pencil"></i></a>
                                        <x-delete-button :action="route('admin.zones.destroy', $zone)" label=""
                                                         :confirm="'Supprimer la zone '.$zone->nom.' ?'" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($zones->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">{{ $zones->links() }}</div>
        @endif
    </div>
@endsection
