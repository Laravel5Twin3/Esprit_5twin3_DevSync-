@extends('layouts.front')

@section('title', $helpOffer->title)

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">

            <div class="mb-2">
                <a href="{{ route('help-offers.index') }}"
                   class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left"></i>
                    Toutes les offres
                </a>
            </div>

            <h1 class="fw-bold">
                {{ $helpOffer->title }}
            </h1>

            <p class="lead mb-0">
                Une proposition d'aide entre voisins
            </p>

        </div>
    </section>
@endsection

@section('content')

    <div class="row g-4">

        <div class="col-lg-8">

            <x-card>

                <div class="d-flex justify-content-between align-items-start mb-3">

                    <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i>
                        {{ $helpOffer->status === 'active' ? 'Disponible' : ucfirst($helpOffer->status) }}
                    </span>

                    @if ($helpOffer->category)
                        <span class="badge bg-light text-dark">
                            {{ $helpOffer->category }}
                        </span>
                    @endif

                </div>

                <h2 class="h4">
                    {{ $helpOffer->title }}
                </h2>

                <p class="mt-3" style="white-space: pre-line;">
                    {{ $helpOffer->description }}
                </p>

                <hr>

                <div class="row g-3">

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Proposé par
                        </div>

                        <div>
                            <i class="bi bi-person"></i>
                            {{ $helpOffer->user->name }}
                        </div>

                    </div>

                    @if ($helpOffer->location)

                        <div class="col-md-6">

                            <div class="text-muted small">
                                Zone
                            </div>

                            <div>
                                <i class="bi bi-geo-alt"></i>
                                {{ $helpOffer->location }}
                            </div>

                        </div>

                    @endif

                    @if ($helpOffer->available_from)

                        <div class="col-md-6">

                            <div class="text-muted small">
                                Disponible à partir de
                            </div>

                            <div>
                                <i class="bi bi-calendar"></i>
                                {{ $helpOffer->available_from->format('d/m/Y H:i') }}
                            </div>

                        </div>

                    @endif

                    @if ($helpOffer->available_until)

                        <div class="col-md-6">

                            <div class="text-muted small">
                                Disponible jusqu'à
                            </div>

                            <div>
                                <i class="bi bi-calendar-check"></i>
                                {{ $helpOffer->available_until->format('d/m/Y H:i') }}
                            </div>

                        </div>

                    @endif

                </div>

            </x-card>

        </div>

        <div class="col-lg-4">

            <x-card title="Actions">

                @auth

                    @if (auth()->id() === $helpOffer->user_id)

                        <a href="{{ route('help-offers.edit', $helpOffer) }}"
                           class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-pencil"></i>
                            Modifier
                        </a>

                        <form method="POST"
                              action="{{ route('help-offers.destroy', $helpOffer) }}"
                              onsubmit="return confirm('Voulez-vous vraiment supprimer cette offre ?')">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash"></i>
                                Supprimer
                            </button>

                        </form>

                    @else

                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i>
                            Cette personne propose son aide aux voisins.
                        </div>

                    @endif

                @else

                    <p class="text-muted">
                        Connectez-vous pour interagir avec cette offre.
                    </p>

                    <a href="{{ route('login') }}"
                       class="btn btn-primary w-100">
                        Se connecter
                    </a>

                @endauth

            </x-card>

        </div>

    </div>

@endsection
