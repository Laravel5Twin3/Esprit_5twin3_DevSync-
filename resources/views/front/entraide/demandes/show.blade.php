@extends('layouts.front')

@section('title', $helpRequest->title)

@section('hero')
    <section class="ha-hero text-white">
        <div class="container py-4">

            <a href="{{ route('help-requests.index') }}"
               class="text-white text-decoration-none">
                <i class="bi bi-arrow-left"></i>
                Toutes les demandes
            </a>

            <h1 class="fw-bold mt-2">
                {{ $helpRequest->title }}
            </h1>

            <p class="lead mb-0">
                Une demande d'aide entre voisins
            </p>

        </div>
    </section>
@endsection

@section('content')

    <div class="row g-4">

        {{-- Demande --}}
        <div class="col-lg-8">

            <x-card>

                <div class="d-flex justify-content-between align-items-start mb-3">

                    @if ($helpRequest->status === 'open')

                        <span class="badge bg-success">
                            <i class="bi bi-unlock"></i>
                            Demande ouverte
                        </span>

                    @elseif ($helpRequest->status === 'in_progress')

                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-hourglass-split"></i>
                            Aide en cours
                        </span>

                    @elseif ($helpRequest->status === 'completed')

                        <span class="badge bg-secondary">
                            <i class="bi bi-check-circle"></i>
                            Terminée
                        </span>

                    @else

                        <span class="badge bg-danger">
                            Annulée
                        </span>

                    @endif

                    @if ($helpRequest->category)
                        <span class="badge bg-light text-dark">
                            {{ $helpRequest->category }}
                        </span>
                    @endif

                </div>

                <p style="white-space: pre-line;">
                    {{ $helpRequest->description }}
                </p>

                <hr>

                <div class="row g-3">

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Demandé par
                        </div>

                        <div>
                            <i class="bi bi-person"></i>
                            {{ $helpRequest->user->name }}
                        </div>

                    </div>

                    @if ($helpRequest->location)

                        <div class="col-md-6">

                            <div class="text-muted small">
                                Zone
                            </div>

                            <div>
                                <i class="bi bi-geo-alt"></i>
                                {{ $helpRequest->location }}
                            </div>

                        </div>

                    @endif

                    @if ($helpRequest->needed_from)

                        <div class="col-md-6">

                            <div class="text-muted small">
                                Besoin à partir de
                            </div>

                            <div>
                                <i class="bi bi-calendar"></i>
                                {{ $helpRequest->needed_from->format('d/m/Y H:i') }}
                            </div>

                        </div>

                    @endif

                    @if ($helpRequest->needed_until)

                        <div class="col-md-6">

                            <div class="text-muted small">
                                Besoin jusqu'à
                            </div>

                            <div>
                                <i class="bi bi-calendar-check"></i>
                                {{ $helpRequest->needed_until->format('d/m/Y H:i') }}
                            </div>

                        </div>

                    @endif

                </div>

            </x-card>


            {{-- Formulaire pour proposer son aide --}}
            @auth

                @if (
                    auth()->id() !== $helpRequest->user_id
                    && $helpRequest->status === 'open'
                )

                    <x-card title="Proposer votre aide" class="mt-4">

                        <form method="POST"
                              action="{{ route('help-responses.store', $helpRequest) }}">

                            @csrf

                            <div class="mb-3">

                                <label for="message" class="form-label">
                                    Votre message
                                </label>

                                <textarea
                                    id="message"
                                    name="message"
                                    rows="4"
                                    class="form-control @error('message') is-invalid @enderror"
                                    placeholder="Expliquez comment vous pouvez aider..."
                                >{{ old('message') }}</textarea>

                                @error('message')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="bi bi-hand-thumbs-up"></i>
                                Je peux aider
                            </button>

                        </form>

                    </x-card>

                @endif

            @endauth


            {{-- Réponses --}}
            <x-card class="mt-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="mb-0">
                        <i class="bi bi-people"></i>
                        Personnes intéressées
                    </h5>

                    <span class="badge bg-primary">
                        {{ $helpRequest->responses->count() }}
                    </span>

                </div>

                @forelse ($helpRequest->responses as $response)

                    <div class="border rounded p-3 mb-3">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>

                                <strong>
                                    {{ $response->user->name }}
                                </strong>

                                <div class="text-muted small">
                                    {{ $response->created_at->diffForHumans() }}
                                </div>

                            </div>

                            @if ($response->status === 'pending')

                                <span class="badge bg-warning text-dark">
                                    En attente
                                </span>

                            @elseif ($response->status === 'accepted')

                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i>
                                    Acceptée
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    Refusée
                                </span>

                            @endif

                        </div>

                        @if ($response->message)

                            <p class="mt-3 mb-3">
                                {{ $response->message }}
                            </p>

                        @endif


                        {{-- Actions du propriétaire --}}
                        @auth

                            @if (
                                auth()->id() === $helpRequest->user_id
                                && $response->status === 'pending'
                            )

                                <div class="d-flex gap-2">

                                    <form method="POST"
                                          action="{{ route('help-responses.accept', $response) }}">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-success btn-sm">
                                            <i class="bi bi-check"></i>
                                            Accepter
                                        </button>

                                    </form>

                                    <form method="POST"
                                          action="{{ route('help-responses.reject', $response) }}">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-x"></i>
                                            Refuser
                                        </button>

                                    </form>

                                </div>

                            @endif


                            {{-- Suppression de sa propre réponse --}}
                            @if (
                                auth()->id() === $response->user_id
                                && $response->status === 'pending'
                            )

                                <form method="POST"
                                      action="{{ route('help-responses.destroy', $response) }}"
                                      class="mt-2"
                                      onsubmit="return confirm('Supprimer votre proposition ?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-link btn-sm text-danger p-0">
                                        Supprimer ma proposition
                                    </button>

                                </form>

                            @endif

                        @endauth

                    </div>

                @empty

                    <div class="text-center py-4">

                        <i class="bi bi-person-plus fs-1 text-muted"></i>

                        <p class="text-muted mt-2 mb-0">
                            Personne n'a encore proposé son aide.
                        </p>

                    </div>

                @endforelse

            </x-card>

        </div>


        {{-- Actions --}}
        <div class="col-lg-4">

            <x-card title="Actions">

                @auth

                    @if (auth()->id() === $helpRequest->user_id)

                        <a href="{{ route('help-requests.edit', $helpRequest) }}"
                           class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-pencil"></i>
                            Modifier
                        </a>

                        <form method="POST"
                              action="{{ route('help-requests.destroy', $helpRequest) }}"
                              onsubmit="return confirm('Voulez-vous vraiment supprimer cette demande ?')">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash"></i>
                                Supprimer
                            </button>

                        </form>

                    @elseif ($helpRequest->status === 'open')

                        <div class="alert alert-info mb-0">
                            <i class="bi bi-heart"></i>
                            Vous pouvez proposer votre aide à ce voisin.
                        </div>

                    @endif

                @else

                    <p class="text-muted">
                        Connectez-vous pour proposer votre aide.
                    </p>

                    <a href="{{ route('login') }}"
                       class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Se connecter
                    </a>

                @endauth

            </x-card>

        </div>

    </div>

@endsection
