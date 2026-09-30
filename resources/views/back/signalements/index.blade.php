@extends('layouts.back')

@section('title', 'Signalements')
@section('page-title', 'Signalements des habitants')

@section('content')
    {{-- Onglets par statut --}}
    <ul class="nav nav-pills mb-3">
        @foreach (['en_attente' => 'En attente', 'valide' => 'Validés', 'rejete' => 'Rejetés', 'tous' => 'Tous'] as $valeur => $label)
            <li class="nav-item">
                <a class="nav-link @if ($statut === $valeur) active @endif"
                   href="{{ route('admin.signalements.index', ['statut' => $valeur, 'zone_id' => request('zone_id')]) }}">
                    {{ $label }}
                    <span class="badge bg-light text-dark ms-1">{{ $valeur === 'tous' ? $compteurs->sum() : ($compteurs[$valeur] ?? 0) }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <form method="GET" class="card card-body shadow-sm border-0 mb-4">
        <input type="hidden" name="statut" value="{{ $statut }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">Zone</label>
                <select name="zone_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes les zones</option>
                    @foreach ($zones as $id => $nom)
                        <option value="{{ $id }}" @selected(request('zone_id') == $id)>{{ $nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if ($signalements->isEmpty())
                <p class="text-center text-muted py-4 mb-0">Aucun signalement.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Constaté le</th>
                                <th>Zone</th>
                                <th>Habitant</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($signalements as $signalement)
                                <tr>
                                    <td class="small text-nowrap">{{ $signalement->date_constat->format('d/m/Y H:i') }}</td>
                                    <td><a href="{{ route('admin.zones.show', $signalement->zone) }}">{{ $signalement->zone->nom }}</a></td>
                                    <td class="small">{{ $signalement->user->name }}</td>
                                    <td class="small">{{ Str::limit($signalement->description, 70) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $signalement->statut_couleur }}">{{ $signalement->statut_label }}</span>
                                        @if ($signalement->coupure)
                                            <a href="{{ route('admin.coupures.show', $signalement->coupure) }}" class="small d-block">Coupure #{{ $signalement->coupure_id }}</a>
                                        @endif
                                    </td>
                                    <td class="text-end text-nowrap">
                                        @if ($signalement->statut === 'en_attente')
                                            <form method="POST" action="{{ route('admin.signalements.valider', $signalement) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success" title="Valider : rattacher ou créer la coupure"><i class="bi bi-check-lg"></i> Valider</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.signalements.rejeter', $signalement) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary" title="Fausse alerte"><i class="bi bi-x-lg"></i> Rejeter</button>
                                            </form>
                                        @endif
                                        <x-delete-button :action="route('admin.signalements.destroy', $signalement)" label="" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if ($signalements->hasPages())
            <div class="card-footer bg-white d-flex justify-content-end">{{ $signalements->links() }}</div>
        @endif
    </div>

    <p class="small text-muted mt-3">
        <i class="bi bi-info-circle"></i> Valider un signalement le rattache à la coupure en cours dans la zone (ou en crée une).
        Les autres signalements en attente de la même zone, constatés à moins de {{ \App\Models\Signalement::FENETRE_HEURES }} h d'écart, sont regroupés automatiquement.
    </p>
@endsection
