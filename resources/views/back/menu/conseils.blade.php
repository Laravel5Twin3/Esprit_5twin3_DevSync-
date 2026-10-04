<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.conseils.*') || request()->routeIs('admin.categories-conseil.*') ? 'active' : '' }}"
       href="{{ route('admin.conseils.index') }}">
        <i class="bi bi-lightbulb"></i>
        <span>Conseils et prévention</span>
    </a>
</li>
