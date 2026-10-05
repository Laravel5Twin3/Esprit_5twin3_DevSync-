@extends('layouts.back')

@section('title', 'Conseils et prévention')
@section('page-title', '💡 Conseils et prévention')

@section('page-actions')
    <a href="{{ route('admin.conseils.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Ajouter un conseil
    </a>
    <a href="{{ route('admin.categories-conseil.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
        <i class="bi bi-tags"></i> Catégories
    </a>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <x-back.stat-card
                label="Total conseils"
                :value="$stats['total']"
                icon="bi-journal-text"
                color="primary"
            />
        </div>
        <div class="col-sm-6 col-lg-3">
            <x-back.stat-card
                label="Conseils actifs"
                :value="$stats['actifs']"
                icon="bi-check-circle-fill"
                color="success"
            />
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Liste des conseils</span>
            <span class="badge bg-secondary">{{ $conseils->total() }} résultats</span>
        </div>
        <div class="card-body p-0">
            @if($conseils->isEmpty())
                <p class="text-center text-muted py-4">Aucun conseil enregistré pour l'instant.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Public</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($conseils as $conseil)
                            <tr>
                                <td class="text-muted small">{{ $conseil->id }}</td>
                                <td class="fw-semibold">{{ $conseil->titre }}</td>
                                <td>
                                    @if($conseil->categorieConseil)
                                        <span class="badge rounded-pill" style="background-color: {{ $conseil->categorieConseil->couleur }}">
                                            <i class="bi {{ $conseil->categorieConseil->icone }}"></i>
                                            {{ $conseil->categorieConseil->nom }}
                                        </span>
                                    @endif
                                </td>
                                <td class="small">{{ $conseil->labelPublicCible() }}</td>
                                <td>
                                    <span class="badge bg-{{ $conseil->badgePriorite() }}">{{ $conseil->labelPriorite() }}</span>
                                </td>
                                <td>
                                    @if($conseil->actif)
                                        <span class="badge bg-success"><i class="bi bi-check-lg"></i> Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.conseils.show', $conseil) }}"
                                           class="btn btn-outline-info" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.conseils.edit', $conseil) }}"
                                           class="btn btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <x-delete-button :action="route('admin.conseils.destroy', $conseil)" />
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($conseils->hasPages())
            <div class="card-footer d-flex justify-content-end">
                {{ $conseils->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
