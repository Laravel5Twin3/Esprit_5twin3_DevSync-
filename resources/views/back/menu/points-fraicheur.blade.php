<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.points-fraicheur.*') || request()->routeIs('admin.categories-point.*') ? 'active' : '' }}"
       href="{{ route('admin.points-fraicheur.index') }}">
        <i class="bi bi-droplet-half"></i>
        <span>Points de fraîcheur</span>
    </a>
</li>
