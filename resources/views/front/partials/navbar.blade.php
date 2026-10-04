<nav class="navbar navbar-expand-lg navbar-dark ha-navbar sticky-top">
    <div class="container">

        {{-- Logo --}}
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="bi bi-thermometer-sun"></i>
            HeatAlert
        </a>

        {{-- Menu mobile --}}
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#frontNav"
            aria-controls="frontNav"
            aria-expanded="false"
            aria-label="Ouvrir le menu"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="frontNav">

            {{-- Navigation principale --}}
            <ul class="navbar-nav me-auto">

                {{-- Accueil --}}
                <li class="nav-item">
                    <a
                        class="nav-link @if (request()->routeIs('home')) active @endif"
                        href="{{ route('home') }}"
                    >
                        <i class="bi bi-house"></i>
                        Accueil
                    </a>
                </li>

                {{-- Liens des modules --}}
                {{-- Un fichier par module dans resources/views/front/menu/ --}}
                @foreach (glob(resource_path('views/front/menu/*.blade.php')) as $menuItem)
                    @include('front.menu.' . basename($menuItem, '.blade.php'))
                @endforeach

            </ul>

            {{-- Partie droite --}}
            <ul class="navbar-nav">

                @auth

                    {{-- Back Office --}}
                    @if (auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                href="{{ route('admin.dashboard') }}"
                            >
                                <i class="bi bi-speedometer2"></i>
                                Back office
                            </a>
                        </li>
                    @endif

                    {{-- Utilisateur --}}
                    <li class="nav-item dropdown">

                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="bi bi-person-circle"></i>
                            {{ auth()->user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">

                            {{-- Demander de l'aide --}}
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="{{ route('help-requests.create') }}"
                                >
                                    <i class="bi bi-hand-index-thumb me-2"></i>
                                    Demander de l'aide
                                </a>
                            </li>

                            {{-- Proposer son aide --}}
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="{{ route('help-offers.create') }}"
                                >
                                    <i class="bi bi-hand-thumbs-up me-2"></i>
                                    Proposer mon aide
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            {{-- Déconnexion --}}
                            <li>
                                <form
                                    method="POST"
                                    action="{{ route('logout') }}"
                                >
                                    @csrf

                                    <button
                                        class="dropdown-item"
                                        type="submit"
                                    >
                                        <i class="bi bi-box-arrow-right"></i>
                                        Déconnexion
                                    </button>
                                </form>
                            </li>

                        </ul>

                    </li>

                @else

                    {{-- Visiteur --}}
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="{{ route('login') }}"
                        >
                            <i class="bi bi-box-arrow-in-right"></i>
                            Connexion
                        </a>
                    </li>

                    <li class="nav-item">
                        <a
                            class="btn btn-light btn-sm ms-lg-2 mt-1"
                            href="{{ route('register') }}"
                        >
                            <i class="bi bi-person-plus"></i>
                            Inscription
                        </a>
                    </li>

                @endauth

            </ul>

        </div>
    </div>
</nav>