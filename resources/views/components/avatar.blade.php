{{-- Photo de profil, ou initiales si l'utilisateur n'en a pas. Usage : <x-avatar :user="$user" size="40" /> --}}
@props(['user', 'size' => 32])

@if ($user->photo_url)
    <img src="{{ $user->photo_url }}" alt="Photo de {{ $user->name }}"
         {{ $attributes->merge(['class' => 'rounded-circle object-fit-cover']) }}
         width="{{ $size }}" height="{{ $size }}">
@else
    <span {{ $attributes->merge(['class' => 'rounded-circle d-inline-flex align-items-center justify-content-center fw-semibold bg-secondary-subtle text-secondary-emphasis']) }}
          style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ round($size * 0.4) }}px;" aria-hidden="true">
        {{ $user->initiales }}
    </span>
@endif
