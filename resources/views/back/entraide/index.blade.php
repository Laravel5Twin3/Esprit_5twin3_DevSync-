@extends('layouts.back')

@section('title', 'Entraide entre voisins')

@section('page-title')
    <i class="bi bi-people me-2"></i>
    Entraide entre voisins
@endsection

@section('page-actions')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>
        Retour au dashboard
    </a>
@endsection

@section('content')

    {{-- ========================================================= --}}
    {{-- STATISTIQUES                                               --}}
    {{-- ========================================================= --}}

    <div class="row g-4 mb-4">

        {{-- Offres --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="bi bi-hand-thumbs-up fs-3 text-success"></i>
                        </div>

                        <div>
                            <div class="text-muted small">
                                Offres d'aide
                            </div>

                            <div class="fs-3 fw-bold">
                                {{ $offersCount }}
                            </div>
                        </div>

                    </div>

                    <div class="mt-3">
                        <span class="badge bg-success">
                            {{ $activeOffersCount }} active(s)
                        </span>
                    </div>

                </div>
            </div>
        </div>


        {{-- Demandes --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-hand-index-thumb fs-3 text-primary"></i>
                        </div>

                        <div>
                            <div class="text-muted small">
                                Demandes d'aide
                            </div>

                            <div class="fs-3 fw-bold">
                                {{ $requestsCount }}
                            </div>
                        </div>

                    </div>

                    <div class="mt-3">
                        <span class="badge bg-primary">
                            {{ $openRequestsCount }} ouverte(s)
                        </span>
                    </div>

                </div>
            </div>
        </div>


        {{-- Réponses --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="bi bi-chat-dots fs-3 text-warning"></i>
                        </div>

                        <div>
                            <div class="text-muted small">
                                Réponses
                            </div>

                            <div class="fs-3 fw-bold">
                                {{ $responsesCount }}
                            </div>
                        </div>

                    </div>

                    <div class="mt-3">
                        <span class="badge bg-warning text-dark">
                            {{ $pendingResponsesCount }} en attente
                        </span>
                    </div>

                </div>
            </div>
        </div>


        {{-- Total entraide --}}
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="bi bi-people fs-3 text-info"></i>
                        </div>

                        <div>
                            <div class="text-muted small">
                                Activité totale
                            </div>

                            <div class="fs-3 fw-bold">
                                {{ $offersCount + $requestsCount }}
                            </div>
                        </div>

                    </div>

                    <div class="mt-3">
                        <span class="text-muted small">
                            Offres + demandes
                        </span>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DEMANDES D'AIDE                                            --}}
    {{-- ========================================================= --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0">
                        <i class="bi bi-hand-index-thumb text-primary me-2"></i>
                        Dernières demandes d'aide
                    </h5>

                    <small class="text-muted">
                        Les demandes les plus récentes des habitants
                    </small>

                </div>

                <div class="d-flex align-items-center gap-2">

                    <span class="badge bg-primary">
                        {{ $requestsCount }} demande(s)
                    </span>

                </div>

            </div>

        </div>


        <div class="card-body p-0">

            @if ($latestRequests->isEmpty())

                <div class="text-center py-5">

                    <i class="bi bi-inbox display-4 text-muted"></i>

                    <h6 class="mt-3">
                        Aucune demande d'aide
                    </h6>

                    <p class="text-muted mb-0">
                        Les demandes publiées apparaîtront ici.
                    </p>

                </div>

            @else

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="ps-4">#</th>
                                <th>Demande</th>
                                <th>Demandeur</th>
                                <th>Statut</th>
                                <th>Réponses</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($latestRequests as $request)

                                <tr>

                                    {{-- ID --}}
                                    <td class="ps-4">

                                        <span class="text-muted">
                                            #{{ $request->id }}
                                        </span>

                                    </td>


                                    {{-- Demande --}}
                                    <td>

                                        <div class="fw-semibold">

                                            {{ $request->title ?? $request->titre ?? 'Sans titre' }}

                                        </div>

                                        @if (!empty($request->description))

                                            <small class="text-muted">

                                                {{ Str::limit($request->description, 70) }}

                                            </small>

                                        @endif

                                    </td>


                                    {{-- Demandeur --}}
                                    <td>

                                        @if ($request->user)

                                            <div class="d-flex align-items-center">

                                                <div class="rounded-circle bg-light p-2 me-2">
                                                    <i class="bi bi-person"></i>
                                                </div>

                                                <div>

                                                    <div class="fw-semibold">
                                                        {{ $request->user->name }}
                                                    </div>

                                                    <small class="text-muted">
                                                        {{ $request->user->email }}
                                                    </small>

                                                </div>

                                            </div>

                                        @else

                                            <span class="text-muted">
                                                Utilisateur supprimé
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Statut --}}
                                    <td>

                                        @if (($request->status ?? null) === 'open')

                                            <span class="badge bg-success">
                                                <i class="bi bi-unlock me-1"></i>
                                                Ouverte
                                            </span>

                                        @elseif (($request->status ?? null) === 'closed')

                                            <span class="badge bg-secondary">
                                                <i class="bi bi-lock me-1"></i>
                                                Fermée
                                            </span>

                                        @elseif (($request->status ?? null) === 'cancelled')

                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Annulée
                                            </span>

                                        @else

                                            <span class="badge bg-light text-dark">
                                                {{ $request->status ?? 'Inconnue' }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Réponses --}}
                                    <td>

                                        @if ($request->responses_count > 0)

                                            <span class="badge bg-primary">

                                                <i class="bi bi-chat-dots me-1"></i>

                                                {{ $request->responses_count }}

                                            </span>

                                        @else

                                            <span class="badge bg-secondary">
                                                0
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Date --}}
                                    <td>

                                        <span class="text-muted">

                                            {{ $request->created_at->format('d/m/Y') }}

                                        </span>

                                        <br>

                                        <small class="text-muted">

                                            {{ $request->created_at->format('H:i') }}

                                        </small>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-end pe-4">

                                        {{-- Voir --}}
                                        <a
                                            href="{{ route('admin.entraide.requests.show', $request) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Voir la demande"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>


                                        {{-- Supprimer --}}
                                        <form
                                            action="{{ route('admin.entraide.requests.destroy', $request) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Supprimer"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- OFFRES D'AIDE                                              --}}
    {{-- ========================================================= --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0">
                        <i class="bi bi-hand-thumbs-up text-success me-2"></i>
                        Dernières offres d'aide
                    </h5>

                    <small class="text-muted">
                        Les offres les plus récentes des habitants
                    </small>

                </div>

                <span class="badge bg-success">
                    {{ $offersCount }} offre(s)
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            @if ($latestOffers->isEmpty())

                <div class="text-center py-5">

                    <i class="bi bi-inbox display-4 text-muted"></i>

                    <h6 class="mt-3">
                        Aucune offre d'aide
                    </h6>

                    <p class="text-muted mb-0">
                        Les offres publiées apparaîtront ici.
                    </p>

                </div>

            @else

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="ps-4">#</th>
                                <th>Offre</th>
                                <th>Proposée par</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($latestOffers as $offer)

                                <tr>

                                    {{-- ID --}}
                                    <td class="ps-4">

                                        <span class="text-muted">
                                            #{{ $offer->id }}
                                        </span>

                                    </td>


                                    {{-- Offre --}}
                                    <td>

                                        <div class="fw-semibold">

                                            {{ $offer->title ?? $offer->titre ?? 'Sans titre' }}

                                        </div>

                                        @if (!empty($offer->description))

                                            <small class="text-muted">

                                                {{ Str::limit($offer->description, 70) }}

                                            </small>

                                        @endif

                                    </td>


                                    {{-- Utilisateur --}}
                                    <td>

                                        @if ($offer->user)

                                            <div class="d-flex align-items-center">

                                                <div class="rounded-circle bg-light p-2 me-2">
                                                    <i class="bi bi-person"></i>
                                                </div>

                                                <div>

                                                    <div class="fw-semibold">
                                                        {{ $offer->user->name }}
                                                    </div>

                                                    <small class="text-muted">
                                                        {{ $offer->user->email }}
                                                    </small>

                                                </div>

                                            </div>

                                        @else

                                            <span class="text-muted">
                                                Utilisateur supprimé
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Statut --}}
                                    <td>

                                        @if (($offer->status ?? null) === 'active')

                                            <span class="badge bg-success">

                                                <i class="bi bi-check-circle me-1"></i>

                                                Active

                                            </span>

                                        @elseif (($offer->status ?? null) === 'inactive')

                                            <span class="badge bg-secondary">

                                                <i class="bi bi-pause-circle me-1"></i>

                                                Inactive

                                            </span>

                                        @elseif (($offer->status ?? null) === 'closed')

                                            <span class="badge bg-dark">

                                                <i class="bi bi-lock me-1"></i>

                                                Fermée

                                            </span>

                                        @else

                                            <span class="badge bg-light text-dark">

                                                {{ $offer->status ?? 'Inconnue' }}

                                            </span>

                                        @endif

                                    </td>


                                    {{-- Date --}}
                                    <td>

                                        <span class="text-muted">

                                            {{ $offer->created_at->format('d/m/Y') }}

                                        </span>

                                        <br>

                                        <small class="text-muted">

                                            {{ $offer->created_at->format('H:i') }}

                                        </small>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="text-end pe-4">

                                        {{-- Voir --}}
                                        <a
                                            href="{{ route('admin.entraide.offers.show', $offer) }}"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Voir l'offre"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>


                                        {{-- Supprimer --}}
                                        <form
                                            action="{{ route('admin.entraide.offers.destroy', $offer) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Supprimer"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

@endsection
