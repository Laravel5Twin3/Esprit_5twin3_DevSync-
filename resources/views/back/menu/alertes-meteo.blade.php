<li class="nav-item">
    <a class="nav-link d-flex align-items-center @if (request()->routeIs('admin.alertes-meteo.*')) active @endif" href="{{ route('admin.alertes-meteo.index') }}">
        <i class="bi bi-thermometer-sun me-2"></i> Alertes météo
        @if ($alertesEnCours = \App\Models\AlerteMeteo::enCours()->count())
            <span class="badge bg-danger ms-auto">{{ $alertesEnCours }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link @if (request()->routeIs('admin.niveaux-vigilance.*')) active @endif" href="{{ route('admin.niveaux-vigilance.index') }}">
        <i class="bi bi-thermometer-half"></i> Niveaux de vigilance
    </a>
</li>
