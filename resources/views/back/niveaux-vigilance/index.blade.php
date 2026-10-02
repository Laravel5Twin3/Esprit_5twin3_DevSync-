@extends('layouts.back')

@section('title', 'Niveaux de vigilance')
@section('page-title', 'Niveaux de vigilance canicule')

@section('page-actions')
    <a href="{{ route('admin.niveaux-vigilance.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Nouveau niveau</a>
    @if (Route::has('admin.alertes-meteo.index'))
        <a href="{{ route('admin.alertes-meteo.index') }}" class="btn btn-outline-secondary btn-sm ms-2"><i class="bi bi-thermometer-sun"></i> Alertes</a>
    @endif
@endsection

@section('content')
    <div class="alert alert-light border small">
        <i class="bi bi-info-circle"></i>
        Les seuils de température servent à choisir <strong>automatiquement</strong> le niveau d'une alerte
        lorsque l'administrateur ne le précise pas.
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Liste des niveaux</span>
            <span class="badge bg-secondary">{{ $niveaux->count() }} niveau(x)</span>
        </div>
        <div class="card-body p-0">
            @if ($niveaux->isEmpty())
                <p class="text-center text-muted py-4 mb-0">Aucun niveau de vigilance.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ordre</th>
                                <th>Niveau</th>
                                <th>Plage de température</th>
                                <th>Consigne</th>
                                <th class="text-center">Alertes actives / total</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($niveaux as $niveau)
                                <tr>
                                    <td>{{ $niveau->ordre }}</td>
                                    <td><x-alerte-meteo.badge-niveau :niveau="$niveau" /></td>
                                    <td class="text-nowrap">{{ $niveau->plage }}</td>
                                    <td class="small text-muted">{{ Str::limit($niveau->consigne, 70) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $niveau->alertes_actives_count ? 'bg-danger' : 'bg-light text-dark' }}">{{ $niveau->alertes_actives_count }}</span>
                                        / {{ $niveau->alertes_count }}
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('admin.niveaux-vigilance.show', $niveau) }}" class="btn btn-sm btn-outline-info" title="Voir"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.niveaux-vigilance.edit', $niveau) }}" class="btn btn-sm btn-outline-warning" title="Modifier"><i class="bi bi-pencil"></i></a>
                                        <x-delete-button :action="route('admin.niveaux-vigilance.destroy', $niveau)" label=""
                                                         :confirm="'Supprimer le niveau '.$niveau->nom.' ?'" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
