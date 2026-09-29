@extends('layouts.back')

@section('title', 'Points de Fraîcheur')
@section('page-title', '🧊 Points de Fraîcheur')

@section('page-actions')
    <a href="{{ route('admin.points-fraicheur.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Ajouter un point
    </a>
    <a href="{{ route('admin.categories-point.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
        <i class="bi bi-tags"></i> Catégories
    </a>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-back.stat-card
                label="Total points"
                :value="$stats['total']"
                icon="bi-geo-alt-fill"
                color="primary"
            />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-back.stat-card
                label="Points actifs"
                :value="$stats['actifs']"
                icon="bi-check-circle-fill"
                color="success"
            />
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Liste des points de fraîcheur</span>
            <span class="badge bg-secondary">{{ $points->total() }} résultats</span>
        </div>
        <div class="card-body p-0">
            @if($points->isEmpty())
                <p class="text-center text-muted py-4">Aucun point enregistré pour l'instant.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Adresse</th>
                                <th>Horaires</th>
                                <th>Capacité</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($points as $point)
                            <tr>
                                <td class="text-muted small">{{ $point->id }}</td>
                                <td class="fw-semibold">{{ $point->nom }}</td>
                                <td>
                                    @if($point->categoriePoint)
                                        <span class="badge rounded-pill" style="background-color: {{ $point->categoriePoint->couleur }}">
                                            <i class="bi {{ $point->categoriePoint->icone }}"></i>
                                            {{ $point->categoriePoint->nom }}
                                        </span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ Str::limit($point->adresse, 40) }}</td>
                                <td class="small">{{ $point->horaires ?? '—' }}</td>
                                <td class="small">{{ $point->capacite ? number_format($point->capacite) . ' pers.' : '—' }}</td>
                                <td>
                                    @if($point->actif)
                                        <span class="badge bg-success"><i class="bi bi-check-lg"></i> Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.points-fraicheur.show', $point) }}"
                                           class="btn btn-outline-info" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.points-fraicheur.edit', $point) }}"
                                           class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <x-delete-button :action="route('admin.points-fraicheur.destroy', $point)" />
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($points->hasPages())
            <div class="card-footer">
                {{ $points->links() }}
            </div>
        @endif
    </div>
@endsection
