<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('conseils.*') ? 'active' : '' }}"
       href="{{ route('conseils.index') }}">
        <i class="bi bi-lightbulb"></i> Conseils
    </a>
</li>
