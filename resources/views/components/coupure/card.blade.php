{{-- Carte d'une coupure pour le Front Office. Usage : <x-coupure.card :coupure="$coupure" /> --}}
@props(['coupure'])

<div {{ $attributes->merge(['class' => 'card h-100 shadow-sm border-0 border-top border-4 border-'.$coupure->statut_couleur]) }}>
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="text-muted small"><i class="bi {{ $coupure->type_icone }}"></i> {{ $coupure->type_label }}</span>
            <x-coupure.statut-badge :coupure="$coupure" />
        </div>

        <h5 class="card-title mb-1">{{ $coupure->titre }}</h5>
        <p class="text-muted small mb-3"><i class="bi bi-geo-alt"></i> {{ $coupure->zone->nom }} — {{ $coupure->zone->gouvernorat }}</p>

        <ul class="list-unstyled small mb-3">
            <li><i class="bi bi-play-circle"></i> Début : <strong>{{ $coupure->date_debut->translatedFormat('D d M à H\hi') }}</strong></li>
            <li>
                <i class="bi bi-stop-circle"></i> Fin :
                <strong>{{ $coupure->date_fin?->translatedFormat('D d M à H\hi') ?? 'indéterminée' }}</strong>
            </li>
            @if ($coupure->foyers_touches)
                <li><i class="bi bi-house"></i> {{ number_format($coupure->foyers_touches, 0, ',', ' ') }} foyers touchés</li>
            @endif
        </ul>

        <a href="{{ route('coupures.show', $coupure) }}" class="btn btn-outline-secondary btn-sm mt-auto">
            Détails <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
