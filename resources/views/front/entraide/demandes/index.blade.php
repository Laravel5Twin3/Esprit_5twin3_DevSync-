@extends('layouts.front')

@section('title', 'Demandes d\'aide')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">

            <h1 class="fw-bold">
                <i class="bi bi-hand-index-thumb"></i>
                Demandes d'aide
            </h1>

            <p class="lead mb-3">
                Découvrez les voisins qui ont besoin d'un coup de main.
            </p>

            @auth
                <a href="{{ route('help-requests.create') }}"
                   class="btn btn-light">
                    <i class="bi bi-plus-circle"></i>
                    Demander de l'aide
                </a>
            @endauth

        </div>
    </section>
@endsection

@section('content')

    @if ($requests->isEmpty())

        <x-card class="text-center py-5">

            <i class="bi bi-people fs-1 text-primary"></i>

            <h4 class="mt-3">
                Aucune demande d'aide
            </h4>

            <p class="text-muted">
                Il n'y a actuellement aucune demande publiée.
            </p>

            @auth
                <a href="{{ route('help-requests.create') }}"
                   class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Publier une demande
                </a>
            @endauth

        </x-card>

    @else

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2 class="h4 mb-1">
                    Demandes de vos voisins
                </h2>

                <p class="text-muted mb-0">
                    {{ $requests->total() }} demande(s)
                </p>
            </div>

            @auth
                <a href="{{ route('help-requests.create') }}"
                   class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Nouvelle demande
                </a>
            @endauth

        </div>

        <div class="row g-4">

            @foreach ($requests as $request)

                <div class="col-md-6 col-xl-4">

                    <x-card class="h-100">

                        <div class="d-flex justify-content-between align-items-start mb-3">

                            @if ($request->status === 'open')
                                <span class="badge bg-success">
                                    <i class="bi bi-unlock"></i>
                                    Ouverte
                                </span>
                            @elseif ($request->status === 'in_progress')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split"></i>
                                    En cours
                                </span>
                            @elseif ($request->status === 'completed')
                                <span class="badge bg-secondary">
                                    Terminée
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    Annulée
                                </span>
                            @endif

                            @if ($request->category)
                                <span class="badge bg-light text-dark">
                                    {{ $request->category }}
                                </span>
                            @endif

                        </div>

                        <h5 class="fw-bold">
                            {{ $request->title }}
                        </h5>

                        <p class="text-muted">
                            {{ Str::limit($request->description, 120) }}
                        </p>

                        <div class="small text-muted mb-3">

                            <div class="mb-1">
                                <i class="bi bi-person"></i>
                                {{ $request->user->name }}
                            </div>

                            @if ($request->location)
                                <div class="mb-1">
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $request->location }}
                                </div>
                            @endif

                            <div>
                                <i class="bi bi-people"></i>
                                {{ $request->responses_count }}
                                proposition(s)
                            </div>

                        </div>

                        <a href="{{ route('help-requests.show', $request) }}"
                           class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye"></i>
                            Voir la demande
                        </a>

                    </x-card>

                </div>

            @endforeach

        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $requests->links() }}
        </div>

    @endif

@endsection
