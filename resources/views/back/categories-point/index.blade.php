@extends('layouts.back')

@section('title', 'Catégories de points')
@section('page-title', '🏷️ Catégories de points')

@section('page-actions')
    <a href="{{ route('admin.categories-point.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Nouvelle catégorie
    </a>
    <a href="{{ route('admin.points-fraicheur.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
        <i class="bi bi-arrow-left"></i> Points de fraîcheur
    </a>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-0">
        @if($categories->isEmpty())
            <p class="text-center text-muted py-4">Aucune catégorie enregistrée.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Icône</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Nb. points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $cat)
                        <tr>
                            <td>
                                <span class="badge rounded-pill fs-6" style="background-color: {{ $cat->couleur }}">
                                    <i class="bi {{ $cat->icone }}"></i>
                                </span>
                            </td>
                            <td class="fw-semibold">{{ $cat->nom }}</td>
                            <td class="small text-muted">{{ Str::limit($cat->description, 60) ?? '—' }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $cat->point_fraicheurs_count }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.categories-point.edit', $cat) }}"
                                       class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <x-delete-button :action="route('admin.categories-point.destroy', $cat)" />
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($categories->hasPages())
        <div class="card-footer">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
