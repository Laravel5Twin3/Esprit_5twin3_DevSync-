<li class="nav-item dropdown">

    <a
        class="nav-link dropdown-toggle
            @if (
                request()->routeIs('help-offers.*') ||
                request()->routeIs('help-requests.*') ||
                request()->routeIs('help-responses.*')
            )
                active
            @endif"
        href="#"
        role="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
    >
        <i class="bi bi-people"></i>
        Entraide
    </a>

    <ul class="dropdown-menu">

        {{-- Liste des demandes --}}
        <li>
            <a
                class="dropdown-item"
                href="{{ route('help-requests.index') }}"
            >
                <i class="bi bi-hand-index-thumb me-2"></i>
                Demandes d'aide
            </a>
        </li>

        {{-- Liste des offres --}}
        <li>
            <a
                class="dropdown-item"
                href="{{ route('help-offers.index') }}"
            >
                <i class="bi bi-hand-thumbs-up me-2"></i>
                Offres d'aide
            </a>
        </li>

        @auth

            <li>
                <hr class="dropdown-divider">
            </li>

            {{-- Créer une demande --}}
            <li>
                <a
                    class="dropdown-item"
                    href="{{ route('help-requests.create') }}"
                >
                    <i class="bi bi-plus-circle me-2"></i>
                    Demander de l'aide
                </a>
            </li>

            {{-- Créer une offre --}}
            <li>
                <a
                    class="dropdown-item"
                    href="{{ route('help-offers.create') }}"
                >
                    <i class="bi bi-heart me-2"></i>
                    Proposer mon aide
                </a>
            </li>

        @endauth

    </ul>

</li>
