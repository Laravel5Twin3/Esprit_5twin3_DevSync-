<li class="nav-item">
    <a class="nav-link @if (request()->routeIs('alertes-meteo.*')) active @endif" href="{{ route('alertes-meteo.index') }}">
        <i class="bi bi-thermometer-sun"></i> Alertes météo
    </a>
</li>
