@extends('layouts.back')

@section('title', 'Alertes météo')
@section('page-title', 'Alertes météo / canicule')

@section('page-actions')
    <a href="{{ route('admin.alertes-meteo.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nouvelle alerte</a>
    <a href="{{ route('admin.alertes-meteo.create') }}#zone_generer" class="btn btn-outline-primary btn-sm ms-2"><i class="bi bi-cloud-sun"></i> Depuis la météo</a>
    <a href="{{ route('admin.niveaux-vigilance.index') }}" class="btn btn-outline-secondary btn-sm ms-2"><i class="bi bi-thermometer-half"></i> Niveaux</a>
@endsection

@section('content')
    {{-- Filtres --}}
    <form method="GET" class="card card-body shadow-sm border-0 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Recherche</label>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Titre ou message">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Zone</label>
                <select name="zone_id" class="form-select">
                    <option value="">Toutes</option>
                    @foreach ($zones as $id => $nom)
                        <option value="{{ $id }}" @selected(request('zone_id') == $id)>{{ $nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Niveau</label>
                <select name="niveau_id" class="form-select">
                    <option value="">Tous</option>
                    @foreach ($niveaux as $id => $nom)
                        <option value="{{ $id }}" @selected(request('niveau_id') == $id)>{{ $nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Statut</label>
                <select name="statut" class="form-select">
                    <option value="">Tous</option>
                    @foreach (App\Models\AlerteMeteo::STATUTS as $value => $label)
                        <option value="{{ $value }}" @selected(request('statut') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-dark flex-grow-1" type="submit"><i class="bi bi-funnel"></i> Filtrer</button>
                <a href="{{ route('admin.alertes-meteo.index') }}" class="btn btn-outline-secondary" title="Réinitialiser"><i class="bi bi-x-lg"></i></a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Liste des alertes</span>
            <span class="badge bg-secondary">{{ $alertes->total() }} résultat(s)</span>
        </div>
        <div class="card-body p-0">
            @if ($alertes->isEmpty())
                <p class="text-center text-muted py-4 mb-0">Aucune alerte trouvée.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Zone</th>
                                <th>Niveau</th>
                                <th>Temp. max</th>
                                <th>Période</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alertes as $alerte)
                                <tr>
                                    <td class="fw-semibold">{{ $alerte->titre }}</td>
                                    <td>{{ $alerte->zone->nom }}</td>
                                    <td><x-alerte-meteo.badge-niveau :niveau="$alerte->niveauVigilance" /></td>
                                    <td>{{ $alerte->temperature_max }} °C</td>
                                    <td class="small text-nowrap">
                                        {{ $alerte->date_debut->format('d/m H:i') }} → {{ $alerte->date_fin->format('d/m H:i') }}
                                    </td>
                                    <td><span class="badge bg-{{ $alerte->statut_couleur }}">{{ $alerte->statut_label }}</span></td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.alertes-meteo.show', $alerte) }}" class="btn btn-sm btn-outline-info" title="Voir"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.alertes-meteo.edit', $alerte) }}" class="btn btn-sm btn-outline-warning" title="Modifier"><i class="bi bi-pencil"></i></a>
                                        <x-delete-button :action="route('admin.alertes-meteo.destroy', $alerte)" label=""
                                                         :confirm="'Supprimer l\'alerte '.$alerte->titre.' ?'" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($alertes->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">{{ $alertes->links() }}</div>
        @endif
    </div>
@endsection
