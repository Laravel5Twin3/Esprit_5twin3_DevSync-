<li class="nav-item">
    <a
        class="nav-link @if (request()->routeIs('admin.entraide.*')) active @endif"
        href="{{ route('admin.entraide.index') }}"
    >
        <i class="bi bi-people"></i>
        Entraide entre voisins
    </a>
</li>
