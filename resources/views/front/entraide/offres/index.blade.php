@extends('layouts.front')

@section('title', 'Offres d\'aide')

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">
            <h1 class="fw-bold">
                <i class="bi bi-hand-thumbs-up"></i>
                Offres d'aide
            </h1>

            <p class="lead mb-3">
                Découvrez les voisins prêts à vous aider dans votre quartier.
            </p>

            @auth
                <a href="{{ route('help-offers.create') }}" class="btn btn-light">
                    <i class="bi bi-plus-circle"></i>
                    Proposer mon aide
                </a>
            @endauth
        </div>
    </section>
@endsection

@section('content')

    @if ($offers->isEmpty())

        <x-card class="text-center py-5">
            <i class="bi bi-people fs-1 text-primary"></i>

            <h4 class="mt-3">
                Aucune offre d'aide
            </h4>

            <p class="text-muted">
                Soyez le premier voisin à proposer votre aide.
            </p>

            @auth
                <a href="{{ route('help-offers.create') }}"
                   class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Proposer mon aide
                </a>
            @endauth
        </x-card>

    @else

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 mb-1">Les offres de vos voisins</h2>
                <p class="text-muted mb-0">
                    {{ $offers->total() }} offre(s) disponible(s)
                </p>
            </div>

            @auth
                <a href="{{ route('help-offers.create') }}"
                   class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Nouvelle offre
                </a>
            @endauth
        </div>

        <div class="row g-4">

            @foreach ($offers as $offer)

                <div class="col-md-6 col-xl-4">

                    <x-card class="h-100">

                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i>
                                {{ $offer->status === 'active' ? 'Disponible' : ucfirst($offer->status) }}
                            </span>

                            @if ($offer->category)
                                <span class="badge bg-light text-dark">
                                    {{ $offer->category }}
                                </span>
                            @endif

                        </div>

                        <h5 class="fw-bold">
                            {{ $offer->title }}
                        </h5>

                        <p class="text-muted">
                            {{ Str::limit($offer->description, 120) }}
                        </p>

                        <div class="small text-muted mb-3">

                            <div class="mb-1">
                                <i class="bi bi-person"></i>
                                {{ $offer->user->name }}
                            </div>

                            @if ($offer->location)
                                <div class="mb-1">
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $offer->location }}
                                </div>
                            @endif

                            @if ($offer->available_from)
                                <div>
                                    <i class="bi bi-calendar"></i>
                                    Disponible à partir du
                                    {{ $offer->available_from->format('d/m/Y H:i') }}
                                </div>
                            @endif

                        </div>

                        <a href="{{ route('help-offers.show', $offer) }}"
                           class="btn btn-outline-primary w-100">
                            <i class="bi bi-eye"></i>
                            Voir l'offre
                        </a>

                    </x-card>

                </div>

            @endforeach

        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $offers->links() }}
        </div>

    @endif

@endsection