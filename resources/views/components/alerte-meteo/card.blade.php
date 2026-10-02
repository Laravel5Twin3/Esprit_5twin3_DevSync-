{{-- Carte d'une alerte côté Front Office. Usage : <x-alerte-meteo.card :alerte="$alerte" /> --}}
@props(['alerte'])

<div class="card h-100 shadow-sm border-0 border-top border-4" style="border-color: {{ $alerte->niveauVigilance->couleur }} !important;">
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <x-alerte-meteo.badge-niveau :niveau="$alerte->niveauVigilance" />
            <span class="badge bg-{{ $alerte->statut_couleur }}">{{ $alerte->statut_label }}</span>
        </div>
        <h5 class="card-title mb-1">{{ $alerte->titre }}</h5>
        <div class="small text-muted mb-2"><i class="bi bi-geo-alt"></i> {{ $alerte->zone->nom }} ({{ $alerte->zone->gouvernorat }})</div>
        <div class="display-6 fw-bold mb-2" style="color: {{ $alerte->niveauVigilance->couleur }}">
            {{ rtrim(rtrim(number_format($alerte->temperature_max, 1, ',', ''), '0'), ',') }} °C
        </div>
        <p class="small mb-3">{{ Str::limit($alerte->message, 110) }}</p>
        <div class="mt-auto d-flex justify-content-between align-items-center">
            <span class="small text-muted"><i class="bi bi-calendar-event"></i> {{ $alerte->date_debut->format('d/m H:i') }} → {{ $alerte->date_fin->format('d/m H:i') }}</span>
            <a href="{{ route('alertes-meteo.show', $alerte) }}" class="btn btn-sm btn-ha">Détails</a>
        </div>
    </div>
</div>
