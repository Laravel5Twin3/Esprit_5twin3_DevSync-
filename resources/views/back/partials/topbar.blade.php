<header class="ha-topbar d-flex align-items-center justify-content-between px-4">
    <button id="sidebarToggle" class="btn btn-outline-secondary btn-sm d-lg-none" type="button">
        <i class="bi bi-list"></i>
    </button>

    <div class="ms-auto d-flex align-items-center gap-3">
        <a href="{{ route('profil.show') }}" class="text-muted text-decoration-none d-flex align-items-center gap-2"><x-avatar :user="auth()->user()" size="28" /> {{ auth()->user()->name }}</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-box-arrow-right"></i> Déconnexion</button>
        </form>
    </div>
</header>
