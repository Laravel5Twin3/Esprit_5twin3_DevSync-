<li class="nav-item">
    <a class="nav-link @if (request()->routeIs('admin.coupures.*')) active @endif" href="{{ route('admin.coupures.index') }}">
        <i class="bi bi-lightning-charge"></i> Coupures
    </a>
</li>
<li class="nav-item">
    <a class="nav-link @if (request()->routeIs('admin.zones.*')) active @endif" href="{{ route('admin.zones.index') }}">
        <i class="bi bi-map"></i> Zones
    </a>
</li>
<li class="nav-item">
    <a class="nav-link d-flex align-items-center @if (request()->routeIs('admin.signalements.*')) active @endif" href="{{ route('admin.signalements.index') }}">
        <i class="bi bi-megaphone me-2"></i> Signalements
        @if ($enAttente = \App\Models\Signalement::enAttente()->count())
            <span class="badge bg-warning text-dark ms-auto">{{ $enAttente }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link @if (request()->routeIs('admin.coupures-ia.*')) active @endif" href="{{ route('admin.coupures-ia.index') }}">
        <i class="bi bi-cpu"></i> Prédiction coupures
    </a>
</li>
