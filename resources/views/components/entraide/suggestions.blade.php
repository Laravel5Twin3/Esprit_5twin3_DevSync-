{{--
    Suggestions de la mise en relation intelligente.
    Usage : <x-entraide.suggestions :suggestions="$suggestions" type="offres" />   (sur une demande)
            <x-entraide.suggestions :suggestions="$suggestions" type="demandes" /> (sur une offre)
--}}
@props(['suggestions', 'type'])

@php
    $estOffre = $type === 'offres';
    $titre = $estOffre ? 'Voisins qui peuvent vous aider' : 'Demandes qui correspondent à cette offre';
    $route = $estOffre ? 'help-offers.show' : 'help-requests.show';
@endphp

<x-card class="mt-3">
    <h6 class="mb-1"><i class="bi bi-stars text-warning"></i> {{ $titre }}</h6>
    <p class="small text-muted mb-3">Suggestions automatiques selon le texte, la catégorie, le quartier et les disponibilités.</p>

    @forelse ($suggestions as $suggestion)
        @php($couleur = $suggestion['score'] >= 60 ? 'success' : ($suggestion['score'] >= 40 ? 'primary' : 'secondary'))
        <a href="{{ route($route, $suggestion['annonce']) }}" class="d-block text-decoration-none text-body border rounded p-2 mb-2">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <strong class="small">{{ $suggestion['annonce']->title }}</strong>
                <span class="badge bg-{{ $couleur }}" title="Score de compatibilité">{{ $suggestion['score'] }} %</span>
            </div>
            <div class="small text-muted mb-1">
                <i class="bi bi-person"></i> {{ $suggestion['annonce']->user->name }}
                @if ($suggestion['annonce']->location)
                    · <i class="bi bi-geo-alt"></i> {{ $suggestion['annonce']->location }}
                @endif
            </div>
            @foreach ($suggestion['raisons'] as $raison)
                <span class="badge rounded-pill text-bg-light border fw-normal">{{ $raison }}</span>
            @endforeach
        </a>
    @empty
        <p class="small text-muted mb-0">
            {{ $estOffre ? 'Aucune offre compatible pour le moment.' : 'Aucune demande compatible pour le moment.' }}
        </p>
    @endforelse
</x-card>
