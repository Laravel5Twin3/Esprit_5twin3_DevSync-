@extends('layouts.front')

@section('title', $conseil->titre . ' – Conseils')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:60px; height:60px; background-color: {{ $conseil->categorieConseil->couleur ?? '#fd7e14' }}; flex-shrink:0;">
                <i class="bi {{ $conseil->categorieConseil->icone ?? 'bi-lightbulb' }} text-white fs-3"></i>
            </div>
            <div>
                <h1 class="h3 mb-1">{{ $conseil->titre }}</h1>
                <span class="badge rounded-pill" style="background-color: {{ $conseil->categorieConseil->couleur ?? '#fd7e14' }}">
                    {{ $conseil->categorieConseil->nom ?? '—' }}
                </span>
                <span class="badge bg-{{ $conseil->badgePriorite() }} ms-1">{{ $conseil->labelPriorite() }}</span>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <p class="text-muted mb-3">
                    <i class="bi bi-people"></i> Public concerné : <strong>{{ $conseil->labelPublicCible() }}</strong>
                </p>
                @if($conseil->resume)
                    <p class="lead">{{ $conseil->resume }}</p>
                @endif
                <div style="white-space: pre-line">{{ $conseil->contenu }}</div>
            </div>
        </div>

        <a href="{{ route('conseils.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Tous les conseils
        </a>
    </div>

    <div class="col-lg-4">
        @if($similaires->isNotEmpty())
            <h5 class="fw-bold mb-3">Dans la même catégorie</h5>
            <div class="d-flex flex-column gap-3">
                @foreach($similaires as $proche)
                    <a href="{{ route('conseils.show', $proche) }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="fw-semibold text-dark">{{ $proche->titre }}</div>
                                <div class="small text-muted mt-1">{{ $proche->resume ?? Str::limit($proche->contenu, 80) }}</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
