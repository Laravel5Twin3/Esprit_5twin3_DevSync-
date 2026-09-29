@props(['icon', 'label', 'value', 'color' => 'primary'])

<div class="card border-0 shadow-sm ha-stat-card border-start border-4 border-{{ $color }}">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div>
            <div class="text-uppercase small text-{{ $color }} fw-bold">{{ $label }}</div>
            <div class="h4 mb-0 fw-bold">{{ $value }}</div>
        </div>
        <i class="bi {{ $icon }} fs-1 text-body-tertiary"></i>
    </div>
</div>
