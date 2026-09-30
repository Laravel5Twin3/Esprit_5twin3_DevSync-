{{-- Usage : <x-coupure.statut-badge :coupure="$coupure" /> --}}
@props(['coupure'])

<span {{ $attributes->merge(['class' => 'badge bg-'.$coupure->statut_couleur]) }}>
    @if ($coupure->statut === 'en_cours')
        <i class="bi bi-circle-fill small"></i>
    @endif
    {{ $coupure->statut_label }}
</span>
