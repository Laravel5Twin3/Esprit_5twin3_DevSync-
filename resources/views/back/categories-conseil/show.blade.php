@extends('layouts.back')

@section('title', $categorie->nom)
@section('page-title', '🔍 ' . $categorie->nom)

@section('page-actions')
    <a href="{{ route('admin.categories-conseil.edit', $categorie) }}" class="btn btn-warning btn-sm">
        <i class="bi bi-pencil"></i> Modifier
    </a>
    <a href="{{ route('admin.conseils.create') }}" class="btn btn-primary btn-sm ms-2">
        <i class="bi bi-plus-lg"></i> Nouveau conseil
    </a>
    <a href="{{ route('admin.categories-conseil.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
        <i class="bi bi-arrow-left"></i> Retour aux catégories
    </a>
@endsection

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Catégorie</dt>
            <dd class="col-sm-9">
                <span class="badge rounded-pill fs-6" style="background-color: {{ $categorie->couleur }}">
                    <i class="bi {{ $categorie->icone }}"></i>
                    {{ $categorie->nom }}
                </span>
            </dd>

            <dt class="col-sm-3">Description</dt>
            <dd class="col-sm-9">{{ $categorie->description ?: '—' }}</dd>

            <dt class="col-sm-3">Nombre de conseils</dt>
            <dd class="col-sm-9">
                <span class="badge bg-secondary">{{ $categorie->conseils->count() }}</span>
            </dd>
        </dl>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-semibold">
        Conseils de cette catégorie
    </div>
    <div class="card-body p-0">
        @if($categorie->conseils->isEmpty())
            <p class="text-center text-muted py-4 mb-0">Aucun conseil n'est encore rattaché à cette catégorie.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Niveau de risque</th>
                            <th>Public</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorie->conseils as $conseil)
                        <tr>
                            <td class="fw-semibold">{{ $conseil->titre }}</td>
                            <td>
                                <span class="badge bg-{{ $conseil->badgePriorite() }}">{{ $conseil->labelPriorite() }}</span>
                            </td>
                            <td class="small">{{ $conseil->labelPublicCible() }}</td>
                            <td>
                                @if($conseil->actif)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.conseils.show', $conseil) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
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
