@extends('layouts.back')

@section('title', 'Prédiction coupures')
@section('page-title', 'Prédiction coupures')

@section('page-actions')
    <form method="POST" action="{{ route('admin.coupures-ia.entrainer') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-arrow-repeat"></i> Réentraîner le modèle</button>
    </form>
    <a href="{{ route('coupures.previsions') }}" class="btn btn-outline-secondary btn-sm ms-2" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Vue habitants</a>
@endsection

@section('content')
    @if (! $infos)
        <div class="alert alert-warning">
            Pas assez de données pour entraîner le modèle. Il faut un historique de coupures résolues sur les
            {{ \App\Services\Coupure\PredictionCoupureService::JOURS_HISTORIQUE }} derniers jours.
        </div>
    @else
        <p class="text-muted small">
            Modèle entraîné le {{ \Carbon\Carbon::parse($infos['entraine_le'])->format('d/m/Y à H:i') }}.
            Réentraînez-le après avoir ajouté ou modifié des coupures pour qu'il tienne compte des nouvelles données.
        </p>

        <x-card title="Prévisions des prochains jours">
            <x-coupure.tableau-previsions :previsions="$previsions" />
        </x-card>
    @endif
@endsection
