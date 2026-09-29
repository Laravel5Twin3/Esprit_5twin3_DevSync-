<aside class="ha-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="ha-sidebar-brand">
        <i class="bi bi-thermometer-sun"></i> HeatAlert <small>Admin</small>
    </a>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link @if (request()->routeIs('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Tableau de bord
            </a>
        </li>

        <li class="ha-sidebar-heading">Modules</li>
        {{-- Liens des modules : un fichier par module dans resources/views/back/menu/ --}}
        @foreach (glob(resource_path('views/back/menu/*.blade.php')) as $menuItem)
            @include('back.menu.'.basename($menuItem, '.blade.php'))
        @endforeach

        <li class="ha-sidebar-heading">Site</li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('home') }}"><i class="bi bi-house"></i> Voir le Front Office</a>
        </li>
    </ul>
</aside>
