@props(['title' => null])

<div {{ $attributes->merge(['class' => 'card shadow-sm border-0']) }}>
    @if ($title)
        <div class="card-header bg-white fw-semibold">{{ $title }}</div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="card-footer bg-white">{{ $footer }}</div>
    @endisset
</div>
