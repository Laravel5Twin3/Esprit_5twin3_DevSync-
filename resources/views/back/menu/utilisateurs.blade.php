@can('gerer utilisateurs')
    <li class="nav-item">
        <a class="nav-link @if (request()->routeIs('admin.utilisateurs.*')) active @endif" href="{{ route('admin.utilisateurs.index') }}">
            <i class="bi bi-people"></i> Utilisateurs
        </a>
    </li>
@endcan
