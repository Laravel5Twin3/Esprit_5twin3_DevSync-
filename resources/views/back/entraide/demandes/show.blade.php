@extends('layouts.back')

@section('title', 'Détail de la demande')

@section('page-title')
    <i class="bi bi-hand-index-thumb me-2"></i>
    Détail de la demande
@endsection

@section('page-actions')
    <a
        href="{{ route('admin.entraide.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Retour
    </a>
@endsection

@section('content')

    <div class="row g-4">

        {{-- ===================================================== --}}
        {{-- INFORMATIONS DEMANDE                                  --}}
        {{-- ===================================================== --}}

        <div class="col-lg-5">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Informations
                    </h5>
                </div>

                <div class="card-body">

                    <h4 class="mb-3">
                        {{ $helpRequest->title ?? $helpRequest->titre ?? 'Sans titre' }}
                    </h4>


                    @if (!empty($helpRequest->description))

                        <p class="text-muted">
                            {{ $helpRequest->description }}
                        </p>

                    @else

                        <p class="text-muted fst-italic">
                            Aucune description.
                        </p>

                    @endif


                    <hr>


                    {{-- Demandeur --}}
                    <div class="mb-4">

                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-person"></i>
                            Demandeur
                        </small>

                        @if ($helpRequest->user)

                            <div class="fw-semibold">
                                {{ $helpRequest->user->name }}
                            </div>

                            <small class="text-muted">
                                {{ $helpRequest->user->email }}
                            </small>

                        @else

                            <span class="text-muted">
                                Utilisateur supprimé
                            </span>

                        @endif

                    </div>


                    {{-- Date --}}
                    <div class="mb-4">

                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-calendar"></i>
                            Date de publication
                        </small>

                        <span>
                            {{ $helpRequest->created_at->format('d/m/Y à H:i') }}
                        </span>

                    </div>


                    {{-- Nombre de réponses --}}
                    <div>

                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-people"></i>
                            Réponses
                        </small>

                        <span class="badge bg-primary fs-6">
                            {{ $helpRequest->responses->count() }}
                        </span>

                    </div>

                </div>

            </div>


            {{-- Supprimer --}}
            <div class="card border-0 shadow-sm mt-4">

                <div class="card-body">

                    <h6 class="text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Zone dangereuse
                    </h6>

                    <p class="small text-muted">
                        La suppression de cette demande peut également supprimer
                        ses réponses associées selon la configuration de la base.
                    </p>

                    <form
                        action="{{ route('admin.entraide.requests.destroy', $helpRequest) }}"
                        method="POST"
                        onsubmit="return confirm('Supprimer définitivement cette demande ?');"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            <i class="bi bi-trash me-1"></i>
                            Supprimer la demande
                        </button>

                    </form>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- REPONSES                                               --}}
        {{-- ===================================================== --}}

        <div class="col-lg-7">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h5 class="mb-0">
                                <i class="bi bi-people text-success me-2"></i>
                                Personnes ayant répondu
                            </h5>

                            <small class="text-muted">
                                Plusieurs voisins peuvent répondre à cette demande.
                            </small>

                        </div>

                        <span class="badge bg-primary">
                            {{ $helpRequest->responses->count() }}
                        </span>

                    </div>

                </div>


                <div class="card-body">

                    @if ($helpRequest->responses->isEmpty())

                        <div class="text-center py-5">

                            <i class="bi bi-person-x display-5 text-muted"></i>

                            <h6 class="mt-3">
                                Aucune réponse
                            </h6>

                            <p class="text-muted mb-0">
                                Aucun voisin n'a encore proposé son aide.
                            </p>

                        </div>

                    @else

                        @foreach ($helpRequest->responses as $response)

                            <div class="border rounded p-3 mb-3">

                                <div class="d-flex justify-content-between align-items-start">

                                    {{-- Utilisateur --}}
                                    <div class="d-flex align-items-center">

                                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3">
                                            <i class="bi bi-person text-success fs-5"></i>
                                        </div>

                                        <div>

                                            @if ($response->user)

                                                <div class="fw-semibold">
                                                    {{ $response->user->name }}
                                                </div>

                                                <small class="text-muted">
                                                    {{ $response->user->email }}
                                                </small>

                                            @else

                                                <span class="text-muted">
                                                    Utilisateur supprimé
                                                </span>

                                            @endif

                                        </div>

                                    </div>


                                    {{-- Statut --}}
                                    @if (($response->status ?? null) === 'accepted')

                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Acceptée
                                        </span>

                                    @elseif (($response->status ?? null) === 'rejected')

                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Refusée
                                        </span>

                                    @else

                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock me-1"></i>
                                            En attente
                                        </span>

                                    @endif

                                </div>


                                {{-- Message --}}
                                @if (!empty($response->message))

                                    <div class="bg-light rounded p-3 mt-3">

                                        <small class="text-muted d-block mb-1">
                                            Message
                                        </small>

                                        {{ $response->message }}

                                    </div>

                                @endif


                                {{-- Footer réponse --}}
                                <div class="d-flex justify-content-between align-items-center mt-3">

                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $response->created_at->format('d/m/Y à H:i') }}
                                    </small>


                                    <form
                                        action="{{ route('admin.entraide.responses.destroy', $response) }}"
                                        method="POST"
                                        onsubmit="return confirm('Supprimer cette réponse ?');"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            <i class="bi bi-trash"></i>
                                            Supprimer
                                        </button>

                                    </form>

                                </div>

                            </div>

                        @endforeach

                    @endif

                </div>

            </div>

        </div>

    </div>

@endsection