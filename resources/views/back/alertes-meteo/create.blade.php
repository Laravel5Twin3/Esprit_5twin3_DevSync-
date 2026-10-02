@extends('layouts.back')

@section('title', 'Nouvelle alerte météo')
@section('page-title', 'Nouvelle alerte météo')

@section('page-actions')
    <a href="{{ route('admin.alertes-meteo.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Retour</a>
@endsection

@section('content')
    {{-- Valeur ajoutée : génération depuis les prévisions météo réelles --}}
    <x-card class="mb-4">
        <form method="GET" action="{{ route('admin.alertes-meteo.generer') }}" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="zone_generer" class="form-label small"><i class="bi bi-cloud-sun"></i> Générer depuis les prévisions Open-Meteo</label>
                <select id="zone_generer" name="zone_id" class="form-select">
                    @foreach ($zones as $id => $nom)
                        <option value="{{ $id }}" @selected(($zoneGeneree->id ?? $alerte->zone_id) == $id)>{{ $nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-magic"></i> Pré-remplir avec la météo réelle</button>
            </div>
        </form>

        @isset($previsions)
            <div class="table-responsive mt-3">
                <table class="table table-sm text-center align-middle mb-0">
                    <tr class="small text-muted">
                        @foreach ($previsions as $jour)
                            <td>{{ $jour['date']->translatedFormat('D d/m') }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($previsions as $jour)
                            <td>
                                <div class="fw-bold">{{ $jour['temperature'] }} °C</div>
                                <x-alerte-meteo.badge-niveau :niveau="$jour['niveau']" />
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
            <p class="small text-muted mt-2 mb-0">
                <i class="bi bi-info-circle"></i> Formulaire pré-rempli avec le jour le plus chaud pour {{ $zoneGeneree->nom }}.
                Vérifiez puis publiez.
            </p>
        @endisset
    </x-card>

    <x-card>
        <form method="POST" action="{{ route('admin.alertes-meteo.store') }}" novalidate>
            @csrf
            @include('back.alertes-meteo._form')
            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Publier l'alerte</button>
        </form>
    </x-card>
@endsection
