<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('points-fraicheur.*') ? 'active' : '' }}"
       href="{{ route('points-fraicheur.index') }}">
        <i class="bi bi-droplet-half"></i> Points de fraîcheur
    </a>
</li>
