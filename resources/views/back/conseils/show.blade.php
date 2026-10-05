@extends('layouts.back')

@section('title', $conseil->titre)
@section('page-title', '🔍 ' . $conseil->titre)

@section('page-actions')
    <a href="{{ route('admin.conseils.edit', $conseil) }}" class="btn btn-warning btn-sm">
        <i class="bi bi-pencil"></i> Modifier
    </a>
    <a href="{{ route('admin.conseils.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Catégorie</dt>
            <dd class="col-sm-9">
                @if($conseil->categorieConseil)
                    <span class="badge rounded-pill fs-6"
                          style="background-color: {{ $conseil->categorieConseil->couleur }}">
                        <i class="bi {{ $conseil->categorieConseil->icone }}"></i>
                        {{ $conseil->categorieConseil->nom }}
                    </span>
                @endif
            </dd>

            <dt class="col-sm-3">Public</dt>
            <dd class="col-sm-9">{{ $conseil->labelPublicCible() }}</dd>

            <dt class="col-sm-3">Priorité</dt>
            <dd class="col-sm-9">
                <span class="badge bg-{{ $conseil->badgePriorite() }}">{{ $conseil->labelPriorite() }}</span>
            </dd>

            <dt class="col-sm-3">Statut</dt>
            <dd class="col-sm-9">
                @if($conseil->actif)
                    <span class="badge bg-success">✓ Actif</span>
                @else
                    <span class="badge bg-secondary">Inactif</span>
                @endif
            </dd>

            @if($conseil->resume)
            <dt class="col-sm-3">Résumé</dt>
            <dd class="col-sm-9">{{ $conseil->resume }}</dd>
            @endif

            <dt class="col-sm-3">Contenu</dt>
            <dd class="col-sm-9" style="white-space: pre-line">{{ $conseil->contenu }}</dd>
        </dl>
    </div>
</div>
@endsection
