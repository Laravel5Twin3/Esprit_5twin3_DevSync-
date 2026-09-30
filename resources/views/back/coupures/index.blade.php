@extends('layouts.back')

@section('title', 'Coupures')
@section('page-title', 'Coupures de courant')

@section('page-actions')
    <a href="{{ route('admin.coupures.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nouvelle coupure</a>
    <a href="{{ route('admin.zones.index') }}" class="btn btn-outline-secondary btn-sm ms-2"><i class="bi bi-map"></i> Zones</a>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3"><x-back.stat-card icon="bi-lightning-charge" label="Total" :value="$stats['total']" color="primary" /></div>
        <div class="col-sm-6 col-xl-3"><x-back.stat-card icon="bi-exclamation-triangle" label="En cours" :value="$stats['en_cours']" color="danger" /></div>
        <div class="col-sm-6 col-xl-3"><x-back.stat-card icon="bi-calendar-event" label="Prévues" :value="$stats['prevues']" color="warning" /></div>
        <div class="col-sm-6 col-xl-3"><x-back.stat-card icon="bi-house" label="Foyers privés de courant" :value="number_format($stats['foyers'], 0, ',', ' ')" color="info" /></div>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="card card-body shadow-sm border-0 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Recherche</label>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Titre…">
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
                <label class="form-label small">Statut</label>
                <select name="statut" class="form-select">
                    <option value="">Tous</option>
                    @foreach (App\Models\Coupure::STATUTS as $value => $label)
                        <option value="{{ $value }}" @selected(request('statut') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Type</label>
                <select name="type" class="form-select">
                    <option value="">Tous</option>
                    @foreach (App\Models\Coupure::TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-dark flex-grow-1" type="submit"><i class="bi bi-funnel"></i> Filtrer</button>
                <a href="{{ route('admin.coupures.index') }}" class="btn btn-outline-secondary" title="Réinitialiser"><i class="bi bi-x-lg"></i></a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Liste des coupures</span>
            <span class="badge bg-secondary">{{ $coupures->total() }} résultat(s)</span>
        </div>
        <div class="card-body p-0">
            @if ($coupures->isEmpty())
                <p class="text-center text-muted py-4 mb-0">Aucune coupure trouvée.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Zone</th>
                                <th>Type</th>
                                <th>Début</th>
                                <th>Durée</th>
                                <th>Foyers</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupures as $coupure)
                                <tr>
                                    <td class="fw-semibold">{{ Str::limit($coupure->titre, 40) }}</td>
                                    <td><a href="{{ route('admin.zones.show', $coupure->zone) }}">{{ $coupure->zone->nom }}</a></td>
                                    <td class="small"><i class="bi {{ $coupure->type_icone }}"></i> {{ $coupure->type_label }}</td>
                                    <td class="small">{{ $coupure->date_debut->format('d/m/Y H:i') }}</td>
                                    <td class="small">{{ $coupure->duree ?? '—' }}</td>
                                    <td class="small">{{ $coupure->foyers_touches ? number_format($coupure->foyers_touches, 0, ',', ' ') : '—' }}</td>
                                    <td><x-coupure.statut-badge :coupure="$coupure" /></td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.coupures.show', $coupure) }}" class="btn btn-sm btn-outline-info" title="Voir"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.coupures.edit', $coupure) }}" class="btn btn-sm btn-outline-warning" title="Modifier"><i class="bi bi-pencil"></i></a>
                                        <x-delete-button :action="route('admin.coupures.destroy', $coupure)" label="" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($coupures->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">{{ $coupures->links() }}</div>
        @endif
    </div>
@endsection
