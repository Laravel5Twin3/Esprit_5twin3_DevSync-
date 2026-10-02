{{-- Badge coloré d'un niveau de vigilance.
     Usage : <x-alerte-meteo.badge-niveau :niveau="$alerte->niveauVigilance" /> --}}
@props(['niveau'])

@if ($niveau)
    <span {{ $attributes->merge(['class' => 'badge rounded-pill']) }}
          style="background-color: {{ $niveau->couleur }}; color: {{ $niveau->couleur_texte }};">
        <i class="bi bi-thermometer-sun"></i> {{ $niveau->nom }}
    </span>
@else
    <span class="badge bg-secondary">—</span>
@endif
